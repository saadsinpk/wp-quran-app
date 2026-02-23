<?php
/**
 * Display Functions for Quran Simple
 *
 * @package QuranSimple
 * @since 2.2.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Display a sura with all verses
 *
 * @param int $sura Sura number
 * @return string HTML output
 */
function showSura($sura) {
    global $suraData;
    $quranFile = $GLOBALS['quran_simple_files']['quran'];
    $transFile = $GLOBALS['quran_simple_files']['english'];
    $transFileUrdu = $GLOBALS['quran_simple_files']['urdu'];
    $audioFileMp3 = $GLOBALS['quran_simple_files']['audio'];

    $suraName = getSuraData($sura, 'name');
    $suraTname = getSuraData($sura, 'tname');
    $totalAyas = getSuraData($sura, 'ayas');
    $totalRukus = getSuraData($sura, 'rukus');
    $suraParas = getSuraParas($sura);
    $suraText = getSuraContents($sura, $quranFile);
    $transText = getSuraContents($sura, $transFile);
    $transTextUrdu = getSuraContents($sura, $transFileUrdu);

    // Parse audio file
    $audioFileContent = file($audioFileMp3);
    $audioFileLines = array();
    foreach ($audioFileContent as $line) {
        $lineData = explode("|", $line);
        if (isset($lineData[1])) {
            $audioFileLines[$lineData[0]] = $lineData[1];
        }
    }

    $audioUrl = isset($audioFileLines[$sura]) ? trim($audioFileLines[$sura]) : '';

    $showBismillah = true;
    $ayaNum = 1;
    $html = '';

    // Audio player

    // Sura name header with stats
    $html .= "<div class='suraName'>";
    $html .= "<span class='sura-stat sura-stat-left'>Ayat: $totalAyas</span>";
    $html .= "<span class='sura-title'>سورة $suraName</span>";
    $html .= "<span class='sura-stat sura-stat-right'>Ruku: $totalRukus | Para: $suraParas</span>";
    $html .= "</div>";

    // Loop through verses
    foreach ($suraText as $aya) {
        $trans = $transText[$ayaNum - 1];
        $transurdu = $transTextUrdu[$ayaNum - 1];
        $transArr = explode("|", $trans);
        $transurduArr = explode("|", $transurdu);
        $transDisplay = '<span class="ayaNum"> (' . $transArr[1] . ') </span>' . $transArr[2] ;
// 		$transDisplay = $transArr[2] .'<span class="ayaNum"> (' . $transArr[1] . ') </span>' ; ayanum end me
        $transurduDisplay = '<span class="ayaNum"> (' . $transurduArr[1] . ') </span>' . $transurduArr[2];
//      $transurduDisplay = $transurduArr[2] .'<span class="ayaNum"> (' . $transurduArr[1] . ') </span>' ;

        // Store clean text for copy/share
        $arabicClean = trim($aya);
        $englishClean = isset($transArr[2]) ? trim($transArr[2]) : '';
        $urduClean = isset($transurduArr[2]) ? trim($transurduArr[2]) : '';

        // Remove bismillahs, except for suras 1 and 9
        if (!$showBismillah && $ayaNum == 1 && $sura != 1 && $sura != 9) {
            $aya = preg_replace('/^(([^ ]+ ){4})/u', '', $aya);
        }

        // Display waqf marks in different style
        $aya = preg_replace('/ ([ۖ-۩])/u', '<span class="sign">&nbsp;$1</span>', $aya);

        // Audio URL for this ayat
        $ayaAudioUrl = 'https://alahazrat.info/library/Al-Quran/Recitation/Alafasy/' . str_pad($sura, 3, '0', STR_PAD_LEFT) . str_pad($ayaNum, 3, '0', STR_PAD_LEFT) . '.mp3';

        // Action buttons (Play, Bookmark, Copy, Share, Image, QR)
        $actionBtns = '<div class="aya-actions">
            <span class="action-btn play-btn" onclick="playAyat(' . $sura . ', ' . $ayaNum . ')" data-sura="' . $sura . '" data-aya="' . $ayaNum . '" data-audio="' . $ayaAudioUrl . '" title="Play">
                <svg class="play-icon" viewBox="0 0 24 24" width="20" height="20">
                    <path d="M8 5v14l11-7z"/>
                </svg>
                <svg class="pause-icon" viewBox="0 0 24 24" width="20" height="20" style="display:none;">
                    <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                </svg>
            </span>
            <span class="action-btn bookmark-btn" onclick="toggleBookmark(' . $sura . ', ' . $ayaNum . ')" data-sura="' . $sura . '" data-aya="' . $ayaNum . '" title="Bookmark">
                <svg class="bookmark-icon" viewBox="0 0 24 24" width="20" height="20">
                    <path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/>
                </svg>
            </span>
            <span class="action-btn copy-btn" onclick="copyVerse(' . $sura . ', ' . $ayaNum . ')" title="Copy">
                <svg viewBox="0 0 24 24" width="20" height="20">
                    <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>
                </svg>
            </span>
            <span class="action-btn share-btn" onclick="shareVerse(' . $sura . ', ' . $ayaNum . ')" title="Share">
                <svg viewBox="0 0 24 24" width="20" height="20">
                    <path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z"/>
                </svg>
            </span>
            <span class="action-btn image-btn" onclick="openImageGenerator(' . $sura . ', ' . $ayaNum . ')" title="Create Image">
                <svg viewBox="0 0 24 24" width="20" height="20">
                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                </svg>
            </span>
            <span class="action-btn qr-btn" onclick="openQRGenerator(' . $sura . ', ' . $ayaNum . ')" title="QR Code">
                <svg viewBox="0 0 24 24" width="20" height="20">
                    <path d="M3 11h8V3H3v8zm2-6h4v4H5V5zm8-2v8h8V3h-8zm6 6h-4V5h4v4zM3 21h8v-8H3v8zm2-6h4v4H5v-4zm13 2h-2v2h2v2h-4v-2h2v-2h-2v-4h2v2h2v2zm0-4h2v4h-2v-4zm-4 4h2v4h-2v-4z"/>
                </svg>
            </span>
        </div>';

        // Hidden data for copy/share
        $verseData = '<div class="verse-data" data-sura="' . $sura . '" data-aya="' . $ayaNum . '"
            data-sura-name="' . $suraTname . '"
            data-arabic="' . htmlspecialchars($arabicClean, ENT_QUOTES) . '"
            data-english="' . htmlspecialchars($englishClean, ENT_QUOTES) . '"
            data-urdu="' . htmlspecialchars($urduClean, ENT_QUOTES) . '" style="display:none;"></div>';

        $html .= "<div class='aya' id='aya-{$sura}-{$ayaNum}'>";
        $html .= $verseData;
//         $html .= "<div class='aya-header'><span class='aya-ref'>{$sura}:{$ayaNum}</span>{$actionBtns}</div>";
        $html .= "<div class='quran'><span class='ayaNum'>($ayaNum) </span><span class='ayaText'>$aya</span></div>";
        $html .= "<div class='englishtrans'>$transDisplay</div>";
        $html .= "<div class='trans'>$transurduDisplay</div>";
		$html .= "<div class='aya-header'><span class='aya-ref'>{$sura}:{$ayaNum}</span>{$actionBtns}</div>";
        $html .= "</div>";
        $ayaNum++;
    }
		// Audio player
		$html .= "<div class='audio-wrapper'>"; // wrapper start

		// settings bar yahin dal do
		$html .= "
		<div class='settings-bar'>
			<div class='settings-group'>
				<div class='dark-mode-toggle' onclick='toggleDarkMode()'>
					<span id='dark-mode-icon'>🌙</span>
					<span id='dark-mode-text'>Dark Mode</span>
				</div>
			</div>

			<div class='settings-group'>
				<span class='settings-label'>Font Size:</span>
				<div class='font-size-controls'>
					<button class='font-btn' onclick='changeFontSize(-1)'>-</button>
					<span id='font-size-display'>100%</span>
					<button class='font-btn' onclick='changeFontSize(1)'>+</button>
				</div>
			</div>
		</div>
		";

		$html .= "<div class='ayat-audio-player' id='ayat-audio-player' style='display:none;'>
			<div class='ayat-player-info'>
				<span class='ayat-player-label' id='ayat-player-label'>Surah " . $suraTname . "</span>
			</div>
			<div class='ayat-player-controls'>
				<button class='ayat-ctrl-btn' onclick='ayatPlayerPrev()' title='Previous'>
					<svg viewBox='0 0 24 24' width='20' height='20'><path d='M6 6h2v12H6zm3.5 6l8.5 6V6z'/></svg>
				</button>
				<button class='ayat-ctrl-btn ayat-play-pause' onclick='ayatPlayerToggle()' title='Play/Pause'>
					<svg class='ap-play' viewBox='0 0 24 24' width='24' height='24'><path d='M8 5v14l11-7z'/></svg>
					<svg class='ap-pause' viewBox='0 0 24 24' width='24' height='24' style='display:none;'><path d='M6 19h4V5H6v14zm8-14v14h4V5h-4z'/></svg>
				</button>
				<button class='ayat-ctrl-btn' onclick='ayatPlayerNext()' title='Next'>
					<svg viewBox='0 0 24 24' width='20' height='20'><path d='M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z'/></svg>
				</button>
			</div>
			<div class='ayat-player-progress'>
				<div class='ayat-progress-bar' id='ayat-progress-bar'>
					<div class='ayat-progress-fill' id='ayat-progress-fill'></div>
				</div>
			</div>
			<button class='ayat-ctrl-btn ayat-close-btn' onclick='ayatPlayerClose()' title='Close'>
				<svg viewBox='0 0 24 24' width='18' height='18'><path d='M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z'/></svg>
			</button>
			<audio id='ayat-audio' data-sura='" . $sura . "' data-total-ayas='" . $totalAyas . "'></audio>
		</div>";

	$html .= "</div>"; // wrapper end

    return $html;
}

/**
 * Main shortcode display function
 *
 * @return string HTML output
 */
function display_quran_func() {
    if (!is_admin()) {
        initSuraData();

        // Get current sura
        $sura = isset($_GET['sura']) ? intval($_GET['sura']) : 1;
        if ($sura < 1) $sura = 1;
        if ($sura > 114) $sura = 114;

        $html = '';

        // Include CSS inline
        $cssFile = QURAN_SIMPLE_PATH . 'assets/css/style.css';
        if (file_exists($cssFile)) {
            $html .= '<style>' . file_get_contents($cssFile) . '</style>';
        }

        // JS is loaded via wp_enqueue_script in index.php (no duplicate inline loading)

        // Wrap everything in container for theming
        $html .= '<div class="quran-container">';

        // Include template widgets
        ob_start();
        include QURAN_SIMPLE_PATH . 'templates/widgets.php';
        $html .= ob_get_clean();

        // Navigation buttons (Previous/Next)
        $html .= getNavigationButtons($sura);

        // Filter dropdowns
        $html .= '<div class="filter-container">
            <div class="filter-group">
                <span class="filter-label">Surah:</span>
                ' . getSuraDropdown($sura) . '
            </div>
            <div class="filter-group">
                <span class="filter-label">Para:</span>
                ' . getJuzDropdown($sura) . '
            </div>
        </div>';

        // Toggle switches (state managed by JavaScript/localStorage)
        $html .= '<div class="toggle-container">
            <div class="toggle-wrapper">
                <label class="toggle-switch">
                    <input type="checkbox" class="arabic" checked>
                    <span class="toggle-slider"></span>
                </label>
                <span class="toggle-label">Arabic</span>
            </div>
            <div class="toggle-wrapper">
                <label class="toggle-switch">
                    <input type="checkbox" class="urdu">
                    <span class="toggle-slider"></span>
                </label>
                <span class="toggle-label">Urdu</span>
            </div>
            <div class="toggle-wrapper">
                <label class="toggle-switch">
                    <input type="checkbox" class="english">
                    <span class="toggle-slider"></span>
                </label>
                <span class="toggle-label">English</span>
            </div>
        </div>';

        // Display sura content (wrapped for RTL in arabic-only mode)
        $html .= '<div class="sura-content-wrapper">';
        $html .= showSura($sura);
        $html .= '</div>';

        // Bottom navigation
        $html .= getNavigationButtons($sura);

        // Include modals template
        ob_start();
        include QURAN_SIMPLE_PATH . 'templates/modals.php';
        $html .= ob_get_clean();

        // Toast notification
        $html .= '<div id="toast" class="toast"></div>';

        $html .= '</div>'; // Close quran-container

        return $html;
    }
    return '';
}
