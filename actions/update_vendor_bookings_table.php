<?php
// Script to update the vendor bookings table with approval fields
require_once '../settings/db_class.php';

$db = new db_connection();
$db->db_connect();

echo "<h2>Updating Vendor Bookings Table</h2>";

// Check if table exists
$result = $db->db_query("SHOW TABLES LIKE 'eventify_vendor_bookings'");
if ($db->db_count() == 0) {
    echo "✗ Table 'eventify_vendor_bookings' does not exist. Please run create_vendor_bookings_table.php first!<br>";
    exit;
}

echo "✓ Table 'eventify_vendor_bookings' exists!<br><br>";

// Check and add approved_by_vendor column
echo "Checking for 'approved_by_vendor' column...<br>";
$result = $db->db_query("SHOW COLUMNS FROM eventify_vendor_bookings LIKE 'approved_by_vendor'");
if ($db->db_count() == 0) {
    echo "Adding 'approved_by_vendor' column...<br>";
    $sql = "ALTER TABLE eventify_vendor_bookings
            ADD COLUMN approved_by_vendor TINYINT(1) DEFAULT 0
            COMMENT '0=pending vendor approval, 1=vendor approved'
            AFTER booking_status";
    if ($db->db_write_query($sql)) {
        echo "✓ Successfully added 'approved_by_vendor' column!<br>";
    } else {
        echo "✗ Failed to add 'approved_by_vendor' column: " . mysqli_error($db->db) . "<br>";
    }
} else {
    echo "✓ Column 'approved_by_vendor' already exists!<br>";
}

// Check and add approved_by_event_manager column
echo "Checking for 'approved_by_event_manager' column...<br>";
$result = $db->db_query("SHOW COLUMNS FROM eventify_vendor_bookings LIKE 'approved_by_event_manager'");
if ($db->db_count() == 0) {
    echo "Adding 'approved_by_event_manager' column...<br>";
    $sql = "ALTER TABLE eventify_vendor_bookings
            ADD COLUMN approved_by_event_manager TINYINT(1) DEFAULT 0
            COMMENT '0=pending event manager approval, 1=event manager approved'
            AFTER approved_by_vendor";
    if ($db->db_write_query($sql)) {
        echo "✓ Successfully added 'approved_by_event_manager' column!<br>";
    } else {
        echo "✗ Failed to add 'approved_by_event_manager' column: " . mysqli_error($db->db) . "<br>";
    }
} else {
    echo "✓ Column 'approved_by_event_manager' already exists!<br>";
}

// Check and add booking_type column (to distinguish customer-initiated vs vendor-initiated bookings)
echo "Checking for 'booking_type' column...<br>";
$result = $db->db_query("SHOW COLUMNS FROM eventify_vendor_bookings LIKE 'booking_type'");
if ($db->db_count() == 0) {
    echo "Adding 'booking_type' column...<br>";
    $sql = "ALTER TABLE eventify_vendor_bookings
            ADD COLUMN booking_type VARCHAR(20) DEFAULT 'customer_to_vendor'
            COMMENT 'customer_to_vendor or vendor_to_event'
            AFTER booking_id";
    if ($db->db_write_query($sql)) {
        echo "✓ Successfully added 'booking_type' column!<br>";
    } else {
        echo "✗ Failed to add 'booking_type' column: " . mysqli_error($db->db) . "<br>";
    }
} else {
    echo "✓ Column 'booking_type' already exists!<br>";
}

// Check and add vendor_approved_date column
echo "Checking for 'vendor_approved_date' column...<br>";
$result = $db->db_query("SHOW COLUMNS FROM eventify_vendor_bookings LIKE 'vendor_approved_date'");
if ($db->db_count() == 0) {
    echo "Adding 'vendor_approved_date' column...<br>";
    $sql = "ALTER TABLE eventify_vendor_bookings
            ADD COLUMN vendor_approved_date DATETIME NULL
            COMMENT 'When vendor approved the booking'
            AFTER approved_by_vendor";
    if ($db->db_write_query($sql)) {
        echo "✓ Successfully added 'vendor_approved_date' column!<br>";
    } else {
        echo "✗ Failed to add 'vendor_approved_date' column: " . mysqli_error($db->db) . "<br>";
    }
} else {
    echo "✓ Column 'vendor_approved_date' already exists!<br>";
}

// Check and add event_manager_approved_date column
echo "Checking for 'event_manager_approved_date' column...<br>";
$result = $db->db_query("SHOW COLUMNS FROM eventify_vendor_bookings LIKE 'event_manager_approved_date'");
if ($db->db_count() == 0) {
    echo "Adding 'event_manager_approved_date' column...<br>";
    $sql = "ALTER TABLE eventify_vendor_bookings
            ADD COLUMN event_manager_approved_date DATETIME NULL
            COMMENT 'When event manager approved the booking'
            AFTER approved_by_event_manager";
    if ($db->db_write_query($sql)) {
        echo "✓ Successfully added 'event_manager_approved_date' column!<br>";
    } else {
        echo "✗ Failed to add 'event_manager_approved_date' column: " . mysqli_error($db->db) . "<br>";
    }
} else {
    echo "✓ Column 'event_manager_approved_date' already exists!<br>";
}

echo "<br><h3>Updated Table Structure:</h3>";
$db->db_query("DESCRIBE eventify_vendor_bookings");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = mysqli_fetch_assoc($db->results)) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
    echo "<td>" . ($row['Extra'] ?? '') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<br><p><strong>Done! The vendor bookings table has been updated with approval fields.</strong></p>";
?>
