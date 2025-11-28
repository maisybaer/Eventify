<?php
require_once '../settings/core.php';
require_once '../controllers/order_controller.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}

$customer_id = getUserID();
$invoice_no = isset($_GET['invoice']) ? htmlspecialchars($_GET['invoice']) : '';
$reference = isset($_GET['reference']) ? htmlspecialchars($_GET['reference']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Eventify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=<?php echo time(); ?>">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

        .success-container {
            max-width: 800px;
            margin: 100px auto 50px;
            padding: 24px;
        }

        .success-card {
            background: white;
            border-radius: 24px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #059669;
            margin-bottom: 1.5rem;
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .success-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .success-subtitle {
            color: #6b7280;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .confirmation-badge {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 2px solid #60a5fa;
            padding: 1.25rem;
            border-radius: 16px;
            color: #1e40af;
            margin-bottom: 2rem;
            font-weight: 600;
        }

        .confirmation-badge i {
            color: #2563eb;
            margin-right: 0.5rem;
        }

        .order-details {
            background: #f9fafb;
            padding: 2rem;
            border-radius: 16px;
            margin: 2rem 0;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #374151;
        }

        .detail-value {
            color: #6b7280;
            word-break: break-all;
            text-align: right;
            max-width: 60%;
        }

        .detail-value.status {
            color: #059669;
            font-weight: 700;
        }

        .buttons-container {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn-custom {
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(249, 115, 22, 0.3);
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(249, 115, 22, 0.4);
            color: white;
        }

        .btn-secondary-custom {
            background: white;
            color: #374151;
            border: 2px solid #e5e7eb;
        }

        .btn-secondary-custom:hover {
            background: #f3f4f6;
            color: #374151;
        }

        @media (max-width: 768px) {
            .success-container {
                margin-top: 80px;
            }

            .success-card {
                padding: 2rem 1.5rem;
            }

            .success-title {
                font-size: 2rem;
            }

            .buttons-container {
                flex-direction: column;
            }

            .btn-custom {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <header class="menu-tray">
        <a href="../index.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-home"></i> Home</a>
        <a href="all_event.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-calendar"></i> Events</a>
        <a href="../login/logout.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </header>

    <main>
        <div class="success-container">
            <div class="success-card">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>

                <h1 class="success-title">Payment Successful!</h1>
                <p class="success-subtitle">Your order has been confirmed and is being processed</p>

                <div class="confirmation-badge">
                    <i class="fas fa-check-circle"></i>
                    Payment Confirmed - Thank you for your purchase!
                </div>

                <div class="order-details">
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-receipt" style="color: #f97316; margin-right: 0.5rem;"></i>Invoice Number</span>
                        <span class="detail-value"><?php echo $invoice_no; ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-hashtag" style="color: #f97316; margin-right: 0.5rem;"></i>Payment Reference</span>
                        <span class="detail-value"><?php echo $reference; ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-calendar" style="color: #f97316; margin-right: 0.5rem;"></i>Order Date</span>
                        <span class="detail-value"><?php echo date('F j, Y'); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-check-circle" style="color: #f97316; margin-right: 0.5rem;"></i>Status</span>
                        <span class="detail-value status">Paid ✓</span>
                    </div>
                </div>

                <div class="buttons-container">
                    <a href="orders.php" class="btn-custom btn-primary-custom">
                        <i class="fas fa-box"></i> View My Orders
                    </a>
                    <a href="all_event.php" class="btn-custom btn-secondary-custom">
                        <i class="fas fa-calendar"></i> Browse Events
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Confetti effect
        function createConfetti() {
            const colors = ['#f97316', '#ea580c', '#059669', '#3b82f6', '#fbbf24'];
            const confettiCount = 60;

            for (let i = 0; i < confettiCount; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.style.cssText = `
                        position: fixed;
                        width: 10px;
                        height: 10px;
                        background: ${colors[Math.floor(Math.random() * colors.length)]};
                        left: ${Math.random() * 100}%;
                        top: -10px;
                        opacity: 1;
                        transform: rotate(${Math.random() * 360}deg);
                        z-index: 10001;
                        pointer-events: none;
                    `;

                    document.body.appendChild(confetti);

                    const duration = 2000 + Math.random() * 1000;
                    const startTime = Date.now();

                    function animateConfetti() {
                        const elapsed = Date.now() - startTime;
                        const progress = elapsed / duration;

                        if (progress < 1) {
                            const top = progress * (window.innerHeight + 50);
                            const wobble = Math.sin(progress * 10) * 50;

                            confetti.style.top = top + 'px';
                            confetti.style.left = `calc(${confetti.style.left} + ${wobble}px)`;
                            confetti.style.opacity = 1 - progress;
                            confetti.style.transform = `rotate(${progress * 720}deg)`;

                            requestAnimationFrame(animateConfetti);
                        } else {
                            confetti.remove();
                        }
                    }

                    animateConfetti();
                }, i * 30);
            }
        }

        // Trigger confetti on page load
        window.addEventListener('load', createConfetti);
    </script>

<?php
$footer_base = '../';
include '../includes/footer.php';
?>
</body>
</html>
