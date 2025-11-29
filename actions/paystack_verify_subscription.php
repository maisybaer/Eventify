<?php
/**
 * Paystack Subscription Payment Verification
 * Handles verification for premium subscription payments
 */

header('Content-Type: application/json');

require_once '../settings/core.php';
require_once '../settings/paystack_config.php';
require_once '../controllers/subscription_controller.php';

error_log("=== PAYSTACK SUBSCRIPTION VERIFICATION ===");

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Session expired. Please login again.']);
    exit();
}

// Get verification reference
$input = json_decode(file_get_contents('php://input'), true);
$reference = isset($input['reference']) ? trim($input['reference']) : null;
$subscription_id = isset($input['subscription_id']) ? intval($input['subscription_id']) : 0;

if (!$reference || !$subscription_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing payment reference or subscription ID']);
    exit();
}

try {
    error_log("Verifying subscription payment - Reference: $reference, Subscription ID: $subscription_id");

    // Verify transaction with Paystack
    $verification_response = paystack_verify_transaction($reference);
    if (!$verification_response || !isset($verification_response['status']) || $verification_response['status'] !== true) {
        throw new Exception('Payment verification failed');
    }

    // Extract transaction data
    $transaction_data = $verification_response['data'] ?? [];
    $payment_status = $transaction_data['status'] ?? null;
    $amount_paid = isset($transaction_data['amount']) ? $transaction_data['amount'] / 100 : 0;

    error_log("Transaction status: $payment_status, Amount: $amount_paid GHS");

    // Validate payment status
    if ($payment_status !== 'success') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Payment was not successful. Status: ' . ucfirst($payment_status),
            'verified' => false
        ]);
        exit();
    }

    // Get subscription details
    $subscription = get_subscription_by_id_ctr($subscription_id);
    if (!$subscription) {
        throw new Exception('Subscription not found');
    }

    // Verify customer ID matches
    if ($subscription['customer_id'] != getUserID()) {
        throw new Exception('Unauthorized: Subscription does not belong to you');
    }

    // Verify amount matches (with tolerance for rounding)
    $expected_amount = floatval($subscription['amount']);
    if (abs($amount_paid - $expected_amount) > 0.01) {
        error_log("Amount mismatch - Expected: $expected_amount GHS, Paid: $amount_paid GHS");
        echo json_encode([
            'status' => 'error',
            'message' => 'Payment amount does not match subscription price',
            'verified' => false
        ]);
        exit();
    }

    // Generate invoice number
    $invoice_no = 'SUB-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

    // Activate subscription
    $activated = activate_subscription_ctr($subscription_id, $reference, $invoice_no);
    if (!$activated) {
        throw new Exception('Failed to activate subscription');
    }

    error_log("Subscription activated - ID: $subscription_id, Invoice: $invoice_no");

    // Clear session payment data
    unset($_SESSION['paystack_ref']);
    unset($_SESSION['paystack_amount']);
    unset($_SESSION['paystack_timestamp']);

    // Return success response
    echo json_encode([
        'status' => 'success',
        'verified' => true,
        'message' => 'Premium subscription activated!',
        'subscription_id' => $subscription_id,
        'invoice_no' => $invoice_no,
        'payment_reference' => $reference,
        'subscription_type' => $subscription['subscription_type'],
        'start_date' => date('F j, Y'),
        'end_date' => date('F j, Y', strtotime('+30 days')),
        'amount' => number_format($amount_paid, 2),
        'currency' => $subscription['currency']
    ]);

} catch (Exception $e) {
    error_log("Subscription verification error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'verified' => false
    ]);
}
?>
