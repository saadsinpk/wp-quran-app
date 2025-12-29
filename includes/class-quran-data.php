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
    xml_parse_into_struct($parser, $quranData, $values, $index);
    xml_parser_free($parser);

    // Parse Sura data
    for ($i = 1; $i <= 114; $i++) {
        $j = $index['SURA'][$i - 1];
        foreach ($dataItems as $item) {
            $suraData[$i][$item] = $values[$j]['attributes'][strtoupper($item)];
        }
    }

    // Parse Juz data
    if (isset($index['JUZ'])) {
        for ($i = 1; $i <= 30; $i++) {
            $j = $index['JUZ'][$i - 1];
            $juzData[$i]['sura'] = $values[$j]['attributes']['SURA'];
            $juzData[$i]['aya'] = $values[$j]['attributes']['AYA'];
        }
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
    for ($i = 30; $i >= 1; $i--) {
        if ($sura >= $juzData[$i]['sura']) {
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
        $html .= "<option value=\"{$juzData[$i]['sura']}\" $selected>Para $i (Juz $i)</option>";
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
