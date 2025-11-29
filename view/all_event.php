<?php
require_once '../settings/core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Events - Eventify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="../settings/favicon.ico"/>
</head>

<body>
    <!-- Navigation -->
    <div class="menu-tray">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="../index.php"><i class="fas fa-home"></i> Home</a>
            <a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
            <a href="../login/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a href="../login/register.php" class="btn btn-sm btn-primary">Register</a>
            <a href="../login/login.php" class="btn btn-sm btn-secondary">Login</a>
        <?php endif; ?>
    </div>

    <!-- Hero Section -->
    <div class="container header-container">
        <div class="text-center mb-5 fade-in">
            <span class="badge mb-3">
                <a href="../index.php"><img src="../settings/logo.png" alt="eventify logo" style="width:80-px; height:80px; margin-right:8px;"></a>     
            </span>
            <h1 class="mb-3">Discover Amazing Events</h1>
            <p class="text-muted" style="font-size: 1.125rem; max-width: 600px; margin: 0 auto;">
                From concerts and workshops to festivals and conferences, find the perfect event for you.
            </p>
        </div>

        <!-- Search and Filter -->
        <div class="search-tray slide-up">
            <input type="text" id="searchBox" placeholder="🔍 Search events by name, location, or description...">
            <select id="categoryFilter">
                <option value="">All Categories</option>
            </select>
            <button class="btn btn-primary" id="searchBtn">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
    </div>

    <!-- Events Grid -->
    <div class="container" style="margin-bottom: 4rem;">
        <div id="eventList" class="product-grid"></div>
        
        <!-- Pagination -->
        <div id="eventPager" style="text-align: center; margin-top: 2rem;"></div>
        
        <!-- Loading State -->
        <div id="loadingState" style="text-align: center; padding: 4rem; display: none;">
            <div style="width: 50px; height: 50px; border: 4px solid #f3f4f6; border-top: 4px solid var(--brand); border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto;"></div>
            <p class="text-muted mt-3">Loading events...</p>
        </div>
        
        <!-- Empty State -->
        <div id="emptyState" style="text-align: center; padding: 4rem; display: none;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">🎭</div>
            <h3>No events found</h3>
            <p class="text-muted">Try adjusting your search or filters</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/all_events.js?v=<?php echo time(); ?>"></script>
    
    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .product-card {
            position: relative;
            overflow: hidden;
        }
        
        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transition: transform 0.3s;
        }
        
        .product-card:hover::before {
            transform: scaleX(1);
        }
        
        .event-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #f0f0f0;
        }
        
        .event-date {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
            font-size: 0.875rem;
        }
        
        .event-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--brand);
        }
        
        .event-location {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
    </style>

<?php
$footer_base = '../';
include '../includes/footer.php';
?>
</body>
</html>
