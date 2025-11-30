<?php
/**
 * Configuration Check - Debug Script
 * Use this to verify your Paystack callback URL is configured correctly
 */

require_once 'db_cred.php';
require_once 'paystack_config.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration Check - Eventify</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .config-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #f97316;
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #f97316;
            padding-bottom: 10px;
        }
        .item {
            margin: 10px 0;
            padding: 10px;
            background: #f9f9f9;
            border-left: 4px solid #f97316;
        }
        .label {
            font-weight: bold;
            color: #666;
        }
        .value {
            color: #333;
            font-family: monospace;
            margin-top: 5px;
        }
        .success {
            color: #059669;
            font-weight: bold;
        }
        .warning {
            color: #f97316;
            font-weight: bold;
        }
        .error {
            color: #dc2626;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>🔧 Eventify Configuration Check</h1>

    <div class="config-box">
        <h2>Server Information</h2>
        <div class="item">
            <div class="label">Server Name:</div>
            <div class="value"><?php echo $_SERVER['SERVER_NAME']; ?></div>
        </div>
        <div class="item">
            <div class="label">Server HTTP Host:</div>
            <div class="value"><?php echo $_SERVER['HTTP_HOST']; ?></div>
        </div>
        <div class="item">
            <div class="label">Script Name:</div>
            <div class="value"><?php echo $_SERVER['SCRIPT_NAME']; ?></div>
        </div>
        <div class="item">
            <div class="label">Environment Detected:</div>
            <div class="value <?php echo (strpos($_SERVER['SERVER_NAME'], 'localhost') !== false) ? 'warning' : 'success'; ?>">
                <?php echo (strpos($_SERVER['SERVER_NAME'], 'localhost') !== false) ? 'LOCAL DEVELOPMENT' : 'PRODUCTION SERVER'; ?>
            </div>
        </div>
    </div>

    <div class="config-box">
        <h2>Database Configuration</h2>
        <div class="item">
            <div class="label">SERVER Constant:</div>
            <div class="value"><?php echo SERVER; ?></div>
        </div>
        <div class="item">
            <div class="label">Database Host:</div>
            <div class="value"><?php echo defined('USERNAME') ? 'Configured ✓' : 'Not configured ✗'; ?></div>
        </div>
        <div class="item">
            <div class="label">Database Name:</div>
            <div class="value"><?php echo DATABASE; ?></div>
        </div>
    </div>

    <div class="config-box">
        <h2>Paystack Configuration</h2>
        <div class="item">
            <div class="label">APP_BASE_URL:</div>
            <div class="value"><?php echo APP_BASE_URL; ?></div>
        </div>
        <div class="item">
            <div class="label">Paystack Callback URL:</div>
            <div class="value success"><?php echo PAYSTACK_CALLBACK_URL; ?></div>
        </div>
        <div class="item">
            <div class="label">Environment:</div>
            <div class="value"><?php echo APP_ENVIRONMENT; ?></div>
        </div>
        <div class="item">
            <div class="label">Public Key:</div>
            <div class="value"><?php echo substr(PAYSTACK_PUBLIC_KEY, 0, 20) . '...'; ?></div>
        </div>
    </div>

    <div class="config-box">
        <h2>✅ Verification Status</h2>
        <?php
        $is_local = (strpos($_SERVER['SERVER_NAME'], 'localhost') !== false);
        $callback_has_localhost = (strpos(PAYSTACK_CALLBACK_URL, 'localhost') !== false);

        if ($is_local && $callback_has_localhost) {
            echo '<div class="item warning">⚠️ You are on LOCAL development. Paystack will redirect to localhost.</div>';
        } elseif (!$is_local && !$callback_has_localhost) {
            echo '<div class="item success">✓ You are on PRODUCTION server. Paystack will redirect to: ' . PAYSTACK_CALLBACK_URL . '</div>';
        } elseif (!$is_local && $callback_has_localhost) {
            echo '<div class="item error">✗ ERROR: You are on PRODUCTION but callback URL points to localhost!</div>';
            echo '<div class="item error">This will cause payment verification to fail. Update your SERVER constant.</div>';
        } else {
            echo '<div class="item warning">⚠️ Configuration mismatch detected.</div>';
        }
        ?>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="../view/checkout.php" style="padding: 10px 20px; background: #f97316; color: white; text-decoration: none; border-radius: 5px;">
            Back to Checkout
        </a>
    </div>
</body>
</html>
