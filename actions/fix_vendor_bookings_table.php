<?php
// Script to fix vendor bookings table - adds missing columns
require_once '../settings/db_class.php';

$db = new db_connection();
$db->db_connect();

echo "<!DOCTYPE html><html><head><title>Fix Vendor Bookings Table</title></head><body>";
echo "<h2>Fixing Vendor Bookings Table</h2>";
echo "<pre>";

// Check if table exists
$result = $db->db_query("SHOW TABLES LIKE 'eventify_vendor_bookings'");
if ($db->db_count() == 0) {
    echo "✗ ERROR: Table 'eventify_vendor_bookings' does not exist!\n";
    echo "Please run create_vendor_bookings_table.php first.\n";
    echo "</pre></body></html>";
    exit;
}

// Get current columns
$db->db_query("DESCRIBE eventify_vendor_bookings");
$existing_columns = [];
while ($row = mysqli_fetch_assoc($db->results)) {
    $existing_columns[] = $row['Field'];
}

echo "Current columns: " . implode(", ", $existing_columns) . "\n\n";

// Add booking_type column if missing
if (!in_array('booking_type', $existing_columns)) {
    echo "Adding 'booking_type' column...\n";
    $sql = "ALTER TABLE eventify_vendor_bookings
            ADD COLUMN booking_type VARCHAR(50) NOT NULL DEFAULT 'customer_to_vendor'
            COMMENT 'customer_to_vendor or vendor_to_event'
            AFTER booking_id";

    if ($db->db_write_query($sql)) {
        echo "✓ Successfully added 'booking_type' column!\n";
    } else {
        echo "✗ Failed to add 'booking_type' column. Error: " . mysqli_error($db->db) . "\n";
    }
} else {
    echo "✓ 'booking_type' column already exists\n";
}

// Add approved_by_vendor column if missing
if (!in_array('approved_by_vendor', $existing_columns)) {
    echo "Adding 'approved_by_vendor' column...\n";
    $sql = "ALTER TABLE eventify_vendor_bookings
            ADD COLUMN approved_by_vendor TINYINT(1) DEFAULT 1
            COMMENT '1 if vendor approved (for vendor_to_event requests)'
            AFTER booking_status";

    if ($db->db_write_query($sql)) {
        echo "✓ Successfully added 'approved_by_vendor' column!\n";
    } else {
        echo "✗ Failed to add 'approved_by_vendor' column. Error: " . mysqli_error($db->db) . "\n";
    }
} else {
    echo "✓ 'approved_by_vendor' column already exists\n";
}

// Add approved_by_event_manager column if missing
if (!in_array('approved_by_event_manager', $existing_columns)) {
    echo "Adding 'approved_by_event_manager' column...\n";
    $sql = "ALTER TABLE eventify_vendor_bookings
            ADD COLUMN approved_by_event_manager TINYINT(1) DEFAULT 0
            COMMENT '1 if event manager approved (for vendor_to_event requests)'
            AFTER approved_by_vendor";

    if ($db->db_write_query($sql)) {
        echo "✓ Successfully added 'approved_by_event_manager' column!\n";
    } else {
        echo "✗ Failed to add 'approved_by_event_manager' column. Error: " . mysqli_error($db->db) . "\n";
    }
} else {
    echo "✓ 'approved_by_event_manager' column already exists\n";
}

// Add event_manager_approved_date column if missing
if (!in_array('event_manager_approved_date', $existing_columns)) {
    echo "Adding 'event_manager_approved_date' column...\n";
    $sql = "ALTER TABLE eventify_vendor_bookings
            ADD COLUMN event_manager_approved_date DATETIME NULL
            COMMENT 'When event manager approved/rejected the request'
            AFTER approved_by_event_manager";

    if ($db->db_write_query($sql)) {
        echo "✓ Successfully added 'event_manager_approved_date' column!\n";
    } else {
        echo "✗ Failed to add 'event_manager_approved_date' column. Error: " . mysqli_error($db->db) . "\n";
    }
} else {
    echo "✓ 'event_manager_approved_date' column already exists\n";
}

echo "\n<h3>Updated Vendor Bookings Table Structure:</h3>";
$db->db_query("DESCRIBE eventify_vendor_bookings");
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr style='background:#f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = mysqli_fetch_assoc($db->results)) {
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

echo "</pre>";
echo "<br><p style='color:green;'><strong>✓ Table structure has been fixed! All required columns are now present.</strong></p>";
echo "<p><a href='../view/vendor_requests.php'>Go to Vendor Requests</a> | <a href='../view/my_bookings.php'>Go to My Bookings</a></p>";
echo "</body></html>";
?>
