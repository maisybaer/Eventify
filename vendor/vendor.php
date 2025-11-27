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
                    <div class="card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="mb-0">Vendor Profile</h3>
                            <button id="editProfileBtn" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit Profile
                            </button>
                        </div>

                        <!-- View Mode -->
                        <div id="viewMode">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($customer['customer_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['customer_email']); ?></p>
                            <p><strong>Contact:</strong> <?php echo htmlspecialchars($customer['customer_contact'] ?? 'Not set'); ?></p>
                            <p><strong>Country:</strong> <?php echo htmlspecialchars($customer['customer_country'] ?? 'Not set'); ?></p>
                            <p><strong>City:</strong> <?php echo htmlspecialchars($customer['customer_city'] ?? 'Not set'); ?></p>
                            <hr>
                            <h5>Vendor Details</h5>
                            <?php if ($vendor): ?>
                                <p><strong>Vendor Type:</strong> <?php echo htmlspecialchars($vendor['vendor_type']); ?></p>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($vendor['vendor_desc']); ?></p>
                            <?php else: ?>
                                <p class="text-muted">No vendor details available</p>
                            <?php endif; ?>
                        </div>

                        <!-- Edit Mode (Hidden by default) -->
                        <div id="editMode" style="display: none;">
                            <form id="vendorProfileForm">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" id="editName" name="name" value="<?php echo htmlspecialchars($customer['customer_name']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Contact</label>
                                    <input type="text" class="form-control" id="editContact" name="contact" value="<?php echo htmlspecialchars($customer['customer_contact'] ?? ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Country</label>
                                    <input type="text" class="form-control" id="editCountry" name="country" value="<?php echo htmlspecialchars($customer['customer_country'] ?? ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" class="form-control" id="editCity" name="city" value="<?php echo htmlspecialchars($customer['customer_city'] ?? ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Vendor Type</label>
                                    <input type="text" class="form-control" id="editVendorType" name="vendor_type" value="<?php echo htmlspecialchars($vendor['vendor_type'] ?? ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" id="editDescription" name="description" rows="3"><?php echo htmlspecialchars($vendor['vendor_desc'] ?? ''); ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Profile Image</label>
                                    <input type="file" class="form-control" id="editImage" name="image" accept="image/*">
                                    <small class="text-muted">Leave blank to keep current image</small>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                    <button type="button" id="cancelEditBtn" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="mt-4">
                            <a href="../admin/event.php" class="btn btn-outline-primary">
                                <i class="fas fa-calendar"></i> My Events
                            </a>
                            <a href="../view/vendor_dashboard.php" class="btn btn-outline-secondary">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
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

<?php
$footer_base = '../';
include '../includes/footer.php';
?>
</body>
</html>
