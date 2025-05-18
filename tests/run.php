<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Running tests...\n";

require_once __DIR__ . '/OAuthTest.php';
require_once __DIR__ . '/ApiTest.php';
?>
