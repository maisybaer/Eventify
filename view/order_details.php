<?php
require_once '../settings/core.php';
require_once '../settings/db_class.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}

$customer_id = getUserID();
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if (!$order_id) {
    header('Location: orders.php');
    exit();
}

// Get order details
$db = new db_connection();
$conn = $db->db_conn();

$order = null;
$order_items = [];

if ($conn) {
    $uid = (int) $customer_id;
    $oid = (int) $order_id;

    // Get order info
    $order_query = "SELECT
                        o.order_id,
                        o.invoice_no,
                        o.order_date,
                        o.order_status,
                        o.customer_id,
                        p.amt as total_amount,
                        p.currency,
                        p.payment_date,
                        c.customer_name,
                        c.customer_email,
                        c.customer_contact
                    FROM eventify_orders o
                    LEFT JOIN eventify_payment p ON o.order_id = p.order_id
                    LEFT JOIN eventify_customer c ON o.customer_id = c.customer_id
                    WHERE o.order_id = $oid AND o.customer_id = $uid
                    LIMIT 1";

    $result = mysqli_query($conn, $order_query);
    if ($result && mysqli_num_rows($result) > 0) {
        $order = mysqli_fetch_assoc($result);

        // Get order items
        $items_query = "SELECT
                            od.qty,
                            e.event_name as product_title,
                            e.event_location,
                            e.event_date,
                            e.event_start,
                            e.event_end,
                            e.product_price,
                            e.flyer as product_image
                        FROM eventify_orderdetails od
                        LEFT JOIN eventify_products e ON od.event_id = e.event_id
                        WHERE od.order_id = $oid";

        $items_result = mysqli_query($conn, $items_query);
        if ($items_result) {
            $order_items = mysqli_fetch_all($items_result, MYSQLI_ASSOC);
        }
    } else {
        // Order not found or doesn't belong to this customer
        header('Location: orders.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - <?php echo htmlspecialchars($order['invoice_no']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=<?php echo time(); ?>">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

        .details-container {
            max-width: 1000px;
            margin: 100px auto 50px;
            padding: 24px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #6b7280;
            text-decoration: none;
            margin-bottom: 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: #f97316;
            transform: translateX(-4px);
        }

        .details-card {
            background: white;
            border-radius: 24px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .order-title {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .order-subtitle {
            color: #6b7280;
            margin-bottom: 2rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 2rem;
        }

        .status-paid {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 2px solid #6ee7b7;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            padding: 2rem;
            background: #f9fafb;
            border-radius: 16px;
            margin-bottom: 2rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .info-label {
            font-size: 0.875rem;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-label i {
            color: #f97316;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            color: #f97316;
        }

        .item-card {
            background: #f9fafb;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 12px;
            flex-shrink: 0;
        }

        .item-image-placeholder {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #92400e;
            font-size: 2rem;
            flex-shrink: 0;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .item-details {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .item-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: #f97316;
            text-align: right;
            flex-shrink: 0;
        }

        .total-section {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            padding: 2rem;
            border-radius: 16px;
            margin-top: 2rem;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: #92400e;
        }

        @media (max-width: 768px) {
            .details-container {
                margin-top: 80px;
            }

            .details-card {
                padding: 2rem 1.5rem;
            }

            .item-card {
                flex-direction: column;
                text-align: center;
            }

            .item-price {
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <header class="menu-tray">
        <a href="../index.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-home"></i> Home</a>
        <a href="orders.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-box"></i> My Orders</a>
        <a href="all_event.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-calendar"></i> Events</a>
        <a href="../login/logout.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </header>

    <main>
        <div class="details-container">
            <a href="orders.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>

            <div class="details-card">
                <h1 class="order-title">
                    <i class="fas fa-receipt"></i> <?php echo htmlspecialchars($order['invoice_no']); ?>
                </h1>
                <p class="order-subtitle">Order placed on <?php echo date('F j, Y', strtotime($order['order_date'])); ?></p>

                <div class="status-badge status-<?php echo strtolower($order['order_status']); ?>">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($order['order_status']); ?>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-user"></i>
                            Customer Name
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-envelope"></i>
                            Email
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($order['customer_email']); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-phone"></i>
                            Contact
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($order['customer_contact'] ?? 'N/A'); ?></div>
                    </div>

                    <?php if ($order['payment_date']): ?>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-credit-card"></i>
                            Payment Date
                        </div>
                        <div class="info-value"><?php echo date('M j, Y', strtotime($order['payment_date'])); ?></div>
                    </div>
                    <?php endif; ?>
                </div>

                <h2 class="section-title">
                    <i class="fas fa-calendar-alt"></i> Event Details
                </h2>

                <?php foreach ($order_items as $item): ?>
                    <div class="item-card">
                        <?php if (!empty($item['product_image'])): ?>
                            <img src="../<?php echo htmlspecialchars($item['product_image']); ?>"
                                 alt="<?php echo htmlspecialchars($item['product_title']); ?>"
                                 class="item-image"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="item-image-placeholder" style="display: none;">
                                <i class="fas fa-calendar"></i>
                            </div>
                        <?php else: ?>
                            <div class="item-image-placeholder">
                                <i class="fas fa-calendar"></i>
                            </div>
                        <?php endif; ?>

                        <div class="item-info">
                            <div class="item-name"><?php echo htmlspecialchars($item['product_title']); ?></div>
                            <div class="item-details">
                                <?php if ($item['event_location']): ?>
                                    <div><i class="fas fa-map-marker-alt" style="color: #f97316; margin-right: 0.5rem;"></i><?php echo htmlspecialchars($item['event_location']); ?></div>
                                <?php endif; ?>
                                <?php if ($item['event_date']): ?>
                                    <div><i class="fas fa-calendar" style="color: #f97316; margin-right: 0.5rem;"></i><?php echo date('F j, Y', strtotime($item['event_date'])); ?></div>
                                <?php endif; ?>
                                <?php if ($item['event_start'] && $item['event_end']): ?>
                                    <div><i class="fas fa-clock" style="color: #f97316; margin-right: 0.5rem;"></i><?php echo date('g:i A', strtotime($item['event_start'])); ?> - <?php echo date('g:i A', strtotime($item['event_end'])); ?></div>
                                <?php endif; ?>
                                <div style="margin-top: 0.5rem;"><strong>Quantity:</strong> <?php echo $item['qty']; ?> ticket(s) × <?php echo $order['currency']; ?> <?php echo number_format($item['product_price'], 2); ?></div>
                            </div>
                        </div>

                        <div class="item-price">
                            <?php echo $order['currency']; ?> <?php echo number_format($item['product_price'] * $item['qty'], 2); ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="total-section">
                    <div class="total-row">
                        <span>Total Paid:</span>
                        <span><?php echo $order['currency']; ?> <?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php
$footer_base = '../';
include '../includes/footer.php';
?>
</body>
</html>
