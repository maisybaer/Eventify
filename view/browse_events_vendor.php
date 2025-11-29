<?php
require_once '../settings/core.php';
require_once '../controllers/event_controller.php';

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../settings/favicon.ico">

    <style>
        body {
            background-color: #f8f9fa;
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
            padding-top: 100px;
        }

        .header-container h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .search-tray {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 3rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .search-tray input,
        .search-tray select {
            flex: 1;
            min-width: 200px;
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .search-tray input:focus,
        .search-tray select:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .event-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .event-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .event-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        .event-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .event-body {
            padding: 1.5rem;
        }

        .event-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.75rem;
        }

        .event-detail {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #4a5568;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .event-detail i {
            color: #667eea;
            width: 20px;
        }

        .event-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
            margin: 1rem 0;
        }

        .btn-request-vendor {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-request-vendor:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-request-vendor:disabled {
            background: #cbd5e0;
            cursor: not-allowed;
            transform: none;
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

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-approved {
            background: #d1fae5;
            color: #065f46;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
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
            <h1><i class="fas fa-calendar-alt"></i> Browse Events</h1>
            <p class="text-muted">Find events where you can offer your vendor services</p>
        </div>

        <div class="search-tray">
            <input type="text" id="searchBox" placeholder="🔍 Search events...">
            <select id="categoryFilter">
                <option value="">All Categories</option>
            </select>
        </div>

        <div class="event-grid" id="eventGrid">
            <div class="text-center" style="grid-column: 1/-1;">
                <p>Loading events...</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            let allEvents = [];
            let existingBookings = [];

            // Load all events
            loadEvents();
            loadExistingBookings();

            function loadEvents() {
                $.ajax({
                    url: '../actions/fetch_event_action.php?action=all',
                    method: 'GET',
                    dataType: 'json'
                }).done(function(response) {
                    console.log('fetch_all_events response:', response);

                    if (Array.isArray(response)) {
                        allEvents = response;
                    } else if (response && response.status === 'success' && response.data) {
                        allEvents = response.data;
                    } else {
                        allEvents = [];
                    }

                    if (!allEvents || allEvents.length === 0) {
                        showEmptyState('No events available');
                        return;
                    }

                    populateCategories(allEvents);
                    renderEvents(allEvents);
                }).fail(function() {
                    showEmptyState('Failed to load events');
                });
            }

            function loadExistingBookings() {
                $.ajax({
                    url: '../actions/fetch_vendor_booking_action.php?action=vendor_requests',
                    method: 'GET',
                    dataType: 'json'
                }).done(function(response) {
                    console.log('fetch_vendor_event_requests response:', response);

                    if (Array.isArray(response)) {
                        existingBookings = response;
                    } else if (response && response.status === 'success' && response.data) {
                        existingBookings = response.data;
                    } else {
                        existingBookings = [];
                    }

                    renderEvents(allEvents); // Re-render with booking status
                });
            }

            function populateCategories(events) {
                const categories = {};
                events.forEach(event => {
                    if (event.cat_id && event.category) {
                        categories[event.cat_id] = event.category;
                    }
                });

                const categoryFilter = $('#categoryFilter');
                Object.keys(categories).forEach(catId => {
                    categoryFilter.append(`<option value="${catId}">${categories[catId]}</option>`);
                });
            }

            function renderEvents(events) {
                const grid = $('#eventGrid');
                grid.empty();

                if (!events || events.length === 0) {
                    showEmptyState('No events found');
                    return;
                }

                events.forEach(event => {
                    const imgSrc = getImageUrl(event.flyer);
                    const eventDate = event.event_date ? new Date(event.event_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'Date TBA';
                    const price = event.event_price ? `GHS ${parseFloat(event.event_price).toFixed(2)}` : 'Free';

                    // Check if vendor already requested for this event
                    const existingBooking = existingBookings.find(b => b.event_id == event.event_id);
                    let bookingBadge = '';
                    let buttonDisabled = '';
                    let buttonText = '<i class="fas fa-hand-paper"></i> Request to Vendor';

                    if (existingBooking) {
                        if (existingBooking.approved_by_event_manager == 1) {
                            bookingBadge = '<span class="badge-approved"><i class="fas fa-check-circle"></i> Approved</span>';
                            buttonDisabled = 'disabled';
                            buttonText = '<i class="fas fa-check"></i> Already Approved';
                        } else {
                            bookingBadge = '<span class="badge-pending"><i class="fas fa-clock"></i> Pending Approval</span>';
                            buttonDisabled = 'disabled';
                            buttonText = '<i class="fas fa-clock"></i> Request Pending';
                        }
                    }

                    const card = `
                        <div class="event-card">
                            <img src="${imgSrc}" class="event-image" alt="${escapeHtml(event.event_desc)}" onerror="this.style.display='none'">
                            <div class="event-body">
                                <div class="event-title">${escapeHtml(event.event_desc || 'Unnamed Event')}</div>
                                ${bookingBadge}
                                <div class="event-detail">
                                    <i class="fas fa-calendar-day"></i>
                                    <span>${eventDate}</span>
                                </div>
                                ${event.event_location ? `
                                    <div class="event-detail">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>${escapeHtml(event.event_location)}</span>
                                    </div>
                                ` : ''}
                                ${event.event_start || event.event_end ? `
                                    <div class="event-detail">
                                        <i class="fas fa-clock"></i>
                                        <span>${event.event_start || ''} ${event.event_end ? '- ' + event.event_end : ''}</span>
                                    </div>
                                ` : ''}
                                <div class="event-price">${price}</div>
                                <div class="d-flex gap-2">
                                    <a href="single_event.php?event_id=${encodeURIComponent(event.event_id)}" class="btn btn-sm btn-outline-primary">View Details</a>
                                    <button class="btn btn-request-vendor" data-event-id="${event.event_id}" ${buttonDisabled}>
                                        ${buttonText}
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    grid.append(card);
                });

                // Attach click handlers
                $('.btn-request-vendor:not(:disabled)').on('click', function() {
                    const eventId = $(this).data('event-id');
                    requestToVendor(eventId);
                });
            }

            function requestToVendor(eventId) {
                Swal.fire({
                    title: 'Request to Vendor',
                    html: `
                        <p>Request to provide vendor services for this event</p>
                        <textarea id="vendorNotes" class="swal2-textarea" placeholder="Brief description of your services (optional)..." rows="4"></textarea>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Send Request',
                    confirmButtonColor: '#667eea',
                    preConfirm: () => {
                        return {
                            notes: document.getElementById('vendorNotes').value
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const notes = result.value.notes;

                        $.ajax({
                            url: '../actions/vendor_request_event_action.php',
                            method: 'POST',
                            data: {
                                event_id: eventId,
                                notes: notes
                            },
                            dataType: 'json'
                        }).done(function(response) {
                            if (response && response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Request Sent!',
                                    text: 'Your vendor request has been sent to the event manager.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    loadExistingBookings();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed',
                                    text: response.message || 'Failed to send request'
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

            function getImageUrl(flyer) {
                if (!flyer) return '../uploads/no-image.svg';
                if (flyer.indexOf('uploads/') === 0) return '../' + flyer;
                if (flyer.indexOf('/uploads/') !== -1) return flyer;
                return '../uploads/' + flyer;
            }

            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function showEmptyState(message) {
                $('#eventGrid').html(`
                    <div class="empty-state" style="grid-column: 1/-1;">
                        <i class="fas fa-calendar-times"></i>
                        <h2>${message}</h2>
                        <p class="text-muted">Check back later for new events</p>
                    </div>
                `);
            }

            // Search and filter
            $('#searchBox').on('input', applyFilters);
            $('#categoryFilter').on('change', applyFilters);

            function applyFilters() {
                const query = $('#searchBox').val().toLowerCase();
                const category = $('#categoryFilter').val();

                const filtered = allEvents.filter(event => {
                    if (category && event.cat_id != category) return false;
                    if (query) {
                        const searchText = (event.event_desc || '') + ' ' + (event.category || '') + ' ' + (event.event_location || '');
                        return searchText.toLowerCase().indexOf(query) !== -1;
                    }
                    return true;
                });

                renderEvents(filtered);
            }
        });
    </script>

<?php
$footer_base = '../';
include '../includes/footer.php';
?>
</body>
</html>
