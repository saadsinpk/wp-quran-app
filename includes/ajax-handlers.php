<?php
/**
 * AJAX Handlers for Quran Simple
 *
 * @package QuranSimple
 * @since 2.2.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Update Arabic visibility session
 */
function update_arabic() {
    if ($_SESSION['arabic'] == 1) {
        $_SESSION['arabic'] = 2;
    } else {
        $_SESSION['arabic'] = 1;
    }
    echo $_SESSION['arabic'];
    exit();
}
add_action('wp_ajax_update_arabic', 'update_arabic');
add_action('wp_ajax_nopriv_update_arabic', 'update_arabic');

/**
 * Update English visibility session
 */
function update_english() {
    if ($_SESSION['english'] == 1) {
        $_SESSION['english'] = 2;
    } else {
        $_SESSION['english'] = 1;
    }
    echo $_SESSION['english'];
    exit();
}
add_action('wp_ajax_update_english', 'update_english');
add_action('wp_ajax_nopriv_update_english', 'update_english');

/**
 * Update Urdu visibility session
 */
function update_urdu() {
    if ($_SESSION['urdu'] == 1) {
        $_SESSION['urdu'] = 2;
    } else {
        $_SESSION['urdu'] = 1;
    }
    echo $_SESSION['urdu'];
    exit();
}
add_action('wp_ajax_update_urdu', 'update_urdu');
add_action('wp_ajax_nopriv_update_urdu', 'update_urdu');

/**
 * Strip Arabic diacritics (harakat) from text for searching
 */
function strip_arabic_diacritics($text) {
    // Arabic diacritics: Fathah, Dammah, Kasrah, Sukun, Shadda, Tanwin (Fathatan, Dammatan, Kasratan), Maddah, Hamza above/below, Superscript Alef, etc.
    $diacritics = array(
        "\xD9\x8E", // Fathah َ
        "\xD9\x8F", // Dammah ُ
        "\xD9\x90", // Kasrah ِ
        "\xD9\x91", // Shadda ّ
        "\xD9\x92", // Sukun ْ
        "\xD9\x8B", // Tanwin Fathatan ً
        "\xD9\x8C", // Tanwin Dammatan ٌ
        "\xD9\x8D", // Tanwin Kasratan ٍ
        "\xD9\x93", // Maddah ٓ
        "\xD9\x94", // Hamza above ٔ
        "\xD9\x95", // Hamza below ٕ
        "\xD9\xB0", // Superscript Alef ٰ
        "\xD8\x90", // Tatweel ـ
        "\xDB\x96", // Small high Seen ۖ
        "\xDB\x97", // Small high rounded zero ۗ
        "\xDB\x98", // Small high Meem ۘ
        "\xDB\x99", // Small high Lam Alef ۙ
        "\xDB\x9A", // Small high Jeem ۚ
        "\xDB\x9B", // Small high three dots ۛ
        "\xDB\x9C", // Small high Seen (2) ۜ
    );
    return str_replace($diacritics, '', $text);
}

/**
 * Search Quran AJAX handler
 */
function search_quran() {
    $quranFile = $GLOBALS['quran_simple_files']['quran'];
    $transFile = $GLOBALS['quran_simple_files']['english'];
    $transFileUrdu = $GLOBALS['quran_simple_files']['urdu'];
    $metadataFile = $GLOBALS['quran_simple_files']['metadata'];

    $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';
    $query = trim($query);

    if (empty($query) || strlen($query) < 2) {
        echo json_encode(array('results' => array(), 'message' => 'Please enter at least 2 characters'));
        exit();
    }

    // Initialize sura data
    $suraData = array();
    $dataItems = array("index", "start", "ayas", "name", "tname", "ename");
    $quranData = file_get_contents($metadataFile);
    $parser = xml_parser_create();
    xml_parse_into_struct($parser, $quranData, $values, $index);
    xml_parser_free($parser);

    for ($i = 1; $i <= 114; $i++) {
        $j = $index['SURA'][$i - 1];
        foreach ($dataItems as $item) {
            $suraData[$i][$item] = $values[$j]['attributes'][strtoupper($item)];
        }
    }

    $results = array();
    $maxResults = 50;

    // Strip diacritics from query for Arabic/Urdu matching
    $queryClean = strip_arabic_diacritics($query);

    // Search Arabic
    $arabicText = file($quranFile);
    $lineNum = 0;
    foreach ($arabicText as $line) {
        if (count($results) >= $maxResults) break;
        $lineClean = strip_arabic_diacritics($line);
        if (mb_stripos($lineClean, $queryClean) !== false) {
            // Find which sura this belongs to
            $currentSura = 1;
            $ayaInSura = $lineNum + 1;
            for ($s = 114; $s >= 1; $s--) {
                if ($lineNum >= $suraData[$s]['start']) {
                    $currentSura = $s;
                    $ayaInSura = $lineNum - $suraData[$s]['start'] + 1;
                    break;
                }
            }
            $results[] = array(
                'sura' => $currentSura,
                'aya' => $ayaInSura,
                'suraName' => $suraData[$currentSura]['tname'],
                'text' => trim($line),
                'type' => 'arabic'
            );
        }
        $lineNum++;
    }

    // Search English
    $englishText = file($transFile);
    foreach ($englishText as $line) {
        if (count($results) >= $maxResults) break;
        $parts = explode("|", $line);
        if (count($parts) >= 3 && mb_stripos($parts[2], $query) !== false) {
            $sura = intval($parts[0]);
            $aya = intval($parts[1]);
            $results[] = array(
                'sura' => $sura,
                'aya' => $aya,
                'suraName' => $suraData[$sura]['tname'],
                'text' => trim($parts[2]),
                'type' => 'english'
            );
        }
    }

    // Search Urdu
    $urduText = file($transFileUrdu);
    foreach ($urduText as $line) {
        if (count($results) >= $maxResults) break;
        $parts = explode("|", $line);
        if (count($parts) >= 3 && mb_stripos(strip_arabic_diacritics($parts[2]), $queryClean) !== false) {
            $sura = intval($parts[0]);
            $aya = intval($parts[1]);
            $results[] = array(
                'sura' => $sura,
                'aya' => $aya,
                'suraName' => $suraData[$sura]['tname'],
                'text' => trim($parts[2]),
                'type' => 'urdu'
            );
        }
    }

    // Remove duplicates (same sura:aya:type)
    $unique = array();
    $seen = array();
    foreach ($results as $r) {
        $key = $r['sura'] . ':' . $r['aya'] . ':' . $r['type'];
        if (!isset($seen[$key])) {
            $seen[$key] = true;
            $unique[] = $r;
        }
    }

    echo json_encode(array('results' => $unique, 'count' => count($unique)));
    exit();
}
add_action('wp_ajax_search_quran', 'search_quran');
add_action('wp_ajax_nopriv_search_quran', 'search_quran');
