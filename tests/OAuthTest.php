<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../includes/class-vintel-zoho-oauth.php';

// Configure options for test
update_option('vintel_zoho_client_id', 'abc');
update_option('vintel_zoho_redirect_uri', 'https://example.com/callback');


$oauth = new Vintel_Zoho_OAuth();
$url = $oauth->get_auth_url();

assert_condition(strpos($url, 'https://accounts.zoho.com/oauth/v2/auth') === 0, 'Base auth URL');
assert_condition(strpos($url, 'client_id=abc') !== false, 'Client ID parameter');
assert_condition(strpos($url, 'redirect_uri=https%3A%2F%2Fexample.com%2Fcallback') !== false, 'Redirect URI parameter');

echo "OAuthTest passed\n";
?>
