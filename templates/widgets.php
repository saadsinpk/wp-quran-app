<?php
/**
 * Quran Simple - Widget Templates
 *
 * @package QuranSimple
 * @since 2.2.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<!-- Last Read Banner -->
<div id="last-read-banner" class="last-read-banner">
    <div class="last-read-info">
        <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
        <span class="last-read-text">Continue reading from <strong id="last-read-sura"></strong></span>
    </div>
    <div>
        <button class="last-read-btn" onclick="continueReading()">Continue</button>
        <button class="dismiss-last-read" onclick="dismissLastRead()">&times;</button>
    </div>
</div>

<!-- Settings Bar (Dark Mode, Font Size) -->
<!-- <div class="settings-bar">
    <div class="settings-group">
        <div class="dark-mode-toggle" onclick="toggleDarkMode()">
            <span id="dark-mode-icon">
                <svg viewBox="0 0 24 24"><path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9c0-.46-.04-.92-.1-1.36-.98 1.37-2.58 2.26-4.4 2.26-2.98 0-5.4-2.42-5.4-5.4 0-1.81.89-3.42 2.26-4.4-.44-.06-.9-.1-1.36-.1z"/></svg>
            </span>
            <span id="dark-mode-text">Dark Mode</span>
        </div>
    </div>
    <div class="settings-group">
        <span class="settings-label">Font Size:</span>
        <div class="font-size-controls">
            <button class="font-btn" onclick="changeFontSize(-1)">-</button>
            <span id="font-size-display" class="font-size-display">100%</span>
            <button class="font-btn" onclick="changeFontSize(1)">+</button>
        </div>
    </div>
</div> -->

<!-- Search and Bookmarks bar -->
<div class="search-container">
    <input type="text" id="quran-search" class="search-input" placeholder="Search Quran (Arabic, English, Urdu)...">
    <button id="search-btn" class="search-btn">Search</button>
    <button class="bookmarks-btn" onclick="showBookmarksPanel()">
        <svg viewBox="0 0 24 24"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
        Bookmarks
    </button>
    <button class="challenges-btn" onclick="openChallengeModal()">
        <svg viewBox="0 0 24 24"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94.63 1.5 1.98 2.63 3.61 2.96V19H7v2h10v-2h-4v-3.1c1.63-.33 2.98-1.46 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/></svg>
        Challenge
    </button>
</div>

<!-- Search results container -->
<div id="search-results" class="search-results"></div>

<!-- Bookmarks panel -->
<div id="bookmarks-panel" class="bookmarks-panel">
    <div class="bookmarks-header">
        <h3>My Bookmarks</h3>
        <button class="close-bookmarks">&times;</button>
    </div>
    <div id="bookmarks-list-container"></div>
</div>

<!-- Challenge Widget -->
<div id="challenge-widget" class="challenge-widget">
    <div class="challenge-header">
        <span class="challenge-title">Daily Reading Challenge</span>
        <span class="streak-badge">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="#fff"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
            <span id="streak-count">1</span> day streak
        </span>
    </div>
    <div class="progress-bar-container">
        <div id="progress-fill" class="progress-bar-fill" style="width: 0%">0%</div>
    </div>
    <div class="challenge-stats">
        <div class="stat-item">
            <div class="stat-value"><span id="today-verses">0</span>/<span id="goal-verses">10</span></div>
            <div class="stat-label">Today</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" id="total-verses">0</div>
            <div class="stat-label">Total Verses</div>
        </div>
    </div>
</div>
