<?php
require_once '../settings/core.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>All Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=1.1">
</head>
<body>

	<div class="menu-tray">
		<?php if (isset($_SESSION['user_id'])): ?>
			
			<a href="../index.php" class="btn btn-sm btn-outline-secondary">Home</a>
			<a href=" ../login/logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>
            <a href="view/basket.php" class="btn btn-sm btn-outline-secondary">Basket</a>

		<?php else: ?>
			<a href="../login/register.php" class="btn btn-sm btn-outline-primary">Register</a>
			<a href="../login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
		<?php endif; ?>	
    </div>


    
	<div class="container" style="padding-top:120px;">
		<div class="text-center">
            
            <h1>All Products</h1>
            <p>Shop all our products</p>

        </div>

            <div class="search-tray">
                <input type="text" id="searchBox" placeholder="Search products...">
                <button class="btn btn-sm btn-outline-secondary" id="searchBtn">Search</button>

                <select id="categoryFilter">
                    <option value="">Filter by Category</option>
                </select>

                <select id="brandFilter">
                    <option value="">Filter by Brand</option>
                </select>
            </div>
        </div>
    </div>

    <div id="productList" class="product-grid"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/all_products.js"></script>
    
</body>
</html>
