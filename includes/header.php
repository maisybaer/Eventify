<!-- Navigation Menu -->
<?php $header_base = isset($header_base) ? $header_base : '../'; ?>
<div class="menu-tray">
    <a href="<?php echo $header_base; ?>home.php" class="menu-logo">
        <img src="<?php echo $header_base; ?>settings/logo.png" alt="Eventify" style="width:44px;height:44px;object-fit:contain;vertical-align:middle;">
    </a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <?php
        $user_role = getUserRole();
        $header_base = isset($header_base) ? $header_base : '../';
        ?>

        <!-- Common Links -->
        <a href="<?php echo $header_base; ?>index.php" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-home"></i> Home
        </a>

        <?php if ($user_role == 2): ?>
            <!-- Vendor Menu -->
            <a href="<?php echo $header_base; ?>admin/event.php" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-calendar"></i> My Events
            </a>
            <a href="<?php echo $header_base; ?>view/browse_events_vendor.php" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-search"></i> Browse Events
            </a>
            <a href="<?php echo $header_base; ?>view/vendor_dashboard.php" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-briefcase"></i> My Requests
            </a>
            <a href="<?php echo $header_base; ?>view/my_bookings.php" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-calendar-check"></i> Customer Bookings
            </a>
            <a href="<?php echo $header_base; ?>view/vendor_requests.php" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-user-check"></i> Approve Vendors
            </a>
        <?php else: ?>
            <!-- Customer Menu -->
            <a href="<?php echo $header_base; ?>view/all_event.php" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-calendar"></i> Events
            </a>
            <a href="<?php echo $header_base; ?>view/browse_vendors.php" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-users"></i> Vendors
            </a>
            <a href="<?php echo $header_base; ?>view/my_bookings.php" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-clipboard-list"></i> My Bookings
            </a>
            <a href="<?php echo $header_base; ?>view/cart.php" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-shopping-cart"></i> Cart
            </a>
        <?php endif; ?>

        <a href="<?php echo $header_base; ?>login/logout.php" class="btn btn-sm btn-outline-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    <?php else: ?>
        <!-- Guest Menu -->
        <a href="<?php echo $header_base; ?>index.php" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-home"></i> Home
        </a>
        <a href="<?php echo $header_base; ?>view/all_event.php" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-calendar"></i> Events
        </a>
        <a href="<?php echo $header_base; ?>login/register_customer.php" class="btn btn-sm btn-primary">
            Register
        </a>
        <a href="<?php echo $header_base; ?>login/login.php" class="btn btn-sm btn-secondary">
            Login
        </a>
    <?php endif; ?>
</div>

<style>
    .menu-tray {
        position: fixed;
        top: 16px;
        right: 16px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 50px;
        padding: 8px 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        z-index: 1200;
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }

    .menu-tray a {
        margin: 0 2px;
        padding: 8px 16px;
        border-radius: 50px;
        transition: all 0.3s ease;
        text-decoration: none;
        font-size: 0.875rem;
        white-space: nowrap;
    }

    .menu-tray .menu-logo {
        margin-right: auto;
        padding: 4px 8px;
        background: transparent;
    }

    .menu-tray .menu-logo img {
        display: block;
        border-radius: 8px;
    }

    .menu-tray a:hover {
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .menu-tray {
            top: 8px;
            right: 8px;
            left: 8px;
            padding: 6px 8px;
        }

        .menu-tray a {
            padding: 6px 12px;
            font-size: 0.8rem;
        }
    }
</style>
