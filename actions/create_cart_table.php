<?php
// Script to create the cart table
require_once '../settings/db_class.php';

$db = new db_connection();
$db->db_connect();

echo "<h2>Creating Cart Table</h2>";

// Check if table already exists
$result = $db->db_query("SHOW TABLES LIKE 'cart'");
if ($db->db_count() > 0) {
    echo "✓ Table 'cart' already exists!<br>";
} else {
    echo "Creating 'cart' table...<br>";

    // Create the cart table
    $sql = "CREATE TABLE cart (
        cart_id INT(11) NOT NULL AUTO_INCREMENT,
        event_id INT(11) NOT NULL,
        customer_id INT(11) NOT NULL,
        qty INT(11) NOT NULL DEFAULT 1,
        PRIMARY KEY (cart_id),
        KEY event_id (event_id),
        KEY customer_id (customer_id),
        CONSTRAINT cart_event_fk FOREIGN KEY (event_id) REFERENCES eventify_products (event_id) ON DELETE CASCADE,
        CONSTRAINT cart_customer_fk FOREIGN KEY (customer_id) REFERENCES eventify_customer (customer_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if ($db->db_write_query($sql)) {
        echo "✓ Successfully created 'cart' table!<br>";
    } else {
        echo "✗ Failed to create 'cart' table. Error: " . mysqli_error($db->db) . "<br>";
    }
}

echo "<br><h3>Current Cart Table Structure:</h3>";
$result = $db->db_query("SHOW TABLES LIKE 'cart'");
if ($db->db_count() > 0) {
    $db->db_query("DESCRIBE cart");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = mysqli_fetch_assoc($db->results)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Cart table does not exist.</p>";
}

echo "<br><p><strong>Done! You can now <a href='../view/single_event.php?event_id=1'>try adding items to cart</a> or <a href='../view/cart.php'>view your cart</a>.</strong></p>";
?>
