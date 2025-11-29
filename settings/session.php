<?php
/**
 * Session management for public pages
 * This file starts the session and provides helper functions
 * but does NOT require authentication
 */

session_start();

//function to get user ID
function getUserID() {
    return $_SESSION['user_id'] ?? null;
}

//function to get username
function getUserName($user_id = null) {
    return $_SESSION['customer_name'] ?? null;
}

//function to get user email
function getUserEmail($user_id = null) {
    // Prefer session value when available
    if (isset($_SESSION['customer_email']) && !empty($_SESSION['customer_email'])) {
        return $_SESSION['customer_email'];
    }

    // Try to fetch from database if we have a user id
    $uid = $user_id ?? ($_SESSION['user_id'] ?? null);
    if (!$uid) {
        return null;
    }

    // Lazy-load database helper and fetch email
    require_once __DIR__ . '/db_class.php';
    $db = new db_connection();
    $uid_int = intval($uid);
    $row = $db->db_fetch_one("SELECT customer_email FROM eventify_customer WHERE customer_id = $uid_int");
    if ($row && isset($row['customer_email'])) {
        // Store in session for future calls
        $_SESSION['customer_email'] = $row['customer_email'];
        return $row['customer_email'];
    }

    return null;
}

//function to check for role (admin, customer, etc)
function getUserRole() {
    return $_SESSION['role'] ?? null;
}

?>
