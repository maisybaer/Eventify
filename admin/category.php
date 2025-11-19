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
    <title>Categories - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css">
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
           
            <h1>Categories</h1>
            <h4>All the categories you have created are listed below</h4>
        
            <!-- Create category form-->
            <div class="col-md-6">
                <div class="card animate__animated animate__zoomIn">
                    <div class="card-header text-center highlight">
                        <h4>Create a new category</h4>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="" class="mt-4" id="addCatForm">
                            <div class="mb-3">
                                <label for="catName" class="form-label">Category Name</label>
                                <input type="text" class="form-control animate__animated animate__fadeInUp" id="cat_name" name="cat_name" required>
                                <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION['user_id'] ?? ''; ?>">
                            </div>

                            <button type="submit" class="btn btn-custom w-100 animate-pulse-custom">Add New Category</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- View Categories-->
            <div class="col-md-6">
                <div class="card animate__animated animate__zoomIn">
                    <div class="card-header text-center highlight">
                        <h4>Your current Categories</h4>
                    </div>


                    <div class="card-body">
                        <table id="catTable">
                            <thead>
                                <tr>
                                    <th>Category ID</th>
                                    <th>Category Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <td>No categories available</td>
                                <td></td>
                                <td>
                                    <button onclick="openForm()" class="small btn btn-custom w-100 animate-pulse-custom">Edit</button>
                                        <!-- Update Category Popup -->
                                            <div id="updatePopup" class="form-popup">
                                                <div class="card mx-auto p-4" style="max-width: 400px; background-color: #fff; border-radius: 10px;">
                                                    <h5 class="text-center mb-3 highlight">Update Category</h5>
                                                    <form id="updateForm">
                                                        <div class="mb-3">
                                                            <label for="update_cat_name" class="form-label">New Category Name</label>
                                                            <input type="text" class="form-control" id="update_cat_name" required>
                                                            <input type="hidden" id="update_cat_id">
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <button type="submit" id="saveUpdate" class="btn btn-custom">Save</button>
                                                            <button type="button" id="cancelUpdate" class="btn btn-secondary">Cancel</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                    <button class="small btn btn-custom w-100 animate-pulse-custom">Delete</button>
                                </td>
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

    
</body>

</html>

