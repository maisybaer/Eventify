<?php
require_once '../settings/core.php';
require_once '../controllers/product_controller.php';

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$product = null;
if ($product_id > 0) {
    $product = view_single_product_ctr($product_id);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=1.1">

</head>

<body>

    <header class="menu-tray mb-3">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="../index.php" class="btn btn-sm btn-outline-secondary">Home</a>
            <a href="../login/logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>
            <a href="cart.php" class="btn btn-sm btn-outline-secondary">Cart</a>
            <a href="all_product.php" class="btn btn-sm btn-outline-secondary">Back</a>
        <?php else: ?>
            <a href="../login/register.php" class="btn btn-sm btn-outline-primary">Register</a>
            <a href="../login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
        <?php endif; ?>
    </header>

    <div>
        <div class="container" style="padding-top:120px;">

            <div class="text-center">
                
                <h1>View Item</h1>

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


    <main class="container product-container">

        <?php if (!$product): ?>
            <div class="alert alert-warning">Product not found.</div>
        <?php else: ?>

            <?php
                // Prepare image URL (site-base aware)
                $imgField = trim((string)($product['product_image'] ?? ''));
                $imgUrl = '';
                $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
                $siteBase = dirname($scriptDir);
                if ($siteBase === '/' || $siteBase === '.') $siteBase = '';
                if ($imgField !== '') {
                    $filename = basename($imgField);
                    $fs = realpath(__DIR__ . '/../uploads/' . $filename);
                    if ($fs && file_exists($fs)) {
                        $imgUrl = $siteBase . '/uploads/' . $filename;
                    }
                }
            ?>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <?php if ($imgUrl): ?>
                        <img src="<?php echo htmlspecialchars($imgUrl); ?>" class="img-fluid product-image" alt="<?php echo htmlspecialchars($product['product_title']); ?>" onerror="this.onerror=null;this.src='<?php echo htmlspecialchars($siteBase . '/uploads/no-image.svg'); ?>'">
                    <?php else: ?>
                        <img src="<?php echo htmlspecialchars($siteBase . '/uploads/no-image.svg'); ?>" class="img-fluid product-image" alt="No image">
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header text-center highlight">
                            <h2 class="mb-0"><?php echo htmlspecialchars($product['product_title']); ?></h2>
                        </div>
                        <div class="card-body">
                            <p><strong>Product ID:</strong> <?php echo (int)$product['product_id']; ?></p>
                            <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category'] ?? $product['product_cat'] ?? ''); ?></p>
                            <p><strong>Brand:</strong> <?php echo htmlspecialchars($product['brand'] ?? $product['product_brand'] ?? ''); ?></p>
                            <p><strong>Price:</strong> $<?php echo htmlspecialchars($product['product_price']); ?></p>
                            <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($product['product_desc'] ?? '')); ?></p>
                            <p><strong>Keywords:</strong> <?php echo htmlspecialchars($product['product_keywords'] ?? ''); ?></p>
                            <div class="mt-3">
                                <label for="productQuantity" class="form-label fw-semibold">Quantity</label>
                                <input
                                    type="number"
                                    id="productQuantity"
                                    name="quantity"
                                    class="form-control"
                                    min="1"
                                    value="1"
                                >
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <form action="#" method="POST">
                                <input type="hidden" name="product_id" value="<?php echo (int)$product['product_id']; ?>">
                                <button id="addToCartBtn" class="btn btn-primary mt-2" data-id="<?php echo (int)$product['product_id']; ?>">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/all_products.js"></script>
    <script>
        $(document).ready(function(){
            $('#addToCartBtn').on('click', function(e){
                e.preventDefault();
                const productId = $(this).data('id');
                const quantity = parseInt($('#productQuantity').val(), 10) || 1;

                if (quantity <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid quantity',
                        text: 'Please enter a quantity of at least 1.'
                    });
                    return;
                }
                
                $.ajax({
                    url: '../actions/add_to_cart_action.php',
                    method: 'POST',
                    data: { product_id: productId, quantity: quantity },
                    dataType: 'json',
                    success: function(response) {
                        if(response.status === 'success'){
                            Swal.fire({
                                icon: 'success',
                                title: 'Added to Cart!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function(){
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to add item to cart. Please try again.'
                        });
                    }
                });
            });
        });
        </script>

</body>
</html>
