<?php
require_once '../settings/core.php';
require_once '../controllers/vendor_booking_controller.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = getUserRole();

// Get bookings based on user role
if ($user_role == 2) {
    // Vendor: show bookings where they are the vendor
    $bookings = get_vendor_bookings_ctr($user_id);
    $pageTitle = 'Bookings for My Services';
} else {
    // Customer: show bookings they made
    $bookings = get_customer_bookings_ctr($user_id);
    $pageTitle = 'My Vendor Bookings';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Eventify</title>
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

        .booking-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.15);
        }

        .booking-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .booking-body {
            padding: 1.5rem;
        }

        .booking-status {
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

        .status-completed {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .booking-detail {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            color: #4a5568;
        }

        .booking-detail i {
            width: 30px;
            color: #667eea;
        }

        .booking-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-action {
            flex: 1;
            min-width: 120px;
            border-radius: 50px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
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
            <h1><i class="fas fa-calendar-check"></i> <?php echo htmlspecialchars($pageTitle); ?></h1>
            <p class="text-muted">
                <?php if ($user_role == 2): ?>
                    Manage bookings from customers for your services
                <?php else: ?>
                    View and manage your vendor bookings
                <?php endif; ?>
            </p>
        </div>

        <?php
        // Filter out cancelled bookings - only show pending, confirmed, and completed
        $active_bookings = array_filter($bookings, function($booking) {
            return in_array($booking['booking_status'], ['pending', 'confirmed', 'completed']);
        });
        ?>

        <?php if (empty($active_bookings)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h2>No Active Bookings</h2>
                <p class="text-muted">
                    <?php if ($user_role == 2): ?>
                        You don't have any active bookings from customers yet.
                    <?php else: ?>
                        You don't have any active bookings. <a href="browse_vendors.php">Browse vendors</a> to get started!
                    <?php endif; ?>
                </p>
            </div>
        <?php else: ?>
            <?php foreach ($active_bookings as $booking): ?>
                <div class="booking-card">
                    <div class="booking-header">
                        <div>
                            <h4 class="mb-0">
                                <?php if ($user_role == 2): ?>
                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($booking['customer_name'] ?? 'Customer'); ?>
                                <?php else: ?>
                                    <i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($booking['vendor_name'] ?? 'Vendor'); ?>
                                <?php endif; ?>
                            </h4>
                            <small><?php echo htmlspecialchars($booking['vendor_type'] ?? ''); ?></small>
                        </div>
                        <span class="booking-status status-<?php echo htmlspecialchars($booking['booking_status']); ?>">
                            <?php echo ucfirst(htmlspecialchars($booking['booking_status'])); ?>
                        </span>
                    </div>

                    <div class="booking-body">
                        <div class="booking-detail">
                            <i class="fas fa-calendar-day"></i>
                            <strong>Event:&nbsp;</strong> <?php echo htmlspecialchars($booking['event_desc'] ?? 'N/A'); ?>
                        </div>

                        <?php if (!empty($booking['event_date'])): ?>
                            <div class="booking-detail">
                                <i class="fas fa-calendar"></i>
                                <strong>Event Date:&nbsp;</strong> <?php echo date('F j, Y', strtotime($booking['event_date'])); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($booking['event_location'])): ?>
                            <div class="booking-detail">
                                <i class="fas fa-map-marker-alt"></i>
                                <strong>Location:&nbsp;</strong> <?php echo htmlspecialchars($booking['event_location']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($booking['service_date'])): ?>
                            <div class="booking-detail">
                                <i class="fas fa-clock"></i>
                                <strong>Service Date:&nbsp;</strong> <?php echo date('F j, Y', strtotime($booking['service_date'])); ?>
                            </div>
                        <?php endif; ?>

                        <div class="booking-detail">
                            <i class="fas fa-info-circle"></i>
                            <strong>Booking Date:&nbsp;</strong> <?php echo date('F j, Y g:i A', strtotime($booking['booking_date'])); ?>
                        </div>

                        <?php if ($user_role != 2): ?>
                            <div class="booking-detail">
                                <i class="fas fa-envelope"></i>
                                <?php echo htmlspecialchars($booking['vendor_email'] ?? 'N/A'); ?>
                            </div>
                            <div class="booking-detail">
                                <i class="fas fa-phone"></i>
                                <?php echo htmlspecialchars($booking['vendor_contact'] ?? 'N/A'); ?>
                            </div>
                        <?php else: ?>
                            <div class="booking-detail">
                                <i class="fas fa-envelope"></i>
                                <?php echo htmlspecialchars($booking['customer_email'] ?? 'N/A'); ?>
                            </div>
                            <div class="booking-detail">
                                <i class="fas fa-phone"></i>
                                <?php echo htmlspecialchars($booking['customer_contact'] ?? 'N/A'); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($booking['notes'])): ?>
                            <div class="booking-detail">
                                <i class="fas fa-sticky-note"></i>
                                <strong>Notes:&nbsp;</strong> <?php echo htmlspecialchars($booking['notes']); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Action buttons for vendor (role=2) -->
                        <?php if ($user_role == 2 && $booking['booking_status'] === 'pending'): ?>
                            <div class="booking-actions">
                                <button class="btn btn-success btn-action confirm-booking" data-booking-id="<?php echo $booking['booking_id']; ?>">
                                    <i class="fas fa-check"></i> Confirm
                                </button>
                                <button class="btn btn-danger btn-action cancel-booking" data-booking-id="<?php echo $booking['booking_id']; ?>">
                                    <i class="fas fa-times"></i> Decline
                                </button>
                            </div>
                        <?php elseif ($user_role == 2 && $booking['booking_status'] === 'confirmed'): ?>
                            <div class="booking-actions">
                                <button class="btn btn-primary btn-action complete-booking" data-booking-id="<?php echo $booking['booking_id']; ?>">
                                    <i class="fas fa-check-double"></i> Mark as Completed
                                </button>
                            </div>
                        <?php endif; ?>

                        <!-- Action buttons for customer -->
                        <?php if ($user_role != 2 && $booking['booking_status'] === 'pending'): ?>
                            <div class="booking-actions">
                                <button class="btn btn-danger btn-action cancel-booking" data-booking-id="<?php echo $booking['booking_id']; ?>">
                                    <i class="fas fa-times"></i> Cancel Booking
                                </button>
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
        $(document).ready(function() {
            // Confirm booking
            $('.confirm-booking').on('click', function() {
                const bookingId = $(this).data('booking-id');
                updateBookingStatus(bookingId, 'confirmed', 'Booking confirmed successfully!');
            });

            // Complete booking
            $('.complete-booking').on('click', function() {
                const bookingId = $(this).data('booking-id');
                updateBookingStatus(bookingId, 'completed', 'Booking marked as completed!');
            });

            // Cancel booking
            $('.cancel-booking').on('click', function() {
                const bookingId = $(this).data('booking-id');
                Swal.fire({
                    title: 'Cancel Booking?',
                    text: 'Are you sure you want to cancel this booking?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, cancel it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateBookingStatus(bookingId, 'cancelled', 'Booking cancelled successfully!');
                    }
                });
            });

            function updateBookingStatus(bookingId, status, successMessage) {
                $.ajax({
                    url: '../actions/update_booking_status_action.php',
                    method: 'POST',
                    data: {
                        booking_id: bookingId,
                        status: status
                    },
                    dataType: 'json'
                }).done(function(response) {
                    if (response && response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: successMessage,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to update booking status'
                        });
                    }
                }).fail(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Request failed. Please try again.'
                    });
                });
            }
        });
    </script>

<?php
$footer_base = '../';
include '../includes/footer.php';
?>
</body>
</html>
