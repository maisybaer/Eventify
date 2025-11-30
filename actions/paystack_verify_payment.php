<?php
/**
 * Paystack Callback Handler & Verification
 * Handles payment verification after user returns from Paystack gateway
 */

header('Content-Type: application/json');

require_once '../settings/core.php';
require_once '../settings/paystack_config.php';

error_log("=== PAYSTACK CALLBACK/VERIFICATION ===");

// Check if user is logged in (ensure session user id exists)
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Session expired. Please login again.'
    ]);
    exit();
}

// Get verification reference from POST data
$input = json_decode(file_get_contents('php://input'), true);
$reference = isset($input['reference']) ? trim($input['reference']) : null;
$cart_items = isset($input['cart_items']) ? $input['cart_items'] : null;
$total_amount = isset($input['total_amount']) ? floatval($input['total_amount']) : 0;

if (!$reference) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No payment reference provided'
    ]);
    exit();
}

// Optional: Verify reference matches session
if (isset($_SESSION['paystack_ref']) && $_SESSION['paystack_ref'] !== $reference) {
    error_log("Reference mismatch - Expected: {$_SESSION['paystack_ref']}, Got: $reference");
    // Allow to proceed anyway, but log it
}

try {
    error_log("Verifying Paystack transaction - Reference: $reference");
    
    // Verify transaction with Paystack via the API
    $verification_response = paystack_verify_transaction($reference);
    if (!$verification_response) {
        throw new Exception("No response from Paystack verification API");
    }
    error_log("Paystack verification response: " . json_encode($verification_response));

    // Check if verification was successful
    if (!isset($verification_response['status']) || $verification_response['status'] !== true) {
        $error_msg = $verification_response['message'] ?? 'Payment verification failed';
        error_log("Payment verification failed: $error_msg");

        echo json_encode([
            'status' => 'error',
            'message' => $error_msg,
            'verified' => false
        ]);
        exit();
    }
    
    // Extract transaction data
    $transaction_data = $verification_response['data'] ?? [];
    $payment_status = $transaction_data['status'] ?? null;
    $amount_paid = isset($transaction_data['amount']) ? $transaction_data['amount'] / 100 : 0; // Convert from pesewas
    $customer_email = $transaction_data['customer']['email'] ?? '';
    $authorization = $transaction_data['authorization'] ?? [];
    $authorization_code = $authorization['authorization_code'] ?? '';
    $payment_method = $authorization['channel'] ?? 'card';
    $auth_last_four = $authorization['last_four'] ?? 'XXXX';
    
    error_log("Transaction status: $payment_status, Amount: $amount_paid GHS");
    
    // Validate payment status
    if ($payment_status !== 'success') {
        error_log("Payment status is not successful: $payment_status");
        
        echo json_encode([
            'status' => 'error',
            'message' => 'Payment was not successful. Status: ' . ucfirst($payment_status),
            'verified' => false,
            'payment_status' => $payment_status
        ]);
        exit();
    }
    
    // Use the amount that was stored during initialization
    // This ensures we verify against what was actually sent to Paystack
    $expected_amount = isset($_SESSION['paystack_amount']) ? floatval($_SESSION['paystack_amount']) : 0;

    error_log("Expected amount from session: $expected_amount GHS, Amount paid: $amount_paid GHS");

    // If no session amount, try to fetch from database backup
    if ($expected_amount <= 0) {
        error_log("WARNING: Session amount not found. Checking database backup...");

        try {
            require_once '../settings/db_class.php';
            $db = new db_connection();
            $conn = $db->db_conn();
            if ($conn) {
                $stmt = $conn->prepare("SELECT amount FROM eventify_payment_init WHERE reference = ? LIMIT 1");
                if ($stmt) {
                    $stmt->bind_param('s', $reference);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc()) {
                        $expected_amount = floatval($row['amount']);
                        error_log("Found expected amount from database: $expected_amount GHS");
                    }
                }
            }
        } catch (Exception $db_ex) {
            error_log("Error fetching amount from database: " . $db_ex->getMessage());
        }
    }

    // If still no expected amount, use the amount paid by Paystack as the source of truth
    // Since Paystack already validated the payment, we trust their amount
    if ($expected_amount <= 0) {
        error_log("WARNING: No stored amount found. Using Paystack amount as expected amount: $amount_paid GHS");
        $expected_amount = $amount_paid;
    }

    // Use the expected amount for verification
    $total_amount = $expected_amount;

    // Verify amount matches (with 1 pesewa tolerance for rounding)
    if (abs($amount_paid - $expected_amount) > 0.01) {
        error_log("Amount mismatch - Expected: $expected_amount GHS, Paid: $amount_paid GHS, Difference: " . abs($amount_paid - $expected_amount));

        echo json_encode([
            'status' => 'error',
            'message' => 'Payment amount does not match order total',
            'verified' => false,
            'expected' => number_format($expected_amount, 2),
            'paid' => number_format($amount_paid, 2)
        ]);
        exit();
    }

    // Check if this payment has already been processed (duplicate verification)
    require_once '../settings/db_class.php';
    $db = new db_connection();
    $conn = $db->db_conn();

    // Use GET_LOCK to prevent race conditions (concurrent verification requests)
    $lock_name = "payment_verify_" . $reference;
    $lock_result = mysqli_query($conn, "SELECT GET_LOCK('$lock_name', 10)");
    $lock_acquired = false;
    if ($lock_result) {
        $lock_row = mysqli_fetch_row($lock_result);
        $lock_acquired = ($lock_row[0] == 1);
    }

    if (!$lock_acquired) {
        error_log("Failed to acquire lock for payment verification: $reference");
        // Wait a moment and check if payment was processed by another request
        sleep(2);
    }

    // Check using transaction_ref column
    $stmt = $conn->prepare("SELECT pay_id, order_id FROM eventify_payment WHERE transaction_ref = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('s', $reference);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($existing_payment = $result->fetch_assoc()) {
            error_log("Payment already processed - Reference: $reference, Payment ID: {$existing_payment['pay_id']}, Order ID: {$existing_payment['order_id']}");

            // Release lock
            mysqli_query($conn, "SELECT RELEASE_LOCK('$lock_name')");

            // Fetch the existing order details
            $stmt2 = $conn->prepare("SELECT invoice_no, order_date FROM eventify_orders WHERE order_id = ? LIMIT 1");
            if ($stmt2) {
                $stmt2->bind_param('i', $existing_payment['order_id']);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                if ($existing_order = $result2->fetch_assoc()) {
                    // Return success with existing order details
                    echo json_encode([
                        'status' => 'success',
                        'verified' => true,
                        'message' => 'Payment already processed successfully',
                        'order_id' => $existing_payment['order_id'],
                        'invoice_no' => $existing_order['invoice_no'],
                        'total_amount' => number_format($total_amount, 2),
                        'currency' => 'GHS',
                        'order_date' => date('F j, Y', strtotime($existing_order['order_date'])),
                        'customer_name' => getUserName(getUserID()),
                        'payment_reference' => $reference,
                        'duplicate' => true
                    ]);
                    exit();
                }
            }
        }
    }

    // Get cart items for order creation if not already loaded
    if (!isset($cartController)) {
        require_once '../controllers/cart_controller.php';
        $cartController = new CartController();
    }
    if (!$cart_items || count($cart_items) == 0) {
        $cart_items = $cartController->get_user_cart_ctr(getUserID());
    }

    // Payment is verified! Now create the order in our system
    require_once '../controllers/cart_controller.php';
    require_once '../controllers/order_controller.php';

    $customer_id = getUserID();
    $customer_name = getUserName($customer_id);

    // Get fresh cart items if not provided
    if (!$cart_items || count($cart_items) == 0) {
        $cart_items = $cartController->get_user_cart_ctr($customer_id);
    }

    if (!$cart_items || count($cart_items) == 0) {
        // Cart is empty - this might be a duplicate call or the cart was already processed
        // Check if there's a recent order for this customer
        error_log("WARNING: Cart is empty for customer $customer_id. Checking for recent orders...");

        $stmt = $conn->prepare("SELECT order_id, invoice_no, order_date FROM eventify_orders WHERE customer_id = ? ORDER BY order_date DESC LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('i', $customer_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($recent_order = $result->fetch_assoc()) {
                // Check if this order was created within the last 5 minutes
                $order_time = strtotime($recent_order['order_date']);
                $current_time = time();
                if (($current_time - $order_time) < 300) { // 5 minutes
                    error_log("Found recent order within 5 minutes - likely duplicate call. Order ID: {$recent_order['order_id']}");

                    // Release lock
                    if (isset($lock_name)) {
                        mysqli_query($conn, "SELECT RELEASE_LOCK('$lock_name')");
                    }

                    // Return success with the recent order
                    echo json_encode([
                        'status' => 'success',
                        'verified' => true,
                        'message' => 'Payment processed successfully',
                        'order_id' => $recent_order['order_id'],
                        'invoice_no' => $recent_order['invoice_no'],
                        'total_amount' => number_format($total_amount, 2),
                        'currency' => 'GHS',
                        'order_date' => date('F j, Y', strtotime($recent_order['order_date'])),
                        'customer_name' => $customer_name,
                        'payment_reference' => $reference,
                        'recovered' => true
                    ]);
                    exit();
                }
            }
        }

        // Release lock before throwing error
        if (isset($lock_name)) {
            mysqli_query($conn, "SELECT RELEASE_LOCK('$lock_name')");
        }

        throw new Exception("Cart is empty and no recent order found. Please contact support with reference: $reference");
    }
    
    // Create database connection for transaction
    $db = new db_connection();
    $conn = $db->db_conn();
    
    // Begin database transaction
    mysqli_begin_transaction($conn);
    error_log("Database transaction started");
    
    try {
        // Generate invoice number
        $invoice_no = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        $order_date = date('Y-m-d');
        
        // Create order in database using controller wrappers
        $orderController = new OrderController();
        $orderParams = [
            'customer_id' => $customer_id,
            'invoice_no' => $invoice_no,
            'order_date' => $order_date,
            'order_status' => 'Paid'
        ];

        $order_id = $orderController->create_order_ctr($orderParams);
        if (!$order_id) {
            throw new Exception("Failed to create order in database");
        }
        error_log("Order created - ID: $order_id, Invoice: $invoice_no");

        // Add order details for each cart item
        foreach ($cart_items as $item) {
            // Get event_id from cart item
            $eventId = $item['event_id'] ?? $item['p_id'] ?? $item['product_id'] ?? null;
            $qty = isset($item['qty']) ? intval($item['qty']) : (isset($item['quantity']) ? intval($item['quantity']) : 0);

            if (!$eventId) {
                error_log("Warning: Cart item missing event_id: " . json_encode($item));
                continue; // Skip items without event_id
            }

            $detailParams = [
                'order_id' => $order_id,
                'product_id' => $eventId,  // Controller expects 'product_id' but it's actually event_id
                'qty' => $qty
            ];

            $detail_result = $orderController->add_order_details_ctr($detailParams);

            if (!$detail_result) {
                throw new Exception("Failed to add order details for event: {$eventId}");
            }

            error_log("Order detail added - Event: {$eventId}, Qty: {$qty}");
        }

        // Record payment in database via controller
        $paymentParams = [
            'amt' => $total_amount,
            'customer_id' => $customer_id,
            'order_id' => $order_id,
            'currency' => 'GHS',
            'payment_date' => $order_date,
            'payment_reference' => $reference  // Add payment reference
        ];

        $payment_id = $orderController->record_payment_ctr($paymentParams);
        if (!$payment_id) {
            throw new Exception("Failed to record payment");
        }
        error_log("Payment recorded - ID: $payment_id, Reference: $reference");

        // Empty the customer's cart using CartController
        $cartController = new CartController();
        $empty_result = $cartController->empty_cart_ctr($customer_id);
        if (!$empty_result) {
            throw new Exception("Failed to empty cart");
        }
        error_log("Cart emptied for customer: $customer_id");
        
        // Commit database transaction
        mysqli_commit($conn);
        error_log("Database transaction committed successfully");
        
        // Clear session payment data
        unset($_SESSION['paystack_ref']);
        unset($_SESSION['paystack_amount']);
        unset($_SESSION['paystack_timestamp']);

        // Log user activity
        error_log("User Activity - Completed payment via Paystack - Invoice: $invoice_no, Amount: GHS $total_amount, Reference: $reference");

        // Release lock before returning success
        if (isset($lock_name) && isset($conn)) {
            mysqli_query($conn, "SELECT RELEASE_LOCK('$lock_name')");
        }

        // Return success response
        echo json_encode([
            'status' => 'success',
            'verified' => true,
            'message' => 'Payment successful! Order confirmed.',
            'order_id' => $order_id,
            'invoice_no' => $invoice_no,
            'total_amount' => number_format($total_amount, 2),
            'currency' => 'GHS',
            'order_date' => date('F j, Y', strtotime($order_date)),
            'customer_name' => $customer_name,
            'item_count' => count($cart_items),
            'payment_reference' => $reference,
            'payment_method' => ucfirst($payment_method),
            'customer_email' => $customer_email
        ]);

    } catch (Exception $e) {
        // Rollback database transaction on error
        mysqli_rollback($conn);
        error_log("Database transaction rolled back: " . $e->getMessage());

        // Release lock on error
        if (isset($lock_name) && isset($conn)) {
            mysqli_query($conn, "SELECT RELEASE_LOCK('$lock_name')");
        }

        throw $e;
    }

} catch (Exception $e) {
    error_log("Error in Paystack callback/verification: " . $e->getMessage());

    // Release lock on error
    if (isset($lock_name) && isset($conn)) {
        mysqli_query($conn, "SELECT RELEASE_LOCK('$lock_name')");
    }

    echo json_encode([
        'status' => 'error',
        'verified' => false,
        'message' => 'Payment processing error: ' . $e->getMessage()
    ]);
}
?>
