<?php
/**
 * Database Migration: Create Subscription Table
 * Run this file once to create the eventify_subscriptions table
 */

require_once '../settings/db_class.php';

$db = new db_connection();
$conn = $db->db_conn();

$sql = "CREATE TABLE IF NOT EXISTS eventify_subscriptions (
    subscription_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    subscription_type VARCHAR(50) DEFAULT 'analytics_premium',
    status ENUM('active', 'expired', 'cancelled', 'pending') DEFAULT 'pending',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'GHS',
    payment_reference VARCHAR(255),
    invoice_no VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES eventify_customer(customer_id) ON DELETE CASCADE,
    INDEX idx_customer_status (customer_id, status),
    INDEX idx_end_date (end_date),
    INDEX idx_subscription_type (subscription_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (mysqli_query($conn, $sql)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Subscription table created successfully'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error creating table: ' . mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>
