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


<header>
	<div>
		<?php if (isset($_SESSION['user_id'])): ?>
			
			<a href="index.php" class="btn btn-sm btn-outline-secondary">Home</a>
			<a href="login/logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>

		<?php else: ?>
			<a href="login/register.php" class="btn btn-sm btn-outline-primary">Register</a>
			<a href="login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
		<?php endif; ?>			
	</div>
</header>

	<main>

	<div class="container" style="padding-top:120px;">
		<div class="text-center">

            <h1>Discover an event near you.</h1> 
            <p>Find and book amazing events happening in your area. From music festivals to art exhibitions, your next adventure awaits.</p>

            
		</div>

		    <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card fade-in">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <h5>Search for an event</h5>
                            </div>

                            <!-- Search and Filters -->
                            <div class="search-tray mb-4">
                                <i class="fas fa-search text-muted"></i>
                                <input type="text" id="searchBox" placeholder="Search for an event...">

                                <select id="typeFilter">
                                    <option value="">All Event Types</option>
                                </select>

                                <button class="btn btn-sm" id="searchBtn">
                                    Search
                                </button>
                            </div>

                            <div class="text-center">
                                <a href="view/all_events.php" class="btn btn-custom btn-lg">
                                    See All Events
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
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <h5>Events</h5>
                                    <p class="text-muted">Manage your events</p>
                                    <a href="admin/events.php" class="btn btn-custom mt-2">
                                        <i class="fas fa-arrow-right me-2"></i>Manage
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Event Cat Card -->
                        <div class="product-card">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3" style="font-size: 3rem; color: var(--brand);">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <h5>Categories</h5>
                                    <p class="text-muted">Manage your events Categories</p>
                                    <a href="admin/category.php" class="btn btn-custom mt-2">
                                        <i class="fas fa-arrow-right me-2"></i>Manage
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Vendors Card -->
                        <div class="product-card">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3" style="font-size: 3rem; color: var(--brand);">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <h5>Vendors</h5>
                                    <p class="text-muted">Find vendors near you</p>
                                    <a href="admin/customer.php" class="btn btn-custom mt-2">
                                        <i class="fas fa-arrow-right me-2"></i>View
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Bookings Card -->
                        <div class="product-card">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3" style="font-size: 3rem; color: var(--brand);">
                                        <i class="fas fa-shopping-bag"></i>
                                    </div>
                                    <h5>Bookings</h5>
                                    <p class="text-muted">Manage your bookings</p>
                                    <a href="admin/orders.php" class="btn btn-custom mt-2">
                                        <i class="fas fa-arrow-right me-2"></i>View
                                    </a>
                                </div>
                            </div>
                        </div>

            
        </div>

	
			<?php endif; ?>
	


	
	</div>
	</main>


	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load categories for search filter
        document.addEventListener('DOMContentLoaded', () => {
            const typeFilter = document.getElementById('typeFilter');
            const searchBox = document.getElementById('searchBox');
            const searchBtn = document.getElementById('searchBtn');

            // Load categories
            fetch('actions/fetch_category_action.php')
                .then(res => res.json())
                .then(categories => {
                    if (Array.isArray(categories)) {
                        categories.forEach(cat => {
                            const option = document.createElement('option');
                            option.value = cat.cat_id;
                            option.textContent = cat.cat_name;
                            typeFilter.appendChild(option);
                        });
                    }
                })
                .catch(err => console.error('Failed to load categories:', err));

            // Search functionality
            function performSearch() {
                const query = searchBox.value.trim();
                const category = typeFilter.value;
                
                const params = new URLSearchParams();
                if (query) params.append('q', query);
                if (category) params.append('category', category);
                
                const url = `view/all_events.php${params.toString() ? '?' + params.toString() : ''}`;
                window.location.href = url;
            }

            if (searchBtn) {
                searchBtn.addEventListener('click', performSearch);
            }
            
            if (searchBox) {
                searchBox.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        performSearch();
                    }
                });
            }
        });
    </script>
</body>
</html>