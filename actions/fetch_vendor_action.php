<?php
header('Content-Type: application/json');
require_once '../settings/core.php';
require_once '../controllers/vendor_controller.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$data = fetch_vendor_ctr($user_id);
if ($data === false) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch vendor data']);
    exit();
}

echo json_encode(['status' => 'success', 'data' => $data]);
?>
