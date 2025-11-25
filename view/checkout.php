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

// Calculate total
$cart_total = 0;
if (!empty($cart_items)) {
    foreach ($cart_items as $item) {
        $cart_total += $item['qty'] * $item['product_price'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css">
    <style>
        .checkout-container {
            max-width: 900px;
            margin: 100px auto 50px;
            padding: 24px;
        }
        .checkout-summary {
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            box-shadow: var(--card-shadow);
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .summary-total {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--brand);
            margin-top: 16px;
            padding-top: 16px;
            border-top: 2px solid var(--brand-light);
        }
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 2000;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(3px);
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .modal.active {
            display: flex;
        }
        .modal-content {
            background: #fff;
            padding: 28px;
            border-radius: 14px;
            max-width: 620px;
            width: 100%;
            text-align: center;
            box-shadow: 0 12px 40px rgba(2,6,23,0.25);
            position: relative;
        }
        .modal-close {
            position: absolute;
            top: 12px;
            right: 12px;
            font-size: 22px;
            color: #6b7280;
            cursor: pointer;
        }
        .modal-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .modal-buttons {
            display: flex;
            gap: 12px;
            margin-top: 22px;
            justify-content: center;
        }
        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background: #0ea5a4;
            color: white;
        }
        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
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
        <div class="checkout-container">
            <h1 class="text-center mb-4">Checkout</h1>
            
            <div class="checkout-summary">
            <h4 class="text-center mb-4">Enter your shipping information</h4>
            <form>
            <table>
                <tr>
                    <td><label for="name">First Name:</label></td>
                    <td><input type="text" id="name" name="name" required></td>
                </tr>
                <tr>
                    <td><label for="name">Last Name:</label></td>
                    <td><input type="text" id="name" name="name" required></td>
                </tr>
                <tr>
                    <td><label for="customer_email">Email:</label></td>
                    <td><input type="email" id="customer_email" name="customer_email" required value="<?= htmlspecialchars(getUserEmail()) ?>"></td>
                </tr>
                <tr>
                    <td><label for="address">Address Line 1:</label></td>
                    <td><input type="text" id="address" name="address" required></td>
                </tr>
                <tr>
                    <td><label for="address">Address Line 2:</label></td>
                    <td><input type="text" id="address" name="address" required></td>
                </tr>
            </table>
        </form>
        </div><br>

            <?php if (!empty($cart_items)): ?>
                <div class="checkout-summary">
                    <h3 class="mb-4">Order Summary</h3>
                    
                    <?php foreach ($cart_items as $item): ?>
                        <div class="summary-item">
                            <div>
                                <strong><?= htmlspecialchars($item['product_title']); ?></strong>
                                <span class="text-muted"> × <?= $item['qty']; ?></span>
                            </div>
                            <div>$<?= number_format($item['product_price'] * $item['qty'], 2); ?></div>
                        </div>
                    <?php endforeach; ?>

                    <div class="summary-total">
                        <div class="d-flex justify-content-between">
                            <span>Total:</span>
                            <span id="checkoutTotal">$<?= number_format($cart_total, 2); ?></span>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                         <button onclick="showPaymentModal()" class="btn btn-primary">💳 Proceed to Payment</button>
                    </div>
                </div>

                <!-- Payment Modal (Paystack) -->
                <div id="paymentModal" class="modal" aria-hidden="true">
                    <div class="modal-content">
                        <span class="modal-close" onclick="closePaymentModal()">&times;</span>
                        <h2 class="modal-title">Secure Payment via Paystack</h2>

                        <div style="text-align: center; margin: 18px 0 8px;">
                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 6px;">Amount to Pay</div>
                            <div id="paymentAmount" style="font-size: 32px; font-weight: 700; color: #dc2626;">$<?= number_format($cart_total, 2); ?></div>
                            <div id="paymentBreakdown" style="margin-top:10px; font-size:14px; color:#f3f4f6;"></div>
                        </div>

                        <div style="background: linear-gradient(135deg, #111827 0%, #374151 100%); color: white; padding: 16px; border-radius: 12px; margin: 18px 0; box-shadow: 0 6px 18px rgba(2,6,23,0.2);">
                            <div style="font-size: 12px; opacity: 0.85;">SECURED PAYMENT</div>
                            <div style="font-size: 16px; font-weight: 700; margin-top: 6px;">🔒 Pay with Paystack</div>
                            <div style="font-size: 12px; opacity: 0.85; margin-top: 8px;">You will be redirected to Paystack's secure gateway to complete payment.</div>
                        </div>

                        <p style="text-align: center; color: #6b7280; font-size: 13px; margin-bottom: 14px;">Please confirm to continue to payment.</p>

                        <div class="modal-buttons">
                            <button class="btn btn-secondary" onclick="closePaymentModal()">Cancel</button>
                            <button id="confirmPaymentBtn" onclick="processCheckout()" class="btn btn-primary">💳 Pay Now</button>
                        </div>
                    </div>
                </div>

                <!-- Success Modal -->
                <div id="successModal" class="modal" aria-hidden="true">
                    <div class="modal-content">
                        <h2 class="modal-title">🎉 Order Successful!</h2>

                        <div style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); padding: 16px; border-radius: 12px; margin: 12px 0; border: 1px solid #6ee7b7;">
                            <div style="text-align: center; margin-bottom: 10px;">
                                <div style="font-size: 12px; color: #065f46; margin-bottom: 4px;">Invoice Number</div>
                                <div id="successInvoice" style="font-size: 18px; font-weight: 700; color: #047857;"></div>
                            </div>
                            <div style="border-top: 1px solid rgba(6, 95, 70, 0.12); padding-top: 12px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 14px; color: #065f46;">
                                    <span>Total Paid:</span>
                                    <span id="successAmount" style="font-weight: 600;"></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 14px; color: #065f46;">
                                    <span>Date:</span>
                                    <span id="successDate"></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 14px; color: #065f46;">
                                    <span>Items:</span>
                                    <span id="successItems"></span>
                                </div>
                            </div>
                        </div>

                        <p style="text-align: center; color: #6b7280; margin-bottom: 16px;">Thank you for your order! A receipt has been sent to your email.</p>

                        <div class="modal-buttons">
                            <button onclick="continueShopping()" class="btn btn-secondary">Continue Shopping</button>
                            <button onclick="viewOrders()" class="btn btn-primary">View Orders</button>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <div class="text-center">
                    <p class="text-muted">Your cart is empty. <a href="all_product.php">Continue shopping</a>.</p>
                </div>
            <?php endif; ?>

            <div id="checkoutMessage" class="mt-4"></div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/checkout.js"></script>
</body>
</html>
