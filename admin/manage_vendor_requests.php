<?php
require_once '../settings/core.php';

// Check if user is logged in and is an event manager (role=1)
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = getUserRole();

// Only event managers (role=1) can access this page
if ($user_role != 1) {
    header('Location: ../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vendor Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../settings/favicon.ico">

    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
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

        .page-header {
            text-align: center;
            padding: 120px 0 50px 0;
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

        .request-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .request-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }

        .request-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            gap: 1rem;
        }

        .event-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .vendor-name {
            font-size: 1.1rem;
            color: #4a5568;
            margin-bottom: 0.25rem;
        }

        .request-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            color: #6b7280;
            font-size: 0.9rem;
            margin: 1rem 0;
        }

        .request-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .request-meta-item i {
            color: #f97316;
        }

        .request-notes {
            background: #f9fafb;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            border-left: 4px solid #f97316;
        }

        .request-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
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

        .btn-deny {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-deny:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
            color: white;
        }

        .badge-pending {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            border: 2px solid #fbbf24;
        }

        .badge-approved {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            border: 2px solid #34d399;
        }

        .badge-denied {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            border: 2px solid #f87171;
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
            gap: 1rem;
            margin-bottom: 2rem;
            background: white;
            padding: 1rem;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .filter-tab {
            flex: 1;
            padding: 0.75rem 1.5rem;
            border: 2px solid #e2e8f0;
            border-radius: 50px;
            background: white;
            color: #4a5568;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .filter-tab:hover {
            border-color: #f97316;
            color: #f97316;
        }

        .filter-tab.active {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            border-color: #f97316;
            color: white;
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

    <div class="container" style="max-width: 1200px;">
        <div class="page-header">
            <h1><i class="fas fa-tasks"></i> Manage Vendor Requests</h1>
            <p class="text-muted">Review and approve vendor requests for your events</p>
        </div>

        <div class="filter-tabs">
            <div class="filter-tab active" data-filter="pending">
                <i class="fas fa-clock"></i> Pending
            </div>
            <div class="filter-tab" data-filter="approved">
                <i class="fas fa-check-circle"></i> Approved
            </div>
            <div class="filter-tab" data-filter="denied">
                <i class="fas fa-times-circle"></i> Denied
            </div>
            <div class="filter-tab" data-filter="all">
                <i class="fas fa-list"></i> All
            </div>
        </div>

        <div id="requestsContainer">
            <div class="text-center" style="padding: 3rem;">
                <p>Loading vendor requests...</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            let allRequests = [];
            let currentFilter = 'pending';

            loadVendorRequests();

            function loadVendorRequests() {
                $.ajax({
                    url: '../actions/fetch_event_manager_vendor_requests_action.php',
                    method: 'GET',
                    dataType: 'json'
                }).done(function(response) {
                    if (response.status === 'success' && response.data) {
                        allRequests = response.data;
                        renderRequests(currentFilter);
                    } else {
                        showEmptyState('No vendor requests found');
                    }
                }).fail(function() {
                    showEmptyState('Failed to load vendor requests');
                });
            }

            function renderRequests(filter) {
                const container = $('#requestsContainer');
                container.empty();

                let filtered = allRequests;
                if (filter !== 'all') {
                    filtered = allRequests.filter(req => {
                        if (filter === 'pending') return req.approved_by_event_manager == 0 && req.booking_status === 'pending';
                        if (filter === 'approved') return req.approved_by_event_manager == 1;
                        if (filter === 'denied') return req.booking_status === 'cancelled';
                        return true;
                    });
                }

                if (filtered.length === 0) {
                    showEmptyState(`No ${filter === 'all' ? '' : filter} vendor requests`);
                    return;
                }

                filtered.forEach(request => {
                    const card = createRequestCard(request);
                    container.append(card);
                });

                // Attach event handlers
                $('.btn-approve-request').on('click', function() {
                    const bookingId = $(this).data('booking-id');
                    approveRequest(bookingId);
                });

                $('.btn-deny-request').on('click', function() {
                    const bookingId = $(this).data('booking-id');
                    denyRequest(bookingId);
                });
            }

            function createRequestCard(request) {
                const isApproved = request.approved_by_event_manager == 1;
                const isDenied = request.booking_status === 'cancelled';
                const isPending = !isApproved && !isDenied;

                let statusBadge = '';
                let actionButtons = '';

                if (isApproved) {
                    statusBadge = '<span class="badge-approved"><i class="fas fa-check-circle"></i> Approved</span>';
                } else if (isDenied) {
                    statusBadge = '<span class="badge-denied"><i class="fas fa-times-circle"></i> Denied</span>';
                } else {
                    statusBadge = '<span class="badge-pending"><i class="fas fa-clock"></i> Pending Approval</span>';
                    actionButtons = `
                        <div class="request-actions">
                            <button class="btn btn-approve btn-approve-request" data-booking-id="${request.booking_id}">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="btn btn-deny btn-deny-request" data-booking-id="${request.booking_id}">
                                <i class="fas fa-times"></i> Deny
                            </button>
                        </div>
                    `;
                }

                const requestDate = request.booking_date ? new Date(request.booking_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'N/A';
                const eventDate = request.event_date ? new Date(request.event_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'Date TBA';

                return `
                    <div class="request-card">
                        <div class="request-header">
                            <div>
                                <div class="event-title">${escapeHtml(request.event_desc || 'Unnamed Event')}</div>
                                <div class="vendor-name"><i class="fas fa-briefcase"></i> ${escapeHtml(request.vendor_name || 'Unknown Vendor')}</div>
                            </div>
                            ${statusBadge}
                        </div>

                        <div class="request-meta">
                            <div class="request-meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Event Date: ${eventDate}</span>
                            </div>
                            <div class="request-meta-item">
                                <i class="fas fa-clock"></i>
                                <span>Requested: ${requestDate}</span>
                            </div>
                            ${request.event_location ? `
                                <div class="request-meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>${escapeHtml(request.event_location)}</span>
                                </div>
                            ` : ''}
                        </div>

                        ${request.notes ? `
                            <div class="request-notes">
                                <strong><i class="fas fa-comment"></i> Vendor Notes:</strong>
                                <p style="margin: 0.5rem 0 0 0;">${escapeHtml(request.notes)}</p>
                            </div>
                        ` : ''}

                        ${actionButtons}
                    </div>
                `;
            }

            function approveRequest(bookingId) {
                Swal.fire({
                    title: 'Approve Vendor Request?',
                    text: 'This vendor will be approved to provide services at your event.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Approve',
                    confirmButtonColor: '#10b981',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '../actions/approve_vendor_request_action.php',
                            method: 'POST',
                            data: { booking_id: bookingId },
                            dataType: 'json'
                        }).done(function(response) {
                            if (response && response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Approved!',
                                    text: 'Vendor request has been approved.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    loadVendorRequests();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed',
                                    text: response.message || 'Failed to approve request'
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
            }

            function denyRequest(bookingId) {
                Swal.fire({
                    title: 'Deny Vendor Request?',
                    text: 'This vendor request will be denied.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Deny',
                    confirmButtonColor: '#ef4444',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '../actions/deny_vendor_request_action.php',
                            method: 'POST',
                            data: { booking_id: bookingId },
                            dataType: 'json'
                        }).done(function(response) {
                            if (response && response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Denied',
                                    text: 'Vendor request has been denied.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    loadVendorRequests();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed',
                                    text: response.message || 'Failed to deny request'
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
            }

            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function showEmptyState(message) {
                $('#requestsContainer').html(`
                    <div class="empty-state">
                        <i class="fas fa-clipboard-list"></i>
                        <h2>${message}</h2>
                        <p class="text-muted">Vendor requests will appear here</p>
                    </div>
                `);
            }

            // Filter tabs
            $('.filter-tab').on('click', function() {
                $('.filter-tab').removeClass('active');
                $(this).addClass('active');
                currentFilter = $(this).data('filter');
                renderRequests(currentFilter);
            });
        });
    </script>

<?php
$footer_base = '../';
include '../includes/footer.php';
?>
</body>
</html>
