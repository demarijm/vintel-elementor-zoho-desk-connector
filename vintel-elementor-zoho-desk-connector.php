<?php
/**
 * Plugin Name: Vintel Elementor to Zoho Desk Connector
 * Description: Sends Elementor form submissions to Zoho Desk as support tickets.
 * Version: 1.0.0
 * Author: Vintel
 * Text Domain: vintel-zoho-desk-connector
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define( 'VINTEL_ZOHO_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'VINTEL_ZOHO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Activation hook to set default options
register_activation_hook( __FILE__, 'vintel_zoho_activate' );
function vintel_zoho_activate() {
    add_option( 'vintel_zoho_debug', 0 );
}

// Load includes
require_once VINTEL_ZOHO_PLUGIN_PATH . 'includes/functions.php';
require_once VINTEL_ZOHO_PLUGIN_PATH . 'includes/class-vintel-zoho-loader.php';
