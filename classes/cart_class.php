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
        $check_query = "SELECT qty FROM eventify_cart WHERE event_id = ? AND customer_id = ?";
        $stmt = $this->db->prepare($check_query);
        $stmt->bind_param("ii", $event_id, $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Product already in cart – update quantity
            $row = $result->fetch_assoc();
            $new_qty = $row['qty'] + $qty;

            $update_query = "UPDATE eventify_cart SET qty = ? WHERE event_id = ? AND customer_id = ?";
            $stmt = $this->db->prepare($update_query);
            $stmt->bind_param("iii", $new_qty, $event_id, $customer_id);
            return $stmt->execute();
        } else {
            // Add new product to cart
            $insert_query = "INSERT INTO eventify_cart (event_id, customer_id, qty) VALUES (?, ?, ?)";
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
        $query = "UPDATE eventify_cart SET qty = ? WHERE event_id = ? AND customer_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iii", $qty, $event_id, $customer_id);
        return $stmt->execute();
    }

    /**
     * Remove a product from the cart.
     */
    public function removeFromCart($event_id, $customer_id)
    {
        $query = "DELETE FROM eventify_cart WHERE event_id = ? AND customer_id = ?";
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

        // helper to check columns on the `eventify_products` table (we aliased eventify_products as `e`)
        $hasProductPrice = $this->hasColumn('eventify_products', 'product_price');
        $hasEventPrice = $this->hasColumn('eventify_products', 'event_price');
        $hasProductTitle = $this->hasColumn('eventify_products', 'product_title');
        $hasEventName = $this->hasColumn('eventify_products', 'event_name');
        $hasProductImage = $this->hasColumn('eventify_products', 'product_image');
        $hasFlyer = $this->hasColumn('eventify_products', 'flyer');
        $hasEventDate = $this->hasColumn('eventify_products', 'event_date');
        $hasEventStart = $this->hasColumn('eventify_products', 'event_start');
        $hasEventEnd = $this->hasColumn('eventify_products', 'event_end');
        $hasEventLocation = $this->hasColumn('eventify_products', 'event_location');
        $hasEventDesc = $this->hasColumn('eventify_products', 'event_desc');
        $hasProductId = $this->hasColumn('eventify_products', 'product_id');
        $hasEventId = $this->hasColumn('eventify_products', 'event_id');

        // pick price column if available
        if ($hasProductPrice) {
            $priceExpr = 'e.product_price';
        } elseif ($hasEventPrice) {
            $priceExpr = 'e.event_price';
        } else {
            $priceExpr = '0';
        }

        // pick title column
        if ($hasProductTitle) {
            $titleExpr = 'e.product_title';
        } elseif ($hasEventName) {
            $titleExpr = 'e.event_name';
        } else {
            $titleExpr = "''";
        }

        // pick image column
        if ($hasProductImage) {
            $imageExpr = 'e.product_image';
        } elseif ($hasFlyer) {
            $imageExpr = 'e.flyer';
        } else {
            $imageExpr = "''";
        }

        // pick id column to determine is_event
        if ($hasProductId) {
            $idExpr = 'e.product_id';
        } elseif ($hasEventId) {
            $idExpr = 'e.event_id';
        } else {
            $idExpr = 'NULL';
        }

        $selectParts = [
            'c.cart_id',
            'c.event_id',
            'c.qty',
            'c.customer_id',
            "{$titleExpr} AS product_title",
            "{$priceExpr} AS product_price",
            "{$imageExpr} AS product_image",
            "CASE WHEN {$idExpr} IS NOT NULL THEN 1 ELSE 0 END AS is_event"
        ];

        // add event/product fields if available (use aliases or NULL)
        $selectParts[] = $hasEventDate ? 'e.event_date' : 'NULL AS event_date';
        $selectParts[] = $hasEventStart ? 'e.event_start' : 'NULL AS event_start';
        $selectParts[] = $hasEventEnd ? 'e.event_end' : 'NULL AS event_end';
        $selectParts[] = $hasEventLocation ? 'e.event_location' : 'NULL AS event_location';
        $selectParts[] = $hasEventDesc ? 'e.event_desc' : 'NULL AS event_desc';

        $select = implode(", ", $selectParts);

          $query = "SELECT {$select} FROM eventify_cart c
              LEFT JOIN eventify_products e ON c.event_id = e.event_id
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
        $query = "DELETE FROM eventify_cart WHERE customer_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $customer_id);
        return $stmt->execute();
    }

    /**
     * Check if an event already exists in the cart for a specific customer.
     */
    public function existingEventCheck($event_id, $customer_id)
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