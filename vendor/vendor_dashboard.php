<?php
require_once '../settings/core.php';
require_once '../classes/vendor_booking_class.php';

// Check if user is logged in and is a vendor
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = getUserRole();

// Only vendors (role=2) can access this page
if ($user_role != 2) {
    header('Location: ../index.php');
    exit;
}

// Get vendor's event requests
$bookingClass = new VendorBooking();
$my_requests = $bookingClass->getVendorEventRequests($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Event Requests - Vendor Dashboard - Eventify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../settings/favicon.ico">

    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 80px;
        }

        .menu-tray {
            position: fixed;
            top: 16px;
            right: 16px;
            background: rgba(255,255,255,0.95);
            border-radius: 50px;
            padding: 8px 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            z-index: 1200;
            backdrop-filter: blur(10px);
        }

        .menu-tray a {
            margin: 0 6px;
            padding: 8px 16px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .header-container {
            text-align: center;
            margin-bottom: 3rem;
            padding-top: 40px;
        }

        .header-container h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .action-btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .action-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            text-align: center;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
        }

        .stat-card.pending .stat-icon {
            background: #fef3c7;
            color: #92400e;
        }

        .stat-card.approved .stat-icon {
            background: #d1fae5;
            color: #065f46;
        }

        .stat-card.total .stat-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
        }

        .stat-label {
            color: #718096;
            font-size: 0.9rem;
        }

        .request-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .request-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .request-status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-confirmed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .request-body {
            padding: 1.5rem;
        }

        .request-detail {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            color: #4a5568;
        }

        .request-detail i {
            width: 30px;
            color: #667eea;
        }

        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .empty-state i {
            font-size: 5rem;
            color: #cbd5e0;
            margin-bottom: 1.5rem;
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

    <div class="container">
        <div class="header-container">
            <h1><i class="fas fa-briefcase"></i> My Event Requests</h1>
            <p class="text-muted">View events where you've requested to provide vendor services</p>
        </div>


        <?php
        // Calculate stats
        $total_requests = count($my_requests);
        $pending_count = count(array_filter($my_requests, fn($r) => $r['booking_status'] === 'pending'));
        $approved_count = count(array_filter($my_requests, fn($r) => $r['booking_status'] === 'confirmed'));
        ?>

        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                <div class="stat-value"><?php echo $total_requests; ?></div>
                <div class="stat-label">Total Requests</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-value"><?php echo $pending_count; ?></div>
                <div class="stat-label">Awaiting Approval</div>
            </div>
            <div class="stat-card approved">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-value"><?php echo $approved_count; ?></div>
                <div class="stat-label">Approved Gigs</div>
            </div>
        </div>

        <?php if (empty($my_requests)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h2>No Event Requests Yet</h2>
                <p class="text-muted">Browse events and request to provide your vendor services!</p>
                <a href="browse_events_vendor.php" class="btn btn-lg btn-primary mt-3" style="border-radius: 50px;">
                    <i class="fas fa-calendar-alt"></i> Browse Events
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($my_requests as $request): ?>
                <div class="request-card">
                    <div class="request-header">
                        <div>
                            <h4 class="mb-0"><?php echo htmlspecialchars($request['event_desc'] ?? 'Event'); ?></h4>
                            <small>Event Manager: <?php echo htmlspecialchars($request['event_creator_name'] ?? 'N/A'); ?></small>
                        </div>
                        <span class="request-status status-<?php echo htmlspecialchars($request['booking_status']); ?>">
                            <?php
                            $statusLabels = [
                                'pending' => '<i class="fas fa-clock"></i> Pending',
                                'confirmed' => '<i class="fas fa-check-circle"></i> Approved',
                                'cancelled' => '<i class="fas fa-times-circle"></i> Rejected'
                            ];
                            echo $statusLabels[$request['booking_status']] ?? ucfirst($request['booking_status']);
                            ?>
                        </span>
                    </div>

                    <div class="request-body">
                        <?php if (!empty($request['event_date'])): ?>
                            <div class="request-detail">
                                <i class="fas fa-calendar"></i>
                                <strong>Event Date:&nbsp;</strong> <?php echo date('F j, Y', strtotime($request['event_date'])); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($request['event_location'])): ?>
                            <div class="request-detail">
                                <i class="fas fa-map-marker-alt"></i>
                                <strong>Location:&nbsp;</strong> <?php echo htmlspecialchars($request['event_location']); ?>
                            </div>
                        <?php endif; ?>

                        <div class="request-detail">
                            <i class="fas fa-clock"></i>
                            <strong>Requested:&nbsp;</strong> <?php echo date('F j, Y g:i A', strtotime($request['booking_date'])); ?>
                        </div>

                        <?php if (!empty($request['notes'])): ?>
                            <div class="request-detail">
                                <i class="fas fa-sticky-note"></i>
                                <div>
                                    <strong>Your Message:</strong><br>
                                    <?php echo nl2br(htmlspecialchars($request['notes'])); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($request['booking_status'] === 'confirmed'): ?>
                            <div class="alert alert-success mt-3 mb-0">
                                <i class="fas fa-check-circle"></i> <strong>Congratulations!</strong> You've been approved to provide services at this event.
                                <?php if (!empty($request['event_manager_approved_date'])): ?>
                                    <br><small>Approved on <?php echo date('F j, Y', strtotime($request['event_manager_approved_date'])); ?></small>
                                <?php endif; ?>
                            </div>
                        <?php elseif ($request['booking_status'] === 'cancelled'): ?>
                            <div class="alert alert-danger mt-3 mb-0">
                                <i class="fas fa-times-circle"></i> Your request was not approved for this event.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mt-3 mb-0">
                                <i class="fas fa-hourglass-half"></i> Waiting for event manager approval...
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
$footer_base = '../';
include '../includes/footer.php';
?>

</body>
</html>
