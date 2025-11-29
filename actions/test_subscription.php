<?php
/**
 * Test subscription functionality
 */

require_once '../settings/db_class.php';

$db = new db_connection();
$conn = $db->db_conn();

echo "<h2>Testing Subscription System</h2>";

// Test 1: Check if table exists
echo "<h3>1. Checking if eventify_subscriptions table exists...</h3>";
$result = mysqli_query($conn, "SHOW TABLES LIKE 'eventify_subscriptions'");
if (mysqli_num_rows($result) > 0) {
    echo "<p style='color: green;'>✓ Table exists</p>";

    // Show table structure
    echo "<h4>Table Structure:</h4>";
    $structure = mysqli_query($conn, "DESCRIBE eventify_subscriptions");
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($structure)) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "<td>{$row['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>✗ Table does NOT exist. Please run create_subscription_table.php first.</p>";
}

// Test 2: Try to insert a test subscription
echo "<h3>2. Testing subscription creation...</h3>";
try {
    $test_customer_id = 2; // Use your actual customer ID
    $subscription_type = 'analytics_premium';
    $amount = 50.00;
    $currency = 'GHS';

    $sql = "INSERT INTO eventify_subscriptions
            (customer_id, subscription_type, status, start_date, end_date, amount, currency)
            VALUES (?, ?, 'pending', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "<p style='color: red;'>✗ Prepare failed: " . $conn->error . "</p>";
    } else {
        $stmt->bind_param("isds", $test_customer_id, $subscription_type, $amount, $currency);

        if ($stmt->execute()) {
            $inserted_id = $conn->insert_id;
            echo "<p style='color: green;'>✓ Test subscription created with ID: $inserted_id</p>";

            // Clean up test data
            mysqli_query($conn, "DELETE FROM eventify_subscriptions WHERE subscription_id = $inserted_id");
            echo "<p style='color: blue;'>ℹ Test subscription deleted (cleanup)</p>";
        } else {
            echo "<p style='color: red;'>✗ Execute failed: " . $stmt->error . "</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Exception: " . $e->getMessage() . "</p>";
}

mysqli_close($conn);
?>
