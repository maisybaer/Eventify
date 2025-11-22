<?php
require_once '../classes/cart_class.php';

class CartController
{
    private $cart;

    public function __construct()
    {
        $this->cart = new Cart();
    }

    /**
     * Add a product to the cart
     */
    public function add_to_cart_ctr($product_id, $customer_id, $qty)
    {
        return $this->cart->addToCart($product_id, $customer_id, $qty);
    }

    /**
     * Update the quantity of an existing cart item
     */
    public function update_cart_item_ctr($product_id, $customer_id, $qty)
    {
        return $this->cart->updateCart($product_id, $customer_id, $qty);
    }

    /**
     * Remove a product from the cart
     */
    public function remove_from_cart_ctr($product_id, $customer_id)
    {
        return $this->cart->removeFromCart($product_id, $customer_id);
    }

    /**
     * Get all items in the user's cart
     */
    public function get_user_cart_ctr($customer_id)
    {
        return $this->cart->getCart($customer_id);
    }

    /**
     * Empty the user's cart
     */
    public function empty_cart_ctr($customer_id)
    {
        return $this->cart->emptyCart($customer_id);
    }

    /**
     * Check if product exists in cart
     */
    public function check_existing_product_ctr($product_id, $customer_id)
    {
        return $this->cart->existingProductCheck($product_id, $customer_id);
    }
}
?>