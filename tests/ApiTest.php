<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../includes/class-vintel-zoho-api.php';

$api = new Vintel_Zoho_API();

// No tokens stored should return WP_Error
$result = $api->create_ticket([
    'email' => 'a@example.com',
    'subject' => 'Test',
    'description' => 'Hello'
]);
assert_condition(is_wp_error($result), 'Returns WP_Error when tokens missing');

echo "ApiTest passed\n";
?>
