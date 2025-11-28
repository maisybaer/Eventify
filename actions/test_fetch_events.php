<?php
// Simple test script to debug event fetching issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../settings/db_class.php';
require_once '../controllers/event_controller.php';

echo "<h2>Event Fetch Debug Test</h2>";

// Test 1: Database Connection
echo "<h3>1. Testing Database Connection...</h3>";
$db = new db_connection();
if ($db->db_connect()) {
    echo "✓ Database connected successfully<br>";
} else {
    echo "✗ Database connection failed<br>";
    exit;
}

// Test 2: Check if eventify_products table exists
echo "<h3>2. Checking if eventify_products table exists...</h3>";
$result = $db->db_query("SHOW TABLES LIKE 'eventify_products'");
if ($result && $db->db_count() > 0) {
    echo "✓ Table 'eventify_products' exists<br>";
} else {
    echo "✗ Table 'eventify_products' does not exist<br>";
}

// Test 3: Check table structure
echo "<h3>3. Checking table structure...</h3>";
$db->db_query("DESCRIBE eventify_products");
echo "<pre>";
while ($row = mysqli_fetch_assoc($db->results)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
echo "</pre>";

// Test 4: Count events in database
echo "<h3>4. Counting events...</h3>";
$db->db_query("SELECT COUNT(*) as total FROM eventify_products");
$count = mysqli_fetch_assoc($db->results);
echo "Total events in database: " . $count['total'] . "<br>";

// Test 5: Try to fetch events
echo "<h3>5. Fetching events using controller...</h3>";
$events = view_all_event_ctr();

if ($events === false) {
    echo "✗ view_all_event_ctr() returned FALSE<br>";
    echo "This means there's a query error. Check PHP error logs.<br>";
} elseif (empty($events)) {
    echo "⚠ view_all_event_ctr() returned an empty array<br>";
    echo "The query worked but no events were found.<br>";
} else {
    echo "✓ Successfully fetched " . count($events) . " events<br>";
    echo "<h4>Sample Event Data:</h4>";
    echo "<pre>";
    print_r($events[0]);
    echo "</pre>";
}

// Test 6: Check if categories table exists
echo "<h3>6. Checking categories...</h3>";
$db->db_query("SELECT COUNT(*) as total FROM eventify_categories");
$catCount = mysqli_fetch_assoc($db->results);
echo "Total categories: " . $catCount['total'] . "<br>";

if ($catCount['total'] == 0) {
    echo "⚠ WARNING: No categories found! Events need categories to display properly.<br>";
}

// Test 7: Check for orphaned events (events without valid categories)
echo "<h3>7. Checking for orphaned events...</h3>";
$db->db_query("SELECT COUNT(*) as total FROM eventify_products p
               LEFT JOIN eventify_categories c ON p.event_cat = c.cat_id
               WHERE c.cat_id IS NULL");
$orphaned = mysqli_fetch_assoc($db->results);
if ($orphaned['total'] > 0) {
    echo "⚠ WARNING: Found " . $orphaned['total'] . " events without valid categories<br>";
} else {
    echo "✓ All events have valid categories<br>";
}

echo "<h3>Test Complete!</h3>";
?>
