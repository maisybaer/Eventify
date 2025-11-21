<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../controllers/cart_controller.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Please login to update cart']);
        exit;
    }

    $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
    $qty = isset($_POST['qty']) ? intval($_POST['qty']) : 0;
    $customer_id = $_SESSION['user_id'];

    if ($event_id <= 0 || $qty < 1) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid event id or quantity']);
        exit;
    }

    error_log("update_quantity_action called - event_id={$event_id}, customer_id={$customer_id}, qty={$qty}");
    $cartController = new CartController();
    $updated = $cartController->update_cart_item_ctr($event_id, $customer_id, $qty);

    if ($updated) {
        echo json_encode(['status' => 'success', 'message' => 'Quantity updated']);
    } else {
        $err = method_exists($cartController, 'get_last_error_ctr') ? $cartController->get_last_error_ctr() : '';
        $msg = 'Failed to update quantity';
        if ($err) $msg .= ': ' . $err;
        echo json_encode(['status' => 'error', 'message' => $msg]);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>