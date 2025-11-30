<?php
// Configure session before starting
if (session_status() === PHP_SESSION_NONE) {
    // Set session cookie parameters for better persistence
    ini_set('session.cookie_lifetime', 7200); // 2 hours
    ini_set('session.gc_maxlifetime', 7200);
    ini_set('session.cookie_path', '/');
    ini_set('session.cookie_httponly', 1);

    // Use cookies only (not URL parameters)
    ini_set('session.use_only_cookies', 1);

    session_start();
}

if (empty($_SESSION['user_id'])) {
    // Determine the correct path to login based on the current script location
    $script_path = $_SERVER['SCRIPT_NAME'];

    // If we're in a subdirectory (like /view/), use ../login/login.php
    if (strpos($script_path, '/view/') !== false ||
        strpos($script_path, '/admin/') !== false ||
        strpos($script_path, '/vendor/') !== false) {
        header('Location: ../login/login.php');
    } else {
        // If we're in the root directory, use login/login.php
        header('Location: login/login.php');
    }
    exit;
}else{
    $user_id=getUserID();
    $user_name=getUserName($user_id);

}

//for header redirection
//ob_start();

//funtion to check for login
function checkLogin($email, $password) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login/login.php");
        exit;
    }
}


//function to get user ID
function getUserID() {
    return $_SESSION['user_id'] ?? null;  
}

//function to get username
function getUserName($user_id) {
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