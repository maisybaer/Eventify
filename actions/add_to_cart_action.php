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

    // Accept either legacy 'product_id' or newer 'event_id'
    $product_id = 0;
    if (isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
    } elseif (isset($_POST['event_id'])) {
        $product_id = intval($_POST['event_id']);
    }
    $customer_id = $_SESSION['user_id'];
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : (isset($_POST['qty']) ? intval($_POST['qty']) : 1);

    // Validate inputs
    if ($product_id <= 0 || $quantity <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product or quantity']);
        exit;
    }

    // Add to cart using controller
    $cartController = new CartController();
    $result = $cartController->add_to_cart_ctr($product_id, $customer_id, $quantity);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Item added to cart successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add item to cart']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
