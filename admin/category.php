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
    <title>Categories - Eventify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=1.1">
</head>

<body>
    <header>
	<div class="menu-tray">
		<span class="me-2">Menu:</span>
		<?php if (isset($_SESSION['user_id'])): ?>
            <a href="../index.php" class="btn btn-sm btn-outline-primary">Home</a>
			<a href="../login/logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>
		<?php else: ?>
            <a href="../index.php" class="btn btn-sm btn-outline-primary">Home</a>
			<a href="../login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
            <p><br>Login to see your Categories</p>
		<?php endif; ?>			
	</div>
    </header>

    <main>
    <div class="container header-container">
        <div class="row justify-content-center animate__animated animate__fadeInDown">
           
            <h1><i class="fas fa-tags me-2"></i>Event Categories</h1>
            <h4>Manage your event categories</h4>
        
            <!-- Create category form-->
            <div class="col-md-6 mb-4">
                <div class="card animate__animated animate__zoomIn">
                    <div class="card-header text-center highlight">
                        <h4 class="text-light"><i class="fas fa-plus me-2"></i>Create New Category</h4>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="" class="mt-4" id="addCatForm">
                            <div class="mb-3">
                                <label for="cat_name" class="form-label">Category Name</label>
                                <input type="text" class="form-control animate__animated animate__fadeInUp" id="cat_name" name="cat_name" placeholder="Enter category name..." required>
                                <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION['user_id'] ?? ''; ?>">
                            </div>

                            <button type="submit" class="btn btn-custom w-100 animate-pulse-custom">
                                <i class="fas fa-plus me-2"></i>Add New Category
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- View Categories-->
            <div class="col-md-6 mb-4">
                <div class="card animate__animated animate__zoomIn">
                    <div class="card-header text-center highlight">
                        <h4 class="text-light"><i class="fas fa-list me-2"></i>Your Categories</h4>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="catTable" class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Category Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="3" class="text-center">Loading categories...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </main>

    <!-- Update Category Modal -->
    <div class="modal fade" id="updateCategoryModal" tabindex="-1" aria-labelledby="updateCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateCategoryModalLabel">
                        <i class="fas fa-edit me-2"></i>Update Category
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateCategoryForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="update_cat_name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="update_cat_name" required>
                            <input type="hidden" id="update_cat_id">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-custom">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/category.js"></script>
</body>

</html>

