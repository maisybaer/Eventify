<?php
require_once '../controllers/vendor_booking_controller.php';
require_once '../settings/core.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

try {
    // Get logged-in vendor
    $vendor_id = getUserID();
    $user_role = getUserRole();

    if (!$vendor_id) {
        echo json_encode(['status' => 'error', 'message' => 'You must be logged in']);
        exit;
    }

    if ($user_role != 2) {
        echo json_encode(['status' => 'error', 'message' => 'Only vendors can request to provide services']);
        exit;
    }

    // Get POST data
    $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
    $notes = isset($_POST['notes']) && !empty($_POST['notes']) ? trim($_POST['notes']) : null;

    // Validate inputs
    if ($event_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid event']);
        exit;
    }

    // Get event details to find the event creator (customer_id)
    require_once '../controllers/event_controller.php';
    $event = viewSingleEvent_ctr($event_id);

    if (!$event) {
        echo json_encode(['status' => 'error', 'message' => 'Event not found']);
        exit;
    }

    $event_creator_id = $event['added_by'] ?? 0;

    if ($event_creator_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Event creator not found']);
        exit;
    }

    // Check if vendor already requested this event
    require_once '../classes/vendor_booking_class.php';
    $bookingClass = new VendorBooking();
    $existingBooking = $bookingClass->checkVendorEventRequest($vendor_id, $event_id);

    if ($existingBooking) {
        echo json_encode(['status' => 'error', 'message' => 'You have already requested to vendor this event']);
        exit;
    }

    // Create the booking request (vendor_to_event type)
    // vendor_id = the vendor making the request
    // customer_id = the event creator (who needs to approve)
    // event_id = the event they want to vendor at
    $booking_id = $bookingClass->createVendorEventRequest($vendor_id, $event_creator_id, $event_id, $notes);

    if ($booking_id) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Vendor request sent successfully. Awaiting event manager approval.',
            'booking_id' => $booking_id
        ]);
    } else {
        error_log("Failed to create vendor event request: vendor_id=$vendor_id, event_id=$event_id");
        echo json_encode(['status' => 'error', 'message' => 'Failed to send request. Please try again.']);
    }
} catch (Exception $e) {
    error_log("Vendor event request exception: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
