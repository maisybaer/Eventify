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
     * $params: ['order_id', 'product_id', 'qty', 'price']
     */
    public function add_order_details_ctr($params)
    {
        return $this->order->addOrderDetails(
            $params['order_id'],
            $params['product_id'],
            $params['qty'],
            $params['price']
        );
    }

    /**
     * Record a payment
     * $params: ['amt', 'customer_id', 'order_id', 'currency', 'payment_date']
     */
    public function record_payment_ctr($params)
    {
        return $this->order->recordPayment(
            $params['amt'],
            $params['customer_id'],
            $params['order_id'],
            $params['currency'],
            $params['payment_date']
        );
    }

    /**
     * Retrieve past orders for a user
     */
    public function get_user_orders_ctr($customer_id)
    {
        return $this->order->getUserOrders($customer_id);
    }
}
?>