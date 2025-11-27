<?php
require_once '../controllers/vendor_booking_controller.php';
require_once '../settings/core.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

try {
    // Get logged-in user
    $user_id = getUserID();
    if (!$user_id) {
        echo json_encode(['status' => 'error', 'message' => 'You must be logged in']);
        exit;
    }

    // Get POST data
    $booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';

    // Validate inputs
    if ($booking_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid booking ID']);
        exit;
    }

    $allowed_statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
    if (!in_array($status, $allowed_statuses)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid status']);
        exit;
    }

    // Get booking to verify ownership
    $booking = get_booking_ctr($booking_id);
    if (!$booking) {
        echo json_encode(['status' => 'error', 'message' => 'Booking not found']);
        exit;
    }

    // Verify user has permission (either vendor or customer of the booking)
    if ($booking['vendor_id'] != $user_id && $booking['customer_id'] != $user_id) {
        echo json_encode(['status' => 'error', 'message' => 'You do not have permission to update this booking']);
        exit;
    }

    // Update the status
    $result = update_booking_status_ctr($booking_id, $status);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Booking status updated successfully'
        ]);
    } else {
        error_log("Failed to update booking status: booking_id=$booking_id, status=$status");
        echo json_encode(['status' => 'error', 'message' => 'Failed to update booking status']);
    }
} catch (Exception $e) {
    error_log("Update booking status exception: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
