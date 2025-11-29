<?php
require_once '../classes/vendor_booking_class.php';
require_once '../settings/core.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

try {
    // Get logged-in user (event manager)
    $user_id = getUserID();
    if (!$user_id) {
        echo json_encode(['status' => 'error', 'message' => 'You must be logged in']);
        exit;
    }

    // Get POST data
    $booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
    $approved = isset($_POST['approved']) ? intval($_POST['approved']) : 0;

    // Validate inputs
    if ($booking_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid booking ID']);
        exit;
    }

    $bookingClass = new VendorBooking();

    // Get booking to verify ownership
    $booking = $bookingClass->getBooking($booking_id);
    if (!$booking) {
        echo json_encode(['status' => 'error', 'message' => 'Booking not found']);
        exit;
    }

    // Verify user is the event creator (customer_id in vendor_to_event bookings)
    if ($booking['customer_id'] != $user_id) {
        echo json_encode(['status' => 'error', 'message' => 'You do not have permission to approve this request']);
        exit;
    }

    // Update approval status
    $result = $bookingClass->updateEventManagerApproval($booking_id, $approved);

    if ($result) {
        $message = $approved ? 'Vendor request approved successfully' : 'Vendor request rejected';
        echo json_encode([
            'status' => 'success',
            'message' => $message
        ]);
    } else {
        error_log("Failed to update vendor request approval: booking_id=$booking_id, approved=$approved");
        echo json_encode(['status' => 'error', 'message' => 'Failed to update request status']);
    }
} catch (Exception $e) {
    error_log("Approve vendor request exception: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
