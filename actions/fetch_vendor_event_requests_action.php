<?php
require_once '../classes/vendor_booking_class.php';
require_once '../settings/core.php';
header('Content-Type: application/json');

try {
    $vendor_id = getUserID();
    $user_role = getUserRole();

    if (!$vendor_id || $user_role != 2) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'data' => []]);
        exit;
    }

    // Get all vendor-to-event requests made by this vendor
    $bookingClass = new VendorBooking();
    $requests = $bookingClass->getVendorEventRequests($vendor_id);

    echo json_encode(['status' => 'success', 'data' => $requests]);
} catch (Throwable $e) {
    error_log('Fetch vendor event requests error: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server error', 'data' => []]);
}
?>
