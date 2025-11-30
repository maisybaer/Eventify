<?php
require_once '../classes/order_class.php';

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
     * $params: ['order_id', 'product_id', 'qty']
     */
    public function add_order_details_ctr($params)
    {
        return $this->order->addOrderDetails(
            $params['order_id'],
            $params['product_id'],
            $params['qty']
        );
    }

    /**
     * Record a payment
     * $params: ['amt', 'customer_id', 'order_id', 'currency', 'payment_date', 'payment_reference' (optional)]
     */
    public function record_payment_ctr($params)
    {
        $payment_reference = $params['payment_reference'] ?? null;

        return $this->order->recordPayment(
            $params['amt'],
            $params['customer_id'],
            $params['order_id'],
            $params['currency'],
            $params['payment_date'],
            $payment_reference
        );
    }

    /**
     * Retrieve past orders for a user
     */
    public function get_user_orders_ctr($customer_id)
    {
        return $this->order->getUserOrders($customer_id);
    }

    //delete order controller
function delete_order_ctr($order_id)
{
    $order = new Order();
    return $order->deleteOrder($order_id);
}

//FOR PAYSTACK

/**
 * Get details of a specific order
 * @param int $order_id - Order ID
 * @param int $customer_id - Customer ID (for security check)
 * @return array|false - Returns order details or false if not found
 */
function get_order_details_ctr($order_id, $customer_id) {
    $order = new Order();
    return $order->get_order_details($order_id, $customer_id);
}

/**
 * Get all products in a specific order
 * @param int $order_id - Order ID
 * @return array|false - Returns array of products in the order or false if failed
 */
function get_order_products_ctr($order_id) {
    $order = new Order();
    return $order->get_order_products($order_id);
}

/**
 * Update order status
 * @param int $order_id - Order ID
 * @param string $order_status - New order status
 * @return bool - Returns true if successful, false if failed
 */
function update_order_status_ctr($order_id, $order_status) {
    $order = new Order();
    return $order->update_order_status($order_id, $order_status);
}


}
