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

// Get customer-to-vendor service requests for this vendor
$bookingClass = new VendorBooking();
$bookings = $bookingClass->getCustomerToVendorRequests($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Requests - Vendor Dashboard - Eventify</title>
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

        .stat-card.rejected .stat-icon {
            background: #fee2e2;
            color: #991b1b;
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
            flex-wrap: wrap;
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

        .status-confirmed, .status-approved {
            background: #d1fae5;
            color: #065f46;
        }

        .status-cancelled, .status-rejected {
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

        .request-actions {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .btn-approve {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            color: white;
        }

        .btn-reject {
            background: white;
            color: #ef4444;
            border: 2px solid #ef4444;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-reject:hover {
            background: #ef4444;
            color: white;
            transform: translateY(-2px);
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
            <h1><i class="fas fa-inbox"></i> Customer Service Requests</h1>
            <p class="text-muted">Manage requests from customers who want to hire your vendor services</p>
        </div>

        <?php
        // Calculate stats
        $total_requests = count($bookings);
        $pending_count = count(array_filter($bookings, fn($r) => $r['booking_status'] === 'pending'));
        $approved_count = count(array_filter($bookings, fn($r) => in_array($r['booking_status'], ['approved', 'confirmed'])));
        $rejected_count = count(array_filter($bookings, fn($r) => in_array($r['booking_status'], ['rejected', 'cancelled'])));
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
                <div class="stat-label">Awaiting Response</div>
            </div>
            <div class="stat-card approved">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-value"><?php echo $approved_count; ?></div>
                <div class="stat-label">Approved</div>
            </div>
            <div class="stat-card rejected">
                <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                <div class="stat-value"><?php echo $rejected_count; ?></div>
                <div class="stat-label">Declined</div>
            </div>
        </div>

        <?php if (empty($bookings)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h2>No Service Requests Yet</h2>
                <p class="text-muted">You haven't received any booking requests from customers for your vendor services.</p>
            </div>
        <?php else: ?>
            <?php foreach ($bookings as $booking): ?>
                <div class="request-card">
                    <div class="request-header">
                        <div>
                            <h4 class="mb-0"><?php echo htmlspecialchars($booking['event_desc'] ?? 'Event Booking'); ?></h4>
                            <small>Customer: <?php echo htmlspecialchars($booking['customer_name'] ?? 'N/A'); ?></small>
                        </div>
                        <span class="request-status status-<?php echo htmlspecialchars($booking['booking_status']); ?>">
                            <?php
                            $statusLabels = [
                                'pending' => '<i class="fas fa-clock"></i> Pending',
                                'approved' => '<i class="fas fa-check-circle"></i> Approved',
                                'confirmed' => '<i class="fas fa-check-circle"></i> Confirmed',
                                'rejected' => '<i class="fas fa-times-circle"></i> Declined',
                                'cancelled' => '<i class="fas fa-times-circle"></i> Cancelled'
                            ];
                            echo $statusLabels[$booking['booking_status']] ?? ucfirst($booking['booking_status']);
                            ?>
                        </span>
                    </div>

                    <div class="request-body">
                        <div class="request-detail">
                            <i class="fas fa-user"></i>
                            <strong>Customer:&nbsp;</strong> <?php echo htmlspecialchars($booking['customer_name'] ?? 'N/A'); ?>
                        </div>

                        <?php if (!empty($booking['customer_email'])): ?>
                            <div class="request-detail">
                                <i class="fas fa-envelope"></i>
                                <strong>Email:&nbsp;</strong> <?php echo htmlspecialchars($booking['customer_email']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($booking['customer_contact'])): ?>
                            <div class="request-detail">
                                <i class="fas fa-phone"></i>
                                <strong>Contact:&nbsp;</strong> <?php echo htmlspecialchars($booking['customer_contact']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($booking['event_date'])): ?>
                            <div class="request-detail">
                                <i class="fas fa-calendar"></i>
                                <strong>Event Date:&nbsp;</strong> <?php echo date('F j, Y', strtotime($booking['event_date'])); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($booking['event_location'])): ?>
                            <div class="request-detail">
                                <i class="fas fa-map-marker-alt"></i>
                                <strong>Location:&nbsp;</strong> <?php echo htmlspecialchars($booking['event_location']); ?>
                            </div>
                        <?php endif; ?>

                        <div class="request-detail">
                            <i class="fas fa-clock"></i>
                            <strong>Requested:&nbsp;</strong> <?php echo date('F j, Y g:i A', strtotime($booking['booking_date'])); ?>
                        </div>

                        <?php if (!empty($booking['notes'])): ?>
                            <div class="request-detail">
                                <i class="fas fa-sticky-note"></i>
                                <div>
                                    <strong>Customer Message:</strong><br>
                                    <?php echo nl2br(htmlspecialchars($booking['notes'])); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($booking['booking_status'] === 'pending'): ?>
                            <div class="request-actions">
                                <button class="btn btn-approve approve-btn" data-booking-id="<?php echo $booking['booking_id']; ?>">
                                    <i class="fas fa-check"></i> Approve Request
                                </button>
                                <button class="btn btn-reject reject-btn" data-booking-id="<?php echo $booking['booking_id']; ?>">
                                    <i class="fas fa-times"></i> Decline Request
                                </button>
                            </div>
                        <?php elseif (in_array($booking['booking_status'], ['approved', 'confirmed'])): ?>
                            <div class="alert alert-success mt-3 mb-0">
                                <i class="fas fa-check-circle"></i> <strong>Accepted!</strong> You've approved this service request.
                            </div>
                        <?php elseif (in_array($booking['booking_status'], ['rejected', 'cancelled'])): ?>
                            <div class="alert alert-danger mt-3 mb-0">
                                <i class="fas fa-times-circle"></i> <strong>Declined.</strong> This request was rejected.
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

    <script>
        // Approve booking
        $(document).on('click', '.approve-btn', function() {
            const bookingId = $(this).data('booking-id');

            Swal.fire({
                title: 'Approve this request?',
                text: "You're confirming to provide your services for this event.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, approve it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../actions/update_booking_status.php',
                        method: 'POST',
                        data: {
                            booking_id: bookingId,
                            status: 'confirmed'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Approved!', 'The booking request has been approved.', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                Swal.fire('Error!', response.message || 'Failed to approve request.', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                        }
                    });
                }
            });
        });

        // Reject booking
        $(document).on('click', '.reject-btn', function() {
            const bookingId = $(this).data('booking-id');

            Swal.fire({
                title: 'Decline this request?',
                text: "This action will decline the service request.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, decline it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../actions/update_booking_status.php',
                        method: 'POST',
                        data: {
                            booking_id: bookingId,
                            status: 'cancelled'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Declined', 'The booking request has been declined.', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                Swal.fire('Error!', response.message || 'Failed to decline request.', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                        }
                    });
                }
            });
        });
    </script>

<?php
$footer_base = '../';
include '../includes/footer.php';
?>

</body>
</html>
