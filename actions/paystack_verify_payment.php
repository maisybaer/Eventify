<?php
header('Content-Type: application/json');

// Include core and Paystack configuration
require_once '../settings/core.php';
require_once '../settings/paystack_config.php';
require_once '../controllers/order_controller.php';

error_log("=== PAYSTACK VERIFY PAYMENT ===");

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please login to verify payment'
    ]);
    exit();
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$reference = isset($input['reference']) ? trim($input['reference']) : '';

if (!$reference) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Payment reference is required'
    ]);
    exit();
}

try {
    error_log("Verifying payment - Reference: $reference");
    
    // Verify transaction with Paystack
    $verification_response = paystack_verify_transaction($reference);
    
    if (!$verification_response) {
        throw new Exception("No response from Paystack verification API");
    }
    
    if (isset($verification_response['status']) && $verification_response['status'] === true) {
        $transaction_data = $verification_response['data'];
        
        // Check if payment was successful
        if ($transaction_data['status'] === 'success') {
            $customer_id = get_user_id();
            $amount = $transaction_data['amount'] / 100; // Paystack returns amount in kobo
            $currency = $transaction_data['currency'];
            
            error_log("Payment verified successfully - Customer: $customer_id, Amount: $amount $currency");
            
            // Create order from cart
            $order_result = create_order_from_cart_ctr($customer_id, $reference, $amount);
            
            if ($order_result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Payment verified and order created successfully',
                    'order_id' => $order_result,
                    'amount' => $amount,
                    'currency' => $currency,
                    'reference' => $reference
                ]);
            } else {
                throw new Exception("Failed to create order after successful payment");
            }
        } else {
            throw new Exception("Payment was not successful: " . $transaction_data['status']);
        }
    } else {
        error_log("Paystack verification error: " . json_encode($verification_response));
        
        $error_message = $verification_response['message'] ?? 'Payment verification failed';
        throw new Exception($error_message);
    }
    
} catch (Exception $e) {
    error_log("Error verifying payment: " . $e->getMessage());
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Payment verification failed: ' . $e->getMessage()
    ]);
}
?>