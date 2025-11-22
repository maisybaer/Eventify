<?php
session_start();
require_once '../controllers/cart_controller.php';
require_once '../controllers/order_controller.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $cartController = new CartController();
    $orderController = new OrderController();

    // Get cart items
    $cart_items = $cartController->get_user_cart_ctr($user_id);
    
    if (empty($cart_items)) {
        echo json_encode(['status' => 'error', 'message' => 'Cart is empty']);
        exit;
    }

    // Generate unique order reference (e.g., timestamp + random)
    $invoice_no = 'ORD-' . time() . '-' . rand(1000, 9999);
    $order_date = date('Y-m-d H:i:s');
    $order_status = 'Pending';

    // Calculate total amount
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['qty'] * $item['product_price'];
    }

    // Create order
    $order_id = $orderController->create_order_ctr([
        'customer_id' => $user_id,
        'invoice_no' => $invoice_no,
        'order_date' => $order_date,
        'order_status' => $order_status
    ]);

    if (!$order_id) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create order']);
        exit;
    }

    // Add order details
    foreach ($cart_items as $item) {
        $detailsAdded = $orderController->add_order_details_ctr([
            'order_id' => $order_id,
            'product_id' => $item['product_id'],
            'qty' => $item['qty'],
            'price' => $item['product_price']
        ]);

        if (!$detailsAdded) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add order details']);
            exit;
        }
    }

    // Record payment
    $payment_success = $orderController->record_payment_ctr([
        'amt' => $total_amount,
        'customer_id' => $user_id,
        'order_id' => $order_id,
        'currency' => 'USD',
        'payment_date' => $order_date
    ]);

    if ($payment_success) {
        // Empty cart after successful checkout
        $cartController->empty_cart_ctr($user_id);

        echo json_encode([
            'status' => 'success',
            'order_id' => $order_id,
            'invoice_no' => $invoice_no,
            'total_amount' => number_format($total_amount, 2),
            'message' => 'Order placed successfully'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Payment failed']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
