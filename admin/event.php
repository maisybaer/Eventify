<?php
//session_start();

require_once '../settings/core.php';
require_once '../controllers/category_controller.php';
//require_once '../controllers/vendor_controller.php';

$user_id = getUserID();
$role = getUserRole();


//get categories and brands for dropdowns
$allCat=get_all_cat_ctr();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css">
</head>

<body>
    <head>
	<div class="menu-tray">
		<span class="me-2">Menu:</span>
		<?php if (isset($_SESSION['user_id'])): ?>
            <a href="../index.php" class="btn btn-sm btn-outline-primary">Home</a>
			<a href="../login/logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>
		<?php else: ?>
            <a href="../index.php" class="btn btn-sm btn-outline-primary">Home</a>
			<a href="../login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
            <a href="../login/register.php" class="btn btn-sm btn-outline-secondary">Register</a>
            <p><br>Login or Register to see your Events</p>
		<?php endif; ?>			
	</div>
        </head>

    <main>
    <div class="container header-container">
        <div class="row justify-content-center animate__animated animate__fadeInDown">
           
            <h1>Products</h1>
            <h4>All the products you have created are listed below</h4>
        
            <!-- Add product form-->
            <div class="col-md-6">
                <div class="card animate__animated animate__zoomIn">
                    <div class="card-header text-center highlight">
                        <h4>Create a new products</h4>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="" class="mt-4" id="addEventForm">
                            <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION['user_id'] ?? ''; ?>">

                            <div class="mb-3">
                                <label for="event_name" class="form-label">Event Name</label>
                                <input type="text" class="form-control animate__animated animate__fadeInUp" id="event_name" name="event_name" required>
                            </div>

                            <div class="mb-3">
                                <label for="event_desc" class="form-label">Event Description</label>
                                <textarea class="form-control animate__animated animate__fadeInUp" id="event_desc" name="event_desc" rows="3" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="event_location" class="form-label">Location</label>
                                <input type="text" class="form-control animate__animated animate__fadeInUp" id="event_location" name="event_location" required>
                            </div>

                            <div class="mb-3">
                                <label for="event_date" class="form-label">Date</label>
                                <input type="date" class="form-control animate__animated animate__fadeInUp" id="event_date" name="event_date" required>
                            </div>

                            <div class="mb-3">
                                <label for="event_start" class="form-label">Start Time</label>
                                <input type="time" class="form-control animate__animated animate__fadeInUp" id="event_start" name="event_start" required>
                            </div>

                            <div class="mb-3">
                                <label for="event_end" class="form-label">End Time</label>
                                <input type="time" class="form-control animate__animated animate__fadeInUp" id="event_end" name="event_end" required>
                            </div>

                            <div class="mb-3">
                                <label for="event_cat" class="form-label">Event Category</label>
                                    <select class="form-control" id="event_cat" name="event_cat" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($allCat as $cat): ?>
                                            <option value="<?php echo htmlspecialchars($cat['cat_id']); ?>">
                                            <?php echo htmlspecialchars($cat['cat_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                            </div>

                            <div class="mb-3">
                                <label for="flyer" class="form-label">Upload your event flyer</label>
                                <input type="file" class="form-control animate__animated animate__fadeInUp" id="flyer" name="flyer">
                            </div> 

                            <button type="submit" class="btn btn-custom w-100 animate-pulse-custom">Add New Event</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- View Product-->
            <div class="col-md-6">
                <div class="card animate__animated animate__zoomIn">
                    <div class="card-header text-center highlight">
                        <h4>Your current Products</h4>
                    </div>


                    <div class="card-body">
                        <div class="table-responsive">
                        <table id="productTable" class="table table-sm table-striped table-bordered" style="min-width:900px;">
                            <thead>
                                <tr>
                                    <th>Event ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Title</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="9" class="text-center">No product available</td>
                                <tr>
                                    
                            
                                        <!-- Update Product Popup -->
                                            <div id="updatePopup" class="form-popup">
                                                <div class="card mx-auto p-4" style="max-width: 400px; background-color: #fff; border-radius: 10px;">
                                                    <h5 class="text-center mb-3 highlight">Update Product</h5>
                                                    <form id="updateForm">
                                                        <div class="mb-3">
                                                            
                                                            <input type="hidden" id="updateProductID">

                                                            <!-- Update Product Category -->

                                                            <label for="updateProductCat" class="form-label">New Product Category</label>
                                                            <select class="form-control" id="updateProductCat" name="updateProductCat" required>
                                                                <option value="">Select Category</option>
                                                                <?php foreach ($allCat as $cat): ?>
                                                                    <option value="<?php echo htmlspecialchars($cat['cat_id']); ?>">
                                                                    <?php echo htmlspecialchars($cat['cat_name']); ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>

                                                            <!-- Update Product Brand -->
                                                            <label for="updateProductBrand" class="form-label" >New Product Brand</label>
                                                            <select class="form-control" id="updateProductBrand" name="updateProductBrand" required>
                                                                <option value="">Select Brand</option>
                                                                <?php foreach ($allBrand as $brand): ?>
                                                                    <option value="<?php echo htmlspecialchars($brand['brand_id']); ?>">
                                                                    <?php echo htmlspecialchars($brand['brand_name']); ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                    
                                                            <!-- Update Product Title -->
                                                            <label for="updateProductTitle" class="form-label">New Product Title</label>
                                                            <input type="text" class="form-control" id="updateProductTitle" name="updateProductTitle" required>

                                                            <!-- Update Product Price -->
                                                            <label for="updateProductPrice" class="form-label">New Product Price</label>
                                                            <input type="number" class="form-control" id="updateProductPrice" name="updateProductPrice" required>

                                                            <!-- Update Product Description -->
                                                            <label for="updateProductDes" class="form-label">New Product Description</label>
                                                            <input type="text" class="form-control" id="updateProductDes" name="updateProductDesc" required>

                                                            <!-- Update Product Image -->
                                                            <label for="updateProductImage" class="form-label">New Product Image</label>
                                                            <input type="file" class="form-control" id="updateProductImage" name="updateProductImage">

                                                            <!-- Update Product Keywords -->
                                                            <label for="updateProductKey" class="form-label">New Product Keywords</label>
                                                            <input type="text" class="form-control" id="updateProductKey" name="updateProductKey" required>

                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                           <button type="submit" id="saveUpdate" class="btn btn-custom">Save</button>
                                                           <button type="button" id="cancelUpdate" class="btn btn-secondary">Cancel</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                </td>
                            </tbody>

                        </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/event.js"></script>

    
</body>

</html>

