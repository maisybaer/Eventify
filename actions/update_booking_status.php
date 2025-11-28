<?php
require_once '../settings/core.php';
require_once '../settings/db_class.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

// Validate required parameters
if (!isset($_POST['booking_id']) || !isset($_POST['status'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit;
}

$booking_id = (int) $_POST['booking_id'];
$status = mysqli_real_escape_string((new db_connection())->db_conn(), $_POST['status']);

// Validate status
$valid_statuses = ['approved', 'rejected', 'cancelled'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid status'
    ]);
    exit;
}

// Get database connection
$db = new db_connection();
$conn = $db->db_conn();

if (!$conn) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit;
}

// Verify user is a vendor and owns this booking
$uid = (int) $_SESSION['user_id'];

// First, get the customer record to check if user is a vendor
$customer_sql = "SELECT * FROM eventify_customer WHERE customer_id = $uid AND user_role = 2 LIMIT 1";
$customer_res = mysqli_query($conn, $customer_sql);

if (!$customer_res || mysqli_num_rows($customer_res) === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized: Not a vendor account'
    ]);
    exit;
}

$customer = mysqli_fetch_assoc($customer_res);
$customer_name = mysqli_real_escape_string($conn, $customer['customer_name']);

// Get vendor ID
$vendor_sql = "SELECT vendor_id FROM eventify_vendor WHERE vendor_desc = '$customer_name' LIMIT 1";
$vendor_res = mysqli_query($conn, $vendor_sql);

if (!$vendor_res || mysqli_num_rows($vendor_res) === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Vendor profile not found'
    ]);
    exit;
}

$vendor = mysqli_fetch_assoc($vendor_res);
$vendor_id = (int) $vendor['vendor_id'];

// Verify booking belongs to this vendor
$verify_sql = "SELECT * FROM eventify_vendor_bookings WHERE booking_id = $booking_id AND vendor_id = $vendor_id LIMIT 1";
$verify_res = mysqli_query($conn, $verify_sql);

if (!$verify_res || mysqli_num_rows($verify_res) === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Booking not found or does not belong to you'
    ]);
    exit;
}

// Update booking status
$update_sql = "UPDATE eventify_vendor_bookings SET booking_status = '$status' WHERE booking_id = $booking_id";
$update_res = mysqli_query($conn, $update_sql);

if ($update_res) {
    echo json_encode([
        'success' => true,
        'message' => 'Booking status updated successfully',
        'status' => $status
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update booking status: ' . mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>
