<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Vintel_Zoho_Loader {

    /**
     * Initialize the plugin hooks.
     */
    public function __construct() {
        // For Phase 5, we only ensure API class can be loaded.
        require_once VINTEL_ZOHO_PLUGIN_PATH . 'includes/class-vintel-zoho-api.php';
    }
}

new Vintel_Zoho_Loader();
