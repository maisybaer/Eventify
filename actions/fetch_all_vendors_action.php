<?php
header('Content-Type: application/json');
require_once '../settings/core.php';
require_once '../controllers/vendor_controller.php';

// Public endpoint to fetch all vendors (customers with role=2)
$data = fetch_all_vendors_ctr();
if ($data === false) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch vendors']);
    exit();
}

echo json_encode(['status' => 'success', 'data' => $data]);
exit();
?>