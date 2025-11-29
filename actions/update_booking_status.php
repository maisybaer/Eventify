<?php
require_once '../settings/core.php';
require_once '../settings/db_class.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access'
    ]);
    exit;
}

// Validate required parameters
if (!isset($_POST['booking_id']) || !isset($_POST['status'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required parameters'
    ]);
    exit;
}

$booking_id = (int) $_POST['booking_id'];
$user_id = (int) $_SESSION['user_id'];
$user_role = getUserRole();

// Get database connection
$db = new db_connection();
$conn = $db->db_conn();

if (!$conn) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed'
    ]);
    exit;
}

$status = mysqli_real_escape_string($conn, $_POST['status']);

// Validate status - add more valid statuses
$valid_statuses = ['approved', 'rejected', 'cancelled', 'confirmed', 'completed', 'pending'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid status'
    ]);
    exit;
}

// Get booking details to verify ownership
$booking_sql = "SELECT vb.*, e.added_by as event_creator_id
                FROM eventify_vendor_bookings vb
                LEFT JOIN eventify_products e ON vb.event_id = e.event_id
                WHERE vb.booking_id = $booking_id
                LIMIT 1";
$booking_res = mysqli_query($conn, $booking_sql);

if (!$booking_res || mysqli_num_rows($booking_res) === 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Booking not found'
    ]);
    exit;
}

$booking = mysqli_fetch_assoc($booking_res);
$is_authorized = false;

// Check authorization based on role and booking type
if ($user_role == 2) {
    // Vendor: can update bookings where they are the vendor
    // Note: vendor_id in eventify_vendor_bookings is actually the customer_id of the vendor (role=2)
    if ($booking['vendor_id'] == $user_id) {
        $is_authorized = true;
    }
} else {
    // Customer/Event Manager: can cancel bookings they made OR bookings for their events
    if ($booking['customer_id'] == $user_id) {
        // They made this booking
        $is_authorized = true;
    } elseif ($booking['event_creator_id'] == $user_id) {
        // They own the event this booking is for
        $is_authorized = true;
    }
}

if (!$is_authorized) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized: You do not have permission to update this booking'
    ]);
    exit;
}

// Update booking status
$update_sql = "UPDATE eventify_vendor_bookings SET booking_status = '$status' WHERE booking_id = $booking_id";
$update_res = mysqli_query($conn, $update_sql);

if ($update_res) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Booking status updated successfully'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update booking status: ' . mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>
