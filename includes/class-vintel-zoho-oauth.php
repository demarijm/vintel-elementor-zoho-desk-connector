<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Vintel_Zoho_OAuth {
    private $client_id;
    private $client_secret;
    private $redirect_uri;

    public function __construct() {
        $this->client_id     = get_option( 'vintel_zoho_client_id', '' );
        $this->client_secret = get_option( 'vintel_zoho_client_secret', '' );
        $this->redirect_uri  = get_option( 'vintel_zoho_redirect_uri', '' );
    }

    public function get_auth_url() {
        if ( empty( $this->client_id ) || empty( $this->redirect_uri ) ) {
            return '';
        }

        $base   = 'https://accounts.zoho.com/oauth/v2/auth';
        $params = array(
            'scope'         => 'Desk.tickets.ALL',
            'client_id'     => $this->client_id,
            'response_type' => 'code',
            'access_type'   => 'offline',
            'redirect_uri'  => $this->redirect_uri,
            'prompt'        => 'consent',
        );

        return add_query_arg( $params, $base );
    }

    public function handle_auth_code() {
        if ( empty( $_GET['code'] ) ) {
            wp_die( 'Missing authorization code.' );
        }
        $code = sanitize_text_field( wp_unslash( $_GET['code'] ) );
        $this->exchange_code_for_token( $code );
        wp_redirect( admin_url( 'options-general.php?page=vintel-zoho-desk' ) );
        exit;
    }

    private function exchange_code_for_token( $code ) {
        $url  = 'https://accounts.zoho.com/oauth/v2/token';
        $body = array(
            'code'          => $code,
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri'  => $this->redirect_uri,
            'grant_type'    => 'authorization_code',
        );

        vintel_zoho_log( 'Requesting Zoho tokens with authorization code' );

        $response = wp_remote_post( $url, array( 'body' => $body ) );
        if ( is_wp_error( $response ) ) {
            vintel_zoho_log( 'Token request failed: ' . $response->get_error_message() );
            return;
        }

        vintel_zoho_log( array(
            'response' => wp_remote_retrieve_body( $response ),
        ) );

        $data = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( isset( $data['access_token'] ) ) {
            update_option( 'zoho_access_token', $data['access_token'] );
        }
        if ( isset( $data['refresh_token'] ) ) {
            update_option( 'zoho_refresh_token', $data['refresh_token'] );
        }
        if ( isset( $data['expires_in'] ) ) {
            update_option( 'zoho_token_expires_at', time() + intval( $data['expires_in'] ) );
        }

        vintel_zoho_log( 'Authorization code exchanged successfully' );
    }

    public function get_access_token() {
        $token      = get_option( 'zoho_access_token', '' );
        $expires_at = intval( get_option( 'zoho_token_expires_at', 0 ) );

        if ( empty( $token ) ) {
            return '';
        }

        if ( time() >= $expires_at ) {
            vintel_zoho_log( 'Stored token expired, refreshing' );
            $token = $this->refresh_access_token();
        }

        return $token;
    }

    private function refresh_access_token() {
        $refresh = get_option( 'zoho_refresh_token', '' );
        if ( empty( $refresh ) ) {
            return '';
        }

        vintel_zoho_log( 'Refreshing Zoho access token' );

        $url  = 'https://accounts.zoho.com/oauth/v2/token';
        $body = array(
            'refresh_token' => $refresh,
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type'    => 'refresh_token',
        );

        $response = wp_remote_post( $url, array( 'body' => $body ) );
        if ( is_wp_error( $response ) ) {
            vintel_zoho_log( 'Token refresh failed: ' . $response->get_error_message() );
            return '';
        }

        vintel_zoho_log( array(
            'response' => wp_remote_retrieve_body( $response ),
        ) );

        $data = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( isset( $data['access_token'] ) ) {
            update_option( 'zoho_access_token', $data['access_token'] );
        }
        if ( isset( $data['expires_in'] ) ) {
            update_option( 'zoho_token_expires_at', time() + intval( $data['expires_in'] ) );
        }

        vintel_zoho_log( 'Access token refreshed successfully' );

        return get_option( 'zoho_access_token', '' );
    }
}
