<?php
/**
 * Migration Script: Rename cart table to eventify_cart
 * This ensures consistency with other table names
 */

require_once '../settings/db_class.php';

$db = new db_connection();
$conn = $db->db_conn();

echo "<h2>Renaming Cart Table</h2>";

if (!$conn) {
    die("✗ Database connection failed!<br>");
}

// Check if old 'cart' table exists
$result = mysqli_query($conn, "SHOW TABLES LIKE 'cart'");
if (mysqli_num_rows($result) > 0) {
    echo "✓ Found 'cart' table. Proceeding with rename...<br>";

    // Check if eventify_cart already exists
    $check = mysqli_query($conn, "SHOW TABLES LIKE 'eventify_cart'");
    if (mysqli_num_rows($check) > 0) {
        echo "⚠ Table 'eventify_cart' already exists!<br>";
        echo "Please manually resolve this conflict.<br>";
    } else {
        // Rename the table
        $rename_sql = "RENAME TABLE cart TO eventify_cart";

        if (mysqli_query($conn, $rename_sql)) {
            echo "✓ Successfully renamed 'cart' to 'eventify_cart'!<br>";
        } else {
            echo "✗ Failed to rename table. Error: " . mysqli_error($conn) . "<br>";
        }
    }
} else {
    echo "⚠ Table 'cart' not found.<br>";

    // Check if eventify_cart exists
    $check = mysqli_query($conn, "SHOW TABLES LIKE 'eventify_cart'");
    if (mysqli_num_rows($check) > 0) {
        echo "✓ Table 'eventify_cart' already exists. No action needed.<br>";
    } else {
        echo "✗ Neither 'cart' nor 'eventify_cart' table exists!<br>";
        echo "Please run create_cart_table.php first.<br>";
    }
}

echo "<br><h3>Verifying eventify_cart Table Structure:</h3>";
$result = mysqli_query($conn, "SHOW TABLES LIKE 'eventify_cart'");
if (mysqli_num_rows($result) > 0) {
    $desc = mysqli_query($conn, "DESCRIBE eventify_cart");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = mysqli_fetch_assoc($desc)) {
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
    echo "<p>eventify_cart table does not exist.</p>";
}

echo "<br><p><strong>Done! You can now <a href='../view/single_event.php?event_id=1'>try adding items to cart</a> or <a href='../view/cart.php'>view your cart</a>.</strong></p>";
?>
