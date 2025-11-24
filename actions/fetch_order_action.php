<?php
require_once '../controllers/order_controller.php';
require_once '../settings/core.php';

header('Content-Type: application/json');

try {
    $user_id = getUserID();

    if (!$user_id) {
        echo json_encode([]);
        exit;
    }

    $orders = get_user_orders_ctr($user_id);

    if (!is_array($orders)) {
        $orders = [];
    }

    echo json_encode($orders);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch orders.'
    ]);
}
