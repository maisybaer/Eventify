<?php
session_start();
require_once '../controllers/cart_controller.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Please login first']);
        exit;
    }

    // Accept either product_id or event_id
    $event_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : (isset($_POST['event_id']) ? intval($_POST['event_id']) : 0);
    $customer_id = $_SESSION['user_id'];

    if ($event_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid event ID']);
        exit;
    }

    $cart = new CartController();
    $removed = $cart->remove_from_cart_ctr($event_id, $customer_id);

    if ($removed) {
        echo json_encode(['status' => 'success', 'message' => 'Item removed from cart']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to remove item']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
