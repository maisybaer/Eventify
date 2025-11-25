<?php
require_once '../settings/core.php';
require_once '../settings/db_class.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit;
}

// Fetch logged-in customer
$db = new db_connection();
$conn = $db->db_conn();
$customer = null;
$vendor = null;
if ($conn) {
    $uid = (int) $_SESSION['user_id'];
    $sql = "SELECT * FROM eventify_customer WHERE customer_id = $uid LIMIT 1";
    $res = mysqli_query($conn, $sql);
    if ($res) {
        $customer = mysqli_fetch_assoc($res);
        // If this user is a vendor (role == 2), try to fetch vendor metadata
        if ($customer && intval($customer['user_role']) === 2) {
            // Best-effort join: vendor_desc was created from customer_name on registration
            $name = mysqli_real_escape_string($conn, $customer['customer_name']);
            $vsql = "SELECT * FROM eventify_vendor WHERE vendor_desc = '$name' LIMIT 1";
            $vres = mysqli_query($conn, $vsql);
            if ($vres) {
                $vendor = mysqli_fetch_assoc($vres);
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor Account</title>
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

    <div class="container" style="padding-top:120px; max-width:900px;">
        <div class="text-center mb-4">
            <h1>Your Vendor Account</h1>
            <p class="text-muted">Manage your vendor profile and details.</p>
        </div>

        <?php if (!$customer): ?>
            <div class="alert alert-warning">Customer record not found.</div>
        <?php elseif (intval($customer['user_role']) !== 2): ?>
            <div class="alert alert-danger">Access denied. This page is for vendor accounts only.</div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-4 text-center">
                    <?php $img = $customer['customer_image'] ?? ''; ?>
                    <img src="<?php echo htmlspecialchars($img ? $img : '../uploads/no-image.svg'); ?>" class="img-fluid" style="max-width:220px; border-radius:8px;" alt="Vendor Image">
                </div>
                <div class="col-md-8">
                    <div class="card p-3">
                        <h3><?php echo htmlspecialchars($customer['customer_name']); ?></h3>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['customer_email']); ?></p>
                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($customer['customer_contact'] ?? ''); ?></p>
                        <p><strong>Country / City:</strong> <?php echo htmlspecialchars(($customer['customer_country'] ?? '') . ' / ' . ($customer['customer_city'] ?? '')); ?></p>
                        <hr>
                        <h5>Vendor Metadata</h5>
                        <?php if ($vendor): ?>
                            <p><strong>Vendor ID:</strong> <?php echo htmlspecialchars($vendor['vendor_id']); ?></p>
                            <p><strong>Vendor Type:</strong> <?php echo htmlspecialchars($vendor['vendor_type']); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($vendor['vendor_desc']); ?></p>
                        <?php else: ?>
                            <div class="alert alert-info">No vendor metadata found. Your account may have been created without vendor details.</div>
                        <?php endif; ?>
                        <div class="mt-3">
                            <a href="../admin/event.php" class="btn btn-outline-primary">My Events</a>
                            <a href="../login/logout.php" class="btn btn-secondary">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/vendor.js"></script>

</body>
</html>
