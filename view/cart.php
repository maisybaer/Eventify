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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Eventify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="../settings/favicon.ico"/>

    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .menu-tray {
            position: fixed;
            top: 16px;
            right: 16px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 50px;
            padding: 8px 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .menu-tray a {
            margin: 0 6px;
            padding: 8px 16px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .cart-container {
            padding-top: 100px;
            padding-bottom: 60px;
            max-width: 1200px;
            margin: 0 auto;
            padding-left: 20px;
            padding-right: 20px;
        }

        .cart-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .cart-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.5rem;
        }

        .cart-header p {
            color: #718096;
            font-size: 1.1rem;
        }

        .cart-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .cart-table-container {
            overflow-x: auto;
        }

        .cart-table {
            width: 100%;
            margin: 0;
        }

        .cart-table thead {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
        }

        .cart-table thead th {
            padding: 1.2rem 1rem;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border: none;
        }

        .cart-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            transition: background-color 0.2s ease;
        }

        .cart-table tbody tr:hover {
            background-color: #f7fafc;
        }

        .cart-table tbody td {
            padding: 1.5rem 1rem;
            vertical-align: middle;
        }

        .cart-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .cart-item-title {
            font-weight: 600;
            color: #2d3748;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .cart-item-details {
            color: #718096;
            font-size: 0.875rem;
            line-height: 1.6;
        }

        .cart-item-details strong {
            color: #4a5568;
        }

        .qty-input {
            width: 70px;
            padding: 0.5rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
            transition: border-color 0.3s ease;
        }

        .qty-input:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        .price-cell {
            font-weight: 600;
            color: #2d3748;
            font-size: 1.1rem;
        }

        .subtotal-cell {
            font-weight: 700;
            color: #f97316;
            font-size: 1.2rem;
        }

        .remove-btn {
            background: #f56565;
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .remove-btn:hover {
            background: #e53e3e;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 101, 101, 0.3);
        }

        .cart-summary {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            padding: 2rem;
            border-top: 3px solid #e2e8f0;
        }

        .cart-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .cart-total-label {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
        }

        .cart-total-amount {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .cart-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .cart-actions .btn {
            flex: 1;
            min-width: 180px;
            padding: 1rem 2rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-continue {
            background: white;
            color: #f97316;
            border: 2px solid #f97316 !important;
        }

        .btn-continue:hover {
            background: #f97316;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.3);
        }

        .btn-empty {
            background: white;
            color: #f56565;
            border: 2px solid #f56565 !important;
        }

        .btn-empty:hover {
            background: #f56565;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 101, 101, 0.3);
        }

        .btn-checkout {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.3);
        }

        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(249, 115, 22, 0.4);
        }

        .empty-cart {
            text-align: center;
            padding: 5rem 2rem;
        }

        .empty-cart-icon {
            font-size: 6rem;
            color: #cbd5e0;
            margin-bottom: 1.5rem;
        }

        .empty-cart h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
        }

        .empty-cart p {
            color: #718096;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .empty-cart .btn {
            padding: 1rem 3rem;
            font-weight: 600;
            border-radius: 50px;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .empty-cart .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(249, 115, 22, 0.4);
        }

        @media (max-width: 768px) {
            .cart-actions {
                flex-direction: column;
            }

            .cart-actions .btn {
                width: 100%;
            }

            .cart-total-row {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
   <div class="menu-tray">
         <a href="home.php" class="logo">
                <img src="../settings/logo.png" alt="eventify logo" style="height:30px;">
         </a> 
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="../index.php"><i class="fas fa-home"></i> Home</a>
            <a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
            <a href ="all_event.php"><i class="fas fa-arrow-right"></i>Back</a>
            <a href="login/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a href="login/register.php" class="btn btn-sm btn-primary">Register</a>
            <a href="login/login.php" class="btn btn-sm btn-secondary">Login</a>
        <?php endif; ?>
    </div>

    <main class="cart-container">
        <!-- Header -->
        <div class="cart-header">
            <h1><i class="fas fa-shopping-cart"></i> Shopping Cart</h1>
            <p>Review your items and proceed to checkout</p>
        </div>

        <div class="cart-card">
            <?php if (!empty($cart_items)): ?>
                <!-- Cart Items Table -->
                <div class="cart-table-container">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Image</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="cartItems">
                            <?php foreach ($cart_items as $item):
                                // Resolve image path
                                $img = $item['product_image'] ?? '';
                                if ($img === null || $img === '') {
                                    $imgUrl = '../uploads/no-image.svg';
                                } elseif (strpos($img, 'uploads/') === 0) {
                                    $imgUrl = '../' . $img;
                                } elseif (strpos($img, '/uploads/') !== false) {
                                    $imgUrl = $img;
                                } else {
                                    $imgUrl = '../uploads/' . $img;
                                }
                            ?>
                                <tr data-event-id="<?= $item['event_id']; ?>">
                                    <td>
                                        <div class="cart-item-title"><?= htmlspecialchars($item['product_title']); ?></div>
                                        <?php if (!empty($item['is_event'])): ?>
                                            <div class="cart-item-details">
                                                <?php if (!empty($item['event_date'])): ?>
                                                    <div><i class="fas fa-calendar-day"></i> <strong>Date:</strong> <?= date('M j, Y', strtotime($item['event_date'])); ?></div>
                                                <?php endif; ?>
                                                <?php if (!empty($item['event_start']) || !empty($item['event_end'])): ?>
                                                    <div><i class="fas fa-clock"></i> <strong>Time:</strong> <?= htmlspecialchars(($item['event_start'] ?? '') . (($item['event_end'] ?? '') ? ' - ' . $item['event_end'] : '')); ?></div>
                                                <?php endif; ?>
                                                <?php if (!empty($item['event_location'])): ?>
                                                    <div><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong> <?= htmlspecialchars($item['event_location']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <img src="<?= htmlspecialchars($imgUrl); ?>"
                                             class="cart-item-image"
                                             alt="<?= htmlspecialchars($item['product_title']); ?>"
                                             onerror="this.src='../uploads/no-image.svg'">
                                    </td>
                                    <td class="price-cell">GHS <?= number_format($item['product_price'], 2); ?></td>
                                    <td>
                                        <input type="number" class="qty-input" value="<?= $item['qty']; ?>"
                                               min="1" max="10" data-event-id="<?= $item['event_id']; ?>">
                                    </td>
                                    <td class="subtotal-cell">GHS <?= number_format($item['product_price'] * $item['qty'], 2); ?></td>
                                    <td>
                                        <button class="remove-btn" data-event-id="<?= $item['event_id']; ?>">
                                            <i class="fas fa-trash-alt"></i> Remove
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Cart Summary -->
                <div class="cart-summary">
                    <div class="cart-total-row">
                        <span class="cart-total-label">Total Amount:</span>
                        <span class="cart-total-amount" id="cartTotal">GHS <?= number_format($cart_total, 2); ?></span>
                    </div>
                    <div class="cart-actions">
                        <button onclick="window.location.href='all_event.php'" class="btn btn-continue">
                            <i class="fas fa-arrow-left"></i> Continue Shopping
                        </button>
                        <button id="emptyCartBtn" class="btn btn-empty">
                            <i class="fas fa-trash"></i> Empty Cart
                        </button>
                        <button id="checkoutBtn" class="btn btn-checkout">
                            <i class="fas fa-credit-card"></i> Proceed to Checkout
                        </button>
                    </div>
                </div>

            <?php else: ?>
                <!-- Empty Cart -->
                <div class="empty-cart">
                    <div class="empty-cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h2>Your Cart is Empty</h2>
                    <p>Looks like you haven't added any events to your cart yet.</p>
                    <button onclick="window.location.href='all_event.php'" class="btn">
                         Browse Events
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/cart.js?v=<?php echo time(); ?>"></script>

<?php
$footer_base = '../';
include '../includes/footer.php';
?>
</body>
</html>
