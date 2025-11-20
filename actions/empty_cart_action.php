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

    $customer_id = $_SESSION['user_id'];

    $cart = new CartController();
    $emptied = $cart->empty_cart_ctr($customer_id);

    if ($emptied) {
        echo json_encode(['status' => 'success', 'message' => 'Cart emptied successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to empty cart']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>