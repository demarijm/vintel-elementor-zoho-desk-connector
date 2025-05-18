<?php
// Minimal stubs for WordPress functions and classes used in tests
$GLOBALS['wp_options'] = [];
// Define ABSPATH to satisfy plugin files
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__);
}
function get_option($key, $default = false) {
    return $GLOBALS['wp_options'][$key] ?? $default;
}
function update_option($key, $value) {
    $GLOBALS['wp_options'][$key] = $value;
}
function add_option($key, $value) {
    $GLOBALS['wp_options'][$key] = $value;
}
function wp_upload_dir() {
    return ['basedir' => sys_get_temp_dir()];
}
function trailingslashit($path) {
    return rtrim($path, '/').'/';
}
function file_put_contents_safe($file, $data) {
    return file_put_contents($file, $data);
}
function assert_condition($condition, $message) {
    if (!$condition) {
        throw new Exception('Assertion failed: ' . $message);
    }
}
function sanitize_text_field($str) { return $str; }
function sanitize_email($str) { return $str; }
function sanitize_textarea_field($str) { return $str; }
function wp_unslash($value) { return $value; }
function add_query_arg($args, $url) {
    return $url.'?'.http_build_query($args, '', '&');
}
function wp_die($msg) { throw new Exception($msg); }
class WP_Error {
    private $message;
    public function __construct($code = '', $message = '') { $this->message = $message; }
    public function get_error_message() { return $this->message; }
}
function is_wp_error($thing) { return $thing instanceof WP_Error; }
function wp_remote_post($url, $args = []) {
    // Simulate successful API response
    return ['response' => ['code' => 200], 'body' => json_encode(['id' => 123])];
}
function wp_remote_retrieve_body($response) { return $response['body']; }
function wp_remote_retrieve_response_code($response) { return $response['response']['code']; }
function __($text, $domain = null) { return $text; }
?>
