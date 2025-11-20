<?php
require_once '../settings/core.php';
require_login('../login/login.php');

// Get reference from URL
$reference = $_GET['reference'] ?? '';

if (empty($reference)) {
    header('Location: cart.php?error=invalid_payment');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Processing - Eventify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=1.1">
    <style>
        .loading-container {
            min-height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .spinner {
            width: 3rem;
            height: 3rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="loading-container">
            <div>
                <div class="spinner-border text-primary spinner mb-3" role="status"></div>
                <h3>Processing Payment...</h3>
                <p class="text-muted">Please wait while we verify your payment.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Redirect to checkout with reference for verification
        const reference = '<?= addslashes($reference); ?>';
        if (reference) {
            window.location.href = `checkout.php?reference=${reference}`;
        } else {
            window.location.href = 'cart.php?error=payment_failed';
        }
    </script>
</body>
</html>