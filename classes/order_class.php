<?php

require_once '../settings/db_class.php';

class Order extends db_connection
{
    public function __construct()
    {
        parent::db_connect();
    }

    /**
     * Create a new order in the orders table.
     * Returns the auto-generated order ID.
     */
    public function createOrder($customer_id, $invoice_no, $order_date, $order_status)
    {
        $query = "INSERT INTO orders (customer_id, invoice_no, order_date, order_status)
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("isss", $customer_id, $invoice_no, $order_date, $order_status);
        $stmt->execute();

        // Return the generated order ID
        return $this->db->insert_id;
    }

    /**
     * Add order details (product_id, quantity, price) to orderdetails table.
     */
    public function addOrderDetails($order_id, $product_id, $qty, $price)
    {
        $query = "INSERT INTO orderdetails (order_id, product_id, qty, price)
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iiid", $order_id, $product_id, $qty, $price);
        return $stmt->execute();
    }

    /**
     * Record a simulated payment in the payments table.
     */
    public function recordPayment($amt, $customer_id, $order_id, $currency, $payment_date)
    {
        $query = "INSERT INTO payments (amt, customer_id, order_id, currency, payment_date)
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("diiss", $amt, $customer_id, $order_id, $currency, $payment_date);
        return $stmt->execute();
    }

    /**
     * Retrieve past orders for a specific user.
     */
    public function getUserOrders($customer_id)
    {
        $query = "SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
