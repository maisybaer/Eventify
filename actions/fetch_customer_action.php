<?php
/**
 * Fetch Customer Action
 * Handles all customer-related fetch operations
 * Usage: ?action=email or ?action=events
 */

header('Content-Type: application/json');
require_once '../settings/core.php';
require_once '../controllers/event_controller.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

$action = $_GET['action'] ?? 'email';

switch ($action) {
    case 'email':
        // Fetch customer email
        $email = getUserEmail();
        if ($email) {
            echo json_encode(['status' => 'success', 'email' => $email]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Email not found']);
        }
        break;

    case 'events':
        // Fetch events created by the logged-in customer
        try {
            $customer_id = getUserID();
            $events = get_event_ctr($customer_id);

            if ($events === false || $events === null) {
                echo json_encode(['status' => 'success', 'data' => []]);
            } else {
                echo json_encode(['status' => 'success', 'data' => $events]);
            }
        } catch (Throwable $e) {
            error_log('Fetch customer events error: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Server error', 'data' => []]);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
?>
