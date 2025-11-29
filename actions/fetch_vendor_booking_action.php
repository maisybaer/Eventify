<?php
/**
 * Fetch Vendor Booking Action
 * Handles all vendor booking-related fetch operations
 * Usage: ?action=vendor_requests (for vendors) or ?action=event_manager_requests (for event managers)
 */

header('Content-Type: application/json');
require_once '../settings/core.php';
require_once '../classes/vendor_booking_class.php';
require_once '../settings/db_class.php';

$action = $_GET['action'] ?? 'vendor_requests';

try {
    switch ($action) {
        case 'vendor_requests':
            // Fetch vendor-to-event requests made by logged-in vendor
            $vendor_id = getUserID();
            $user_role = getUserRole();

            if (!$vendor_id || $user_role != 2) {
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'data' => []]);
                exit;
            }

            $bookingClass = new VendorBooking();
            $requests = $bookingClass->getVendorEventRequests($vendor_id);
            echo json_encode(['status' => 'success', 'data' => $requests]);
            break;

        case 'event_manager_requests':
            // Fetch vendor requests for events created by logged-in event manager
            if (!isset($_SESSION['user_id']) || getUserRole() != 1) {
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
                exit();
            }

            $event_manager_id = (int) $_SESSION['user_id'];
            $db = new db_connection();
            $conn = $db->db_conn();

            if (!$conn) {
                throw new Exception('Database connection failed');
            }

            $sql = "SELECT
                        vb.booking_id,
                        vb.vendor_id,
                        vb.event_id,
                        vb.booking_status,
                        vb.booking_date,
                        vb.notes,
                        vb.approved_by_event_manager,
                        vb.event_manager_approved_date,
                        c.customer_name as vendor_name,
                        c.customer_email as vendor_email,
                        c.customer_contact as vendor_contact,
                        e.event_desc,
                        e.event_date,
                        e.event_location,
                        e.event_price
                    FROM eventify_vendor_bookings vb
                    LEFT JOIN eventify_customer c ON vb.vendor_id = c.customer_id
                    LEFT JOIN eventify_products e ON vb.event_id = e.event_id
                    WHERE e.added_by = ?
                    AND vb.booking_type = 'vendor_to_event'
                    ORDER BY vb.booking_date DESC";

            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $event_manager_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $requests = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $requests[] = $row;
            }

            echo json_encode(['status' => 'success', 'data' => $requests]);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
} catch (Throwable $e) {
    error_log('Fetch vendor booking error: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server error', 'data' => []]);
}
?>
