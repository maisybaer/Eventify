<?php
// Script to create the vendor bookings table
require_once '../settings/db_class.php';

$db = new db_connection();
$db->db_connect();

echo "<h2>Creating Vendor Bookings Table</h2>";

// Check if table already exists
$result = $db->db_query("SHOW TABLES LIKE 'eventify_vendor_bookings'");
if ($db->db_count() > 0) {
    echo "✓ Table 'eventify_vendor_bookings' already exists!<br>";
} else {
    echo "Creating 'eventify_vendor_bookings' table...<br>";

    // Create the vendor bookings table
    $sql = "CREATE TABLE eventify_vendor_bookings (
        booking_id INT(11) NOT NULL AUTO_INCREMENT,
        vendor_id INT(11) NOT NULL COMMENT 'The vendor being booked (customer_id with role=2)',
        customer_id INT(11) NOT NULL COMMENT 'The customer making the booking',
        event_id INT(11) NOT NULL COMMENT 'The customer event for which vendor is being booked',
        booking_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        booking_status VARCHAR(50) NOT NULL DEFAULT 'pending' COMMENT 'pending, confirmed, completed, cancelled',
        service_date DATE NULL COMMENT 'Date when vendor service is needed',
        notes TEXT NULL COMMENT 'Additional booking notes or requirements',
        price DECIMAL(10,2) NULL COMMENT 'Agreed price for the service',
        PRIMARY KEY (booking_id),
        KEY vendor_id (vendor_id),
        KEY customer_id (customer_id),
        KEY event_id (event_id),
        KEY booking_status (booking_status),
        CONSTRAINT vendor_bookings_vendor_fk FOREIGN KEY (vendor_id) REFERENCES eventify_customer (customer_id) ON DELETE CASCADE,
        CONSTRAINT vendor_bookings_customer_fk FOREIGN KEY (customer_id) REFERENCES eventify_customer (customer_id) ON DELETE CASCADE,
        CONSTRAINT vendor_bookings_event_fk FOREIGN KEY (event_id) REFERENCES eventify_products (event_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if ($db->db_write_query($sql)) {
        echo "✓ Successfully created 'eventify_vendor_bookings' table!<br>";
    } else {
        echo "✗ Failed to create 'eventify_vendor_bookings' table. Error: " . mysqli_error($db->db) . "<br>";
    }
}

echo "<br><h3>Current Vendor Bookings Table Structure:</h3>";
$result = $db->db_query("SHOW TABLES LIKE 'eventify_vendor_bookings'");
if ($db->db_count() > 0) {
    $db->db_query("DESCRIBE eventify_vendor_bookings");
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
    echo "<p>Vendor bookings table does not exist.</p>";
}

echo "<br><p><strong>Done! You can now <a href='../view/browse_vendors.php'>browse vendors</a> and make bookings.</strong></p>";
?>
