<?php
require_once '../settings/core.php';
require_once '../settings/db_class.php';

$user_id = getUserID();
$role = getUserRole();

// Fetch analytics data
$db = new db_connection();
$db->db_connect();

// Get total tickets sold for user's events
$ticketQuery = "SELECT
    COUNT(DISTINCT o.order_id) as total_orders,
    COALESCE(SUM(od.qty), 0) as total_tickets,
    COALESCE(SUM(od.qty * pr.event_price), 0) as total_revenue,
    COUNT(DISTINCT od.event_id) as events_with_sales
FROM eventify_orders o
JOIN eventify_orderdetails od ON o.order_id = od.order_id
JOIN eventify_products pr ON od.event_id = pr.event_id
WHERE pr.added_by = " . intval($user_id);

$analytics = $db->db_fetch_one($ticketQuery);

// Get top selling events
$topEventsQuery = "SELECT
    pr.event_id,
    pr.event_desc,
    pr.event_price,
    pr.event_date,
    SUM(od.qty) as tickets_sold,
    SUM(od.qty * pr.event_price) as revenue
FROM eventify_products pr
JOIN eventify_orderdetails od ON pr.event_id = od.event_id
JOIN eventify_orders o ON od.order_id = o.order_id
WHERE pr.added_by = " . intval($user_id) . "
GROUP BY pr.event_id
ORDER BY tickets_sold DESC
LIMIT 5";

$topEvents = $db->db_fetch_all($topEventsQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders & Analytics - Eventify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../settings/favicon.ico">

    <style>
        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-card.orders .stat-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .stat-card.tickets .stat-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .stat-card.revenue .stat-icon {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .stat-card.events .stat-icon {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin: 0.5rem 0;
        }

        .stat-label {
            color: #718096;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .top-events-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }

        .top-event-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            transition: background-color 0.2s ease;
        }

        .top-event-item:hover {
            background-color: #f7fafc;
        }

        .top-event-item:last-child {
            border-bottom: none;
        }

        .event-rank {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .event-info {
            flex: 1;
            margin: 0 1rem;
        }

        .event-name {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }

        .event-date {
            color: #718096;
            font-size: 0.875rem;
        }

        .event-stats {
            text-align: right;
        }

        .event-tickets {
            font-weight: 700;
            color: #667eea;
            font-size: 1.1rem;
        }

        .event-revenue {
            color: #718096;
            font-size: 0.875rem;
        }

        .table-container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
    </style>
</head>

<body>
    <header>
        <!-- Navigation -->
        <div class="menu-tray">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../index.php"><i class="fas fa-home"></i> Home</a>
                <a href="event.php"><i class="fas fa-calendar"></i> My Events</a>
                <a href="../view/browse_vendors.php"><i class="fas fa-users"></i> Browse Vendors</a>
                <a href="../login/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="../index.php" class="btn btn-sm btn-primary">Home</a>
                <a href="../login/login.php" class="btn btn-sm btn-secondary">Login</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="container header-container">
        <div class="text-center mb-5 fade-in">
            <span class="badge mb-3">
                <a href="../home.php"><img src="../settings/logo.png" alt="eventify logo" style="width:80px; height:80px; margin-right:8px;"></a>
            </span>
            <h1 class="mb-2"><i class="fas fa-chart-line"></i> Orders & Analytics</h1>
            <p class="text-muted">Track sales and revenue for your events</p>
        </div>

        <!-- Analytics Cards -->
        <div class="analytics-grid slide-up">
            <div class="stat-card orders">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-value"><?php echo number_format($analytics['total_orders'] ?? 0); ?></div>
                <div class="stat-label">Total Orders</div>
            </div>

            <div class="stat-card tickets">
                <div class="stat-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-value"><?php echo number_format($analytics['total_tickets'] ?? 0); ?></div>
                <div class="stat-label">Tickets Sold</div>
            </div>

            <div class="stat-card revenue">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-value">GHS <?php echo number_format($analytics['total_revenue'] ?? 0, 2); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>

            <div class="stat-card events">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-value"><?php echo number_format($analytics['events_with_sales'] ?? 0); ?></div>
                <div class="stat-label">Events with Sales</div>
            </div>
        </div>

        <!-- Top Selling Events -->
        <?php if (!empty($topEvents)): ?>
        <div class="top-events-card slide-up">
            <h3 class="mb-4"><i class="fas fa-trophy"></i> Top Selling Events</h3>
            <?php foreach ($topEvents as $index => $event): ?>
                <div class="top-event-item">
                    <div class="event-rank"><?php echo $index + 1; ?></div>
                    <div class="event-info">
                        <div class="event-name"><?php echo htmlspecialchars($event['event_desc']); ?></div>
                        <div class="event-date">
                            <?php
                            if (!empty($event['event_date'])) {
                                echo '<i class="fas fa-calendar-day"></i> ' . date('M j, Y', strtotime($event['event_date']));
                            }
                            ?>
                        </div>
                    </div>
                    <div class="event-stats">
                        <div class="event-tickets"><?php echo number_format($event['tickets_sold']); ?> tickets</div>
                        <div class="event-revenue">GHS <?php echo number_format($event['revenue'], 2); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Orders Table -->
        <div class="table-container slide-up">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0"><i class="fas fa-list"></i> Recent Orders</h3>
                <input type="text" id="searchOrders" class="form-control" placeholder="Search orders..." style="max-width:300px;">
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="orderTable">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Invoice No</th>
                            <th>Customer ID</th>
                            <th>Items</th>
                            <th>Total Price</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="8" class="text-center">Loading orders...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Expose current user to client scripts
        window.currentUserId = <?php echo json_encode($user_id ?? null); ?>;
        window.currentUserRole = <?php echo json_encode($role ?? null); ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/order.js"></script>

<?php
$footer_base = '../';
include '../includes/footer.php';
?>
</body>
</html>
