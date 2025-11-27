<?php
require_once '../controllers/event_controller.php';
require_once '../settings/core.php';
header('Content-Type: application/json');

try {
    $customer_id = getUserID();
    if (!$customer_id) {
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        exit;
    }

    // Fetch events created by the logged-in customer
    $events = get_event_ctr($customer_id);

    if ($events === false || $events === null) {
        echo json_encode(['status' => 'success', 'data' => []]);
    } else {
        echo json_encode(['status' => 'success', 'data' => $events]);
    }
} catch (Throwable $e) {
    error_log('Fetch customer events error: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server error', 'data' => []]);
}
?>
