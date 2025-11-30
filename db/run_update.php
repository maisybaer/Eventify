<?php
/**
 * Database Update Runner
 * Run this file ONCE to update your database structure
 * Access via: http://your-server/path/to/Eventify/db/run_update.php
 */

require_once '../settings/db_cred.php';

// Security check - only run if logged in as admin or on localhost
session_start();
$is_localhost = (
    $_SERVER['SERVER_NAME'] === 'localhost' ||
    $_SERVER['SERVER_NAME'] === '127.0.0.1' ||
    strpos($_SERVER['HTTP_HOST'], 'localhost') !== false
);

if (!$is_localhost && (!isset($_SESSION['role']) || $_SESSION['role'] != 1)) {
    die('Access denied. This script can only be run by administrators or on localhost.');
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Update - Eventify</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #f97316; margin-bottom: 20px; }
        .success { background: #d1fae5; color: #065f46; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #059669; }
        .error { background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #dc2626; }
        .info { background: #dbeafe; color: #1e40af; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #3b82f6; }
        pre { background: #f3f4f6; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .btn { display: inline-block; padding: 10px 20px; background: #f97316; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .btn:hover { background: #ea580c; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Eventify Database Update</h1>
";

// Connect to database
$conn = mysqli_connect(
    $is_localhost ? 'localhost' : $_SERVER['SERVER_NAME'],
    USERNAME,
    PASSWD,
    DATABASE
);

if (!$conn) {
    echo "<div class='error'><strong>Connection Failed:</strong> " . mysqli_connect_error() . "</div>";
    die("</div></body></html>");
}

echo "<div class='info'><strong>Connected to database:</strong> " . DATABASE . "</div>";

// Read SQL file
$sql_file = __DIR__ . '/update_database.sql';
if (!file_exists($sql_file)) {
    echo "<div class='error'><strong>Error:</strong> update_database.sql not found!</div>";
    die("</div></body></html>");
}

$sql_content = file_get_contents($sql_file);

// Split into individual queries
$queries = array_filter(
    array_map('trim', explode(';', $sql_content)),
    function($query) {
        // Remove comments and empty lines
        $query = preg_replace('/^--.*$/m', '', $query);
        $query = trim($query);
        return !empty($query);
    }
);

echo "<h2>Executing Updates...</h2>";

$success_count = 0;
$error_count = 0;

foreach ($queries as $index => $query) {
    if (empty($query)) continue;

    // Show what we're doing
    $preview = substr($query, 0, 100) . (strlen($query) > 100 ? '...' : '');
    echo "<div class='info'><strong>Query " . ($index + 1) . ":</strong><br><code>" . htmlspecialchars($preview) . "</code></div>";

    if (mysqli_query($conn, $query)) {
        echo "<div class='success'>✅ Successfully executed!</div>";
        $success_count++;
    } else {
        $error = mysqli_error($conn);
        // Check if error is just "duplicate key" or "already exists" - these are OK
        if (stripos($error, 'duplicate') !== false ||
            stripos($error, 'already exists') !== false ||
            stripos($error, 'Duplicate key name') !== false) {
            echo "<div class='info'>ℹ️ Already exists (skipping): " . htmlspecialchars($error) . "</div>";
            $success_count++;
        } else {
            echo "<div class='error'><strong>❌ Error:</strong> " . htmlspecialchars($error) . "</div>";
            $error_count++;
        }
    }
}

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<div class='success'><strong>✅ Successful:</strong> $success_count queries</div>";
if ($error_count > 0) {
    echo "<div class='error'><strong>❌ Failed:</strong> $error_count queries</div>";
}

// Verify the changes
echo "<h2>Verification</h2>";

// Check invoice_no column type
$result = mysqli_query($conn, "SHOW COLUMNS FROM eventify_orders LIKE 'invoice_no'");
if ($result && $row = mysqli_fetch_assoc($result)) {
    $type = $row['Type'];
    if (stripos($type, 'varchar') !== false) {
        echo "<div class='success'>✅ invoice_no column is now VARCHAR</div>";
    } else {
        echo "<div class='error'>❌ invoice_no column is still $type (should be VARCHAR)</div>";
    }
}

// Check if payment_init table exists
$result = mysqli_query($conn, "SHOW TABLES LIKE 'eventify_payment_init'");
if ($result && mysqli_num_rows($result) > 0) {
    echo "<div class='success'>✅ eventify_payment_init table created</div>";
} else {
    echo "<div class='error'>❌ eventify_payment_init table not found</div>";
}

// Check transaction_ref index
$result = mysqli_query($conn, "SHOW INDEX FROM eventify_payment WHERE Key_name = 'idx_transaction_ref'");
if ($result && mysqli_num_rows($result) > 0) {
    echo "<div class='success'>✅ transaction_ref index exists</div>";
} else {
    echo "<div class='info'>ℹ️ transaction_ref index may already exist with different name</div>";
}

mysqli_close($conn);

echo "<hr>";
echo "<div class='success'>";
echo "<h3>✅ Database update complete!</h3>";
echo "<p>You can now test the payment flow. Make sure to:</p>";
echo "<ul>";
echo "<li>Clear your browser cache</li>";
echo "<li>Empty your cart and add fresh items</li>";
echo "<li>Test the complete checkout process</li>";
echo "</ul>";
echo "</div>";

echo "<a href='../view/checkout.php' class='btn'>Go to Checkout</a> ";
echo "<a href='../index.php' class='btn' style='background: #6b7280;'>Go to Home</a>";

echo "
    </div>
</body>
</html>";
?>
