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
        .payment-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 2000;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
        }
        .payment-modal.active {
            display: flex;
        }
        .modal-content {
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            max-width: 500px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .modal-buttons {
            display: flex;
            gap: 12px;
            margin-top: 24px;
            justify-content: center;
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
                    <td><label for="address">Address Line 1:</label></td>
                    <td><input type="text" id="address" name="address" required></td>
                </tr>
                <tr>
                    <td><label for="address">Address Line 2:</label></td>
                    <td><input type="text" id="address" name="address" required></td>
                </tr>
                <tr>
                    <td><label for="payment">Payment Method:</label></td>
                    <td>
                        <div class="form-check custom-radio">
                        <input class="form-check-input" type="radio" name="payment_method" id="payment_method" value="Credit Card">
                        <label class="form-check-label" for="payment_method">Credit Card</label>
                    </div>
                    <div class="form-check custom-radio">
                        <input class="form-check-input" type="radio" name="payment_method" id="payment_method" value="Mobile Money">
                        <label class="form-check-label" for="payment_method">Mobile Money</label>
                    </div>
                    <div class="form-check custom-radio">
                        <input class="form-check-input" type="radio" name="payment_method" id="payment_method" value="Cash on Delivery">
                        <label class="form-check-label" for="payment_method">Cash on Delivery</label>
                    </div>
                    </td>
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

                <!-- Payment Modal -->
                <div id="paymentModal" class="payment-modal">
                    <div class="modal-content">
                        <h2 class="mb-3">Confirm Payment</h2>
                        <p class="text-muted">Click confirm to proceed.</p>
                        <p class="mt-3"><strong>Total Amount: $<?= number_format($cart_total, 2); ?></strong></p>
                        <div class="modal-buttons">
                            <button id="confirmPayBtn" class="btn btn-custom btn-lg">
                                <i class="fas fa-check"></i> Yes, I've Paid
                            </button>
                            <button id="cancelPayBtn" class="btn btn-outline-secondary btn-lg">Cancel</button>
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
