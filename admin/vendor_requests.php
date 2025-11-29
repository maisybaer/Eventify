<?php
require_once '../settings/core.php';
require_once '../classes/vendor_booking_class.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = getUserRole();

// Get vendor requests for this event manager's events
$bookingClass = new VendorBooking();
$vendor_requests = $bookingClass->getEventManagerVendorRequests($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Requests</title>
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
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .request-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.15);
        }

        .request-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .vendor-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .vendor-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
        }

        .vendor-details h4 {
            margin: 0;
            font-size: 1.3rem;
        }

        .vendor-details small {
            opacity: 0.9;
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

        .request-actions {
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

        .filter-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            border: 2px solid #e2e8f0;
            background: white;
            color: #4a5568;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-tab:hover {
            border-color: #667eea;
            color: #667eea;
        }

        .filter-tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
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
            <h1><i class="fas fa-user-check"></i> Vendor Requests</h1>
            <p class="text-muted">Review and approve vendors who want to participate in your events</p>
        </div>

        <?php
        // Calculate stats
        $total_requests = count($vendor_requests);
        $pending_count = count(array_filter($vendor_requests, fn($r) => $r['booking_status'] === 'pending'));
        $approved_count = count(array_filter($vendor_requests, fn($r) => $r['booking_status'] === 'confirmed'));
        $rejected_count = count(array_filter($vendor_requests, fn($r) => $r['booking_status'] === 'cancelled'));
        ?>

        <div class="stats-grid">
            <div class="stat-card pending">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-value"><?php echo $pending_count; ?></div>
                <div class="stat-label">Pending Requests</div>
            </div>
            <div class="stat-card approved">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-value"><?php echo $approved_count; ?></div>
                <div class="stat-label">Approved</div>
            </div>
            <div class="stat-card rejected">
                <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                <div class="stat-value"><?php echo $rejected_count; ?></div>
                <div class="stat-label">Rejected</div>
            </div>
        </div>

        <div class="filter-tabs">
            <button class="filter-tab active" data-filter="all">All Requests (<?php echo $total_requests; ?>)</button>
            <button class="filter-tab" data-filter="pending">Pending (<?php echo $pending_count; ?>)</button>
            <button class="filter-tab" data-filter="confirmed">Approved (<?php echo $approved_count; ?>)</button>
            <button class="filter-tab" data-filter="cancelled">Rejected (<?php echo $rejected_count; ?>)</button>
        </div>

        <div id="requestsContainer">
            <?php if (empty($vendor_requests)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h2>No Vendor Requests Yet</h2>
                    <p class="text-muted">When vendors request to participate in your events, they will appear here.</p>
                </div>
            <?php else: ?>
                <?php foreach ($vendor_requests as $request): ?>
                    <div class="request-card" data-status="<?php echo htmlspecialchars($request['booking_status']); ?>">
                        <div class="request-header">
                            <div class="vendor-info">
                                <?php
                                $vendorImg = !empty($request['vendor_image']) ?
                                    (strpos($request['vendor_image'], 'uploads/') === 0 ? '../' . $request['vendor_image'] : '../uploads/' . basename($request['vendor_image'])) :
                                    '../uploads/no-image.svg';
                                ?>
                                <img src="<?php echo htmlspecialchars($vendorImg); ?>"
                                     class="vendor-avatar"
                                     alt="Vendor"
                                     onerror="this.src='../uploads/no-image.svg'">
                                <div class="vendor-details">
                                    <h4><?php echo htmlspecialchars($request['vendor_name'] ?? 'Vendor'); ?></h4>
                                    <small><?php echo htmlspecialchars($request['vendor_type'] ?? 'Service Provider'); ?></small>
                                </div>
                            </div>
                            <span class="request-status status-<?php echo htmlspecialchars($request['booking_status']); ?>">
                                <?php
                                $statusLabels = [
                                    'pending' => '<i class="fas fa-clock"></i> Pending Review',
                                    'confirmed' => '<i class="fas fa-check-circle"></i> Approved',
                                    'cancelled' => '<i class="fas fa-times-circle"></i> Rejected'
                                ];
                                echo $statusLabels[$request['booking_status']] ?? ucfirst($request['booking_status']);
                                ?>
                            </span>
                        </div>

                        <div class="request-body">
                            <div class="request-detail">
                                <i class="fas fa-calendar-day"></i>
                                <strong>Event:&nbsp;</strong> <?php echo htmlspecialchars($request['event_desc'] ?? 'N/A'); ?>
                            </div>

                            <?php if (!empty($request['event_date'])): ?>
                                <div class="request-detail">
                                    <i class="fas fa-calendar"></i>
                                    <strong>Date:&nbsp;</strong> <?php echo date('F j, Y', strtotime($request['event_date'])); ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($request['event_location'])): ?>
                                <div class="request-detail">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <strong>Location:&nbsp;</strong> <?php echo htmlspecialchars($request['event_location']); ?>
                                </div>
                            <?php endif; ?>

                            <div class="request-detail">
                                <i class="fas fa-envelope"></i>
                                <?php echo htmlspecialchars($request['vendor_email'] ?? 'N/A'); ?>
                            </div>

                            <div class="request-detail">
                                <i class="fas fa-phone"></i>
                                <?php echo htmlspecialchars($request['vendor_contact'] ?? 'N/A'); ?>
                            </div>

                            <div class="request-detail">
                                <i class="fas fa-clock"></i>
                                <strong>Requested:&nbsp;</strong> <?php echo date('F j, Y g:i A', strtotime($request['booking_date'])); ?>
                            </div>

                            <?php if (!empty($request['notes'])): ?>
                                <div class="request-detail">
                                    <i class="fas fa-sticky-note"></i>
                                    <div>
                                        <strong>Vendor's Message:</strong><br>
                                        <?php echo nl2br(htmlspecialchars($request['notes'])); ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($request['booking_status'] === 'pending'): ?>
                                <div class="request-actions">
                                    <button class="btn btn-success btn-action approve-request" data-booking-id="<?php echo $request['booking_id']; ?>">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button class="btn btn-danger btn-action reject-request" data-booking-id="<?php echo $request['booking_id']; ?>">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </div>
                            <?php elseif ($request['booking_status'] === 'confirmed' && !empty($request['event_manager_approved_date'])): ?>
                                <div class="alert alert-success mt-3 mb-0">
                                    <i class="fas fa-check-circle"></i> Approved on <?php echo date('F j, Y', strtotime($request['event_manager_approved_date'])); ?>
                                </div>
                            <?php elseif ($request['booking_status'] === 'cancelled' && !empty($request['event_manager_approved_date'])): ?>
                                <div class="alert alert-danger mt-3 mb-0">
                                    <i class="fas fa-times-circle"></i> Rejected on <?php echo date('F j, Y', strtotime($request['event_manager_approved_date'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Filter tabs
            $('.filter-tab').on('click', function() {
                $('.filter-tab').removeClass('active');
                $(this).addClass('active');

                const filter = $(this).data('filter');

                if (filter === 'all') {
                    $('.request-card').show();
                } else {
                    $('.request-card').hide();
                    $(`.request-card[data-status="${filter}"]`).show();
                }
            });

            // Approve request
            $('.approve-request').on('click', function() {
                const bookingId = $(this).data('booking-id');

                Swal.fire({
                    title: 'Approve Vendor Request?',
                    text: 'This vendor will be approved to participate in your event.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, approve'
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateRequestStatus(bookingId, true);
                    }
                });
            });

            // Reject request
            $('.reject-request').on('click', function() {
                const bookingId = $(this).data('booking-id');

                Swal.fire({
                    title: 'Reject Vendor Request?',
                    text: 'This vendor will not be able to participate in your event.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, reject'
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateRequestStatus(bookingId, false);
                    }
                });
            });

            function updateRequestStatus(bookingId, approved) {
                $.ajax({
                    url: '../actions/update_vendor_booking_action.php',
                    method: 'POST',
                    data: {
                        booking_id: bookingId,
                        approved: approved ? 1 : 0
                    },
                    dataType: 'json'
                }).done(function(response) {
                    if (response && response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: approved ? 'Approved!' : 'Rejected',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to update request status'
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
