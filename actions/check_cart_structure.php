<?php
/**
 * Diagnostic Script: Check Cart Table Structure
 */

require_once '../settings/db_class.php';

$db = new db_connection();
$conn = $db->db_conn();

echo "<h2>Cart Table Diagnostic</h2>";

if (!$conn) {
    die("✗ Database connection failed!<br>");
}

// Check for eventify_cart table
echo "<h3>Checking eventify_cart table:</h3>";
$result = mysqli_query($conn, "SHOW TABLES LIKE 'eventify_cart'");
if (mysqli_num_rows($result) > 0) {
    echo "✓ Table 'eventify_cart' exists<br><br>";

    echo "<h4>Table Structure:</h4>";
    $desc = mysqli_query($conn, "DESCRIBE eventify_cart");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($desc)) {
        echo "<tr>";
        echo "<td><strong>" . $row['Field'] . "</strong></td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . ($row['Extra'] ?? '') . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Count records
    $count = mysqli_query($conn, "SELECT COUNT(*) as total FROM eventify_cart");
    $count_row = mysqli_fetch_assoc($count);
    echo "<br><p>Total records in cart: <strong>" . $count_row['total'] . "</strong></p>";

    // Sample data if exists
    if ($count_row['total'] > 0) {
        echo "<h4>Sample Cart Data (first 5 records):</h4>";
        $sample = mysqli_query($conn, "SELECT * FROM eventify_cart LIMIT 5");
        echo "<table border='1' cellpadding='5'>";
        $first_row = true;
        while ($row = mysqli_fetch_assoc($sample)) {
            if ($first_row) {
                echo "<tr>";
                foreach (array_keys($row) as $column) {
                    echo "<th>$column</th>";
                }
                echo "</tr>";
                $first_row = false;
            }
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "✗ Table 'eventify_cart' does NOT exist<br>";
}

// Also check for old 'cart' table
echo "<br><h3>Checking for old 'cart' table:</h3>";
$result = mysqli_query($conn, "SHOW TABLES LIKE 'cart'");
if (mysqli_num_rows($result) > 0) {
    echo "⚠ Old 'cart' table still exists!<br>";

    echo "<h4>Table Structure:</h4>";
    $desc = mysqli_query($conn, "DESCRIBE cart");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($desc)) {
        echo "<tr>";
        echo "<td><strong>" . $row['Field'] . "</strong></td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . ($row['Extra'] ?? '') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "✓ Old 'cart' table does not exist (good)<br>";
}

echo "<br><h3>Testing a Cart Query:</h3>";
try {
    $test_query = "SELECT * FROM eventify_cart WHERE event_id = 1 AND customer_id = 1";
    echo "Query: <code>$test_query</code><br>";
    $test_result = mysqli_query($conn, $test_query);
    if ($test_result) {
        echo "✓ Query executed successfully<br>";
    } else {
        echo "✗ Query failed: " . mysqli_error($conn) . "<br>";
    }
} catch (Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "<br>";
}

echo "<br><p><strong>Analysis complete!</strong></p>";
?>
