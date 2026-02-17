<?php
/**
 * Plugin Name:       ST AdBlocker Detector
 * Plugin URI:        https://sampathTharanga.dev
 * Description:       Detects ad blocker extensions and completely blocks website access until the user disables the ad blocker. Fully customizable popup with admin settings panel.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Tested up to:      6.5
 * Author:            Sampath Tharanga
 * Author URI:        https://sampathTharanga.dev
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       st-adblocker-detector
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Define constants
define( 'ST_ADB_VERSION',     '1.0.0' );
define( 'ST_ADB_DIR',         plugin_dir_path( __FILE__ ) );
define( 'ST_ADB_URL',         plugin_dir_url( __FILE__ ) );
define( 'ST_ADB_PLUGIN_NAME', plugin_basename( __FILE__ ) );

// Load includes
require_once ST_ADB_DIR . 'includes/functions.php';
require_once ST_ADB_DIR . 'includes/settings.php';
require_once ST_ADB_DIR . 'includes/ajax.php';
require_once ST_ADB_DIR . 'includes/frontend.php';

// Activation hook – set default options
register_activation_hook( __FILE__, function () {
    if ( empty( get_option( 'st_adb_settings' ) ) ) {
        st_adb_set_defaults();
    }
} );

// Init all modules
add_action( 'plugins_loaded', function () {
    st_adb_settings_init();
    st_adb_ajax_init();
    st_adb_frontend_init();
} );
