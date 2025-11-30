<?php
//Database credentials
// Settings/db_cred.php

// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_NAME', 'dbforlab');


//if (!defined("SERVER")) {
//    define("SERVER", "localhost");
//}

//if (!defined("USERNAME")) {
//    define("USERNAME", "root");
//}

//if (!defined("PASSWD")) {
//   define("PASSWD", "");
//}

//if (!defined("DATABASE")) {
//    define("DATABASE", "shoppin");
//}

// Auto-detect environment (local vs server)
$is_local = (
    $_SERVER['SERVER_NAME'] === 'localhost' ||
    $_SERVER['SERVER_NAME'] === '127.0.0.1' ||
    strpos($_SERVER['SERVER_NAME'], 'localhost') !== false
);

if (!defined("SERVER")) {
    if ($is_local) {
        // Local development
        define("SERVER", "localhost");
    } else {
        // Production server - use the actual server URL
        // This will be used for Paystack callback URL
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST']; // This will be 169.239.251.102:442
        $base_path = dirname(dirname($_SERVER['SCRIPT_NAME'])); // Gets the base path
        define("SERVER", $protocol . '://' . $host . $base_path);
    }
}

if (!defined("USERNAME")) {
    define("USERNAME", "root");
}

if (!defined("PASSWD")) {
    define("PASSWD", "");
}

if (!defined("DATABASE")) {
    define("DATABASE", "eventify");
}
?>