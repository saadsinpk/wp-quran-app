<?php
/**
 * Quran Simple - Modal Templates
 *
 * @package QuranSimple
 * @since 2.2.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<!-- Share Modal -->
<div id="share-modal" class="share-modal">
    <div class="share-modal-content">
        <div class="share-modal-header">
            <h3>Share Verse</h3>
            <button class="close-share-modal" onclick="closeShareModal()">&times;</button>
        </div>
        <div class="share-buttons">
            <div class="share-option share-whatsapp" onclick="shareToWhatsApp()">
                <svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                <span>WhatsApp</span>
            </div>
            <div class="share-option share-twitter" onclick="shareToTwitter()">
                <svg viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                <span>Twitter</span>
            </div>
            <div class="share-option share-facebook" onclick="shareToFacebook()">
                <svg viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                <span>Facebook</span>
            </div>
            <div class="share-option share-copy" onclick="copyFromModal()">
                <svg viewBox="0 0 24 24"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>
                <span>Copy</span>
            </div>
        </div>
    </div>
</div>

<!-- Image Generator Modal -->
<div id="image-modal" class="image-modal">
    <div class="image-modal-content">
        <div class="share-modal-header">
            <h3>Create Verse Image</h3>
            <button class="close-share-modal" onclick="closeImageModal()">&times;</button>
        </div>
        <div class="template-options">
            <div class="template-btn template-1 active" onclick="selectTemplate(1)"></div>
            <div class="template-btn template-2" onclick="selectTemplate(2)"></div>
            <div class="template-btn template-3" onclick="selectTemplate(3)"></div>
            <div class="template-btn template-4" onclick="selectTemplate(4)"></div>
            <div class="template-btn template-5" onclick="selectTemplate(5)"></div>
            <div class="template-btn template-6" onclick="selectTemplate(6)"></div>
        </div>
        <div class="image-preview-container">
            <canvas id="verse-canvas" width="800" height="600"></canvas>
        </div>
        <div class="image-actions">
            <button class="download-btn" onclick="downloadImage()">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="#fff"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                Download PNG
            </button>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div id="qr-modal" class="qr-modal">
    <div class="qr-modal-content">
        <div class="share-modal-header">
            <h3>QR Code for Verse</h3>
            <button class="close-share-modal" onclick="closeQRModal()">&times;</button>
        </div>
        <canvas id="qr-canvas"></canvas>
        <div class="qr-link" id="qr-link"></div>
        <div class="image-actions">
            <button class="download-btn" onclick="downloadQR()">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="#fff"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                Download QR
            </button>
        </div>
    </div>
</div>

<!-- Challenge Modal -->
<div id="challenge-modal" class="challenge-modal">
    <div class="challenge-modal-content">
        <div class="share-modal-header">
            <h3>Reading Challenge</h3>
            <button class="close-share-modal" onclick="closeChallengeModal()">&times;</button>
        </div>
        <p style="font-family: Calibri; color: var(--text-muted); margin: 10px 0;">Set a daily reading goal and track your progress!</p>
        <div class="goal-setting">
            <div class="goal-option" data-goal="easy" onclick="selectGoal('easy')">
                <div class="goal-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                </div>
                <div class="goal-info">
                    <div class="goal-name">Easy - 10 Verses</div>
                    <div class="goal-desc">Perfect for beginners, takes about 5 minutes</div>
                </div>
            </div>
            <div class="goal-option" data-goal="medium" onclick="selectGoal('medium')">
                <div class="goal-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                </div>
                <div class="goal-info">
                    <div class="goal-name">Medium - 1 Page (30 verses)</div>
                    <div class="goal-desc">Read about 1 page daily, takes about 15 minutes</div>
                </div>
            </div>
            <div class="goal-option" data-goal="hard" onclick="selectGoal('hard')">
                <div class="goal-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                </div>
                <div class="goal-info">
                    <div class="goal-name">Committed - 3 Pages (100 verses)</div>
                    <div class="goal-desc">For dedicated readers, takes about 45 minutes</div>
                </div>
            </div>
            <div class="goal-option" data-goal="intense" onclick="selectGoal('intense')">
                <div class="goal-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                </div>
                <div class="goal-info">
                    <div class="goal-name">Intense - 1 Juz (200 verses)</div>
                    <div class="goal-desc">Complete Quran in 30 days!</div>
                </div>
            </div>
        </div>
        <div class="notification-toggle">
            <span style="font-family: Calibri; color: var(--text-primary);">Enable reminders</span>
            <label class="toggle-switch">
                <input type="checkbox" id="notification-toggle">
                <span class="toggle-slider"></span>
            </label>
        </div>
        <button class="save-goal-btn" onclick="saveChallenge()">Start Challenge</button>
    </div>
</div>
