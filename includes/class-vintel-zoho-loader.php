<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Vintel_Zoho_Loader {
    public function __construct() {
        add_action( 'elementor_pro/forms/actions/register', [ $this, 'register_actions' ] );
    }

    public function register_actions( $actions ) {
        require_once VINTEL_ZOHO_PLUGIN_PATH . 'includes/class-vintel-zoho-elementor-action.php';
        $actions->register( new Vintel_Zoho_Elementor_Action() );
    }
}

new Vintel_Zoho_Loader();
