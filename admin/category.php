<?php
//session_start();
require_once '../settings/core.php';
require_once '../settings/db_class.php';

$user_id = getUserID();
$role = getUserRole();


$db = new db_connection();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css">
    <link rel="icon" href="../settings/favicon.ico">
</head>

<body>
    <header>
    <!-- Navigation -->
    <div class="menu-tray">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="../index.php"><i class="fas fa-home"></i> Home</a>
            <a href="../view/browse_vendors.php"><i class="fas fa-users"></i> Browse Vendors</a>
            <a href="../login/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a href="../index.php" class="btn btn-sm btn-primary">Home</a>
            <a href="../login/login.php" class="btn btn-sm btn-secondary">Login</a>
        <?php endif; ?>
    </div>
    </header>

    <main>
    <div class="container header-container">
        <div class="text-center mb-4 fade-in">
            <span class="badge mb-3">
                <a href="../home.php"><img src="../settings/logo.png" alt="eventify logo" style="width:80-px; height:80px; margin-right:8px;"></a>     
            </span>
            <h1 class="mb-2">Categories</h1>
            <p class="text-muted">Create and manage your categories for easy event organization.</p>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-section slide-up">
                    <h3 class="mb-3"><i class="fas fa-plus-circle"></i> Create New Category</h3>
                    <form id="addCatForm">
                        <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION['user_id'] ?? ''; ?>">
                        <div class="mb-3">
                            <label for="catName" class="form-label">Category Name *</label>
                            <input type="text" class="form-control" id="cat_name" name="cat_name" placeholder="Enter category name" required>
                        </div>
                        <button type="submit" class="btn btn-custom btn-lg w-100">Add Category</button>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="table-container slide-up">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="mb-0"><i class="fas fa-list"></i> Your Categories</h3>
                        <input type="text" id="searchCats" class="form-control" placeholder="Search categories..." style="max-width:300px;">
                    </div>

                    <div class="table-responsive">
                        <table class="table" id="catTable">
                            <thead>
                                <tr>
                                    <th>Category ID</th>
                                    <th>Category Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="3" class="text-center" style="padding:2rem;">No categories yet. Create one using the form to the left.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/category.js"></script>

<?php
$footer_base = '../';
include '../includes/footer.php';
?>
</body>
</html>

