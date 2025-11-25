<?php
require_once '../settings/core.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>All Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=1.1">

    <style>
        .product-card {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            width: 200px;
            margin: 10px;
        }
        .product-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        select, input[type="text"] {
            padding: 5px;
            margin: 5px;
        }
    </style>
</head>
<body>

	<div class="menu-tray">
		<?php if (isset($_SESSION['user_id'])): ?>
			
			<a href="../index.php" class="btn btn-sm btn-outline-secondary">Home</a>
			<a href=" ../login/logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>
            <a href="cart.php" class="btn btn-sm btn-outline-secondary">Cart</a>

		<?php else: ?>
			<a href="../login/register.php" class="btn btn-sm btn-outline-primary">Register</a>
			<a href="../login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
		<?php endif; ?>	
    </div>


    
	<div class="container" style="padding-top:120px;">
		<div class="text-center">
            
            <h1>Discover an event near you.</h1> 
            <p>Find and book amazing events happening in your area. From music festivals to art exhibitions, your next adventure awaits.</p>

        </div>

            <div class="search-tray">
                <input type="text" id="searchBox" placeholder="Search events...">
                <button class="btn btn-sm btn-outline-secondary" id="searchBtn">Search</button>

                <select id="categoryFilter">
                    <option value="">Filter by Category</option>
                </select>

            </div>
        </div>
    </div>

    <div id="eventList" class="product-grid"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/all_events.js"></script>
    
</body>
</html>
