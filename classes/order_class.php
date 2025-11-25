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
        $query = "INSERT INTO eventify_orders (customer_id, invoice_no, order_date, order_status)
              VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("isss", $customer_id, $invoice_no, $order_date, $order_status);
        $stmt->execute();

        // Return the generated order ID
        return $this->db->insert_id;
    }

    /**
     * Add order details (event_id, quantity) to eventify_orderdetails table.
     */
    public function addOrderDetails($order_id, $product_id, $qty)
    {
        $query = "INSERT INTO eventify_orderdetails (order_id, event_id, qty)
              VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iii", $order_id, $product_id, $qty);
        return $stmt->execute();
    }

    /**
     * Record a simulated payment in the payments table.
     */
    public function recordPayment($amt, $customer_id, $order_id, $currency, $payment_date)
    {
        $query = "INSERT INTO eventify_payment (amt, customer_id, order_id, currency, payment_date)
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
        $query = "SELECT * FROM eventify_orders WHERE customer_id = ? ORDER BY order_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    //function to delete order
    public function deleteOrder($order_id)
    {
        $stmt = $this->db->prepare("DELETE FROM eventify_orders WHERE order_id=?");
        $stmt->bind_param("i",$order_id);
        return $stmt->execute();
    }


       public function create_order($customer_id, $invoice_no, $order_date, $order_status) {
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
            
                $sql = "INSERT INTO eventify_orders (customer_id, invoice_no, order_date, order_status) 
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
            error_log("Exception in create_order: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Add order details (products) to an order
     * @param int $order_id - Order ID
     * @param int $product_id - Product ID
     * @param int $qty - Quantity ordered
     * @return bool - Returns true if successful, false if failed
     */
    public function add_order_details($order_id, $product_id, $qty) {
        try {
            $order_id = (int)$order_id;
            $product_id = (int)$product_id;
            $qty = (int)$qty;
            
                $sql = "INSERT INTO eventify_orderdetails (order_id, event_id, qty) 
                    VALUES ($order_id, $product_id, $qty)";
            
            error_log("Adding order detail - Order: $order_id, Product: $product_id, Qty: $qty");
            
            return $this->db_write_query($sql);
            
        } catch (Exception $e) {
            error_log("Error adding order details: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Record a payment for an order
     * @param float $amount - Payment amount
     * @param int $customer_id - Customer ID
     * @param int $order_id - Order ID
     * @param string $currency - Currency code (e.g., 'GHS', 'USD')
     * @param string $payment_date - Payment date (YYYY-MM-DD)
     * @param string $payment_method - Payment method (e.g., 'paystack', 'cash', 'bank_transfer')
     * @param string $transaction_ref - Transaction reference/ID from payment gateway
     * @param string $authorization_code - Authorization code from payment gateway
     * @param string $payment_channel - Payment channel (e.g., 'card', 'mobile_money')
     * @return int|false - Returns payment_id if successful, false if failed
     */
    public function record_payment($amount, $customer_id, $order_id, $currency, $payment_date, $payment_method = 'direct', $transaction_ref = null, $authorization_code = null, $payment_channel = null) {
        error_log("=== RECORD_PAYMENT METHOD CALLED ===");
        try {
            $amount = (float)$amount;
            $customer_id = (int)$customer_id;
            $order_id = (int)$order_id;
            $currency = mysqli_real_escape_string($this->db_conn(), $currency);
            $payment_date = mysqli_real_escape_string($this->db_conn(), $payment_date);
            $payment_method = mysqli_real_escape_string($this->db_conn(), $payment_method);
            $transaction_ref = $transaction_ref ? mysqli_real_escape_string($this->db_conn(), $transaction_ref) : null;
            $authorization_code = $authorization_code ? mysqli_real_escape_string($this->db_conn(), $authorization_code) : null;
            $payment_channel = $payment_channel ? mysqli_real_escape_string($this->db_conn(), $payment_channel) : null;
            
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
            
            $sql = "INSERT INTO eventify_payment $columns VALUES $values";
            
            error_log("Executing SQL: $sql");
            
            if ($this->db_write_query($sql)) {
                $payment_id = $this->last_insert_id();
                error_log("Payment recorded successfully with ID: $payment_id");
                return $payment_id;
            } else {
                $error = mysqli_error($this->db_conn());
                error_log("Payment recording failed. MySQL error: " . $error);
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Error recording payment: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all orders for a user
     * @param int $customer_id - Customer ID
     * @return array|false - Returns array of orders or false if failed
     */
    public function get_user_orders($customer_id) {
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
                    FROM eventify_orders o
                    LEFT JOIN eventify_payment p ON o.order_id = p.order_id
                    LEFT JOIN eventify_orderdetails od ON o.order_id = od.order_id
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
     * @param int $order_id - Order ID
     * @param int $customer_id - Customer ID (for security check)
     * @return array|false - Returns order details or false if not found
     */
    public function get_order_details($order_id, $customer_id) {
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
                    FROM eventify_orders o
                    LEFT JOIN eventify_payment p ON o.order_id = p.order_id
                    WHERE o.order_id = $order_id AND o.customer_id = $customer_id";
            
            return $this->db_fetch_one($sql);
            
        } catch (Exception $e) {
            error_log("Error getting order details: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all products in a specific order
     * @param int $order_id - Order ID
     * @return array|false - Returns array of products in the order or false if failed
     */
    public function get_order_products($order_id) {
        try {
            $order_id = (int)$order_id;
            
            $sql = "SELECT 
                        od.event_id as product_id,
                        od.qty,
                        p.event_desc as product_title,
                        p.event_price as product_price,
                        p.flyer as product_image,
                        (od.qty * p.event_price) as subtotal
                    FROM eventify_orderdetails od
                    INNER JOIN eventify_products p ON od.event_id = p.event_id
                    WHERE od.order_id = $order_id";
            
            return $this->db_fetch_all($sql);
            
        } catch (Exception $e) {
            error_log("Error getting order products: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update order status
     * @param int $order_id - Order ID
     * @param string $order_status - New order status
     * @return bool - Returns true if successful, false if failed
     */
    public function update_order_status($order_id, $order_status) {
        try {
            $order_id = (int)$order_id;
            $order_status = mysqli_real_escape_string($this->db_conn(), $order_status);
            
            $sql = "UPDATE eventify_orders SET order_status = '$order_status' WHERE order_id = $order_id";
            
            error_log("Updating order status: $order_id to $order_status");
            
            return $this->db_write_query($sql);
            
        } catch (Exception $e) {
            error_log("Error updating order status: " . $e->getMessage());
            return false;
        }
    }
}
?>
