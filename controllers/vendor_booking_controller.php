<?php
require_once(__DIR__ . '/../classes/vendor_booking_class.php');

/**
 * Create a new vendor booking
 */
function create_vendor_booking_ctr($vendor_id, $customer_id, $event_id, $service_date = null, $notes = null, $price = null)
{
    $booking = new VendorBooking();
    return $booking->createBooking($vendor_id, $customer_id, $event_id, $service_date, $notes, $price);
}

/**
 * Get all bookings for a customer
 */
function get_customer_bookings_ctr($customer_id)
{
    $booking = new VendorBooking();
    return $booking->getCustomerBookings($customer_id);
}

/**
 * Get all bookings for a vendor
 */
function get_vendor_bookings_ctr($vendor_id)
{
    $booking = new VendorBooking();
    return $booking->getVendorBookings($vendor_id);
}

/**
 * Get a single booking by ID
 */
function get_booking_ctr($booking_id)
{
    $booking = new VendorBooking();
    return $booking->getBooking($booking_id);
}

/**
 * Update booking status
 */
function update_booking_status_ctr($booking_id, $status)
{
    $booking = new VendorBooking();
    return $booking->updateBookingStatus($booking_id, $status);
}

/**
 * Update booking details
 */
function update_booking_ctr($booking_id, $service_date = null, $notes = null, $price = null)
{
    $booking = new VendorBooking();
    return $booking->updateBooking($booking_id, $service_date, $notes, $price);
}

/**
 * Delete a booking
 */
function delete_booking_ctr($booking_id)
{
    $booking = new VendorBooking();
    return $booking->deleteBooking($booking_id);
}
?>
