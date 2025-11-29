<?php
/**
 * Get Cart Items for Current User
 * Returns cart items as JSON
 */

header('Content-Type: application/json');

require_once '../settings/core.php';
require_once '../controllers/cart_controller.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Not logged in',
        'items' => []
    ]);
    exit();
}

try {
    $customer_id = $_SESSION['user_id'];
    $cartController = new CartController();
    $cart_items = $cartController->get_user_cart_ctr($customer_id);

    // Calculate total
    $total = 0;
    if ($cart_items && is_array($cart_items)) {
        foreach ($cart_items as &$item) {
            // Calculate subtotal for each item
            $price = isset($item['product_price']) ? floatval($item['product_price']) : 0;
            $qty = isset($item['qty']) ? intval($item['qty']) : 0;
            $subtotal = $price * $qty;
            $item['subtotal'] = $subtotal;
            $total += $subtotal;
        }
    }

    // Add service fee (15%)
    $service_fee = $total * 0.15;
    $total_with_fee = $total + $service_fee;

    echo json_encode([
        'status' => 'success',
        'items' => $cart_items ?: [],
        'subtotal' => number_format($total, 2),
        'service_fee' => number_format($service_fee, 2),
        'total' => number_format($total_with_fee, 2)
    ]);

} catch (Exception $e) {
    error_log("Error in get_cart_action.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'items' => []
    ]);
}
?>
