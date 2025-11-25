<?php
header('Content-Type: application/json');
require_once '../settings/core.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Not logged in'
    ]);
    exit();
}

$email = getUserEmail();
if ($email) {
    echo json_encode([
        'status' => 'success',
        'email' => $email
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Email not found'
    ]);
}

?>