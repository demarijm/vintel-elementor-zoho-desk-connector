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

// Load includes
require_once VINTEL_ZOHO_PLUGIN_PATH . 'includes/class-vintel-zoho-loader.php';
