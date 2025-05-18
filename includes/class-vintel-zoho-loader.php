<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Vintel_Zoho_Loader {
    private static $instance = null;
    private $oauth;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        require_once VINTEL_ZOHO_PLUGIN_PATH . 'includes/class-vintel-zoho-oauth.php';
        $this->oauth = new Vintel_Zoho_OAuth();
        add_action( 'admin_post_zoho_auth', array( $this->oauth, 'handle_auth_code' ) );
    }

    public function get_oauth() {
        return $this->oauth;
    }
}
