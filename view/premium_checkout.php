<?php
require_once '../settings/core.php';
require_once '../controllers/subscription_controller.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}

$customer_id = getUserID();
$customer_name = getUserName($customer_id);
$customer_email = getUserEmail();
$user_role = getUserRole();

// Only admins and event managers (role=1) can subscribe to premium analytics
if ($user_role != 1) {
    header('Location: ../index.php');
    exit();
}

// Check if already has active subscription
$active_subscription = get_active_subscription_ctr($customer_id, 'analytics_premium');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Analytics - Eventify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=<?php echo time(); ?>">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .premium-container {
            max-width: 900px;
            margin: 100px auto 50px;
            padding: 24px;
        }

        .premium-card {
            background: white;
            border-radius: 24px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .premium-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .premium-badge {
            display: inline-block;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .premium-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .premium-subtitle {
            color: #6b7280;
            font-size: 1.1rem;
        }

        .pricing-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 20px;
            text-align: center;
            margin: 2rem 0;
        }

        .price {
            font-size: 3.5rem;
            font-weight: 700;
            margin: 1rem 0;
        }

        .price-currency {
            font-size: 1.5rem;
            vertical-align: super;
        }

        .price-period {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .features-list {
            list-style: none;
            padding: 0;
            margin: 2rem 0;
        }

        .features-list li {
            padding: 1rem 0;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .features-list li:last-child {
            border-bottom: none;
        }

        .feature-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #059669;
            flex-shrink: 0;
        }

        .btn-subscribe {
            width: 100%;
            padding: 1.25rem;
            border-radius: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-subscribe:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.5);
        }

        .btn-subscribe:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .active-subscription-banner {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border: 2px solid #6ee7b7;
            color: #065f46;
            padding: 1.5rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            text-align: center;
        }
    </style>
</head>

<body>
    <header class="menu-tray">
        <a href="../index.php" class="btn btn-sm btn-outline-light"><i class="fas fa-home"></i> Home</a>
        <a href="../admin/analytics.php" class="btn btn-sm btn-outline-light"><i class="fas fa-chart-line"></i> Analytics</a>
        <a href="../login/logout.php" class="btn btn-sm btn-outline-light"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </header>

    <main>
        <div class="premium-container">
            <div class="premium-card">
                <div class="premium-header">
                    <div class="premium-badge">
                        <i class="fas fa-crown"></i> PREMIUM
                    </div>
                    <h1 class="premium-title">Premium Analytics</h1>
                    <p class="premium-subtitle">Unlock powerful insights for your events</p>
                </div>

                <?php if ($active_subscription): ?>
                    <div class="active-subscription-banner">
                        <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                        <h4><strong>You Already Have Premium Access!</strong></h4>
                        <p class="mb-0">Your subscription is active until <strong><?php echo date('F j, Y', strtotime($active_subscription['end_date'])); ?></strong></p>
                    </div>
                    <div class="text-center">
                        <a href="../admin/analytics.php" class="btn btn-subscribe">
                            <i class="fas fa-chart-line"></i> Go to Premium Analytics
                        </a>
                    </div>
                <?php else: ?>
                    <div class="pricing-box">
                        <div>Monthly Subscription</div>
                        <div class="price">
                            <span class="price-currency">₵</span>50<span class="price-period">/month</span>
                        </div>
                        <div>Billed monthly • Cancel anytime</div>
                    </div>

                    <ul class="features-list">
                        <li>
                            <div class="feature-icon"><i class="fas fa-chart-bar"></i></div>
                            <div><strong>Advanced Analytics Dashboard</strong> - Detailed insights into ticket sales and revenue</div>
                        </li>
                        <li>
                            <div class="feature-icon"><i class="fas fa-users"></i></div>
                            <div><strong>Customer Demographics</strong> - Understand your audience better</div>
                        </li>
                        <li>
                            <div class="feature-icon"><i class="fas fa-calendar-check"></i></div>
                            <div><strong>Event Performance Tracking</strong> - Monitor each event's success</div>
                        </li>
                        <li>
                            <div class="feature-icon"><i class="fas fa-download"></i></div>
                            <div><strong>Export Reports</strong> - Download data as PDF or Excel</div>
                        </li>
                        <li>
                            <div class="feature-icon"><i class="fas fa-bell"></i></div>
                            <div><strong>Real-time Notifications</strong> - Get instant alerts on sales</div>
                        </li>
                        <li>
                            <div class="feature-icon"><i class="fas fa-headset"></i></div>
                            <div><strong>Priority Support</strong> - Get help when you need it</div>
                        </li>
                    </ul>

                    <button id="subscribeBtn" class="btn-subscribe">
                        <i class="fas fa-lock"></i> Subscribe Now - ₵50/month
                    </button>

                    <p class="text-center text-muted mt-3" style="font-size: 0.875rem;">
                        Secure payment powered by Paystack
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#subscribeBtn').click(function() {
                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

                // Create subscription record
                $.ajax({
                    url: '../actions/add_subscription_action.php',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        subscription_type: 'analytics_premium',
                        amount: 50.00,
                        currency: 'GHS'
                    },
                    success: function(response) {
                        console.log('Subscription response:', response);
                        if (response.status === 'success') {
                            // Initialize Paystack payment
                            initiatePaystackPayment(response.subscription_id, response.amount);
                        } else {
                            Swal.fire('Error', response.message || 'Failed to create subscription', 'error');
                            btn.prop('disabled', false).html('<i class="fas fa-lock"></i> Subscribe Now - ₵50/month');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', xhr.responseText);
                        let errorMsg = 'Failed to create subscription. Please try again.';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMsg = response.message;
                            }
                        } catch(e) {
                            // If response is not JSON, show raw text
                            if (xhr.responseText) {
                                errorMsg = xhr.responseText.substring(0, 200);
                            }
                        }
                        Swal.fire('Error', errorMsg, 'error');
                        btn.prop('disabled', false).html('<i class="fas fa-lock"></i> Subscribe Now - ₵50/month');
                    }
                });
            });

            function initiatePaystackPayment(subscriptionId, amount) {
                $.ajax({
                    url: '../actions/paystack_init_transaction.php',
                    method: 'POST',
                    dataType: 'json',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        amount: amount,
                        email: '<?php echo $customer_email; ?>',
                        metadata: {
                            subscription_id: subscriptionId,
                            customer_name: '<?php echo $customer_name; ?>',
                            payment_type: 'subscription'
                        }
                    }),
                    success: function(response) {
                        console.log('Paystack init response:', response);
                        if (response.status && response.authorization_url) {
                            const reference = response.reference;

                            // Open Paystack popup
                            try {
                                const handler = PaystackPop.setup({
                                    key: response.public_key,
                                    email: '<?php echo $customer_email; ?>',
                                    amount: amount * 100,
                                    currency: 'GHS',
                                    ref: reference,
                                    metadata: {
                                        subscription_id: subscriptionId,
                                        customer_name: '<?php echo $customer_name; ?>'
                                    },
                                    callback: function(response) {
                                        verifyPayment(response.reference, subscriptionId);
                                    },
                                    onClose: function() {
                                        $('#subscribeBtn').prop('disabled', false).html('<i class="fas fa-lock"></i> Subscribe Now - ₵50/month');
                                    }
                                });
                                handler.openIframe();
                            } catch(e) {
                                console.error('Paystack setup error:', e);
                                Swal.fire('Error', 'Failed to open payment window: ' + e.message, 'error');
                                $('#subscribeBtn').prop('disabled', false).html('<i class="fas fa-lock"></i> Subscribe Now - ₵50/month');
                            }
                        } else {
                            console.error('Invalid response:', response);
                            Swal.fire('Error', response.message || 'Failed to initialize payment', 'error');
                            $('#subscribeBtn').prop('disabled', false).html('<i class="fas fa-lock"></i> Subscribe Now - ₵50/month');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Payment init AJAX error:', xhr.responseText);
                        let errorMsg = 'Payment initialization failed';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMsg = response.message;
                            }
                        } catch(e) {}
                        Swal.fire('Error', errorMsg, 'error');
                        $('#subscribeBtn').prop('disabled', false).html('<i class="fas fa-lock"></i> Subscribe Now - ₵50/month');
                    }
                });
            }

            function verifyPayment(reference, subscriptionId) {
                $.ajax({
                    url: '../actions/paystack_verify_subscription.php',
                    method: 'POST',
                    dataType: 'json',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        reference: reference,
                        subscription_id: subscriptionId
                    }),
                    success: function(response) {
                        console.log('Verification response:', response);
                        if (response.status === 'success' && response.verified) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Premium Activated!',
                                text: 'Your premium analytics subscription is now active!',
                                confirmButtonText: 'Go to Analytics'
                            }).then(() => {
                                window.location.href = '../admin/analytics.php';
                            });
                        } else {
                            Swal.fire('Payment Failed', response.message || 'Verification failed', 'error');
                            $('#subscribeBtn').prop('disabled', false).html('<i class="fas fa-lock"></i> Subscribe Now - ₵50/month');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Verification AJAX error:', xhr.responseText);
                        let errorMsg = 'Payment verification failed';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMsg = response.message;
                            }
                        } catch(e) {}
                        Swal.fire('Error', errorMsg, 'error');
                        $('#subscribeBtn').prop('disabled', false).html('<i class="fas fa-lock"></i> Subscribe Now - ₵50/month');
                    }
                });
            }
        });
    </script>

<?php
$footer_base = '../';
include '../includes/footer.php';
?>
</body>
</html>
