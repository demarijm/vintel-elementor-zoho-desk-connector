<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function vintel_zoho_log( $message ) {
    $debug = get_option( 'vintel_zoho_debug', 0 );
    if ( ! $debug ) {
        return;
    }
    $upload_dir = wp_upload_dir();
    $log_file   = trailingslashit( $upload_dir['basedir'] ) . 'zoho-integration.log';
    if ( is_array( $message ) || is_object( $message ) ) {
        $message = print_r( $message, true );
    }
    $timestamp = date( 'Y-m-d H:i:s' );
    $entry     = sprintf( "[%s] %s\n", $timestamp, $message );
    file_put_contents( $log_file, $entry, FILE_APPEND | LOCK_EX );
}
