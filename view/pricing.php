<?php
session_start();
// This is a public page - no authentication required
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../settings/favicon.ico">

    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

        .menu-tray {
            position: fixed;
            top: 16px;
            right: 16px;
            background: rgba(255,255,255,0.95);
            border-radius: 50px;
            padding: 8px 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            z-index: 1200;
            backdrop-filter: blur(10px);
        }

        .menu-tray a {
            margin: 0 6px;
            padding: 8px 16px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .header-container {
            text-align: center;
            padding-top: 120px;
            margin-bottom: 3rem;
        }

        .header-container h1 {
            font-size: 3rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
        }

        .header-container p {
            font-size: 1.2rem;
            color: #718096;
            max-width: 600px;
            margin: 0 auto;
        }

        .pricing-section {
            margin-bottom: 4rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            color: #667eea;
        }

        .pricing-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        .pricing-card.featured {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .pricing-card.featured .card-title,
        .pricing-card.featured .price,
        .pricing-card.featured .price-period,
        .pricing-card.featured .feature-item {
            color: white;
        }

        .pricing-card.featured .feature-icon {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .card-subtitle {
            color: #718096;
            margin-bottom: 1.5rem;
        }

        .pricing-card.featured .card-subtitle {
            color: rgba(255,255,255,0.8);
        }

        .price {
            font-size: 3rem;
            font-weight: 700;
            color: #667eea;
        }

        .price-period {
            font-size: 1rem;
            color: #718096;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 1.5rem 0;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 0;
            color: #4a5568;
            border-bottom: 1px solid #e2e8f0;
        }

        .pricing-card.featured .feature-item {
            border-bottom-color: rgba(255,255,255,0.2);
        }

        .feature-item:last-child {
            border-bottom: none;
        }

        .feature-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        .info-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .info-card h3 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-card h3 i {
            color: #667eea;
        }

        .info-card p {
            color: #4a5568;
            line-height: 1.8;
            margin-bottom: 0;
        }

        .highlight-box {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 1rem;
        }

        .highlight-box p {
            color: #92400e;
            font-weight: 600;
            margin: 0;
        }

        .cta-section {
            text-align: center;
            padding: 3rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            margin-bottom: 3rem;
        }

        .cta-section h2 {
            color: white;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .cta-section p {
            color: rgba(255,255,255,0.9);
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .btn-cta {
            background: white;
            color: #667eea;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            color: #764ba2;
        }

        .transaction-fee-badge {
            display: inline-block;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.2rem;
            margin: 1rem 0;
        }
    </style>
</head>

<body>
    <header>
    <div class="menu-tray">
         <a href="../home.php" class="logo">
                <img src="../settings/logo.png" alt="eventify logo" style="height:30px;">
         </a> 
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="../index.php"><i class="fas fa-home"></i> Home</a>
            <a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
            <a href="../login/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a href="../login/register_customer.php" class="btn btn-sm btn-primary">Register</a>
            <a href="../login/login.php" class="btn btn-sm btn-secondary">Login</a>
        <?php endif; ?>
    </div>
    </header>

    <div class="container">
        <div class="header-container">
            <h1><i class="fas fa-tags"></i> Pricing</h1>
            <p>Eventify uses a hybrid revenue model designed to be accessible for everyone while providing premium features for businesses.</p>
        </div>

        <!-- Transaction Fee Section -->
        <div class="pricing-section">
            <h2 class="section-title"><i class="fas fa-percentage"></i> Transaction Fee</h2>
            <div class="info-card">
                <h3><i class="fas fa-ticket-alt"></i> Ticket Purchases</h3>
                <p>A simple and transparent pricing model for all ticket sales on Eventify.</p>
                <div class="text-center">
                    <div class="transaction-fee-badge">15% per transaction</div>
                </div>
                <p class="mt-3">This fee is automatically applied to each ticket purchased through the platform. It covers payment processing, platform maintenance, customer support, and continuous feature development.</p>
            </div>
        </div>

        <!-- Freemium Model Section -->
        <div class="pricing-section">
            <h2 class="section-title"><i class="fas fa-layer-group"></i> Freemium Model</h2>
            
            <div class="row g-4">
                <!-- Free Tier - Attendees -->
                <div class="col-md-4">
                    <div class="pricing-card">
                        <div class="card-title">Attendee</div>
                        <div class="card-subtitle">For event-goers</div>
                        <div class="price">Free</div>
                        <div class="price-period">Forever</div>
                        <ul class="feature-list">
                            <li class="feature-item">
                                <span class="feature-icon"><i class="fas fa-check"></i></span>
                                Search all events
                            </li>
                            <li class="feature-item">
                                <span class="feature-icon"><i class="fas fa-check"></i></span>
                                Book any event
                            </li>
                            <li class="feature-item">
                                <span class="feature-icon"><i class="fas fa-check"></i></span>
                                View event details
                            </li>
                            <li class="feature-item">
                                <span class="feature-icon"><i class="fas fa-check"></i></span>
                                Manage your bookings
                            </li>
                            <li class="feature-item">
                                <span class="feature-icon"><i class="fas fa-check"></i></span>
                                Save favorite events
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Free Tier - Business Basic -->
                <div class="col-md-4">
                    <div class="pricing-card">
                        <div class="card-title">Business Basic</div>
                        <div class="card-subtitle">For organisers & vendors</div>
                        <div class="price">Free</div>
                        <div class="price-period">Basic features</div>
                        <ul class="feature-list">
                            <li class="feature-item">
                                <span class="feature-icon"><i class="fas fa-check"></i></span>
                                Search local events
                            </li>
                            <li class="feature-item">
                                <span class="feature-icon"><i class="fas fa-check"></i></span>
                                Search local vendors
                            </li>
                            <li class="feature-item">
                                <span class="feature-icon"><i class="fas fa-check"></i></span>
                                Instant messaging
                            </li>
                            <li class="feature-item">
                                <span class="feature-icon"><i class="fas fa-check"></i></span>
                                User profile page
                            </li>
                            <li class="feature-item">
                                <span class="feature-icon"><i class="fas fa-check"></i></span>
                                Advertise services & past work
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Premium Tier - Business Pro -->
                <div class="col-md-4">
                    <div class="pricing-card featured">
                        <div class="card-title">Business Pro</div>
                        <div class="card-subtitle">For serious businesses</div>
                        <div class="price">GHS 50</div>
                        <div class="price-period">per month</div>
                        <ul class="feature-list">
                            <li class="feature-item">
                                <span class="feature-icon"><i class="fas fa-check"></i></span>
                                All Basic features
                            </li>
                            <li class="feature-item">
                                <span class="feature-icon"><i class="fas fa-check"></i></span>
                                Business analytics dashboard
                            </li>
                            <li class="feature-item">
                                <span class="feature-icon"><i class="fas fa-check"></i></span>
                                Sales & revenue reports
                            </li>
                            <li class="feature-item">
                                <span class="feature-icon"><i class="fas fa-check"></i></span>
                                Insider advice & tips
                            </li>
                            <li class="feature-item">
                                <span class="feature-icon"><i class="fas fa-check"></i></span>
                                Priority support
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Team Pricing -->
            <div class="info-card mt-4">
                <h3><i class="fas fa-users"></i> Team Pricing</h3>
                <p>For businesses with multiple team members, we offer a discounted team rate:</p>
                <div class="highlight-box">
                    <p><i class="fas fa-tag"></i> GHS 35 per user per month for teams</p>
                </div>
                <p class="mt-3">Team pricing is perfect for event management companies, large vendors, or organisations with multiple staff members who need access to premium features.</p>
            </div>
        </div>

        <!-- Advertising Revenue Section -->
        <div class="pricing-section">
            <h2 class="section-title"><i class="fas fa-bullhorn"></i> Advertising & Sponsored Listings</h2>
            <div class="info-card">
                <h3><i class="fas fa-star"></i> Boost Your Visibility</h3>
                <p>Want to stand out from the crowd? Businesses can pay for sponsored listings to appear at the top of search results and get featured on our homepage.</p>
                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="text-center p-3" style="background: #f7fafc; border-radius: 15px;">
                            <i class="fas fa-search fa-2x mb-2" style="color: #667eea;"></i>
                            <h5>Search Priority</h5>
                            <p class="small text-muted mb-0">Appear at the top of relevant searches</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="text-center p-3" style="background: #f7fafc; border-radius: 15px;">
                            <i class="fas fa-home fa-2x mb-2" style="color: #667eea;"></i>
                            <h5>Homepage Feature</h5>
                            <p class="small text-muted mb-0">Get featured on the Eventify homepage</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="text-center p-3" style="background: #f7fafc; border-radius: 15px;">
                            <i class="fas fa-badge-check fa-2x mb-2" style="color: #667eea;"></i>
                            <h5>Verified Badge</h5>
                            <p class="small text-muted mb-0">Build trust with a verified sponsor badge</p>
                        </div>
                    </div>
                </div>
                <p class="mt-3"><strong>Interested in advertising?</strong> Contact our team for custom advertising packages tailored to your business needs.</p>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="cta-section">
            <h2>Ready to get started?</h2>
            <p>Join thousands of event organisers, vendors, and attendees already using Eventify.</p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="../login/register_customer.php" class="btn-cta"><i class="fas fa-rocket"></i> Sign Up Free</a>
            <?php else: ?>
                <a href="../index.php" class="btn-cta"><i class="fas fa-calendar-alt"></i> Browse Events</a>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<?php
$footer_base = '../';
include '../includes/footer.php';
?>
</body>
</html>
