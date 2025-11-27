<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventify - Book Amazing Events</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #fef3f0 0%, #fff5f2 50%, #fef8f5 100%);
            color: #1a1a1a;
            overflow-x: hidden;
        }

        /* Floating background elements */
        .bg-decoration {
            position: fixed;
            border-radius: 50%;
            opacity: 0.6;
            filter: blur(80px);
            z-index: 0;
            animation: float 20s ease-in-out infinite;
        }

        .bg-decoration-1 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #ff6b6b20 0%, #ff8e5320 100%);
            top: -100px;
            right: -100px;
            animation-delay: 0s;
        }

        .bg-decoration-2 {
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, #ffd93d20 0%, #ff8e5320 100%);
            bottom: -150px;
            left: -150px;
            animation-delay: 7s;
        }

        .bg-decoration-3 {
            width: 350px;
            height: 350px;
            background: linear-gradient(135deg, #6bcf7f20 0%, #4ecdc420 100%);
            top: 50%;
            right: 10%;
            animation-delay: 14s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }

        /* Navigation */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 107, 107, 0.1);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: #ff6b6b;
            text-decoration: none;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8e53 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            align-items: center;
        }

        .nav-links a {
            color: #4a4a4a;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #ff6b6b;
        }

        .btn {
            padding: 0.75rem 1.75rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8e53 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(255, 107, 107, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #ff6b6b;
            border: 2px solid #ff6b6b;
        }

        .btn-secondary:hover {
            background: #ff6b6b;
            color: white;
        }

        /* Hero Section */
        .hero {
            position: relative;
            max-width: 1400px;
            margin: 0 auto;
            padding: 10rem 2rem 6rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            min-height: 90vh;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 107, 107, 0.1);
            color: #ff6b6b;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .hero h1 {
            font-size: 3.75rem;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1a1a1a 0%, #4a4a4a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero h1 .highlight {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8e53 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 1.25rem;
            color: #666;
            margin-bottom: 2.5rem;
            line-height: 1.7;
        }

        .feature-pills {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .pill {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: white;
            padding: 0.75rem 1.25rem;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .pill-icon {
            width: 20px;
            height: 20px;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
        }

        /* Hero Visual */
        .hero-visual {
            position: relative;
            z-index: 1;
        }

        .event-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            transform: perspective(1000px) rotateY(-5deg);
            animation: cardFloat 6s ease-in-out infinite;
        }

        @keyframes cardFloat {
            0%, 100% { transform: perspective(1000px) rotateY(-5deg) translateY(0); }
            50% { transform: perspective(1000px) rotateY(-5deg) translateY(-15px); }
        }

        .event-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.4s;
        }

        .event-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
        }

        .event-card:nth-child(1) {
            animation-delay: 0.2s;
        }

        .event-card:nth-child(2) {
            animation-delay: 0.4s;
            transform: translateY(30px);
        }

        .event-card:nth-child(3) {
            animation-delay: 0.6s;
        }

        .event-card:nth-child(4) {
            animation-delay: 0.8s;
            transform: translateY(30px);
        }

        .event-image {
            width: 100%;
            height: 140px;
            background: linear-gradient(135deg, #ffd93d 0%, #ff8e53 100%);
            border-radius: 12px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }

        .event-card:nth-child(2) .event-image {
            background: linear-gradient(135deg, #6bcf7f 0%, #4ecdc4 100%);
        }

        .event-card:nth-child(3) .event-image {
            background: linear-gradient(135deg, #a78bfa 0%, #ec4899 100%);
        }

        .event-card:nth-child(4) .event-image {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8e53 100%);
        }

        .event-title {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #1a1a1a;
        }

        .event-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: #666;
        }

        .event-date {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .event-price {
            font-weight: 700;
            color: #ff6b6b;
        }

        /* Features Section */
        .features {
            position: relative;
            max-width: 1400px;
            margin: 6rem auto;
            padding: 0 2rem;
            z-index: 1;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-badge {
            display: inline-block;
            background: rgba(255, 107, 107, 0.1);
            color: #ff6b6b;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .section-header h2 {
            font-size: 2.75rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: #1a1a1a;
        }

        .section-header p {
            font-size: 1.125rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2.5rem;
        }

        .feature-card {
            background: white;
            padding: 2.5rem;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
            transition: all 0.4s;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8e53 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
            color: white;
        }

        .feature-card:nth-child(2) .feature-icon {
            background: linear-gradient(135deg, #6bcf7f 0%, #4ecdc4 100%);
        }

        .feature-card:nth-child(3) .feature-icon {
            background: linear-gradient(135deg, #a78bfa 0%, #ec4899 100%);
        }

        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1a1a1a;
        }

        .feature-card p {
            color: #666;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }

        .feature-list {
            list-style: none;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #666;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .feature-list li::before {
            content: "✓";
            color: #6bcf7f;
            font-weight: 700;
            font-size: 1.1rem;
        }

        /* CTA Section */
        .cta {
            position: relative;
            max-width: 1200px;
            margin: 6rem auto;
            padding: 0 2rem;
            z-index: 1;
        }

        .cta-container {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8e53 100%);
            border-radius: 32px;
            padding: 4rem;
            text-align: center;
            box-shadow: 0 20px 60px rgba(255, 107, 107, 0.3);
            position: relative;
            overflow: hidden;
        }

        .cta-container::before {
            content: "";
            position: absolute;
            top: -50%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .cta-container h2 {
            font-size: 2.5rem;
            color: white;
            margin-bottom: 1rem;
            font-weight: 800;
            position: relative;
        }

        .cta-container p {
            font-size: 1.125rem;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 2rem;
            position: relative;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            position: relative;
        }

        .btn-white {
            background: white;
            color: #ff6b6b;
        }

        .btn-white:hover {
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-2px);
        }

        .btn-outline-white {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-outline-white:hover {
            background: white;
            color: #ff6b6b;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .hero {
                grid-template-columns: 1fr;
                padding: 8rem 2rem 4rem;
                gap: 3rem;
            }

            .hero h1 {
                font-size: 2.75rem;
            }

            .event-cards {
                transform: none;
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .nav-links {
                display: none;
            }

            .feature-pills {
                flex-wrap: wrap;
            }

            .hero-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="bg-decoration bg-decoration-1"></div>
    <div class="bg-decoration bg-decoration-2"></div>
    <div class="bg-decoration bg-decoration-3"></div>

    <nav>
        <div class="nav-container">
            <a href="#" class="logo">
                <div class="logo-icon">🎉</div>
                <span>Eventify</span>
            </a>
            <div class="nav-links">
                <a href="#features">Features</a>
                <a href="#how-it-works">How it works</a>
                <a href="#for-vendors">For Vendors</a>
                <a href="#pricing">Pricing</a>
                <a href="login/login.php" class="btn btn-primary">Get Started →</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <div class="badge">
                🎯 Over 1,000+ events hosted successfully
            </div>
            <h1>
                Discover events.<br>
                Book tickets.<br>
                <span class="highlight">Experience magic.</span>
            </h1>
            <p>
                Find and book amazing events happening in your area. From music festivals to art exhibitions, your next adventure awaits.
            </p>
            <div class="feature-pills">
                <div class="pill">
                    <svg class="pill-icon" fill="none" stroke="#ff6b6b" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Instant Booking
                </div>
                <div class="pill">
                    <svg class="pill-icon" fill="none" stroke="#6bcf7f" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Secure Payment
                </div>
            </div>
            <div class="hero-buttons">
                <a href="view/all_event.php" class="btn btn-primary">Browse Events</a>
                <a href="login/register_business.php" class="btn btn-secondary">List Your Event</a>
            </div>
        </div>

        <div class="hero-visual">
            <div class="event-cards">
                <div class="event-card">
                    <div class="event-image">🎵</div>
                    <div class="event-title">Summer Music Fest</div>
                    <div class="event-meta">
                        <span class="event-date">📅 Jun 15</span>
                        <span class="event-price">$45</span>
                    </div>
                </div>
                <div class="event-card">
                    <div class="event-image">🎨</div>
                    <div class="event-title">Art Gallery Opening</div>
                    <div class="event-meta">
                        <span class="event-date">📅 Jun 20</span>
                        <span class="event-price">$25</span>
                    </div>
                </div>
                <div class="event-card">
                    <div class="event-image">🍷</div>
                    <div class="event-title">Wine Tasting Night</div>
                    <div class="event-meta">
                        <span class="event-date">📅 Jun 22</span>
                        <span class="event-price">$35</span>
                    </div>
                </div>
                <div class="event-card">
                    <div class="event-image">🎭</div>
                    <div class="event-title">Theater Performance</div>
                    <div class="event-meta">
                        <span class="event-date">📅 Jun 25</span>
                        <span class="event-price">$50</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="section-header">
            <span class="section-badge">Why Choose Eventify</span>
            <h2>Everything you need in one platform</h2>
            <p>Powerful tools for event managers, vendors, and customers</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>For Event Managers</h3>
                <p>Create and manage your events with ease. Track ticket sales and book vendors all in one place.</p>
                <ul class="feature-list">
                    <li>Create unlimited events</li>
                    <li>Real-time ticket tracking</li>
                    <li>Vendor booking system</li>
                    <li>Analytics dashboard</li>
                </ul>
            </div>

            <div class="feature-card">
                <div class="feature-icon">🛍️</div>
                <h3>For Vendors</h3>
                <p>Showcase your services and get booked for amazing events. Build your profile and grow your business.</p>
                <ul class="feature-list">
                    <li>Professional profile</li>
                    <li>Direct event bookings</li>
                    <li>Portfolio showcase</li>
                    <li>Customer reviews</li>
                </ul>
            </div>

            <div class="feature-card">
                <div class="feature-icon">🎫</div>
                <h3>For Customers</h3>
                <p>Discover amazing events and book tickets instantly. Secure payment and easy access to all your bookings.</p>
                <ul class="feature-list">
                    <li>Browse all events</li>
                    <li>Instant ticket booking</li>
                    <li>Secure payments</li>
                    <li>Order history</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="cta-container">
            <h2>Ready to get started?</h2>
            <p>Join thousands of event managers, vendors, and customers using Eventify</p>
            <div class="cta-buttons">
                <a href="login/register.php" class="btn btn-white">Create Free Account</a>
                <a href="view/all_event.php" class="btn btn-outline-white">Explore Events</a>
            </div>
        </div>
    </section>

<?php
$footer_base = '';
include 'includes/footer.php';
?>
</body>
</html>