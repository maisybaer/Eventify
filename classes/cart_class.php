<?php

require_once '../settings/db_class.php';

class Cart extends db_connection
{

    public function __construct()
    {
        parent::db_connect();
    }

    /**
     * Add a event to cart or update quantity if it exists
     */
    public function addToCart($event_id, $customer_id, $qty)
    {
        // Check if the event already exists in the cart for this customer
        $check_query = "SELECT qty FROM eventify_cart WHERE event_id = ? AND customer_id = ?";
        $stmt = $this->db->prepare($check_query);
        $stmt->bind_param("ii", $event_id, $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // event already in cart – update quantity
            $row = $result->fetch_assoc();
            $new_qty = $row['qty'] + $qty;

            $update_query = "UPDATE eventify_cart SET qty = ? WHERE event_id = ? AND customer_id = ?";
            $stmt = $this->db->prepare($update_query);
            $stmt->bind_param("iii", $new_qty, $event_id, $customer_id);
            return $stmt->execute();
        } else {
            // Add new event to cart
            $insert_query = "INSERT INTO eventify_cart (event_id, customer_id, qty) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($insert_query);
            $stmt->bind_param("iii", $event_id, $customer_id, $qty);
            return $stmt->execute();
        }
    }

    /**
     * Update the quantity of a event in the cart.
     */
    public function updateCart($event_id, $customer_id, $qty)
    {
        $query = "UPDATE eventify_cart SET qty = ? WHERE event_id = ? AND customer_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iii", $qty, $event_id, $customer_id);
        return $stmt->execute();
    }

    /**
     * Remove a event from the cart.
     */
    public function removeFromCart($event_id, $customer_id)
    {
        $query = "DELETE FROM eventify_cart WHERE event_id = ? AND customer_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $event_id, $customer_id);
        return $stmt->execute();
    }

    /**
     * Retrieve all cart items for a specific customer (joins with events for display).
     */
public function getCart($customer_id)
{
         $query = "SELECT c.cart_id, c.event_id AS product_id, c.qty, c.customer_id,
             p.event_desc AS product_title, p.event_price AS product_price, p.flyer AS product_image
        FROM eventify_cart c
        JOIN eventify_products p ON c.event_id = p.event_id
        WHERE c.customer_id = ?";
    
    $stmt = $this->db->prepare($query);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}


    /**
     * Empty the cart completely for a specific customer.
     */
    public function emptyCart($customer_id)
    {
        $query = "DELETE FROM eventify_cart WHERE customer_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $customer_id);
        return $stmt->execute();
    }

    /**
     * Check if a event already exists in the cart for a specific customer.
     */
    public function existingeventCheck($event_id, $customer_id)
    {
        $query = "SELECT * FROM eventify_cart WHERE event_id = ? AND customer_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $event_id, $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
}
?>