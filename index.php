<?php
require_once 'settings/core.php';

// check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login.php");
    exit;
}

// get user info
$user_id = getUserID();
$customer_name = getUserName($user_id) ?? '';
$role = getUserRole();

// Set footer base path
$footer_base = '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home - Eventify</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="settings/styles.css?v=<?php echo time(); ?>">
    <link rel="icon" href="settings/favicon.ico">

    <style>
        /* Orange theme for event management */
        .event-section .section-divider h2 {
            color: #f97316;
        }

        .event-section .section-divider h2::before,
        .event-section .section-divider h2::after {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        }

        .event-section .product-card .card {
            border-top: 3px solid #f97316;
        }

        .event-section .product-card i {
            color: #f97316 !important;
        }

        .event-section .btn-custom {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            border: none;
        }

        .event-section .btn-custom:hover {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
        }

        /* Purple theme for vendor management */
        .vendor-section .section-divider h2 {
            color: #667eea;
        }

        .vendor-section .section-divider h2::before,
        .vendor-section .section-divider h2::after {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .vendor-section .product-card .card {
            border-top: 3px solid #667eea;
        }

        .vendor-section .product-card i {
            color: #667eea !important;
        }

        .vendor-section .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .vendor-section .btn-custom:hover {
            background: linear-gradient(135deg, #764ba2 0%, #5b21b6 100%);
        }

        .section-divider {
            margin: 4rem 0 3rem 0;
            text-align: center;
        }

        .section-divider h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            display: inline-block;
            padding: 0 2rem;
        }

        .section-divider h2::before,
        .section-divider h2::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 100px;
            height: 2px;
        }

        .section-divider h2::before {
            right: 100%;
            margin-right: 1rem;
        }

        .section-divider h2::after {
            left: 100%;
            margin-left: 1rem;
        }

        .section-divider p {
            color: #718096;
            margin-top: 0.5rem;
        }

        .vendor-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 3rem 2rem;
            margin: 2rem 0 3rem 0;
            color: white;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        .vendor-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .vendor-banner::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .vendor-banner-content {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .vendor-banner h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .vendor-banner p {
            font-size: 1.1rem;
            opacity: 0.95;
            margin-bottom: 2rem;
        }

        .vendor-banner .btn {
            background: white;
            color: #667eea;
            font-weight: 600;
            padding: 1rem 3rem;
            border-radius: 50px;
            border: none;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .vendor-banner .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            color: #764ba2;
        }

        .vendor-banner .btn i {
            margin-right: 0.5rem;
        }

        /* Event search section with orange theme */
        .event-search-section {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            border-radius: 20px;
            padding: 3rem 2rem;
            margin: 2rem 0 3rem 0;
            color: white;
            box-shadow: 0 10px 40px rgba(249, 115, 22, 0.3);
            position: relative;
            overflow: hidden;
        }

        .event-search-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .event-search-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .event-search-content {
            position: relative;
            z-index: 1;
        }

        .event-search-content h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .search-controls {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .search-controls input,
        .search-controls select {
            padding: 0.75rem 1rem;
            border: none;
            border-radius: 50px;
            font-size: 0.95rem;
            min-width: 200px;
            flex: 1;
        }

        .search-controls button {
            background: white;
            color: #f97316;
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            border: none;
            transition: all 0.3s ease;
        }

        .search-controls button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .event-search-content .btn-browse {
            background: white;
            color: #f97316;
            font-weight: 600;
            padding: 1rem 3rem;
            border-radius: 50px;
            border: none;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .event-search-content .btn-browse:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            color: #ea580c;
        }

        @media (max-width: 768px) {
            .section-divider h2::before,
            .section-divider h2::after {
                display: none;
            }

            .vendor-banner h3,
            .event-search-content h3 {
                font-size: 1.8rem;
            }

            .vendor-banner p {
                font-size: 1rem;
            }

            .search-controls {
                flex-direction: column;
            }

            .search-controls input,
            .search-controls select {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="menu-tray">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
            <a href="view/cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
            <a href="login/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a href="login/register.php" class="btn btn-sm btn-primary">Register</a>
            <a href="login/login.php" class="btn btn-sm btn-secondary">Login</a>
        <?php endif; ?>
    </div>

	<main>
	<div class="container" style="padding-top:120px; padding-bottom: 3rem;">
		<div class="text-center">
            <span class="badge mb-3">
                <a href="index.php"><img src="settings/logo.png" alt="eventify logo" style="width:80px; height:80px; margin-right:8px;"></a>
            </span>

			<?php if ($role == 1) : ?>
				<h1>Welcome <?php echo htmlspecialchars($customer_name ?: 'Admin'); ?></h1>
				<p class="text-muted">Manage your events, orders, and vendor bookings</p>
            <?php elseif ($role == 2) : ?>
				<h1>Welcome <?php echo htmlspecialchars($customer_name ?: 'Vendor'); ?></h1>
				<p class="text-muted">Manage your events, bookings, and grow your business</p>
			<?php elseif ($role == 3) : ?>
				<h1>Welcome <?php echo htmlspecialchars($customer_name ?: ''); ?>!</h1>
				<p>Find and book amazing events happening in your area. From music festivals to art exhibitions, your next adventure awaits.</p>
			<?php endif; ?>
		</div>

        <!-- Event Search Section (Always at top) -->
        <div class="event-search-section">
            <div class="event-search-content">
                <h3><i class="fas fa-search"></i> Discover Amazing Events</h3>

                <div class="search-controls">
                    <input type="text" id="searchBox" placeholder="🔍 Search events by name, category, location...">
                    <select id="categoryFilter">
                        <option value="">All Categories</option>
                    </select>
                    <button id="searchBtn">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>

                <div class="text-center">
                    <a href="view/all_event.php" class="btn-browse">
                        <i class="fas fa-calendar-alt"></i> Browse All Events
                    </a>
                </div>
            </div>
        </div>

		<?php if ($role == 1 || $role == 2) : ?>
            <!-- ============= EVENT MANAGEMENT SECTION ============= -->
            <div class="event-section">
                <div class="section-divider">
                    <h2><i class="fas fa-calendar"></i> Event Management</h2>
                    <p>Create, manage, and track your events</p>
                </div>

                <div class="product-grid">
                    <?php if ($role == 1) : ?>
                        <!-- Events Card -->
                        <div class="product-card">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3" style="font-size: 3rem;">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <h5>My Events</h5>
                                    <p class="text-muted">Manage your events</p>
                                    <a href="admin/event.php" class="btn btn-custom mt-2">
                                        <i class="fas fa-arrow-right me-2"></i>Manage
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Categories Card -->
                        <div class="product-card">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3" style="font-size: 3rem;">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <h5>Categories</h5>
                                    <p class="text-muted">Manage event categories</p>
                                    <a href="admin/category.php" class="btn btn-custom mt-2">
                                        <i class="fas fa-arrow-right me-2"></i>Manage
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Sales Card -->
                        <div class="product-card">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3" style="font-size: 3rem;">
                                        <i class="fas fa-shopping-bag"></i>
                                    </div>
                                    <h5>Sales</h5>
                                    <p class="text-muted">View your ticket sales</p>
                                    <a href="admin/sales.php" class="btn btn-custom mt-2">
                                        <i class="fas fa-arrow-right me-2"></i>View
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Analytics Card -->
                        <div class="product-card">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3" style="font-size: 3rem;">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <h5>Analytics</h5>
                                    <p class="text-muted">Track your business performance</p>
                                    <a href="admin/analytics.php" class="btn btn-custom mt-2">
                                        <i class="fas fa-arrow-right me-2"></i>View
                                    </a>
                                </div>
                            </div>
                        </div>
                </div>
            </div>

            <!-- ============= VENDOR MANAGEMENT SECTION ============= -->
            <div class="vendor-section">
                <div class="section-divider">
                    <h2><i class="fas fa-users"></i> Vendor Management</h2>
                    <p>Connect with vendors and manage bookings</p>
                </div>

                <!-- Search Vendors Banner -->
                <div class="vendor-banner">
                    <div class="vendor-banner-content">
                        <h3><i class="fas fa-search"></i> Find the Perfect Vendor</h3>
                        <p>Browse through our curated list of professional vendors for your events</p>
                        <a href="view/<?php echo ($role == 1) ? 'all_vendor.php' : 'browse_vendors.php'; ?>" class="btn">
                            <i class="fas fa-users"></i> Browse All Vendors
                        </a>
                    </div>
                </div>

                <div class="product-grid">
                
                        <!-- My Bookings Card -->
                        <div class="product-card">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3" style="font-size: 3rem;">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <h5>My Bookings</h5>
                                    <p class="text-muted">View your vendor bookings</p>
                                    <a href="view/my_bookings.php" class="btn btn-custom mt-2">
                                        <i class="fas fa-arrow-right me-2"></i>View
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Vendor Requests Card -->
                        <div class="product-card">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3" style="font-size: 3rem;">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                    <h5>Vendor Requests</h5>
                                    <p class="text-muted">Approve vendors for your events</p>
                                    <a href="admin/vendor_requests.php" class="btn btn-custom mt-2">
                                        <i class="fas fa-arrow-right me-2"></i>Manage
                                    </a>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($role == 2) : ?>

                        <!-- Browse Events Card -->
                        <div class="product-card">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3" style="font-size: 3rem;">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <h5>Browse Events</h5>
                                    <p class="text-muted">Find events to vendor</p>
                                    <a href="view/browse_events_vendor.php" class="btn btn-custom mt-2">
                                        <i class="fas fa-arrow-right me-2"></i>Browse
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- My Event Requests Card -->
                        <div class="product-card">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3" style="font-size: 3rem;">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                    <h5>My Event Requests</h5>
                                    <p class="text-muted">Track your vendor requests</p>
                                    <a href="vendor/vendor_dashboard.php" class="btn btn-custom mt-2">
                                        <i class="fas fa-arrow-right me-2"></i>View
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="product-card">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3" style="font-size: 3rem;">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                    <h5>Your Account</h5>
                                    <p class="text-muted">Manage your vendor account</p>
                                    <a href="vendor/vendor.php" class="btn btn-custom mt-2">
                                        <i class="fas fa-arrow-right me-2"></i>Manage
                                    </a>
                                </div>
                            </div>
                        </div>

                    

                    <?php endif; ?>
                </div>
            </div>

        <?php endif; ?>

	</div>
	</main>

    <?php include 'includes/footer.php'; ?>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
