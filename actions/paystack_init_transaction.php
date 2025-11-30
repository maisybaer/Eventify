<?php
header('Content-Type: application/json');

// Include core and Paystack configuration
require_once '../settings/core.php';
require_once '../settings/paystack_config.php';

error_log("=== PAYSTACK INITIALIZE TRANSACTION ===");

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please login to complete payment'
    ]);
    exit();
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$amount = isset($input['amount']) ? floatval($input['amount']) : 0;
$customer_email = isset($input['email']) ? trim($input['email']) : '';

if (!$amount || !$customer_email) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid amount or email'
    ]);
    exit();
}

// Validate amount
if ($amount <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Amount must be greater than 0'
    ]);
    exit();
}

// Validate email
if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid email address'
    ]);
    exit();
}

try {
    // Generate unique reference
    $customer_id = getUserID();
    $reference = 'EVENTIFY-' . $customer_id . '-' . time();

    error_log("Initializing transaction - Customer: $customer_id, Amount: $amount GHS, Email: $customer_email, Reference: $reference");

    // Store transaction reference in session for verification later
    $_SESSION['paystack_ref'] = $reference;
    $_SESSION['paystack_amount'] = $amount;
    $_SESSION['paystack_timestamp'] = time();

    // Also store in a persistent way (database) as a backup for session loss
    try {
        require_once '../settings/db_class.php';
        $db = new db_connection();
        $conn = $db->db_conn();
        if ($conn) {
            $stmt = $conn->prepare("INSERT INTO eventify_payment_init (customer_id, reference, amount, email, created_at) VALUES (?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE amount = ?, email = ?, created_at = NOW()");
            if ($stmt) {
                $stmt->bind_param('isdsds', $customer_id, $reference, $amount, $customer_email, $amount, $customer_email);
                $stmt->execute();
                error_log("Payment initialization stored in database for reference: $reference");
            }
        }
    } catch (Exception $db_ex) {
        // Don't fail the payment if database storage fails
        error_log("Warning: Could not store payment init in database: " . $db_ex->getMessage());
    }

    // Initialize Paystack transaction (live/test real API call)
    $paystack_response = paystack_initialize_transaction($amount, $customer_email, $reference);

    if (!$paystack_response) {
        throw new Exception("No response from Paystack API");
    }

    if (isset($paystack_response['status']) && $paystack_response['status'] === true) {
        // Store transaction reference in session for verification later
        $_SESSION['paystack_ref'] = $reference;
        $_SESSION['paystack_amount'] = $amount;
        $_SESSION['paystack_timestamp'] = time();

        error_log("Paystack transaction initialized successfully - Reference: $reference");

        echo json_encode([
            'status' => true,
            'authorization_url' => $paystack_response['data']['authorization_url'],
            'reference' => $reference,
            'access_code' => $paystack_response['data']['access_code'],
            'public_key' => PAYSTACK_PUBLIC_KEY,
            'message' => 'Redirecting to payment gateway...'
        ]);
    } else {
        error_log("Paystack API error: " . json_encode($paystack_response));

        $error_message = $paystack_response['message'] ?? 'Payment gateway error';
        throw new Exception($error_message);
    }
    
} catch (Exception $e) {
    error_log("Error initializing Paystack transaction: " . $e->getMessage());
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to initialize payment: ' . $e->getMessage()
    ]);
}
?>
