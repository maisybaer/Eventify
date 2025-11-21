<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../controllers/cart_controller.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Please login to add items to cart']);
        exit;
    }

    // Expect an event_id in POST
    $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
    $customer_id = $_SESSION['user_id'];
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    // Validate inputs
    if ($event_id <= 0 || $quantity <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid event or quantity']);
        exit;
    }

    // Add to cart using controller (log parameters)
    error_log("add_to_cart_action called - event_id={$event_id}, customer_id={$customer_id}, qty={$quantity}");
    $cartController = new CartController();
    $result = $cartController->add_to_cart_ctr($event_id, $customer_id, $quantity);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Item added to cart successfully']);
    } else {
        // include DB error for debugging
        $dbErr = method_exists($cartController, 'get_last_error_ctr') ? $cartController->get_last_error_ctr() : '';
        $msg = 'Failed to add item to cart';
        if ($dbErr) $msg .= ': ' . $dbErr;
        echo json_encode(['status' => 'error', 'message' => $msg]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
