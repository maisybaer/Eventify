<?php
require_once '../settings/core.php';
require_once '../controllers/event_controller.php';
require_once '../controllers/vendor_controller.php';
require_once '../settings/db_class.php';

// Accept either ?vendor_id=<id> (from vendor table) or ?customer_id=<id>
$vendor_id = isset($_GET['vendor_id']) ? (int)$_GET['vendor_id'] : 0;
$customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : 0;
$vendor = null;
$customer = null;
$events = [];

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
                }
            }
        }
    }
}

// If we have a customer_id (explicit or resolved), fetch vendor via controller and events
if ($customer_id > 0) {
    $vendorData = fetch_vendor_ctr($customer_id);
    if ($vendorData && isset($vendorData['customer'])) {
        $customer = $vendorData['customer'];
        $vendor = $vendorData['vendor'] ?? $vendor;
    }

    // fetch events added by this customer
    $events = get_event_ctr($customer_id) ?: [];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=1.1">
    
    <style>
        body { background-color: #f8f9fa; }
        .product-container { padding-top: 100px; }
        .card { max-width: 500px; margin: auto; }
        .event-image { max-width: 100%; border-radius: 10px; }
        .menu-tray {
            position: fixed; top: 16px; right: 16px;
            background: rgba(255,255,255,0.95);
            border: 1px solid #e9e9e9;
            border-radius: 10px; padding: 6px 10px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
            z-index: 1200;
        }
    </style>
    
    </style>

</head>

<body>

    <header class="menu-tray mb-3">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="../index.php" class="btn btn-sm btn-outline-secondary">Home</a>
            <a href="../login/logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>
            <a href="all_event.php" class="btn btn-sm btn-outline-secondary">Back</a>
        <?php else: ?>
            <a href="../login/register.php" class="btn btn-sm btn-outline-primary">Register</a>
            <a href="../login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
        <?php endif; ?>
    </header>

    <div>
        <div class="container" style="padding-top:120px;">

            <div class="text-center">
                
                <h1>Book you vendor</h1>

            </div>
        </div>
    </div>


    <main class="container event-container">
        <?php if (!$customer && !$vendor): ?>
            <div class="alert alert-warning">Vendor not found.</div>
        <?php else: ?>
            <?php
                // Determine image URL (prefer customer_image, fallback to vendor image or no-image)
                $imgUrl = '';
                $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
                $siteBase = dirname($scriptDir);
                if ($siteBase === '/' || $siteBase === '.') $siteBase = '';

                $imgField = '';
                if (!empty($customer['customer_image'])) $imgField = $customer['customer_image'];
                elseif (!empty($vendor['vendor_image'])) $imgField = $vendor['vendor_image'];

                if (!empty($imgField)) {
                    $filename = basename($imgField);
                    $fs = realpath(__DIR__ . '/../uploads/' . $filename);
                    if ($fs && file_exists($fs)) {
                        $imgUrl = $siteBase . '/uploads/' . $filename;
                    } else {
                        $imgUrl = $imgField; // maybe absolute URL
                    }
                }
            ?>

            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <img src="<?php echo htmlspecialchars($imgUrl ?: ($siteBase . '/uploads/no-image.svg')); ?>" class="img-fluid rounded mb-3" style="height:180px;object-fit:cover;width:100%;" alt="Vendor Image" onerror="this.onerror=null;this.src='<?php echo htmlspecialchars($siteBase . '/uploads/no-image.svg'); ?>'">
                            <h4><?php echo htmlspecialchars($customer['customer_name'] ?? ($vendor['vendor_desc'] ?? 'Vendor')); ?></h4>
                            <p class="text-muted mb-1"><?php echo htmlspecialchars($vendor['vendor_type'] ?? ''); ?></p>
                            <p style="font-size:0.9rem;"><?php echo htmlspecialchars($customer['customer_email'] ?? ''); ?><br><?php echo htmlspecialchars($customer['customer_contact'] ?? ''); ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <strong>Create Booking</strong>
                        </div>
                        <div class="card-body">
                            <p>Select one of the events created by this vendor to add a booking to your cart.</p>

                            <div class="mb-3">
                                <label for="vendorEvents" class="form-label">Your Events</label>
                                <select id="vendorEvents" class="form-select">
                                    <?php if (empty($events)): ?>
                                        <option value="">-- No events available --</option>
                                    <?php else: ?>
                                        <option value="">-- Select an event --</option>
                                        <?php foreach ($events as $ev): ?>
                                            <option value="<?php echo (int)$ev['event_id']; ?>"><?php echo htmlspecialchars($ev['event_desc'] . ' — ' . ($ev['category'] ?? '')); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Quantity removed: bookings default to 1 -->

                            <div>
                                <button id="bookEventBtn" class="btn btn-primary">Book Selected Event</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/all_events.js"></script>
    <script>
        $(document).ready(function(){
            $('#bookEventBtn').on('click', function(e){
                e.preventDefault();
                const eventId = $('#vendorEvents').val();
                // quantity option removed — default to 1
                const quantity = 1;

                if (!eventId) {
                    Swal.fire({ icon: 'warning', title: 'No event selected', text: 'Please select an event to book.' });
                    return;
                }

                $.ajax({
                    url: '../actions/add_to_cart_action.php',
                    method: 'POST',
                    data: { event_id: eventId, quantity: quantity },
                    dataType: 'json'
                }).done(function(response){
                    if (response && response.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Added to Cart', text: response.message || 'Event added to cart', timer: 1500, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: (response && response.message) ? response.message : 'Failed to add to cart' });
                    }
                }).fail(function(xhr, status, err){
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed: ' + status });
                });
            });
        });
    </script>

</body>
</html>
