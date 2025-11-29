<?php
require_once '../settings/core.php';
require_once '../controllers/event_controller.php';
require_once '../settings/db_class.php';

$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;
$event = null;
if ($event_id > 0) {
    $event = view_single_event_ctr($event_id);
}

// Check if logged-in user is a vendor
$is_vendor = false;
if (isset($_SESSION['user_id'])) {
    $db = new db_connection();
    $conn = $db->db_conn();
    if ($conn) {
        $uid = (int) $_SESSION['user_id'];
        $sql = "SELECT user_role FROM eventify_customer WHERE customer_id = $uid LIMIT 1";
        $res = mysqli_query($conn, $sql);
        if ($res) {
            $user = mysqli_fetch_assoc($res);
            if ($user && intval($user['user_role']) === 2) {
                $is_vendor = true;
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=1.1">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .product-container {
            padding-top: 100px;
        }

        .card {
            max-width: 900px;
            margin: auto;
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            border-radius: 20px 20px 0 0 !important;
            padding: 1.5rem;
        }

        .card-header h2 {
            color: white !important;
        }

        .card-body {
            padding: 2rem;
        }

        .card-body p {
            margin-bottom: 1rem;
            font-size: 1.05rem;
        }

        .card-body strong {
            color: #f97316;
            font-weight: 600;
        }

        .card-footer {
            background: #f8f9fa;
            border-radius: 0 0 20px 20px;
            padding: 1.5rem;
            border-top: 1px solid #e9ecef;
        }

        .event-image {
            max-width: 100%;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
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

        .btn-primary {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.4);
        }

        .form-control:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 0.2rem rgba(249, 115, 22, 0.25);
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
        }
    </style>

</head>

<body>

    <div class="menu-tray">
        <a href="home.php" class="logo">
                <img src="../settings/logo.png" alt="eventify logo" style="height:30px;">
         </a> 
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="../index.php"><i class="fas fa-home"></i> Home</a>
            <?php if ($is_vendor): ?>
                <a href="../vendor/vendor.php"><i class="fas fa-user"></i> Profile</a>
                <a href="../vendor/vendor_requests.php"><i class="fas fa-inbox"></i> Requests</a>
            <?php else: ?>
                <a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
            <?php endif; ?>
            <a href="all_event.php"><i class="fas fa-arrow-left"></i> Back</a>
            <a href="../login/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a href="../login/register.php" class="btn btn-sm btn-primary">Register</a>
            <a href="../login/login.php" class="btn btn-sm btn-secondary">Login</a>
        <?php endif; ?>
    </div>

    <div>
        <div class="container" style="padding-top:120px;">
            <div class="text-center">
                <h1>View Event</h1>
            </div>
        </div>
    </div>

    <main class="container event-container" style="padding-top:40px;">

        <?php if (!$event): ?>
            <div class="alert alert-warning">Event not found.</div>
        <?php else: ?>

            <?php
                // Prepare image URL (site-base aware)
                $imgField = trim((string)($event['flyer'] ?? ''));
                $imgUrl = '';
                $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
                $siteBase = dirname($scriptDir);
                if ($siteBase === '/' || $siteBase === '.') $siteBase = '';
                if ($imgField !== '') {
                    $filename = basename($imgField);
                    $fs = realpath(__DIR__ . '/../uploads/' . $filename);
                    if ($fs && file_exists($fs)) {
                        $imgUrl = $siteBase . '/uploads/' . $filename;
                    }
                }
            ?>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <?php if ($imgUrl): ?>
                        <img src="<?php echo htmlspecialchars($imgUrl); ?>" class="img-fluid event-image" alt="<?php echo htmlspecialchars($event['event_desc'] ?? ''); ?>" onerror="this.onerror=null;this.src='<?php echo htmlspecialchars($siteBase . '/uploads/no-image.svg'); ?>'">
                    <?php else: ?>
                        <img src="<?php echo htmlspecialchars($siteBase . '/uploads/no-image.svg'); ?>" class="img-fluid event-image" alt="No image">
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header text-center highlight">
                            <h2 class="mb-0"><?php echo htmlspecialchars($event['event_desc'] ?? ''); ?></h2>
                        </div>
                        <div class="card-body">
                            <p><strong>Category:</strong> <?php echo htmlspecialchars($event['category'] ?? $event['event_cat'] ?? ''); ?></p>
                            <p><strong>Price:</strong> GHS <?php echo htmlspecialchars($event['event_price']); ?></p>
                            <?php if (!empty($event['event_date'])): ?>
                            <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
                            <?php endif; ?>
                            <p><strong>Time:</strong> <?php echo htmlspecialchars($event['event_start'] ?? '') . ' - ' . htmlspecialchars($event['event_end'] ?? ''); ?></p>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($event['event_location'] ?? ''); ?></p>
                            <?php if (!empty($event['event_keywords'])): ?>
                            <p><strong>Keywords:</strong> <?php echo htmlspecialchars($event['event_keywords'] ?? ''); ?></p>
                            <?php endif; ?>

                            <?php if (!$is_vendor): ?>
                                <div class="mt-3">
                                    <label for="eventQuantity" class="form-label fw-semibold">Quantity</label>
                                    <input
                                        type="number"
                                        id="eventQuantity"
                                        name="quantity"
                                        class="form-control"
                                        min="1"
                                        value="1"
                                    >
                                </div>
                            <?php else: ?>
                                <div class="mt-3 alert alert-info">
                                    <i class="fas fa-info-circle"></i> Vendors cannot purchase event tickets. This page is for viewing event details only.
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (!$is_vendor): ?>
                            <div class="card-footer text-center">
                                <button id="addToCartBtn" class="btn btn-primary mt-2" data-id="<?php echo (int)$event['event_id']; ?>">Add to Cart</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function(){
            console.log('Page loaded, session user:', <?php echo json_encode($_SESSION['user_id'] ?? null); ?>);

            $('#addToCartBtn').on('click', function(e){
                e.preventDefault();
                const eventId = $(this).data('id');
                const quantity = parseInt($('#eventQuantity').val(), 10) || 1;

                console.log('Adding to cart:', { event_id: eventId, quantity: quantity });

                if (quantity <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid quantity',
                        text: 'Please enter a quantity of at least 1.'
                    });
                    return;
                }

                $.ajax({
                    url: '../actions/add_to_cart_action.php',
                    method: 'POST',
                    data: { event_id: eventId, quantity: quantity },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Server response:', response);
                        if(response.status === 'success'){
                            Swal.fire({
                                icon: 'success',
                                title: 'Added to Cart!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error){
                        console.error('AJAX Error:', status, error);
                        console.error('Response Text:', xhr.responseText);
                        console.error('Status Code:', xhr.status);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to add item to cart. Check console for details.'
                        });
                    }
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
