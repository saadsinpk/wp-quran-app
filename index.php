<?php
/*
Plugin Name: Quran Simple
Plugin URI: https://www.sidtechno.com/
Description: Quran Simple with Search, Bookmarks, Dark Mode, Image Generator, QR Codes, Reading Challenges, and Modern UI
Author: Muhammad Saad
Author URI: https://www.sidtechno.com/
Version: 2.2.0
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('QURAN_SIMPLE_VERSION', '2.3.0');
define('QURAN_SIMPLE_PATH', plugin_dir_path(__FILE__));
define('QURAN_SIMPLE_URL', plugin_dir_url(__FILE__));

// Data file paths
$GLOBALS['quran_simple_files'] = array(
    'quran' => QURAN_SIMPLE_PATH . 'data/true-quran.txt',
    'english' => QURAN_SIMPLE_PATH . 'data/trans-english.txt',
    'urdu' => QURAN_SIMPLE_PATH . 'data/trans-urdu.txt',
    'metadata' => QURAN_SIMPLE_PATH . 'data/quran-data.xml',
    'audio' => QURAN_SIMPLE_PATH . 'data/audio-links.txt'
);

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once QURAN_SIMPLE_PATH . 'includes/class-quran-data.php';
require_once QURAN_SIMPLE_PATH . 'includes/ajax-handlers.php';
require_once QURAN_SIMPLE_PATH . 'includes/display-functions.php';

// Enqueue scripts and styles
function quran_simple_enqueue_assets() {
    if (!is_admin()) {
        // Enqueue jQuery
        wp_enqueue_script('jquery');

        // Enqueue plugin CSS
        wp_enqueue_style(
            'quran-simple-style',
            QURAN_SIMPLE_URL . 'assets/css/style.css',
            array(),
            QURAN_SIMPLE_VERSION
        );

        // Enqueue plugin JavaScript
        wp_enqueue_script(
            'quran-simple-script',
            QURAN_SIMPLE_URL . 'assets/js/main.js',
            array('jquery'),
            QURAN_SIMPLE_VERSION,
            true
        );

        // Pass data to JavaScript
        wp_localize_script('quran-simple-script', 'quranSimple', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'currentSura' => isset($_GET['sura']) ? intval($_GET['sura']) : 1
        ));
    }
}
add_action('wp_enqueue_scripts', 'quran_simple_enqueue_assets');

// Register shortcode
add_shortcode('display_quran', 'display_quran_func');
