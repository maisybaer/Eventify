<?php

require_once '../settings/db_class.php';

class Cart extends db_connection
{

    public function __construct()
    {
        parent::db_connect();
    }

    /**
     * Return the last mysqli error string for debugging
     */
    public function getLastError()
    {
        if (!$this->db) $this->db_connect();
        return $this->db ? $this->db->error : '';
    }

    /**
     * Add an event to cart or update quantity if it exists
     */
    public function addToCart($event_id, $customer_id, $qty)
    {
        // Check if the event already exists in the cart for this customer
        $check_query = "SELECT qty FROM cart WHERE event_id = ? AND customer_id = ?";
        $stmt = $this->db->prepare($check_query);
        $stmt->bind_param("ii", $event_id, $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Product already in cart – update quantity
            $row = $result->fetch_assoc();
            $new_qty = $row['qty'] + $qty;

            $update_query = "UPDATE cart SET qty = ? WHERE event_id = ? AND customer_id = ?";
            $stmt = $this->db->prepare($update_query);
            $stmt->bind_param("iii", $new_qty, $event_id, $customer_id);
            return $stmt->execute();
        } else {
            // Add new product to cart
            $insert_query = "INSERT INTO cart (event_id, customer_id, qty) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($insert_query);
            $stmt->bind_param("iii", $event_id, $customer_id, $qty);
            return $stmt->execute();
        }
    }

    /**
     * Update the quantity of a product in the cart.
     */
    public function updateCart($event_id, $customer_id, $qty)
    {
        $query = "UPDATE cart SET qty = ? WHERE event_id = ? AND customer_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iii", $qty, $event_id, $customer_id);
        return $stmt->execute();
    }

    /**
     * Remove a product from the cart.
     */
    public function removeFromCart($event_id, $customer_id)
    {
        $query = "DELETE FROM cart WHERE event_id = ? AND customer_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $event_id, $customer_id);
        return $stmt->execute();
    }

    /**
     * Retrieve all cart items for a specific customer (joins with products for display).
     */
    public function getCart($customer_id)
    {
        // Build query dynamically to avoid referencing non-existent event columns (e.g. event_price)
        if (!$this->db) $this->db_connect();

        // helper to check columns
        $hasEventPrice = $this->hasColumn('events', 'event_price');
        $hasEventDate = $this->hasColumn('events', 'event_date');
        $hasEventStart = $this->hasColumn('events', 'event_start');
        $hasEventEnd = $this->hasColumn('events', 'event_end');
        $hasEventLocation = $this->hasColumn('events', 'event_location');
        $hasEventDesc = $this->hasColumn('events', 'event_desc');

        $eventPriceExpr = $hasEventPrice ? 'e.event_price' : '0';

        $selectParts = [
            'c.cart_id',
            'c.event_id',
            'c.qty',
            'c.customer_id',
            'e.event_name AS product_title',
            "{$eventPriceExpr} AS product_price",
            'e.flyer AS product_image',
            '1 AS is_event'
        ];

        // add event fields if available (use aliases or NULL)
        $selectParts[] = $hasEventDate ? 'e.event_date' : 'NULL AS event_date';
        $selectParts[] = $hasEventStart ? 'e.event_start' : 'NULL AS event_start';
        $selectParts[] = $hasEventEnd ? 'e.event_end' : 'NULL AS event_end';
        $selectParts[] = $hasEventLocation ? 'e.event_location' : 'NULL AS event_location';
        $selectParts[] = $hasEventDesc ? 'e.event_desc' : 'NULL AS event_desc';

        $select = implode(", ", $selectParts);

        $query = "SELECT {$select} FROM cart c
              LEFT JOIN events e ON c.event_id = e.event_id
              WHERE c.customer_id = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) return [];
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Check whether a column exists on a table (safe helper)
    private function hasColumn($table, $column)
    {
        if (!$this->db) $this->db_connect();
        $tbl = $this->db->real_escape_string($table);
        $col = $this->db->real_escape_string($column);
        $res = mysqli_query($this->db, "SHOW COLUMNS FROM `{$tbl}` LIKE '{$col}'");
        if ($res === false) return false;
        return mysqli_num_rows($res) > 0;
    }


    /**
     * Empty the cart completely for a specific customer.
     */
    public function emptyCart($customer_id)
    {
        $query = "DELETE FROM cart WHERE customer_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $customer_id);
        return $stmt->execute();
    }

    /**
     * Check if an event already exists in the cart for a specific customer.
     */
    public function existingEventCheck($event_id, $customer_id)
    {
        $query = "SELECT * FROM cart WHERE event_id = ? AND customer_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $event_id, $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
}
?>