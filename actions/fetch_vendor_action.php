<?php
/**
 * Fetch Vendor Action
 * Handles all vendor-related fetch operations
 * Usage: ?action=all or ?action=single (requires login)
 */

header('Content-Type: application/json');
require_once '../settings/session.php';
require_once '../controllers/vendor_controller.php';

$action = $_GET['action'] ?? 'all';

switch ($action) {
    case 'all':
        // Public endpoint to fetch all vendors (customers with role=2)
        $data = fetch_all_vendors_ctr();
        if ($data === false) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch vendors']);
            exit();
        }
        echo json_encode(['status' => 'success', 'data' => $data]);
        break;

    case 'single':
        // Fetch vendor data for logged-in user
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
            exit();
        }

        $user_id = (int) $_SESSION['user_id'];
        $data = fetch_vendor_ctr($user_id);
        if ($data === false) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch vendor data']);
            exit();
        }
        echo json_encode(['status' => 'success', 'data' => $data]);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
?>
