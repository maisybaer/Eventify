<?php
require_once '../settings/core.php';
require_once '../settings/db_class.php';
require_once '../controllers/subscription_controller.php';

$user_id = getUserID();
$role = getUserRole();

// Check if user has premium access
$has_premium = has_active_premium_ctr($user_id, 'analytics_premium');
$active_subscription = get_active_subscription_ctr($user_id, 'analytics_premium');

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
    <title>Analytics</title>
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
            
            <a href="../home.php" class="logo">
                <div class="logo-icon"><img src="../settings/logo.png" alt="eventify logo" style="height:30px;"></div>
            </a>
            

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../index.php"><i class="fas fa-home"></i> Home</a>
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
            <h1 class="mb-2"><i class="fas fa-chart-line"></i> Analytics</h1>
            <p class="text-muted">Track sales and revenue for your events</p>
        </div>

        <?php if (!$has_premium): ?>
        <!-- Premium Upgrade Banner -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; padding: 2.5rem; margin-bottom: 2rem; color: white; position: relative; overflow: hidden; box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);">
            <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="position: relative; z-index: 1; text-align: center;">
                <div style="display: inline-block; background: rgba(255,255,255,0.2); padding: 0.5rem 1.5rem; border-radius: 50px; margin-bottom: 1rem; backdrop-filter: blur(10px);">
                    <i class="fas fa-crown"></i> PREMIUM ANALYTICS
                </div>
                <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 1rem;">Unlock Advanced Analytics</h2>
                <p style="font-size: 1.1rem; opacity: 0.95; margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">
                    Get detailed insights, customer demographics, performance tracking, and export capabilities for just ₵50/month
                </p>
                <a href="../view/premium_checkout.php" style="display: inline-block; background: white; color: #667eea; padding: 1rem 3rem; border-radius: 50px; text-decoration: none; font-weight: 700; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.2)';">
                    <i class="fas fa-rocket"></i> Upgrade to Premium - ₵50/month
                </a>
            </div>
        </div>

        <!-- Basic Analytics (Locked for Non-Premium) -->
        <div style="position: relative;">
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.95); backdrop-filter: blur(5px); z-index: 10; border-radius: 15px; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 2rem;">
                <i class="fas fa-lock" style="font-size: 4rem; color: #cbd5e0; margin-bottom: 1rem;"></i>
                <h3 style="color: #2d3748; margin-bottom: 0.5rem;">Premium Feature</h3>
                <p style="color: #718096; text-align: center; max-width: 400px;">Upgrade to Premium Analytics to unlock detailed insights and advanced reporting features.</p>
            </div>
        <?php else: ?>
        <!-- Premium Active Banner -->
        <div style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border: 2px solid #6ee7b7; color: #065f46; padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <i class="fas fa-crown" style="font-size: 2rem;"></i>
                <div>
                    <h5 style="margin: 0; font-weight: 700;">Premium Analytics Active</h5>
                    <p style="margin: 0; font-size: 0.875rem;">Your subscription is active until <?php echo date('F j, Y', strtotime($active_subscription['end_date'])); ?></p>
                </div>
            </div>
            <span style="background: rgba(5, 150, 105, 0.2); padding: 0.5rem 1rem; border-radius: 50px; font-weight: 600; font-size: 0.875rem;">
                <i class="fas fa-check-circle"></i> ACTIVE
            </span>
        </div>
        <?php endif; ?>

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

        

    <?php if (!$has_premium): ?>
        </div> <!-- Close locked overlay div -->
    <?php endif; ?>

    </div>

    <script>
        // Expose current user to client scripts
        window.currentUserId = <?php echo json_encode($user_id ?? null); ?>;
        window.currentUserRole = <?php echo json_encode($role ?? null); ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/order.js?v=<?php echo time(); ?>"></script>

<?php
$footer_base = '../';
include '../includes/footer.php';
?>
</body>
</html>