<?php
require_once '../settings/core.php';
require_once '../controllers/order_controller.php';
require_once '../settings/db_class.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}

$customer_id = getUserID();

// Get all orders for this customer
$db = new db_connection();
$conn = $db->db_conn();

$orders = [];
if ($conn) {
    $uid = (int) $customer_id;

    // Query to get orders with their details
    $query = "SELECT
                o.order_id,
                o.invoice_no,
                o.order_date,
                o.order_status,
                p.amt as total_amount,
                p.currency,
                p.payment_date,
                COUNT(od.event_id) as item_count
              FROM eventify_orders o
              LEFT JOIN eventify_payment p ON o.order_id = p.order_id
              LEFT JOIN eventify_orderdetails od ON o.order_id = od.order_id
              WHERE o.customer_id = $uid
              GROUP BY o.order_id, o.invoice_no, o.order_date, o.order_status, p.amt, p.currency, p.payment_date
              ORDER BY o.order_date DESC";

    $result = mysqli_query($conn, $query);
    if ($result) {
        $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Eventify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=<?php echo time(); ?>">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

        .orders-container {
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

        .order-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.12);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #f3f4f6;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .order-invoice {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
        }

        .order-status {
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .status-paid {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 2px solid #6ee7b7;
        }

        .status-pending {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 2px solid #fbbf24;
        }

        .status-cancelled {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 2px solid #f87171;
        }

        .order-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .detail-label {
            font-size: 0.875rem;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-label i {
            color: #f97316;
        }

        .detail-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
        }

        .order-actions {
            display: flex;
            gap: 1rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f3f4f6;
            flex-wrap: wrap;
        }

        .btn-view {
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.4);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .empty-state i {
            font-size: 5rem;
            color: #e5e7eb;
            margin-bottom: 1rem;
        }

        .empty-state h2 {
            color: #374151;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #6b7280;
            margin-bottom: 2rem;
        }

        .btn-browse {
            padding: 1rem 2rem;
            border-radius: 50px;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-browse:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(249, 115, 22, 0.4);
            color: white;
        }

        @media (max-width: 768px) {
            .orders-container {
                margin-top: 80px;
            }

            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .order-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header class="menu-tray">
        <a href="../index.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-home"></i> Home</a>
        <a href="all_event.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-calendar"></i> Events</a>
        <a href="cart.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-shopping-cart"></i> Cart</a>
        <a href="../login/logout.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </header>

    <main>
        <div class="orders-container">
            <div class="page-header">
                <h1><i class="fas fa-box"></i> My Orders</h1>
                <p>View and track all your event bookings</p>
            </div>

            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-invoice">
                                <i class="fas fa-receipt" style="color: #f97316; margin-right: 0.5rem;"></i>
                                <?php echo htmlspecialchars($order['invoice_no']); ?>
                            </div>
                            <div class="order-status status-<?php echo strtolower($order['order_status']); ?>">
                                <?php
                                $status_icons = [
                                    'Paid' => 'fa-check-circle',
                                    'Pending' => 'fa-clock',
                                    'Cancelled' => 'fa-times-circle'
                                ];
                                $icon = $status_icons[$order['order_status']] ?? 'fa-info-circle';
                                ?>
                                <i class="fas <?php echo $icon; ?>"></i>
                                <?php echo htmlspecialchars($order['order_status']); ?>
                            </div>
                        </div>

                        <div class="order-details">
                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="fas fa-calendar"></i>
                                    Order Date
                                </div>
                                <div class="detail-value">
                                    <?php echo date('M j, Y', strtotime($order['order_date'])); ?>
                                </div>
                            </div>

                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="fas fa-dollar-sign"></i>
                                    Total Amount
                                </div>
                                <div class="detail-value">
                                    <?php
                                    $currency = $order['currency'] ?? 'GHS';
                                    $amount = $order['total_amount'] ?? 0;
                                    echo $currency . ' ' . number_format($amount, 2);
                                    ?>
                                </div>
                            </div>

                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="fas fa-shopping-bag"></i>
                                    Items
                                </div>
                                <div class="detail-value">
                                    <?php echo $order['item_count']; ?> item(s)
                                </div>
                            </div>

                            <?php if ($order['payment_date']): ?>
                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="fas fa-credit-card"></i>
                                    Payment Date
                                </div>
                                <div class="detail-value">
                                    <?php echo date('M j, Y', strtotime($order['payment_date'])); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="order-actions">
                            <a href="order_details.php?order_id=<?php echo $order['order_id']; ?>" class="btn-view">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h2>No Orders Yet</h2>
                    <p>You haven't placed any orders yet. Start browsing events!</p>
                    <a href="all_event.php" class="btn-browse">
                        <i class="fas fa-calendar"></i> Browse Events
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

<?php
$footer_base = '../';
include '../includes/footer.php';
?>
</body>
</html>
