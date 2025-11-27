<?php
// Script to add the missing event_date column to the database
require_once '../settings/db_class.php';

$db = new db_connection();
$db->db_connect();

echo "<h2>Adding Missing event_date Column</h2>";

// Check if column already exists
$result = $db->db_query("SHOW COLUMNS FROM eventify_products LIKE 'event_date'");
if ($db->db_count() > 0) {
    echo "✓ Column 'event_date' already exists!<br>";
} else {
    echo "Adding 'event_date' column...<br>";

    // Add the column after event_location
    $sql = "ALTER TABLE eventify_products ADD COLUMN event_date DATE NULL AFTER event_location";

    if ($db->db_write_query($sql)) {
        echo "✓ Successfully added 'event_date' column!<br>";
    } else {
        echo "✗ Failed to add 'event_date' column. Error: " . mysqli_error($db->db) . "<br>";
    }
}

echo "<br><h3>Current Table Structure:</h3>";
$db->db_query("DESCRIBE eventify_products");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th></tr>";
while ($row = mysqli_fetch_assoc($db->results)) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<br><p><strong>Done! You can now <a href='test_fetch_events.php'>run the test script again</a> or <a href='../admin/event.php'>go to your events page</a>.</strong></p>";
?>
