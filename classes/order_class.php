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
        error_log("=== CREATE_ORDER METHOD CALLED ===");
        try {
            // Get connection first
            $conn = $this->db_conn();
            
            if (!$conn) {
                error_log("Failed to get database connection");
                return false;
            }
            
            $customer_id = (int)$customer_id;
            $invoice_no = mysqli_real_escape_string($conn, $invoice_no);
            $order_date = mysqli_real_escape_string($conn, $order_date);
            $order_status = mysqli_real_escape_string($conn, $order_status);
            
            $sql = "INSERT INTO orders (customer_id, invoice_no, order_date, order_status) 
                    VALUES ($customer_id, '$invoice_no', '$order_date', '$order_status')";
            
            error_log("Executing SQL: $sql");
            
            // Execute directly on the connection
            $result = mysqli_query($conn, $sql);
            
            if ($result) {
                // Get insert ID immediately from the same connection
                $order_id = mysqli_insert_id($conn);
                error_log("Order created successfully with ID: $order_id");
                
                if ($order_id > 0) {
                    return $order_id;
                } else {
                    error_log("Insert succeeded but ID is 0");
                    return false;
                }
            } else {
                $error = mysqli_error($conn);
                error_log("Order creation failed. MySQL error: " . $error);
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Exception in createOrder: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add order details for both products and events
     */
    public function addOrderDetails($order_id, $event_id, $qty, $price = null)
    {
        try {
            $order_id = (int)$order_id;
            $event_id = (int)$event_id;
            $qty = (int)$qty;
            
            // If price not provided, get it from product/event tables
            if ($price === null) {
                $conn = $this->db_conn();
                
                // Try to get price from products table first
                $sql = "SELECT product_price as price FROM products WHERE product_id = $event_id";
                $result = mysqli_query($conn, $sql);
                
                if ($result && mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $price = $row['price'];
                } else {
                    // Try events table (assuming events might have a price column in future)
                    $sql = "SELECT COALESCE(event_price, 0) as price FROM events WHERE event_id = $event_id";
                    $result = mysqli_query($conn, $sql);
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        $price = $row['price'];
                    } else {
                        $price = 0; // Default price for events without price
                    }
                }
            }
            
                $sql = "INSERT INTO orderdetails (order_id, event_id, qty) 
                    VALUES ($order_id, $event_id, $qty)";
            
                error_log("Adding order detail - Order: $order_id, Event: $event_id, Qty: $qty");
            
            return $this->db_write_query($sql);
            
        } catch (Exception $e) {
            error_log("Error adding order details: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Record payment with support for Paystack integration
     */
    public function recordPayment($amount, $customer_id, $order_id, $currency = 'GHS', $payment_date = null, $payment_method = 'paystack', $transaction_ref = null, $authorization_code = null, $payment_channel = null) {
        error_log("=== RECORD_PAYMENT METHOD CALLED ===");
        try {
            $amount = (float)$amount;
            $customer_id = (int)$customer_id;
            $order_id = (int)$order_id;
            $payment_date = $payment_date ?: date('Y-m-d H:i:s');
            
            $conn = $this->db_conn();
            $currency = mysqli_real_escape_string($conn, $currency);
            $payment_date = mysqli_real_escape_string($conn, $payment_date);
            $payment_method = mysqli_real_escape_string($conn, $payment_method);
            $transaction_ref = $transaction_ref ? mysqli_real_escape_string($conn, $transaction_ref) : null;
            $authorization_code = $authorization_code ? mysqli_real_escape_string($conn, $authorization_code) : null;
            $payment_channel = $payment_channel ? mysqli_real_escape_string($conn, $payment_channel) : null;
            
            // Build SQL with optional fields
            $columns = "(amt, customer_id, order_id, currency, payment_date, payment_method";
            $values = "($amount, $customer_id, $order_id, '$currency', '$payment_date', '$payment_method'";
            
            if ($transaction_ref) {
                $columns .= ", transaction_ref";
                $values .= ", '$transaction_ref'";
            }
            if ($authorization_code) {
                $columns .= ", authorization_code";
                $values .= ", '$authorization_code'";
            }
            if ($payment_channel) {
                $columns .= ", payment_channel";
                $values .= ", '$payment_channel'";
            }
            
            $columns .= ")";
            $values .= ")";
            
            $sql = "INSERT INTO payment $columns VALUES $values";
            
            error_log("Executing SQL: $sql");
            
            if ($this->db_write_query($sql)) {
                $payment_id = mysqli_insert_id($conn);
                error_log("Payment recorded successfully with ID: $payment_id");
                return $payment_id;
            } else {
                $error = mysqli_error($conn);
                error_log("Payment recording failed. MySQL error: " . $error);
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Error recording payment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all orders for a user with payment information
     */
    public function getUserOrders($customer_id)
    {
        try {
            $customer_id = (int)$customer_id;
            
            $sql = "SELECT 
                        o.order_id,
                        o.invoice_no,
                        o.order_date,
                        o.order_status,
                        p.amt as total_amount,
                        p.currency,
                        COUNT(od.event_id) as item_count
                    FROM orders o
                    LEFT JOIN payment p ON o.order_id = p.order_id
                    LEFT JOIN orderdetails od ON o.order_id = od.order_id
                    WHERE o.customer_id = $customer_id
                    GROUP BY o.order_id
                    ORDER BY o.order_date DESC, o.order_id DESC";
            
            return $this->db_fetch_all($sql);
            
        } catch (Exception $e) {
            error_log("Error getting user orders: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get details of a specific order
     */
    public function getOrderDetails($order_id, $customer_id) {
        try {
            $order_id = (int)$order_id;
            $customer_id = (int)$customer_id;
            
            $sql = "SELECT 
                        o.order_id,
                        o.invoice_no,
                        o.order_date,
                        o.order_status,
                        o.customer_id,
                        p.amt as total_amount,
                        p.currency,
                        p.payment_date
                    FROM orders o
                    LEFT JOIN payment p ON o.order_id = p.order_id
                    WHERE o.order_id = $order_id AND o.customer_id = $customer_id";
            
            return $this->db_fetch_one($sql);
            
        } catch (Exception $e) {
            error_log("Error getting order details: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all products/events in a specific order
     */
    public function getOrderProducts($order_id) {
        try {
            $order_id = (int)$order_id;
            
            $sql = "SELECT 
                        od.event_id,
                        od.qty,
                        e.event_name as product_title,
                        COALESCE(e.event_price, 0) as product_price,
                        e.flyer as product_image,
                        1 AS is_event,
                        (od.qty * COALESCE(e.event_price, 0)) as subtotal
                    FROM orderdetails od
                    LEFT JOIN events e ON od.event_id = e.event_id
                    WHERE od.order_id = $order_id";
            
            return $this->db_fetch_all($sql);
            
        } catch (Exception $e) {
            error_log("Error getting order products: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update order status
     */
    public function updateOrderStatus($order_id, $order_status) {
        try {
            $order_id = (int)$order_id;
            $order_status = mysqli_real_escape_string($this->db_conn(), $order_status);
            
            $sql = "UPDATE orders SET order_status = '$order_status' WHERE order_id = $order_id";
            
            error_log("Updating order status: $order_id to $order_status");
            
            return $this->db_write_query($sql);
            
        } catch (Exception $e) {
            error_log("Error updating order status: " . $e->getMessage());
            return false;
        }
    }
}
?>
