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

    // Accept either legacy 'product_id' or newer 'event_id'
    if (isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
    } elseif (isset($_POST['event_id'])) {
        $product_id = intval($_POST['event_id']);
    } else {
        $product_id = 0;
    }

    $qty = isset($_POST['qty']) ? intval($_POST['qty']) : (isset($_POST['quantity']) ? intval($_POST['quantity']) : 0);
    $customer_id = $_SESSION['user_id'];

    // Validate inputs
    if ($product_id <= 0 || $qty <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product or quantity']);
        exit;
    }

    $cart = new CartController();
    $updated = $cart->update_cart_item_ctr($product_id, $customer_id, $qty);

    if ($updated) {
        echo json_encode(['status' => 'success', 'message' => 'Quantity updated']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update quantity']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
