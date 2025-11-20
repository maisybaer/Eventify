<?php
require_once '../settings/core.php';
require_login('../login/login.php');

// Check if cart is not empty
require_once '../controllers/cart_controller.php';
$customer_id = get_user_id();
$cart_controller = new CartController();
$cart_items = $cart_controller->get_user_cart_ctr($customer_id);

if (!$cart_items || count($cart_items) == 0) {
    header('Location: cart.php');
    exit();
}

// Calculate total
$cart_total = 0;
foreach ($cart_items as $item) {
    $cart_total += $item['qty'] * $item['product_price'];
}

// Get user email for payment (we'll need to add this to session or get from customer table)
$user_email = $_SESSION['customer_email'] ?? 'user@example.com'; // Default for now
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Eventify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=1.1">
    <style>
        .checkout-section { 
            background: white; 
            padding: 30px; 
            border-radius: 16px; 
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06); 
            margin-bottom: 20px; 
        }
        .summary-total { 
            font-size: 32px; 
            font-weight: 700; 
            color: var(--brand); 
            padding: 20px 0; 
            text-align: center; 
            border-top: 2px solid #f3f4f6; 
            border-bottom: 2px solid #f3f4f6; 
            margin: 20px 0; 
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .payment-modal { 
            display: none; 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(0,0,0,0.7); 
            z-index: 1000; 
            align-items: center; 
            justify-content: center; 
            opacity: 0; 
            transition: opacity 0.3s ease; 
        }
        .modal-content { 
            background: white; 
            max-width: 500px; 
            width: 90%; 
            padding: 40px; 
            border-radius: 20px; 
            position: relative; 
            transform: scale(0.9); 
            transition: transform 0.3s ease; 
            box-shadow: 0 20px 60px rgba(0,0,0,0.3); 
        }
    </style>
</head>

<body>
    <header class="menu-tray mb-3">
        <a href="../index.php" class="btn btn-sm btn-outline-secondary">Home</a>
        <a href="cart.php" class="btn btn-sm btn-outline-secondary">Back to Cart</a>
        <a href="../login/logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>
    </header>

    <main>
        <div class="container header-container">
            <div class="page-header text-center mb-4">
                <h1>Checkout</h1>
                <p>Review your order and complete payment</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="checkout-section">
                        <h2 class="mb-4"><i class="fas fa-shopping-bag me-2"></i>Order Summary</h2>
                        
                        <div id="checkoutItemsContainer">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="summary-item">
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($item['product_image'])): ?>
                                            <img src="../<?= htmlspecialchars($item['product_image']); ?>" 
                                                 alt="<?= htmlspecialchars($item['product_title']); ?>" 
                                                 class="me-3" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                        <?php endif; ?>
                                        <div>
                                            <strong><?= htmlspecialchars($item['product_title']); ?></strong>
                                            <?php if (isset($item['is_event']) && $item['is_event']): ?>
                                                <br><small class="text-muted"><i class="fas fa-calendar-alt"></i> Event Ticket</small>
                                                <?php if (!empty($item['event_date'])): ?>
                                                    <br><small class="text-muted"><?= date('M j, Y', strtotime($item['event_date'])); ?></small>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <br><small class="text-muted">Quantity: <?= $item['qty']; ?></small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <strong>₵<?= number_format($item['product_price'] * $item['qty'], 2); ?></strong>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="summary-total">
                            Total: <span id="checkoutTotal">₵<?= number_format($cart_total, 2); ?></span>
                        </div>
                        
                        <div class="text-center">
                            <button onclick="showPaymentModal()" class="btn btn-custom btn-lg">
                                <i class="fas fa-credit-card me-2"></i>Proceed to Payment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Payment Modal -->
    <div id="paymentModal" class="payment-modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closePaymentModal()" style="position: absolute; top: 15px; right: 20px; font-size: 28px; cursor: pointer; color: #6b7280;">&times;</span>
            <h2 style="font-size: 28px; color: #1a1a1a; margin-bottom: 20px; text-align: center;">Secure Payment via Paystack</h2>
            
            <div style="text-align: center; margin: 30px 0;">
                <div style="font-size: 14px; color: #6b7280; margin-bottom: 10px;">Amount to Pay</div>
                <div id="paymentAmount" style="font-size: 36px; font-weight: 700; color: var(--brand);">₵<?= number_format($cart_total, 2); ?></div>
            </div>
            
            <div style="background: linear-gradient(135deg, #1f2937 0%, #374151 100%); color: white; padding: 20px; border-radius: 12px; margin: 20px 0; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                <div style="font-size: 12px; margin-bottom: 10px; opacity: 0.8;">SECURED PAYMENT</div>
                <div style="font-size: 18px; letter-spacing: 2px; margin-bottom: 15px;">🔒 Powered by Paystack</div>
                <div style="font-size: 12px; opacity: 0.8;">Your payment information is 100% secure and encrypted</div>
            </div>
            
            <p style="text-align: center; color: #6b7280; font-size: 13px; margin-bottom: 20px;">
                You will be redirected to Paystack's secure payment gateway
            </p>
            
            <div style="display: flex; gap: 12px; margin-top: 30px;">
                <button onclick="closePaymentModal()" class="btn btn-outline-secondary" style="flex: 1;">Cancel</button>
                <button onclick="processPaystackCheckout()" id="confirmPaymentBtn" class="btn btn-custom" style="flex: 1;">
                    <i class="fas fa-credit-card me-2"></i>Pay Now
                </button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="payment-modal">
        <div class="modal-content">
            <h2 style="font-size: 28px; color: #1a1a1a; margin-bottom: 20px; text-align: center;">🎉 Payment Successful!</h2>
            
            <div style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); padding: 20px; border-radius: 12px; margin: 20px 0; border: 2px solid #6ee7b7;">
                <div style="text-align: center; margin-bottom: 15px;">
                    <div style="font-size: 14px; color: #065f46; margin-bottom: 5px;">Order Number</div>
                    <div id="successOrderId" style="font-size: 20px; font-weight: 700; color: #047857;"></div>
                </div>
                <div style="border-top: 1px solid rgba(6, 95, 70, 0.2); padding-top: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; color: #065f46;">
                        <span>Total Paid:</span>
                        <span style="font-weight: 600;" id="successAmount"></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; color: #065f46;">
                        <span>Date:</span>
                        <span id="successDate"></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; color: #065f46;">
                        <span>Items:</span>
                        <span id="successItems"><?= count($cart_items); ?></span>
                    </div>
                </div>
            </div>
            
            <p style="text-align: center; color: #6b7280; margin-bottom: 25px;">Thank you for your order! Your tickets/items are being processed.</p>
            
            <div style="display: flex; gap: 12px; margin-top: 30px;">
                <button onclick="continueShopping()" class="btn btn-outline-secondary" style="flex: 1;">Continue Shopping</button>
                <button onclick="viewOrders()" class="btn btn-custom" style="flex: 1;">View Orders</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const CART_TOTAL = <?= $cart_total; ?>;
        const USER_EMAIL = '<?= addslashes($user_email); ?>';
        
        function showPaymentModal() {
            const modal = document.getElementById('paymentModal');
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.style.opacity = '1';
                modal.querySelector('.modal-content').style.transform = 'scale(1)';
            }, 10);
        }
        
        function closePaymentModal() {
            const modal = document.getElementById('paymentModal');
            modal.style.opacity = '0';
            modal.querySelector('.modal-content').style.transform = 'scale(0.9)';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
        
        function processPaystackCheckout() {
            const button = document.getElementById('confirmPaymentBtn');
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            
            // Initialize Paystack transaction
            fetch('../actions/paystack_init_transaction.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    amount: CART_TOTAL,
                    email: USER_EMAIL
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Store reference for verification
                    sessionStorage.setItem('paystack_reference', data.reference);
                    
                    // Redirect to Paystack
                    window.location.href = data.authorization_url;
                } else {
                    throw new Error(data.message || 'Payment initialization failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Payment Error',
                    text: error.message || 'Something went wrong. Please try again.'
                });
                
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-credit-card me-2"></i>Pay Now';
            });
        }
        
        function showSuccessModal(orderData) {
            closePaymentModal();
            
            document.getElementById('successOrderId').textContent = `#${orderData.order_id}`;
            document.getElementById('successAmount').textContent = `₵${orderData.amount.toFixed(2)}`;
            document.getElementById('successDate').textContent = new Date().toLocaleDateString();
            
            const modal = document.getElementById('successModal');
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.style.opacity = '1';
                modal.querySelector('.modal-content').style.transform = 'scale(1)';
            }, 10);
        }
        
        function continueShopping() {
            window.location.href = '../view/all_events.php';
        }
        
        function viewOrders() {
            window.location.href = '../admin/orders.php';
        }
        
        // Check for payment verification on page load (if redirected from Paystack)
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const reference = urlParams.get('reference');
            
            if (reference) {
                // Verify payment
                fetch('../actions/paystack_verify_payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        reference: reference
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showSuccessModal(data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Payment Verification Failed',
                            text: data.message || 'Unable to verify payment. Please contact support.'
                        });
                    }
                })
                .catch(error => {
                    console.error('Verification Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Verification Error',
                        text: 'Unable to verify payment. Please contact support.'
                    });
                });
            }
        });
    </script>
</body>
</html>
