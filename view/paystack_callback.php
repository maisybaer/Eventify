<?php
/**
 * Paystack Payment Callback Handler
 * This page is called after Paystack payment process
 * User is redirected here by Paystack after payment
 */

require_once '../settings/core.php';
require_once '../settings/paystack_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}

// Get reference from URL
$reference = isset($_GET['reference']) ? trim($_GET['reference']) : null;

if (!$reference) {
    // Payment cancelled or reference missing
    header('Location: checkout.php?error=cancelled');
    exit();
}

error_log("=== PAYSTACK CALLBACK PAGE ===");
error_log("Reference from URL: $reference");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment - Eventify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 550px;
            width: 100%;
            background: white;
            padding: 3rem 2.5rem;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .spinner {
            display: inline-block;
            width: 60px;
            height: 60px;
            border: 5px solid #f3f4f6;
            border-top: 5px solid #f97316;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-bottom: 2rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }

        p {
            color: #6b7280;
            font-size: 1.05rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .reference {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            padding: 1.25rem;
            border-radius: 12px;
            margin: 2rem 0;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            color: #92400e;
            border: 2px solid #fbbf24;
            box-shadow: 0 4px 12px rgba(251, 191, 36, 0.15);
        }

        .reference strong {
            color: #78350f;
            font-weight: 700;
        }

        .error {
            color: #991b1b;
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border: 2px solid #f87171;
            padding: 1.25rem;
            border-radius: 12px;
            margin: 1.5rem 0;
            display: none;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
        }

        .success {
            color: #065f46;
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border: 2px solid #34d399;
            padding: 1.25rem;
            border-radius: 12px;
            margin: 1.5rem 0;
            display: none;
            box-shadow: 0 4px 12px rgba(52, 211, 153, 0.15);
        }

        .icon-wrapper {
            margin-bottom: 1.5rem;
        }

        .check-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border-radius: 50%;
            color: #059669;
            font-size: 2rem;
        }

        .error-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-radius: 50%;
            color: #dc2626;
            font-size: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner" id="spinner"></div>
        
        <h1>Verifying Payment</h1>
        <p>Please wait while we verify your payment with Paystack...</p>
        
        <div class="reference">
            Payment Reference: <strong><?php echo htmlspecialchars($reference); ?></strong>
        </div>
        
        <div class="error" id="errorBox">
            <strong>Error:</strong> <span id="errorMessage"></span>
        </div>
        
        <div class="success" id="successBox">
            <strong>Success!</strong> Your payment has been verified. Redirecting...
        </div>
    </div>

    <script>
        /**
         * Verify payment with backend
         */
        async function verifyPayment() {
            const reference = '<?php echo htmlspecialchars($reference); ?>';
            
            try {
                const payload = {
                    reference: reference,
                    cart_items: null, // Will be fetched from backend
                    total_amount: null // Will be calculated from cart
                };

                console.log('Sending verification request with payload:', payload);

                const response = await fetch('../actions/paystack_verify_payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);

                // Get the raw response text first
                const responseText = await response.text();
                console.log('Raw response:', responseText);

                // Try to parse as JSON
                let data;
                try {
                    data = JSON.parse(responseText);
                    console.log('Parsed verification response:', data);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    console.error('Response was not valid JSON:', responseText);
                    throw new Error('Invalid response from server. Expected JSON but got: ' + responseText.substring(0, 200));
                }

                // Hide spinner
                document.getElementById('spinner').style.display = 'none';

                if (data.status === 'success' && data.verified) {
                    // Payment verified successfully
                    // Show success icon
                    const iconWrapper = document.createElement('div');
                    iconWrapper.className = 'icon-wrapper';
                    iconWrapper.innerHTML = '<div class="check-icon"><i class="fas fa-check"></i></div>';
                    document.querySelector('.container').insertBefore(iconWrapper, document.querySelector('h1'));

                    // Update heading
                    document.querySelector('h1').textContent = 'Payment Successful!';
                    document.querySelector('p').textContent = 'Your payment has been verified. Redirecting...';

                    document.getElementById('successBox').style.display = 'block';

                    // Redirect to success page in the current window
                    setTimeout(() => {
                        window.location.replace(`payment_success.php?reference=${encodeURIComponent(reference)}&invoice=${encodeURIComponent(data.invoice_no)}`);
                    }, 1000);

                } else {
                    // Payment verification failed
                    const errorMsg = data.message || 'Payment verification failed';
                    console.error('Verification failed:', errorMsg, data);
                    showError(errorMsg);

                    // Redirect after 5 seconds
                    setTimeout(() => {
                        window.location.href = 'checkout.php?error=verification_failed';
                    }, 5000);
                }

            } catch (error) {
                console.error('Verification error details:', error);
                console.error('Error type:', error.name);
                console.error('Error message:', error.message);
                console.error('Error stack:', error.stack);

                // Show detailed error message
                let errorMessage = 'Connection error: ' + error.message;
                showError(errorMessage);

                // Check if cart is empty (which would indicate payment succeeded despite error)
                checkCartStatus().then(cartEmpty => {
                    if (cartEmpty) {
                        console.log('Cart is empty - payment likely succeeded. Redirecting to orders page.');
                        setTimeout(() => {
                            window.location.href = 'orders.php';
                        }, 3000);
                    } else {
                        // Redirect to checkout with error after 5 seconds
                        setTimeout(() => {
                            window.location.href = 'checkout.php?error=connection_error';
                        }, 5000);
                    }
                });
            }
        }
        
        /**
         * Check if cart is empty (indicates successful payment)
         */
        async function checkCartStatus() {
            try {
                const response = await fetch('../actions/get_cart_action.php', {
                    method: 'POST'
                });
                const data = await response.json();
                console.log('Cart status check:', data);

                // Cart is empty if items array is empty or doesn't exist
                return !data.items || data.items.length === 0;
            } catch (error) {
                console.error('Error checking cart status:', error);
                return false; // Assume cart is not empty if we can't check
            }
        }

        /**
         * Show error message
         */
        function showError(message) {
            // Hide spinner
            document.getElementById('spinner').style.display = 'none';

            // Show error icon
            const iconWrapper = document.createElement('div');
            iconWrapper.className = 'icon-wrapper';
            iconWrapper.innerHTML = '<div class="error-icon"><i class="fas fa-times"></i></div>';
            document.querySelector('.container').insertBefore(iconWrapper, document.querySelector('h1'));

            // Update heading
            document.querySelector('h1').textContent = 'Payment Failed';

            // Show error message
            document.getElementById('errorBox').style.display = 'block';
            document.getElementById('errorMessage').textContent = message;
        }
        
        // Start verification when page loads
        window.addEventListener('load', verifyPayment);
    </script>
</body>
</html>
