<?php
//session_start();

require_once '../settings/core.php';

$products = get_all_products_ctr();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>See Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=1.1">
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

    <div class="container mt-5">
        <h2 class="text-center mb-4">Product Management</h2>

        <table class="table table-bordered table-striped" id="productTable">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Keywords</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Filled dynamically with JS -->
            </tbody>
        </table>
    </div> 


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/product.js"></script>

    
</body>

</html>