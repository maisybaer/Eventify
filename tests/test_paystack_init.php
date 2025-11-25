<?php
// Simple CLI test for paystack_init_transaction.php
// Usage: php tests/test_paystack_init.php

$host = 'http://localhost/Eventify'; // adjust if needed
$url = $host . '/actions/paystack_init_transaction.php';

$payload = [
    'amount' => 115.00,
    'email' => 'tester+local@example.com'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
// Use a cookie jar to persist session if server requires it
$cookieFile = sys_get_temp_dir() . '/eventify_test_cookies.txt';
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);

$response = curl_exec($ch);
$err = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($err) {
    echo "CURL error: $err\n";
    exit(1);
}

echo "HTTP $http_code\n";
echo $response . "\n";

// Try to pretty-print JSON if possible
$json = json_decode($response, true);
if ($json) {
    echo "\nParsed JSON:\n";
    echo json_encode($json, JSON_PRETTY_PRINT) . "\n";
}

?>