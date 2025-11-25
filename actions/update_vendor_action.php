<?php
header('Content-Type: application/json');
require_once '../settings/core.php';
require_once '../controllers/vendor_controller.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit();
}

$customer_id = (int) $_SESSION['user_id'];
$payload = [
    'customer_id' => $customer_id,
    'customer_name' => isset($input['customer_name']) ? $input['customer_name'] : null,
    'customer_email' => isset($input['customer_email']) ? $input['customer_email'] : null,
    'customer_contact' => isset($input['customer_contact']) ? $input['customer_contact'] : null,
    'vendor_id' => isset($input['vendor_id']) ? (int)$input['vendor_id'] : null,
    'vendor_type' => isset($input['vendor_type']) ? $input['vendor_type'] : null,
    'vendor_desc' => isset($input['vendor_desc']) ? $input['vendor_desc'] : null,
];

$ok = update_vendor_ctr($payload);
if ($ok) {
    $fresh = fetch_vendor_ctr($customer_id);
    echo json_encode(['status' => 'success', 'message' => 'Updated successfully', 'data' => $fresh]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Update failed']);
}

?>
