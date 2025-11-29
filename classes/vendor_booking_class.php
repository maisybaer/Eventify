<?php
require_once(__DIR__ . '/../settings/db_class.php');

class VendorBooking extends db_connection
{
    /**
     * Create a new vendor booking
     */
    public function createBooking($vendor_id, $customer_id, $event_id, $service_date = null, $notes = null, $price = null)
    {
        if (!$this->db) $this->db_connect();

        $stmt = $this->db->prepare(
            "INSERT INTO eventify_vendor_bookings
            (vendor_id, customer_id, event_id, service_date, notes, price, booking_status)
            VALUES (?, ?, ?, ?, ?, ?, 'pending')"
        );

        if (!$stmt) {
            error_log('createBooking prepare failed: ' . $this->db->error);
            return false;
        }

        $stmt->bind_param("iiissd", $vendor_id, $customer_id, $event_id, $service_date, $notes, $price);
        $result = $stmt->execute();

        if (!$result) {
            error_log('createBooking execute failed: ' . $stmt->error);
            return false;
        }

        return $this->db->insert_id;
    }

    /**
     * Get all bookings for a specific customer
     */
    public function getCustomerBookings($customer_id)
    {
        if (!$this->db) $this->db_connect();

        $stmt = $this->db->prepare(
            "SELECT
                vb.*,
                vc.customer_name as vendor_name,
                vc.customer_email as vendor_email,
                vc.customer_contact as vendor_contact,
                vc.customer_image as vendor_image,
                v.vendor_type,
                v.vendor_desc,
                e.event_desc,
                e.event_date,
                e.event_location
            FROM eventify_vendor_bookings vb
            LEFT JOIN eventify_customer vc ON vb.vendor_id = vc.customer_id
            LEFT JOIN eventify_vendor v ON vc.customer_name = v.vendor_desc
            LEFT JOIN eventify_products e ON vb.event_id = e.event_id
            WHERE vb.customer_id = ?
            ORDER BY vb.booking_date DESC"
        );

        if (!$stmt) {
            error_log('getCustomerBookings prepare failed: ' . $this->db->error);
            return [];
        }

        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get all bookings for a specific vendor
     */
    public function getVendorBookings($vendor_id)
    {
        if (!$this->db) $this->db_connect();

        $stmt = $this->db->prepare(
            "SELECT
                vb.*,
                c.customer_name,
                c.customer_email,
                c.customer_contact,
                e.event_desc,
                e.event_date,
                e.event_location
            FROM eventify_vendor_bookings vb
            LEFT JOIN eventify_customer c ON vb.customer_id = c.customer_id
            LEFT JOIN eventify_products e ON vb.event_id = e.event_id
            WHERE vb.vendor_id = ?
            ORDER BY vb.booking_date DESC"
        );

        if (!$stmt) {
            error_log('getVendorBookings prepare failed: ' . $this->db->error);
            return [];
        }

        $stmt->bind_param("i", $vendor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get customer-to-vendor booking requests for a specific vendor
     */
    public function getCustomerToVendorRequests($vendor_id)
    {
        if (!$this->db) $this->db_connect();

        $stmt = $this->db->prepare(
            "SELECT
                vb.*,
                c.customer_name,
                c.customer_email,
                c.customer_contact,
                e.event_desc,
                e.event_date,
                e.event_location
            FROM eventify_vendor_bookings vb
            LEFT JOIN eventify_customer c ON vb.customer_id = c.customer_id
            LEFT JOIN eventify_products e ON vb.event_id = e.event_id
            WHERE vb.vendor_id = ? AND (vb.booking_type = 'customer_to_vendor' OR vb.booking_type IS NULL OR vb.booking_type = '')
            ORDER BY vb.booking_date DESC"
        );

        if (!$stmt) {
            error_log('getCustomerToVendorRequests prepare failed: ' . $this->db->error);
            return [];
        }

        $stmt->bind_param("i", $vendor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get a single booking by ID
     */
    public function getBooking($booking_id)
    {
        if (!$this->db) $this->db_connect();

        $stmt = $this->db->prepare(
            "SELECT
                vb.*,
                vc.customer_name as vendor_name,
                vc.customer_email as vendor_email,
                vc.customer_contact as vendor_contact,
                v.vendor_type,
                c.customer_name,
                c.customer_email,
                c.customer_contact,
                e.event_desc,
                e.event_date,
                e.event_location
            FROM eventify_vendor_bookings vb
            LEFT JOIN eventify_customer vc ON vb.vendor_id = vc.customer_id
            LEFT JOIN eventify_vendor v ON vc.customer_name = v.vendor_desc
            LEFT JOIN eventify_customer c ON vb.customer_id = c.customer_id
            LEFT JOIN eventify_products e ON vb.event_id = e.event_id
            WHERE vb.booking_id = ?"
        );

        if (!$stmt) {
            error_log('getBooking prepare failed: ' . $this->db->error);
            return null;
        }

        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Update booking status
     */
    public function updateBookingStatus($booking_id, $status)
    {
        if (!$this->db) $this->db_connect();

        $allowed_statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($status, $allowed_statuses)) {
            error_log('Invalid booking status: ' . $status);
            return false;
        }

        $stmt = $this->db->prepare(
            "UPDATE eventify_vendor_bookings SET booking_status = ? WHERE booking_id = ?"
        );

        if (!$stmt) {
            error_log('updateBookingStatus prepare failed: ' . $this->db->error);
            return false;
        }

        $stmt->bind_param("si", $status, $booking_id);
        return $stmt->execute();
    }

    /**
     * Update booking details
     */
    public function updateBooking($booking_id, $service_date = null, $notes = null, $price = null)
    {
        if (!$this->db) $this->db_connect();

        $stmt = $this->db->prepare(
            "UPDATE eventify_vendor_bookings
            SET service_date = ?, notes = ?, price = ?
            WHERE booking_id = ?"
        );

        if (!$stmt) {
            error_log('updateBooking prepare failed: ' . $this->db->error);
            return false;
        }

        $stmt->bind_param("ssdi", $service_date, $notes, $price, $booking_id);
        return $stmt->execute();
    }

    /**
     * Delete a booking
     */
    public function deleteBooking($booking_id)
    {
        if (!$this->db) $this->db_connect();

        $stmt = $this->db->prepare("DELETE FROM eventify_vendor_bookings WHERE booking_id = ?");

        if (!$stmt) {
            error_log('deleteBooking prepare failed: ' . $this->db->error);
            return false;
        }

        $stmt->bind_param("i", $booking_id);
        return $stmt->execute();
    }

    /**
     * Create a vendor-to-event request (vendor wants to provide services at an event)
     */
    public function createVendorEventRequest($vendor_id, $event_creator_id, $event_id, $notes = null)
    {
        if (!$this->db) $this->db_connect();

        $stmt = $this->db->prepare(
            "INSERT INTO eventify_vendor_bookings
            (booking_type, vendor_id, customer_id, event_id, notes, booking_status, approved_by_vendor, approved_by_event_manager)
            VALUES ('vendor_to_event', ?, ?, ?, ?, 'pending', 1, 0)"
        );

        if (!$stmt) {
            error_log('createVendorEventRequest prepare failed: ' . $this->db->error);
            return false;
        }

        $stmt->bind_param("iiis", $vendor_id, $event_creator_id, $event_id, $notes);
        $result = $stmt->execute();

        if (!$result) {
            error_log('createVendorEventRequest execute failed: ' . $stmt->error);
            return false;
        }

        return $this->db->insert_id;
    }

    /**
     * Check if vendor already requested to provide services at an event
     */
    public function checkVendorEventRequest($vendor_id, $event_id)
    {
        if (!$this->db) $this->db_connect();

        $stmt = $this->db->prepare(
            "SELECT booking_id FROM eventify_vendor_bookings
            WHERE vendor_id = ? AND event_id = ? AND booking_type = 'vendor_to_event'
            LIMIT 1"
        );

        if (!$stmt) {
            error_log('checkVendorEventRequest prepare failed: ' . $this->db->error);
            return false;
        }

        $stmt->bind_param("ii", $vendor_id, $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Get all vendor-to-event requests made by a vendor
     */
    public function getVendorEventRequests($vendor_id)
    {
        if (!$this->db) $this->db_connect();

        $stmt = $this->db->prepare(
            "SELECT
                vb.*,
                e.event_desc,
                e.event_date,
                e.event_location,
                e.added_by as event_creator_id,
                c.customer_name as event_creator_name
            FROM eventify_vendor_bookings vb
            LEFT JOIN eventify_products e ON vb.event_id = e.event_id
            LEFT JOIN eventify_customer c ON e.added_by = c.customer_id
            WHERE vb.vendor_id = ? AND vb.booking_type = 'vendor_to_event'
            ORDER BY vb.booking_date DESC"
        );

        if (!$stmt) {
            error_log('getVendorEventRequests prepare failed: ' . $this->db->error);
            return [];
        }

        $stmt->bind_param("i", $vendor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get all vendor requests for events created by a specific event manager
     */
    public function getEventManagerVendorRequests($event_creator_id)
    {
        if (!$this->db) $this->db_connect();

        $stmt = $this->db->prepare(
            "SELECT
                vb.*,
                vc.customer_name as vendor_name,
                vc.customer_email as vendor_email,
                vc.customer_contact as vendor_contact,
                v.vendor_type,
                e.event_desc,
                e.event_date,
                e.event_location
            FROM eventify_vendor_bookings vb
            LEFT JOIN eventify_customer vc ON vb.vendor_id = vc.customer_id
            LEFT JOIN eventify_vendor v ON vc.customer_name = v.vendor_desc
            LEFT JOIN eventify_products e ON vb.event_id = e.event_id
            WHERE vb.customer_id = ? AND vb.booking_type = 'vendor_to_event'
            ORDER BY vb.booking_date DESC"
        );

        if (!$stmt) {
            error_log('getEventManagerVendorRequests prepare failed: ' . $this->db->error);
            return [];
        }

        $stmt->bind_param("i", $event_creator_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Approve or reject vendor request by event manager
     */
    public function updateEventManagerApproval($booking_id, $approved)
    {
        if (!$this->db) $this->db_connect();

        $approval_value = $approved ? 1 : 0;
        $status = $approved ? 'confirmed' : 'cancelled';
        $now = date('Y-m-d H:i:s');

        $stmt = $this->db->prepare(
            "UPDATE eventify_vendor_bookings
            SET approved_by_event_manager = ?,
                event_manager_approved_date = ?,
                booking_status = ?
            WHERE booking_id = ?"
        );

        if (!$stmt) {
            error_log('updateEventManagerApproval prepare failed: ' . $this->db->error);
            return false;
        }

        $stmt->bind_param("issi", $approval_value, $now, $status, $booking_id);
        return $stmt->execute();
    }
}
?>
