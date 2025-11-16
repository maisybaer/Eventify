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
    <title>Shopping Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=1.1">
</head>

<body>
    <header class="menu-tray mb-3">
        <a href="../index.php" class="btn btn-sm btn-outline-secondary">Home</a>
        <a href="all_product.php" class="btn btn-sm btn-outline-secondary">Continue Shopping</a>
        <a href="../login/logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>
    </header>

    <main>  
        <div class="cart-container">
            <div class="cart-card">
                <h2 class="mb-4">Your Shopping Cart</h2>

                <?php if (!empty($cart_items)): ?>
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Image</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="cartItems">
                            <?php foreach ($cart_items as $item): ?>
                                <tr data-product-id="<?= $item['product_id']; ?>">
                                    <td><?= htmlspecialchars($item['product_title']); ?></td>
                                    <td>
                                        <img src="../uploads/<?= htmlspecialchars($item['product_image']); ?>" 
                                             alt="<?= htmlspecialchars($item['product_title']); ?>"
                                             onerror="this.src='../uploads/no-image.svg'">
                                    </td>
                                    <td>$<?= number_format($item['product_price'], 2); ?></td>
                                    <td>
                                        <input type="number" class="qty-input" value="<?= $item['qty']; ?>" 
                                               min="1" data-product-id="<?= $item['product_id']; ?>">
                                    </td>
                                    <td class="subtotal">$<?= number_format($item['product_price'] * $item['qty'], 2); ?></td>
                                    <td>
                                        <button class="btn btn-danger btn-sm remove-btn" 
                                                data-product-id="<?= $item['product_id']; ?>">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="cart-summary">
                        <h3>Cart Total: <span id="cartTotal">$<?= number_format($cart_total, 2); ?></span></h3>
                        <div class="cart-actions">
                            <button onclick="window.location.href='all_product.php'" class="btn btn-custom">
                                Continue Shopping
                            </button>
                            <button id="emptyCartBtn" class="btn btn-outline-danger">Empty Cart</button>
                            <button id="checkoutBtn" class="btn btn-custom">Proceed to Checkout</button>
                        </div>
                    </div>

                <?php else: ?>
                    <p class="empty-msg">Your cart is empty. <a href="all_product.php">Continue shopping</a>.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/cart.js"></script>
</body>
</html>