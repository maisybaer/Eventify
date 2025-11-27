<?php
require_once '../settings/core.php';
require_once '../controllers/cart_controller.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$cartController = new CartController();
$cart_items = $cartController->get_user_cart_ctr($user_id);

// Get user information
require_once '../settings/db_class.php';
$db = new db_connection();
$conn = $db->db_conn();
$user_info = null;
if ($conn) {
    $uid = (int) $user_id;
    $sql = "SELECT customer_name, customer_email, customer_contact FROM eventify_customer WHERE customer_id = $uid LIMIT 1";
    $res = mysqli_query($conn, $sql);
    if ($res) {
        $user_info = mysqli_fetch_assoc($res);
    }
}

// Calculate subtotal and total with service fee
$cart_subtotal = 0;
if (!empty($cart_items)) {
    foreach ($cart_items as $item) {
        $cart_subtotal += $item['qty'] * $item['product_price'];
    }
}
// No service fee - customer pays only the subtotal
$service_fee = 0;
$cart_total = $cart_subtotal;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Eventify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=<?php echo time(); ?>">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

        .checkout-container {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 24px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: #6b7280;
            font-size: 1.1rem;
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 450px;
            gap: 2rem;
        }

        @media (max-width: 992px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
        }

        .checkout-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-title i {
            color: #f97316;
        }

        .user-info-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .user-info-table tr:not(:last-child) td {
            border-bottom: 1px solid #e5e7eb;
        }

        .user-info-table td {
            padding: 1rem 0.75rem;
        }

        .user-info-table td:first-child {
            font-weight: 600;
            color: #4b5563;
            width: 140px;
        }

        .user-info-table td:last-child {
            color: #1f2937;
        }

        .user-info-table i {
            color: #f97316;
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .summary-item:last-of-type {
            border-bottom: none;
        }

        .item-details {
            flex: 1;
        }

        .item-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .item-qty {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .item-price {
            font-weight: 700;
            color: #f97316;
            font-size: 1.1rem;
        }

        .subtotal-row, .fee-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            color: #4b5563;
            border-top: 1px solid #e5e7eb;
        }

        .fee-row {
            color: #059669;
        }

        .fee-row small {
            display: block;
            font-size: 0.75rem;
            color: #6b7280;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.75rem;
            font-weight: 700;
            color: #1f2937;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 3px solid #f97316;
        }

        .summary-total .amount {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-checkout {
            width: 100%;
            padding: 1rem 2rem;
            border: none;
            border-radius: 50px;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1.5rem;
        }

        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(249, 115, 22, 0.4);
        }

        .btn-checkout:active {
            transform: translateY(0);
        }

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 99999 !important;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(5px);
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .modal.active {
            display: flex;
        }

        /* Ensure menu and footer stay behind modal */
        .menu-tray {
            z-index: 100 !important;
        }

        footer {
            z-index: 1 !important;
            position: relative;
        }

        header {
            z-index: 100 !important;
        }

        /* Hide menu and footer when modal is active */
        body.modal-open .menu-tray,
        body.modal-open header,
        body.modal-open footer {
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
            transition: opacity 0.3s ease;
        }

        /* Prevent body scroll when modal is open */
        body.modal-open {
            overflow: hidden;
        }

        .modal-content {
            background: white;
            padding: 2.5rem;
            border-radius: 24px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.5rem;
            color: #6b7280;
            cursor: pointer;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: #f3f4f6;
            color: #1f2937;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1f2937;
        }

        .payment-amount {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 1rem 0;
        }

        .payment-breakdown {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 1.5rem;
        }

        .secure-badge {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 16px;
            margin: 1.5rem 0;
        }

        .secure-badge i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .modal-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .btn {
            flex: 1;
            padding: 0.875rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.4);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .empty-cart {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .empty-cart i {
            font-size: 5rem;
            color: #cbd5e0;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <header class="menu-tray">
        <a href="../index.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-home"></i> Home</a>
        <a href="cart.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back to Cart</a>
        <a href="../login/logout.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </header>

    <main>
        <div class="checkout-container">
            <div class="page-header">
                <h1><i class="fas fa-credit-card"></i> Checkout</h1>
                <p>Review your order and complete payment</p>
            </div>

            <?php if (!empty($cart_items)): ?>
                <div class="checkout-grid">
                    <!-- User Information Card -->
                    <div class="checkout-card">
                        <h3 class="card-title">
                            <i class="fas fa-user-circle"></i>
                            Your Information
                        </h3>

                        <table class="user-info-table">
                            <tr>
                                <td><i class="fas fa-user"></i><strong>Name</strong></td>
                                <td><?= htmlspecialchars($user_info['customer_name'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-envelope"></i><strong>Email</strong></td>
                                <td>
                                    <input type="email" id="customer_email" name="customer_email"
                                           value="<?= htmlspecialchars($user_info['customer_email'] ?? ''); ?>"
                                           style="border: none; background: transparent; width: 100%; font-size: inherit; color: inherit;"
                                           required>
                                </td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-phone"></i><strong>Phone</strong></td>
                                <td><?= htmlspecialchars($user_info['customer_contact'] ?? 'Not set'); ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Order Summary Card -->
                    <div class="checkout-card">
                        <h3 class="card-title">
                            <i class="fas fa-shopping-bag"></i>
                            Order Summary
                        </h3>

                        <?php foreach ($cart_items as $item): ?>
                            <div class="summary-item">
                                <div class="item-details">
                                    <div class="item-title"><?= htmlspecialchars($item['product_title']); ?></div>
                                    <div class="item-qty">Qty: <?= $item['qty']; ?> × GHS <?= number_format($item['product_price'], 2); ?></div>
                                </div>
                                <div class="item-price">
                                    GHS <?= number_format($item['product_price'] * $item['qty'], 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="subtotal-row">
                            <div><strong>Subtotal</strong></div>
                            <div><strong>GHS <?= number_format($cart_subtotal, 2); ?></strong></div>
                        </div>

                        <div class="fee-row">
                            <div>
                                <strong>Service Fee (15%)</strong>
                                <small>Processing and platform fee</small>
                            </div>
                            <div><strong>GHS <?= number_format($service_fee, 2); ?></strong></div>
                        </div>

                        <div class="summary-total">
                            <div>Total</div>
                            <div class="amount" id="checkoutTotal">GHS <?= number_format($cart_total, 2); ?></div>
                        </div>

                        <button onclick="showPaymentModal()" class="btn-checkout">
                            <i class="fas fa-lock"></i> Proceed to Secure Payment
                        </button>
                    </div>
                </div>

                <!-- Payment Modal -->
                <div id="paymentModal" class="modal" aria-hidden="true">
                    <div class="modal-content">
                        <span class="modal-close" onclick="closePaymentModal()">×</span>
                        <h2 class="modal-title"><i class="fas fa-shield-alt"></i> Secure Payment</h2>

                        <div class="payment-amount" id="paymentAmount">
                            GHS <?= number_format($cart_total, 2); ?>
                        </div>

                        <div class="payment-breakdown" id="paymentBreakdown">
                            Subtotal: GHS <?= number_format($cart_subtotal, 2); ?> + Service Fee (15%): GHS <?= number_format($service_fee, 2); ?>
                        </div>

                        <div class="secure-badge">
                            <i class="fas fa-lock"></i>
                            <div style="font-weight: 700; font-size: 1.1rem; margin-top: 0.5rem;">Pay with Paystack</div>
                            <div style="font-size: 0.875rem; opacity: 0.85; margin-top: 0.5rem;">
                                You'll be redirected to Paystack's secure payment gateway
                            </div>
                        </div>

                        <p style="color: #6b7280; font-size: 0.875rem;">
                            <i class="fas fa-info-circle"></i> Your payment information is encrypted and secure
                        </p>

                        <div class="modal-buttons">
                            <button class="btn btn-secondary" onclick="closePaymentModal()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button id="confirmPaymentBtn" onclick="processCheckout()" class="btn btn-primary">
                                <i class="fas fa-credit-card"></i> Pay Now
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Success Modal -->
                <div id="successModal" class="modal" aria-hidden="true">
                    <div class="modal-content">
                        <h2 class="modal-title"><i class="fas fa-check-circle" style="color: #059669;"></i> Payment Successful!</h2>

                        <div style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); padding: 1.5rem; border-radius: 16px; margin: 1.5rem 0; border: 2px solid #6ee7b7;">
                            <div style="margin-bottom: 1rem;">
                                <div style="font-size: 0.875rem; color: #065f46; margin-bottom: 0.5rem;">Invoice Number</div>
                                <div id="successInvoice" style="font-size: 1.5rem; font-weight: 700; color: #047857;"></div>
                            </div>
                            <div style="border-top: 1px solid rgba(6, 95, 70, 0.2); padding-top: 1rem; text-align: left;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: #065f46;">
                                    <span>Total Paid:</span>
                                    <span id="successAmount" style="font-weight: 700;"></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: #065f46;">
                                    <span>Date:</span>
                                    <span id="successDate"></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; color: #065f46;">
                                    <span>Items:</span>
                                    <span id="successItems"></span>
                                </div>
                            </div>
                        </div>

                        <p style="color: #6b7280;">Thank you for your order! A receipt has been sent to your email.</p>

                        <div class="modal-buttons">
                            <button onclick="continueShopping()" class="btn btn-secondary">
                                <i class="fas fa-shopping-bag"></i> Continue Shopping
                            </button>
                            <button onclick="viewOrders()" class="btn btn-primary">
                                <i class="fas fa-receipt"></i> View Orders
                            </button>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h2>Your cart is empty</h2>
                    <p class="text-muted">Add some items to your cart to proceed to checkout</p>
                    <a href="all_event.php" class="btn-checkout" style="display: inline-block; width: auto; margin-top: 1rem;">
                        <i class="fas fa-calendar"></i> Browse Events
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/checkout.js"></script>

<?php
$footer_base = '../';
include '../includes/footer.php';
?>
</body>
</html>
