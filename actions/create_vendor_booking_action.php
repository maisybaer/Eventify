<?php
require_once '../controllers/vendor_booking_controller.php';
require_once '../settings/core.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

try {
    // Get logged-in customer
    $customer_id = getUserID();
    if (!$customer_id) {
        echo json_encode(['status' => 'error', 'message' => 'You must be logged in to book a vendor']);
        exit;
    }

    // Get POST data
    $vendor_id = isset($_POST['vendor_id']) ? intval($_POST['vendor_id']) : 0;
    $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
    $service_date = isset($_POST['service_date']) && !empty($_POST['service_date']) ? $_POST['service_date'] : null;
    $notes = isset($_POST['notes']) && !empty($_POST['notes']) ? trim($_POST['notes']) : null;
    $price = isset($_POST['price']) && !empty($_POST['price']) ? floatval($_POST['price']) : null;

    // Validate inputs
    if ($vendor_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid vendor']);
        exit;
    }

    if ($event_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Please select an event']);
        exit;
    }

    // Create the booking
    $booking_id = create_vendor_booking_ctr($vendor_id, $customer_id, $event_id, $service_date, $notes, $price);

    if ($booking_id) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Vendor booking created successfully',
            'booking_id' => $booking_id
        ]);
    } else {
        error_log("Failed to create vendor booking: vendor_id=$vendor_id, customer_id=$customer_id, event_id=$event_id");
        echo json_encode(['status' => 'error', 'message' => 'Failed to create booking. Please try again.']);
    }
} catch (Exception $e) {
    error_log("Vendor booking exception: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
