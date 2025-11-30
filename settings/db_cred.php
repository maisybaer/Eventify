<?php
/**
 * Database & Server Configuration
 * Auto-detects environment and sets appropriate values
 */

// Auto-detect if we're on production or local
if (!defined("SERVER")) {
    // Check if we're on localhost or production
    $is_localhost = (
        $_SERVER['SERVER_NAME'] === 'localhost' ||
        $_SERVER['SERVER_NAME'] === '127.0.0.1' ||
        strpos($_SERVER['HTTP_HOST'], 'localhost') !== false
    );

    if ($is_localhost) {
        // Local development - use localhost
        define("SERVER", "localhost");
    } else {
        // Production server - build full URL dynamically
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST']; // e.g., 169.239.251.102:442

        // Get the base path from the script name
        // Remove /settings/db_cred.php to get base path
        $script_dir = dirname(dirname($_SERVER['SCRIPT_NAME'])); // Gets /~maisy.baer/eventify/Eventify

        define("SERVER", "$protocol://$host$script_dir");
    }
}

if (!defined("USERNAME")) {
    define("USERNAME", "root");
}

if (!defined("PASSWD")) {
    define("PASSWD", "");
}

if (!defined("DATABASE")) {
    // Auto-detect database name too
    $is_localhost = (strpos(SERVER, 'localhost') !== false);
    if ($is_localhost) {
        define("DATABASE", "eventify"); // Local database
    } else {
        define("DATABASE", "ecommerce_2025A_maisy_baer"); // Production database
    }
}
?>