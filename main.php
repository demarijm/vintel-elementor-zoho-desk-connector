<?php
/**
 * Plugin Name: Vintel Elementor to Zoho Desk Connector
 * Description: Sends Elementor form submissions to Zoho Desk as support tickets.
 * Version: 1.1.0
 * Author: Vintel
 * Text Domain: vintel-zoho-desk-connector
 * Requires at least: 5.0
 * Tested up to: 6.5
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define( 'VINTEL_ZOHO_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'VINTEL_ZOHO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Load includes
require_once VINTEL_ZOHO_PLUGIN_PATH . 'includes/functions.php';
require_once VINTEL_ZOHO_PLUGIN_PATH . 'includes/class-vintel-zoho-loader.php';

// Activation hook to set default options
function vintel_zoho_activate() {
    add_option( 'vintel_zoho_debug', 0 );
}
register_activation_hook( __FILE__, 'vintel_zoho_activate' );

// Deactivation hook
function vintel_zoho_deactivate() {
    // Placeholder for deactivation logic.
}
register_deactivation_hook( __FILE__, 'vintel_zoho_deactivate' );

// Initialize plugin after all plugins are loaded.
function vintel_zoho_init() {
    Vintel_Zoho_Loader::get_instance();
}
add_action( 'plugins_loaded', 'vintel_zoho_init' );
