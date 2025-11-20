<?php
require_once '../classes/order_class.php';
require_once '../controllers/cart_controller.php';

class OrderController
{
    private $order;

    public function __construct()
    {
        $this->order = new Order();
    }

    /**
     * Create a new order
     * $params: ['customer_id', 'invoice_no', 'order_date', 'order_status']
     */
    public function create_order_ctr($params)
    {
        return $this->order->createOrder(
            $params['customer_id'],
            $params['invoice_no'],
            $params['order_date'],
            $params['order_status']
        );
    }

    /**
     * Add order details (cart items)
     * $params: ['order_id', 'product_id', 'qty', 'price']
     */
    public function add_order_details_ctr($params)
    {
        return $this->order->addOrderDetails(
            $params['order_id'],
            $params['product_id'],
            $params['qty'],
            $params['price'] ?? null
        );
    }

    /**
     * Record a payment with Paystack support
     */
    public function record_payment_ctr($amount, $customer_id, $order_id, $currency = 'GHS', $payment_date = null, $payment_method = 'paystack', $transaction_ref = null, $authorization_code = null, $payment_channel = null)
    {
        return $this->order->recordPayment(
            $amount,
            $customer_id,
            $order_id,
            $currency,
            $payment_date,
            $payment_method,
            $transaction_ref,
            $authorization_code,
            $payment_channel
        );
    }

    /**
     * Retrieve past orders for a user
     */
    public function get_user_orders_ctr($customer_id)
    {
        return $this->order->getUserOrders($customer_id);
    }

    /**
     * Get order details
     */
    public function get_order_details_ctr($order_id, $customer_id)
    {
        return $this->order->getOrderDetails($order_id, $customer_id);
    }

    /**
     * Get order products/events
     */
    public function get_order_products_ctr($order_id)
    {
        return $this->order->getOrderProducts($order_id);
    }

    /**
     * Update order status
     */
    public function update_order_status_ctr($order_id, $order_status)
    {
        return $this->order->updateOrderStatus($order_id, $order_status);
    }
}

/**
 * Standalone function to create order from cart (for Paystack integration)
 */
function create_order_from_cart_ctr($customer_id, $transaction_ref, $amount) {
    try {
        error_log("=== CREATE_ORDER_FROM_CART_CTR CALLED ===");
        error_log("Customer: $customer_id, Ref: $transaction_ref, Amount: $amount");
        
        // Get cart items
        $cart_controller = new CartController();
        $cart_items = $cart_controller->get_user_cart_ctr($customer_id);
        
        if (!$cart_items || count($cart_items) == 0) {
            error_log("Cart is empty for customer: $customer_id");
            return false;
        }
        
        // Create order
        $order_controller = new OrderController();
        
        // Generate invoice number
        $invoice_no = 'INV-' . $customer_id . '-' . time();
        $order_date = date('Y-m-d H:i:s');
        $order_status = 'Completed';
        
        $order_params = [
            'customer_id' => $customer_id,
            'invoice_no' => $invoice_no,
            'order_date' => $order_date,
            'order_status' => $order_status
        ];
        
        $order_id = $order_controller->create_order_ctr($order_params);
        
        if (!$order_id) {
            error_log("Failed to create order");
            return false;
        }
        
        error_log("Order created with ID: $order_id");
        
        // Add order details from cart
        foreach ($cart_items as $item) {
            $detail_params = [
                'order_id' => $order_id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
                'price' => $item['product_price']
            ];
            
            $order_controller->add_order_details_ctr($detail_params);
            error_log("Added order detail - Product: {$item['product_id']}, Qty: {$item['qty']}");
        }
        
        // Record payment
        $payment_id = $order_controller->record_payment_ctr(
            $amount,
            $customer_id,
            $order_id,
            'GHS',
            date('Y-m-d H:i:s'),
            'paystack',
            $transaction_ref
        );
        
        if ($payment_id) {
            error_log("Payment recorded with ID: $payment_id");
            
            // Clear cart after successful order
            $cart_controller->empty_cart_ctr($customer_id);
            error_log("Cart cleared for customer: $customer_id");
            
            return $order_id;
        } else {
            error_log("Failed to record payment");
            return false;
        }
        
    } catch (Exception $e) {
        error_log("Error in create_order_from_cart_ctr: " . $e->getMessage());
        return false;
    }
}
?>