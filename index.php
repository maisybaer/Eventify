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
echo $customer_name;
$role = getUserRole();
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="settings/styles.css?v=1.1">

</head>

<body>



	<div class="menu-tray">
		<?php if (isset($_SESSION['user_id'])): ?>
			
			<a href="index.php" class="btn btn-sm btn-outline-secondary">Home</a>
			<a href="login/logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>

		<?php else: ?>
			<a href="login/register.php" class="btn btn-sm btn-outline-primary">Register</a>
			<a href="login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
		<?php endif; ?>			
	</div>

	<main>

	<div class="container" style="padding-top:120px;">
		<div class="text-center">

			<?php if ($role == 1) : ?>
				<h1>Welcome <?php echo htmlspecialchars($customer_name ?: 'Admin'); ?></h1>
            <?php elseif ($role == 2) : ?>
				<h1>Welcome <?php echo htmlspecialchars($customer_name ?: 'Vendor'); ?></h1>
			<?php elseif ($role == 3) : ?>
				<h1>Welcome <?php echo htmlspecialchars($customer_name ?: ''); ?>!</h1>
				<p>Find and book amazing events happening in your area. From music festivals to art exhibitions, your next adventure awaits.</p>
			<?php endif; ?>
		</div>

		    <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card fade-in">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <h3>Shop</h3>
                            </div>

                            <!-- Search and Filters -->
                            <div class="search-tray mb-4">
                                <i class="fas fa-search text-muted"></i>
                                <input type="text" id="searchBox" placeholder="Search events...">
                                
                                <select id="categoryFilter">
                                    <option value="">All Categories</option>
                                </select>

                                <button class="btn btn-sm" id="searchBtn">
                                    Search
                                </button>
                            </div>

                            <div class="text-center">
                                <a href="view/all_event.php" class="btn btn-custom btn-lg">
                                    Browse All Events
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

		<div class="product-grid">
			<?php if ($role == 1) : ?>
                        <!-- Events Card -->
                        <div class="product-card">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3" style="font-size: 3rem; color: var(--brand);">
                                        <i class="fas fa-tag"></i></i>
                                    </div>
                                    <h5>Events</h5>
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
                                    <div class="mb-3" style="font-size: 3rem; color: var(--brand);">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <h5>Categories</h5>
                                    <p class="text-muted">Manage product categories</p>
                                    <a href="admin/category.php" class="btn btn-custom mt-2">
                                        <i class="fas fa-arrow-right me-2"></i>Manage
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Orders Card -->
                        <div class="product-card">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3" style="font-size: 3rem; color: var(--brand);">
                                        <i class="fas fa-shopping-bag"></i>
                                    </div>
                                    <h5>Orders</h5>
                                    <p class="text-muted">Track all orders</p>
                                    <a href="admin/orders.php" class="btn btn-custom mt-2">
                                        <i class="fas fa-arrow-right me-2"></i>View
                                    </a>
                                </div>
                            </div>
                        </div>

            <?php elseif ($role == 2) : ?>
                        <!-- Vendor Card -->
                        <div class="product-card">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3" style="font-size: 3rem; color: var(--brand);">
                                        <i class="fas fa-people"></i>
                                    </div>
                                    <h5>Vendors</h5>
                                    <p class="text-muted">Manage your account</p>
                                    <a href="admin/vendor.php" class="btn btn-custom mt-2">
                                        <i class="fas fa-arrow-right me-2"></i>Manage
                                    </a>
                                </div>
                            </div>
                        </div>

            
        </div>

	
			<?php endif; ?>
	


	
	</div>
	</main>


	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>