<?php
/**
 * Fetch Subscription Action
 * Handles subscription-related fetch operations
 * Usage: ?action=active or ?action=all or ?action=stats
 */

header('Content-Type: application/json');
require_once '../settings/core.php';
require_once '../controllers/subscription_controller.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

$customer_id = getUserID();
$action = $_GET['action'] ?? 'active';

switch ($action) {
    case 'active':
        // Get active subscription
        $subscription_type = $_GET['type'] ?? 'analytics_premium';
        $subscription = get_active_subscription_ctr($customer_id, $subscription_type);

        if ($subscription) {
            echo json_encode([
                'status' => 'success',
                'has_premium' => true,
                'data' => $subscription
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'has_premium' => false,
                'data' => null
            ]);
        }
        break;

    case 'all':
        // Get all subscriptions for customer
        $subscriptions = get_customer_subscriptions_ctr($customer_id);
        echo json_encode([
            'status' => 'success',
            'data' => $subscriptions
        ]);
        break;

    case 'check':
        // Simple premium check
        $subscription_type = $_GET['type'] ?? 'analytics_premium';
        $has_premium = has_active_premium_ctr($customer_id, $subscription_type);

        echo json_encode([
            'status' => 'success',
            'has_premium' => $has_premium
        ]);
        break;

    case 'stats':
        // Get subscription stats (admin only)
        $user_role = getUserRole();
        if ($user_role != 1) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit();
        }

        $stats = get_subscription_stats_ctr();
        echo json_encode([
            'status' => 'success',
            'data' => $stats
        ]);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
?>
