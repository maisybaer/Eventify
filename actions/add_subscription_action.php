<?php
/**
 * Add Subscription Action
 * Creates a new premium subscription and initiates payment
 */

header('Content-Type: application/json');

require_once '../settings/core.php';
require_once '../controllers/subscription_controller.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    error_log("Session check failed - user_id: " . ($_SESSION['user_id'] ?? 'not set'));
    echo json_encode(['status' => 'error', 'message' => 'Please login first']);
    exit;
}

error_log("Session user_id: " . $_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $customer_id = getUserID();
        $subscription_type = $_POST['subscription_type'] ?? 'analytics_premium';
        $amount = floatval($_POST['amount'] ?? 50.00);
        $currency = $_POST['currency'] ?? 'GHS';

        error_log("Creating subscription - Customer: $customer_id, Type: $subscription_type, Amount: $amount");

        // Validate amount (analytics premium is 50 GHS)
        if ($subscription_type === 'analytics_premium' && $amount != 50.00) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid subscription amount']);
            exit;
        }

        // Check if user already has an active subscription
        $active_subscription = get_active_subscription_ctr($customer_id, $subscription_type);
        if ($active_subscription) {
            echo json_encode([
                'status' => 'error',
                'message' => 'You already have an active subscription until ' . date('F j, Y', strtotime($active_subscription['end_date']))
            ]);
            exit;
        }

        // Create subscription record
        $subscription_id = create_subscription_ctr($customer_id, $subscription_type, $amount, $currency);

        error_log("Subscription ID returned: " . var_export($subscription_id, true));

        if ($subscription_id) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Subscription created successfully',
                'subscription_id' => $subscription_id,
                'amount' => $amount,
                'currency' => $currency
            ]);
        } else {
            error_log("Failed to create subscription - no ID returned");
            echo json_encode(['status' => 'error', 'message' => 'Failed to create subscription. Please try again.']);
        }
    } catch (Exception $e) {
        error_log("Subscription creation error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
