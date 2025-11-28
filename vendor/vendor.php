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
    <link rel="icon" href="../settings/favicon.ico">
    
    <style>
        .vendor-page {
            padding-top: 120px;
            padding-bottom: 60px;
        }

        .vendor-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .vendor-header h1 {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .vendor-header p {
            font-size: 1.125rem;
            color: var(--text-muted);
        }

        .vendor-image-container {
            position: relative;
            text-align: center;
            margin-bottom: 2rem;
        }

        .vendor-image {
            width: 220px;
            height: 220px;
            object-fit: cover;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            border: 4px solid white;
            transition: transform 0.3s;
        }

        .vendor-image:hover {
            transform: scale(1.05);
        }

        .vendor-profile-card {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            padding: 2.5rem;
            transition: all 0.3s;
        }

        .vendor-profile-card:hover {
            box-shadow: var(--shadow-lg);
        }

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--bg-secondary);
        }

        .profile-header h3 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-dark);
        }

        .profile-info-item {
            padding: 1rem 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: start;
            gap: 1rem;
        }

        .profile-info-item:last-child {
            border-bottom: none;
        }

        .profile-info-item i {
            color: var(--brand);
            width: 24px;
            font-size: 1.125rem;
            margin-top: 0.25rem;
        }

        .profile-info-item strong {
            color: var(--text-dark);
            display: block;
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .profile-info-item .info-value {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .vendor-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid var(--bg-secondary);
        }

        .vendor-section h5 {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
        }

        .vendor-section i {
            color: var(--brand);
            font-size: 1.25rem;
        }

        .edit-mode-form {
            animation: fadeIn 0.3s ease-in-out;
        }

        .alert {
            padding: 1.25rem 1.5rem;
            border-radius: var(--radius-md);
            margin-bottom: 2rem;
            border: none;
            font-weight: 500;
        }

        .alert-warning {
            background: rgba(255, 217, 61, 0.15);
            color: #ff9800;
        }

        .alert-danger {
            background: rgba(255, 82, 82, 0.15);
            color: var(--error);
        }

        .vendor-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .stat-card {
            background: var(--gradient-primary);
            padding: 1.5rem;
            border-radius: var(--radius-md);
            text-align: center;
            color: white;
            box-shadow: var(--shadow-brand);
        }

        .stat-card i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            display: block;
        }

        .stat-card .stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
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
            <a href="vendor.php"><i class="fas fa-user"></i> Profile</a>
            <a href="vendor_requests.php"><i class="fas fa-inbox"></i> Requests</a>
            <a href="../login/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a href="../index.php" class="btn btn-sm btn-primary">Home</a>
            <a href="../login/login.php" class="btn btn-sm btn-secondary">Login</a>
        <?php endif; ?>
    </div>
    </header>

    <div class="container vendor-page" style="max-width:1100px;">
        <div class="vendor-header animate__animated animate__fadeInDown">
            <h1><i class="fas fa-store"></i> Your Vendor Account</h1>
            <p>Manage your vendor profile and grow your business</p>
        </div>

        <?php if (!$customer): ?>
            <div class="alert alert-warning animate__animated animate__fadeIn">
                <i class="fas fa-exclamation-triangle"></i> Customer record not found.
            </div>
        <?php elseif (intval($customer['user_role']) !== 2): ?>
            <div class="alert alert-danger animate__animated animate__fadeIn">
                <i class="fas fa-ban"></i> Access denied. This page is for vendor accounts only.
            </div>
        <?php else: ?>
            <div class="row animate__animated animate__fadeInUp">
                <div class="col-md-4">
                    <div class="vendor-image-container">
                        <?php $img = $customer['customer_image'] ?? ''; ?>
                        <img src="<?php echo htmlspecialchars($img ? $img : '../uploads/no-image.svg'); ?>"
                             class="vendor-image"
                             alt="Vendor Image">
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="vendor-profile-card">
                        <div class="profile-header">
                            <h3>
                                <i class="fas fa-user-tie"></i>
                                Vendor Profile
                            </h3>
                            <button id="editProfileBtn" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit Profile
                            </button>
                        </div>

                        <!-- View Mode -->
                        <div id="viewMode">
                            <div class="profile-info-item">
                                <i class="fas fa-user"></i>
                                <div class="flex-grow-1">
                                    <strong>Name</strong>
                                    <div class="info-value"><?php echo htmlspecialchars($customer['customer_name']); ?></div>
                                </div>
                            </div>

                            <div class="profile-info-item">
                                <i class="fas fa-envelope"></i>
                                <div class="flex-grow-1">
                                    <strong>Email</strong>
                                    <div class="info-value"><?php echo htmlspecialchars($customer['customer_email']); ?></div>
                                </div>
                            </div>

                            <div class="profile-info-item">
                                <i class="fas fa-phone"></i>
                                <div class="flex-grow-1">
                                    <strong>Contact</strong>
                                    <div class="info-value"><?php echo htmlspecialchars($customer['customer_contact'] ?? 'Not set'); ?></div>
                                </div>
                            </div>

                            <div class="profile-info-item">
                                <i class="fas fa-globe"></i>
                                <div class="flex-grow-1">
                                    <strong>Country</strong>
                                    <div class="info-value"><?php echo htmlspecialchars($customer['customer_country'] ?? 'Not set'); ?></div>
                                </div>
                            </div>

                            <div class="profile-info-item">
                                <i class="fas fa-city"></i>
                                <div class="flex-grow-1">
                                    <strong>City</strong>
                                    <div class="info-value"><?php echo htmlspecialchars($customer['customer_city'] ?? 'Not set'); ?></div>
                                </div>
                            </div>

                            <div class="vendor-section">
                                <h5>
                                    <i class="fas fa-briefcase"></i>
                                    Vendor Details
                                </h5>
                                <?php if ($vendor): ?>
                                    <div class="profile-info-item">
                                        <i class="fas fa-tag"></i>
                                        <div class="flex-grow-1">
                                            <strong>Vendor Type</strong>
                                            <div class="info-value"><?php echo htmlspecialchars($vendor['vendor_type']); ?></div>
                                        </div>
                                    </div>

                                    <div class="profile-info-item">
                                        <i class="fas fa-info-circle"></i>
                                        <div class="flex-grow-1">
                                            <strong>Description</strong>
                                            <div class="info-value"><?php echo htmlspecialchars($vendor['vendor_desc']); ?></div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted"><i class="fas fa-exclamation-circle"></i> No vendor details available</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Edit Mode (Hidden by default) -->
                        <div id="editMode" class="edit-mode-form" style="display: none;">
                            <form id="vendorProfileForm">
                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-user"></i> Name</label>
                                    <input type="text" class="form-control" id="editName" name="name" value="<?php echo htmlspecialchars($customer['customer_name']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-phone"></i> Contact</label>
                                    <input type="text" class="form-control" id="editContact" name="contact" value="<?php echo htmlspecialchars($customer['customer_contact'] ?? ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-globe"></i> Country</label>
                                    <input type="text" class="form-control" id="editCountry" name="country" value="<?php echo htmlspecialchars($customer['customer_country'] ?? ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-city"></i> City</label>
                                    <input type="text" class="form-control" id="editCity" name="city" value="<?php echo htmlspecialchars($customer['customer_city'] ?? ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-tag"></i> Vendor Type</label>
                                    <input type="text" class="form-control" id="editVendorType" name="vendor_type" value="<?php echo htmlspecialchars($vendor['vendor_type'] ?? ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-info-circle"></i> Description</label>
                                    <textarea class="form-control" id="editDescription" name="description" rows="3"><?php echo htmlspecialchars($vendor['vendor_desc'] ?? ''); ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="fas fa-image"></i> Profile Image</label>
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
