<?php
require_once '../settings/core.php';
require_once '../controllers/event_controller.php';
require_once '../controllers/vendor_controller.php';
require_once '../settings/db_class.php';

// Check if user is logged in
$logged_in_user_id = getUserID();
$logged_in_user_role = getUserRole();

// Accept either ?vendor_id=<id> (from vendor table) or ?customer_id=<id>
$vendor_id = isset($_GET['vendor_id']) ? (int)$_GET['vendor_id'] : 0;
$customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : 0;
$vendor = null;
$customer = null;
$vendor_customer_id = 0; // The actual customer_id of the vendor

// Resolve customer from vendor_id when vendor_id provided
if ($vendor_id > 0 && $customer_id <= 0) {
    $db = new db_connection();
    $conn = $db->db_conn();
    if ($conn) {
        $vid = (int)$vendor_id;
        $vrow = $db->db_fetch_one("SELECT * FROM eventify_vendor WHERE vendor_id = $vid LIMIT 1");
        if ($vrow) {
            $vendor = $vrow;
            $vdesc = isset($vrow['vendor_desc']) ? mysqli_real_escape_string($conn, $vrow['vendor_desc']) : '';
            if ($vdesc !== '') {
                $crow = $db->db_fetch_one("SELECT * FROM eventify_customer WHERE customer_name = '$vdesc' LIMIT 1");
                if ($crow) {
                    $customer = $crow;
                    $customer_id = (int)$crow['customer_id'];
                    $vendor_customer_id = $customer_id;
                }
            }
        }
    }
}

// If we have a customer_id (explicit or resolved), fetch vendor via controller
if ($customer_id > 0) {
    $vendorData = fetch_vendor_ctr($customer_id);
    if ($vendorData && isset($vendorData['customer'])) {
        $customer = $vendorData['customer'];
        $vendor = $vendorData['vendor'] ?? $vendor;
        $vendor_customer_id = $customer_id;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Vendor - Eventify</title>
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

        .vendor-header {
            text-align: center;
            margin-bottom: 3rem;
            padding-top: 40px;
        }

        .vendor-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .vendor-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .vendor-image-container {
            position: relative;
            height: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .vendor-image {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .vendor-info {
            padding: 2rem;
            text-align: center;
        }

        .vendor-name {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .vendor-type {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .vendor-contact-info {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #4a5568;
        }

        .contact-item i {
            color: #667eea;
        }

        .booking-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 2rem;
        }

        .booking-card h3 {
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-book {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
</head>

<body>
    <header class="menu-tray">
        <a href="home.php" class="logo">
                <img src="../settings/logo.png" alt="eventify logo" style="height:30px;">
         </a> 
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="../index.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-home"></i> Home</a>
            <a href="browse_vendors.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back</a>
            <a href="../login/logout.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a href="../login/register.php" class="btn btn-sm btn-outline-primary">Register</a>
            <a href="../login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
        <?php endif; ?>
    </header>

    

    <div class="container">
        <div class="vendor-header">
            <h1><i class="fas fa-calendar-check"></i> Book Your Vendor</h1>
            <p class="text-muted">Select one of your events to book this vendor</p>
        </div>

        <?php if (!$customer && !$vendor): ?>
            <div class="alert alert-warning text-center">
                <i class="fas fa-exclamation-triangle"></i> Vendor not found.
            </div>
        <?php else: ?>
            <?php
                // Determine image URL
                $imgUrl = '';
                $imgField = '';
                if (!empty($customer['customer_image'])) $imgField = $customer['customer_image'];
                elseif (!empty($vendor['vendor_image'])) $imgField = $vendor['vendor_image'];

                if (!empty($imgField)) {
                    // Remove any /vendor/ prefix if it exists
                    $imgField = str_replace('/vendor/uploads/', 'uploads/', $imgField);
                    $imgField = str_replace('vendor/uploads/', 'uploads/', $imgField);

                    if (strpos($imgField, 'uploads/') === 0) {
                        $imgUrl = '../' . $imgField;
                    } elseif (strpos($imgField, '/uploads/') !== false) {
                        // Extract just the filename and use correct path
                        $imgUrl = '../uploads/' . basename($imgField);
                    } else {
                        $imgUrl = '../uploads/' . basename($imgField);
                    }
                } else {
                    $imgUrl = '../uploads/no-image.svg';
                }
            ?>

            <div class="row">
                <div class="col-md-5 mb-4">
                    <div class="vendor-card">
                        <div class="vendor-image-container">
                            <img src="<?php echo htmlspecialchars($imgUrl); ?>"
                                 class="vendor-image"
                                 alt="Vendor Image"
                                 onerror="this.src='../uploads/no-image.svg'">
                        </div>
                        <div class="vendor-info">
                            <h2 class="vendor-name">
                                <?php echo htmlspecialchars($customer['customer_name'] ?? ($vendor['vendor_desc'] ?? 'Vendor')); ?>
                            </h2>
                            <?php if (!empty($vendor['vendor_type'])): ?>
                                <span class="vendor-type">
                                    <i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($vendor['vendor_type']); ?>
                                </span>
                            <?php endif; ?>

                            <div class="vendor-contact-info">
                                <?php if (!empty($customer['customer_email'])): ?>
                                    <div class="contact-item">
                                        <i class="fas fa-envelope"></i>
                                        <span><?php echo htmlspecialchars($customer['customer_email']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($customer['customer_contact'])): ?>
                                    <div class="contact-item">
                                        <i class="fas fa-phone"></i>
                                        <span><?php echo htmlspecialchars($customer['customer_contact']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="booking-card">
                        <h3><i class="fas fa-calendar-plus"></i> Create Booking</h3>

                        <?php if (!$logged_in_user_id): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Please <a href="../login/login.php">login</a> to book this vendor.
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-4">Select one of your events to book this vendor for your event.</p>

                            <form id="bookingForm">
                                <input type="hidden" id="vendorCustomerId" value="<?php echo $vendor_customer_id; ?>">

                                <div class="mb-4">
                                    <label for="customerEvents" class="form-label">
                                        <i class="fas fa-calendar"></i> Select Your Event
                                    </label>
                                    <select id="customerEvents" class="form-select" required>
                                        <option value="">-- Loading your events... --</option>
                                    </select>
                                    <small class="form-text text-muted">Choose which event you want to book this vendor for</small>
                                </div>

                                <div class="mb-4">
                                    <label for="serviceDate" class="form-label">
                                        <i class="fas fa-calendar-day"></i> Service Date (Optional)
                                    </label>
                                    <input type="date" id="serviceDate" class="form-control">
                                    <small class="form-text text-muted">When do you need the vendor's services?</small>
                                </div>

                                <div class="mb-4">
                                    <label for="bookingNotes" class="form-label">
                                        <i class="fas fa-sticky-note"></i> Additional Notes (Optional)
                                    </label>
                                    <textarea id="bookingNotes" class="form-control" rows="3"
                                              placeholder="Any special requirements or details..."></textarea>
                                </div>

                                <button type="submit" id="bookVendorBtn" class="btn btn-book">
                                    <i class="fas fa-check-circle"></i> Book This Vendor
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Load customer's events
            loadCustomerEvents();

            function loadCustomerEvents() {
                $.ajax({
                    url: '../actions/fetch_event_action.php?action=by_customer',
                    method: 'GET',
                    dataType: 'json'
                }).done(function(response) {
                    console.log('Customer events response:', response);
                    const eventSelect = $('#customerEvents');
                    eventSelect.html('<option value="">-- Select an event --</option>');

                    // Handle response format
                    let events = [];
                    if (response && response.status === 'success' && Array.isArray(response.data)) {
                        events = response.data;
                    } else if (Array.isArray(response)) {
                        events = response;
                    }

                    if (events.length > 0) {
                        events.forEach(function(event) {
                            const eventName = event.event_desc || 'Unnamed Event';
                            const eventDate = event.event_date ? ' (' + event.event_date + ')' : '';
                            eventSelect.append(
                                `<option value="${event.event_id}">${eventName}${eventDate}</option>`
                            );
                        });
                    } else {
                        eventSelect.html('<option value="">-- No events found. Create an event first. --</option>');
                    }
                }).fail(function(xhr, status, error) {
                    console.error('Failed to load customer events:', xhr.responseText);
                    $('#customerEvents').html('<option value="">-- Failed to load events --</option>');
                });
            }

            $('#bookingForm').on('submit', function(e) {
                e.preventDefault();

                const vendorId = parseInt($('#vendorCustomerId').val(), 10);
                const eventId = parseInt($('#customerEvents').val(), 10);
                const serviceDate = $('#serviceDate').val();
                const notes = $('#bookingNotes').val().trim();

                if (!eventId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Event Selected',
                        text: 'Please select an event for this booking.'
                    });
                    return;
                }

                if (!vendorId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Invalid vendor information.'
                    });
                    return;
                }

                // Create the booking
                $.ajax({
                    url: '../actions/add_vendor_booking_action.php',
                    method: 'POST',
                    data: {
                        vendor_id: vendorId,
                        event_id: eventId,
                        service_date: serviceDate,
                        notes: notes
                    },
                    dataType: 'json'
                }).done(function(response) {
                    if (response && response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Booking Created!',
                            text: response.message || 'Vendor has been booked successfully.',
                            showConfirmButton: true,
                            confirmButtonText: 'View My Bookings'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '../admin/my_bookings.php';
                            }
                        });

                        // Reset form
                        $('#bookingForm')[0].reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Booking Failed',
                            text: response.message || 'Failed to create booking. Please try again.'
                        });
                    }
                }).fail(function(xhr, status, err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Request failed: ' + status
                    });
                });
            });
        });
    </script>

<?php
$footer_base = '../';
include '../includes/footer.php';
?>
</body>
</html>
