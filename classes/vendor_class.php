<?php
require_once __DIR__ . '/../settings/db_class.php';

class VendorClass extends db_connection
{
    public function __construct()
    {
        parent::db_connect();
    }

    /**
     * Fetch customer and vendor metadata for a given customer_id
     * Returns associative array with keys: customer, vendor (vendor may be null)
     */
    public function getVendorByCustomerId($customer_id)
    {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $customer_id = (int)$customer_id;
        $custSql = "SELECT * FROM eventify_customer WHERE customer_id = $customer_id LIMIT 1";
        $custRes = mysqli_query($conn, $custSql);
        if (!$custRes) return false;
        $customer = mysqli_fetch_assoc($custRes);

        $vendor = null;
        if ($customer) {
            // Try to find vendor row by vendor_desc matching customer_name
            $name = mysqli_real_escape_string($conn, $customer['customer_name']);
            $vSql = "SELECT * FROM eventify_vendor WHERE vendor_desc = '$name' LIMIT 1";
            $vRes = mysqli_query($conn, $vSql);
            if ($vRes && mysqli_num_rows($vRes) > 0) {
                $vendor = mysqli_fetch_assoc($vRes);
            }
        }

        return [
            'customer' => $customer,
            'vendor' => $vendor
        ];
    }

    /**
     * Update customer and vendor metadata in a transaction.
     * $data may contain: customer_id, customer_name, customer_email, customer_contact, vendor_id (optional), vendor_type, vendor_desc
     */
    public function updateVendorAndCustomer($data)
    {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $customer_id = isset($data['customer_id']) ? (int)$data['customer_id'] : 0;
        if ($customer_id <= 0) return false;

        $cust_name = isset($data['customer_name']) ? mysqli_real_escape_string($conn, $data['customer_name']) : null;
        $cust_email = isset($data['customer_email']) ? mysqli_real_escape_string($conn, $data['customer_email']) : null;
        $cust_contact = isset($data['customer_contact']) ? mysqli_real_escape_string($conn, $data['customer_contact']) : null;

        $vendor_id = isset($data['vendor_id']) ? (int)$data['vendor_id'] : null;
        $vendor_type = isset($data['vendor_type']) ? mysqli_real_escape_string($conn, $data['vendor_type']) : null;
        $vendor_desc = isset($data['vendor_desc']) ? mysqli_real_escape_string($conn, $data['vendor_desc']) : null;

        mysqli_begin_transaction($conn);
        try {
            // Update customer fields
            $updates = [];
            if ($cust_name !== null) $updates[] = "customer_name = '$cust_name'";
            if ($cust_email !== null) $updates[] = "customer_email = '$cust_email'";
            if ($cust_contact !== null) $updates[] = "customer_contact = '$cust_contact'";

            if (!empty($updates)) {
                $sql = "UPDATE eventify_customer SET " . implode(', ', $updates) . " WHERE customer_id = $customer_id";
                if (!mysqli_query($conn, $sql)) {
                    throw new Exception('Failed to update customer: ' . mysqli_error($conn));
                }
            }

            // Handle vendor row
            if ($vendor_id) {
                // Update existing vendor by id
                $vUpdates = [];
                if ($vendor_type !== null) $vUpdates[] = "vendor_type = '$vendor_type'";
                if ($vendor_desc !== null) $vUpdates[] = "vendor_desc = '$vendor_desc'";
                if (!empty($vUpdates)) {
                    $vsql = "UPDATE eventify_vendor SET " . implode(', ', $vUpdates) . " WHERE vendor_id = $vendor_id";
                    if (!mysqli_query($conn, $vsql)) {
                        throw new Exception('Failed to update vendor: ' . mysqli_error($conn));
                    }
                }
            } elseif ($vendor_desc !== null || $vendor_type !== null) {
                // Try to find vendor by current customer name
                $searchName = mysqli_real_escape_string($conn, $cust_name ?: '');
                $vfind = "SELECT vendor_id FROM eventify_vendor WHERE vendor_desc = '$searchName' LIMIT 1";
                $vres = mysqli_query($conn, $vfind);
                if ($vres && mysqli_num_rows($vres) > 0) {
                    $row = mysqli_fetch_assoc($vres);
                    $vid = (int)$row['vendor_id'];
                    $vUpdates = [];
                    if ($vendor_type !== null) $vUpdates[] = "vendor_type = '$vendor_type'";
                    if ($vendor_desc !== null) $vUpdates[] = "vendor_desc = '$vendor_desc'";
                    if (!empty($vUpdates)) {
                        $vsql = "UPDATE eventify_vendor SET " . implode(', ', $vUpdates) . " WHERE vendor_id = $vid";
                        if (!mysqli_query($conn, $vsql)) {
                            throw new Exception('Failed to update vendor (by find): ' . mysqli_error($conn));
                        }
                    }
                } else {
                    // Insert new vendor row
                    $desc = $vendor_desc !== null ? $vendor_desc : ($cust_name ?: '');
                    $type = $vendor_type !== null ? $vendor_type : 'default';
                    $ins = "INSERT INTO eventify_vendor (vendor_desc, vendor_type) VALUES ('" . mysqli_real_escape_string($conn, $desc) . "', '" . mysqli_real_escape_string($conn, $type) . "')";
                    if (!mysqli_query($conn, $ins)) {
                        throw new Exception('Failed to insert vendor: ' . mysqli_error($conn));
                    }
                }
            }

            mysqli_commit($conn);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            error_log('Vendor update error: ' . $e->getMessage());
            return false;
        }
    }
}

?>
