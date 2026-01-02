<?php
/**
 * Quran Data Handler Class
 *
 * @package QuranSimple
 * @since 2.2.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Global arrays for sura and juz data
$GLOBALS['suraData'] = array();
$GLOBALS['juzData'] = array();

/**
 * Initialize sura and juz data from XML
 */
function initSuraData() {
    global $suraData, $juzData;
    $metadataFile = $GLOBALS['quran_simple_files']['metadata'];
    $dataItems = array("index", "start", "ayas", "name", "tname", "ename", "type", "rukus");

    $quranData = file_get_contents($metadataFile);
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 1);
    xml_parse_into_struct($parser, $quranData, $values, $index);
    xml_parser_free($parser);

    // Parse Sura data
    for ($i = 1; $i <= 114; $i++) {
        $j = $index['SURA'][$i - 1];
        foreach ($dataItems as $item) {
            $suraData[$i][$item] = $values[$j]['attributes'][strtoupper($item)];
        }
    }

    // Parse Juz data from XML
    if (isset($index['JUZ']) && count($index['JUZ']) >= 30) {
        for ($i = 1; $i <= 30; $i++) {
            $j = $index['JUZ'][$i - 1];
            $juzData[$i]['sura'] = intval($values[$j]['attributes']['SURA']);
            $juzData[$i]['aya'] = intval($values[$j]['attributes']['AYA']);
        }
    } else {
        // Fallback: Hardcoded Juz/Para data if XML parsing fails
        $juzData = array(
            1  => array('sura' => 1,  'aya' => 1),
            2  => array('sura' => 2,  'aya' => 142),
            3  => array('sura' => 2,  'aya' => 253),
            4  => array('sura' => 3,  'aya' => 93),
            5  => array('sura' => 4,  'aya' => 24),
            6  => array('sura' => 4,  'aya' => 148),
            7  => array('sura' => 5,  'aya' => 82),
            8  => array('sura' => 6,  'aya' => 111),
            9  => array('sura' => 7,  'aya' => 88),
            10 => array('sura' => 8,  'aya' => 41),
            11 => array('sura' => 9,  'aya' => 93),
            12 => array('sura' => 11, 'aya' => 6),
            13 => array('sura' => 12, 'aya' => 53),
            14 => array('sura' => 15, 'aya' => 1),
            15 => array('sura' => 17, 'aya' => 1),
            16 => array('sura' => 18, 'aya' => 75),
            17 => array('sura' => 21, 'aya' => 1),
            18 => array('sura' => 23, 'aya' => 1),
            19 => array('sura' => 25, 'aya' => 21),
            20 => array('sura' => 27, 'aya' => 56),
            21 => array('sura' => 29, 'aya' => 46),
            22 => array('sura' => 33, 'aya' => 31),
            23 => array('sura' => 36, 'aya' => 28),
            24 => array('sura' => 39, 'aya' => 32),
            25 => array('sura' => 41, 'aya' => 47),
            26 => array('sura' => 46, 'aya' => 1),
            27 => array('sura' => 51, 'aya' => 31),
            28 => array('sura' => 58, 'aya' => 1),
            29 => array('sura' => 67, 'aya' => 1),
            30 => array('sura' => 78, 'aya' => 1)
        );
    }
}

/**
 * Get sura property
 *
 * @param int $sura Sura number
 * @param string $property Property name
 * @return mixed
 */
function getSuraData($sura, $property) {
    global $suraData;
    return isset($suraData[$sura][$property]) ? $suraData[$sura][$property] : null;
}

/**
 * Get juz property
 *
 * @param int $juz Juz number
 * @param string $property Property name
 * @return mixed
 */
function getJuzData($juz, $property) {
    global $juzData;
    return isset($juzData[$juz][$property]) ? $juzData[$juz][$property] : null;
}

/**
 * Get current juz for a sura
 *
 * @param int $sura Sura number
 * @return int
 */
function getCurrentJuz($sura) {
    global $juzData;
    $currentJuz = 1;

    // Ensure juzData is populated
    if (empty($juzData)) {
        return 1;
    }

    for ($i = 30; $i >= 1; $i--) {
        if (isset($juzData[$i]['sura']) && $sura >= $juzData[$i]['sura']) {
            $currentJuz = $i;
            break;
        }
    }
    return $currentJuz;
}

/**
 * Get sura contents from file
 *
 * @param int $sura Sura number
 * @param string $file File path
 * @return array
 */
function getSuraContents($sura, $file) {
    $text = file($file);
    $startAya = getSuraData($sura, 'start');
    $endAya = $startAya + getSuraData($sura, 'ayas');
    $content = array_slice($text, $startAya, $endAya - $startAya);
    return $content;
}

/**
 * Generate Sura dropdown HTML
 *
 * @param int $currentSura Current sura number
 * @return string
 */
function getSuraDropdown($currentSura) {
    global $suraData;
    $html = '<select id="sura-select" class="quran-dropdown">';
    for ($i = 1; $i <= 114; $i++) {
        $selected = ($i == $currentSura) ? 'selected' : '';
        $name = $suraData[$i]['name'];
        $tname = $suraData[$i]['tname'];
        $html .= "<option value=\"$i\" $selected>$i. $tname ($name)</option>";
    }
    $html .= '</select>';
    return $html;
}

/**
 * Generate Juz dropdown HTML
 *
 * @param int $currentSura Current sura number
 * @return string
 */
function getJuzDropdown($currentSura) {
    global $juzData;
    $currentJuz = getCurrentJuz($currentSura);
    $html = '<select id="juz-select" class="quran-dropdown">';
    for ($i = 1; $i <= 30; $i++) {
        $selected = ($i == $currentJuz) ? 'selected' : '';
        $juzSura = isset($juzData[$i]['sura']) ? intval($juzData[$i]['sura']) : 1;
        $juzAya = isset($juzData[$i]['aya']) ? intval($juzData[$i]['aya']) : 1;
        $html .= "<option value=\"$i\" data-sura=\"$juzSura\" data-aya=\"$juzAya\" $selected>Para $i</option>";
    }
    $html .= '</select>';
    return $html;
}

/**
 * Generate Previous/Next navigation buttons
 *
 * @param int $currentSura Current sura number
 * @return string
 */
function getNavigationButtons($currentSura) {
    global $suraData;
    $html = '<div class="nav-buttons">';

    // Previous button
    if ($currentSura > 1) {
        $prevSura = $currentSura - 1;
        $prevName = $suraData[$prevSura]['tname'];
        $html .= '<a href="?sura=' . $prevSura . '" class="nav-btn nav-prev">
            <svg viewBox="0 0 24 24" width="20" height="20"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
            <span>' . $prevSura . '. ' . $prevName . '</span>
        </a>';
    } else {
        $html .= '<span class="nav-btn nav-prev disabled">
            <svg viewBox="0 0 24 24" width="20" height="20"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
            <span>Start</span>
        </span>';
    }

    // Current surah info
    $html .= '<span class="nav-current">' . $currentSura . ' / 114</span>';

    // Next button
    if ($currentSura < 114) {
        $nextSura = $currentSura + 1;
        $nextName = $suraData[$nextSura]['tname'];
        $html .= '<a href="?sura=' . $nextSura . '" class="nav-btn nav-next">
            <span>' . $nextSura . '. ' . $nextName . '</span>
            <svg viewBox="0 0 24 24" width="20" height="20"><path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z"/></svg>
        </a>';
    } else {
        $html .= '<span class="nav-btn nav-next disabled">
            <span>End</span>
            <svg viewBox="0 0 24 24" width="20" height="20"><path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z"/></svg>
        </span>';
    }

    $html .= '</div>';
    return $html;
}
