<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles communication with the Zoho Desk API.
 */
class Vintel_Zoho_API {

    /**
     * Zoho Desk tickets endpoint.
     *
     * @var string
     */
    private $endpoint = 'https://desk.zoho.com/api/v1/tickets';

    /**
     * Creates a ticket in Zoho Desk.
     *
     * @param array $data Associative array of ticket data (email, subject, description, department_id optional).
     * @return array|WP_Error Response body array on success, WP_Error on failure.
     */
    public function create_ticket( array $data ) {
        $token = $this->maybe_refresh_token();
        if ( is_wp_error( $token ) ) {
            return $token;
        }

        $payload = $this->build_payload( $data );

        vintel_zoho_log( 'Creating ticket payload: ' . wp_json_encode( $payload ) );

        $args = array(
            'headers' => array(
                'Authorization' => 'Zoho-oauthtoken ' . $token,
                'Content-Type'  => 'application/json',
            ),
            'body'    => wp_json_encode( $payload ),
            'timeout' => 20,
        );

        $response = wp_remote_post( $this->endpoint, $args );

        vintel_zoho_log( array(
            'request_url'  => $this->endpoint,
            'response'     => is_wp_error( $response ) ? $response->get_error_message() : wp_remote_retrieve_body( $response ),
            'status_code'  => is_wp_error( $response ) ? 0 : wp_remote_retrieve_response_code( $response ),
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $code >= 200 && $code < 300 ) {
            vintel_zoho_log( 'Ticket created successfully' );
            return $body;
        }

        if ( isset( $body['message'] ) ) {
            vintel_zoho_log( 'Zoho error: ' . $body['message'] );
            return new WP_Error( 'zoho_error', $body['message'] );
        }
        vintel_zoho_log( 'Zoho error: unknown error' );
        return new WP_Error( 'zoho_error', __( 'Unknown error from Zoho Desk.', 'vintel-zoho-desk-connector' ) );
    }

    /**
     * Build ticket payload for API request.
     *
     * @param array $data Raw data.
     * @return array
     */
    private function build_payload( array $data ) {
        $payload = array(
            'contact'     => array( 'email' => $data['email'] ),
            'subject'     => $data['subject'],
            'description' => $data['description'],
        );

        if ( ! empty( $data['department_id'] ) ) {
            $payload['departmentId'] = $data['department_id'];
        }

        return $payload;
    }

    /**
     * Retrieve stored access token, refreshing if necessary.
     *
     * @return string|WP_Error Access token string or WP_Error on failure.
     */
    private function maybe_refresh_token() {
        $access_token  = get_option( 'zoho_access_token' );
        $refresh_token = get_option( 'zoho_refresh_token' );
        $expires_at    = (int) get_option( 'zoho_token_expires_at' );

        if ( empty( $access_token ) || empty( $refresh_token ) ) {
            return new WP_Error( 'missing_tokens', __( 'Zoho Desk tokens are missing.', 'vintel-zoho-desk-connector' ) );
        }

        // Allow a 2 minute buffer before actual expiry.
        if ( time() + 120 < $expires_at ) {
            return $access_token;
        }

        $client_id     = get_option( 'vintel_zoho_client_id' );
        $client_secret = get_option( 'vintel_zoho_client_secret' );

        vintel_zoho_log( 'Refreshing access token...' );

        if ( empty( $client_id ) || empty( $client_secret ) ) {
            return new WP_Error( 'missing_credentials', __( 'Zoho Desk credentials are missing.', 'vintel-zoho-desk-connector' ) );
        }

        $response = wp_remote_post( 'https://accounts.zoho.com/oauth/v2/token', array(
            'body' => array(
                'refresh_token' => $refresh_token,
                'client_id'     => $client_id,
                'client_secret' => $client_secret,
                'grant_type'    => 'refresh_token',
            ),
            'timeout' => 20,
        ) );

        vintel_zoho_log( array(
            'refresh_request' => 'token',
            'response'        => is_wp_error( $response ) ? $response->get_error_message() : wp_remote_retrieve_body( $response ),
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $code >= 200 && $code < 300 && ! empty( $body['access_token'] ) ) {
            $access_token = sanitize_text_field( $body['access_token'] );
            $expires_in   = isset( $body['expires_in'] ) ? intval( $body['expires_in'] ) : 3600;

            update_option( 'zoho_access_token', $access_token );
            update_option( 'zoho_token_expires_at', time() + $expires_in );

            vintel_zoho_log( 'Access token refreshed successfully' );

            return $access_token;
        }

        if ( isset( $body['error'] ) ) {
            vintel_zoho_log( 'Token refresh error: ' . $body['error'] );
            return new WP_Error( 'token_refresh_error', $body['error'] );
        }

        vintel_zoho_log( 'Token refresh error: unknown error' );
        return new WP_Error( 'token_refresh_error', __( 'Unable to refresh Zoho token.', 'vintel-zoho-desk-connector' ) );
    }
}
