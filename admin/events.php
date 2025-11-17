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
    <title>Manage Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=1.1">
</head>

<body>
    
	<div class="menu-tray">
		<span class="me-2">Menu:</span>
		<?php if (isset($_SESSION['user_id'])): ?>
            <a href="../index.php" class="btn btn-sm btn-outline-primary">Home</a>
			<a href="../login/logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>
		<?php else: ?>
            <a href="../index.php" class="btn btn-sm btn-outline-primary">Home</a>
			<a href="../login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
            <p><br>Login to see your Events</p>
		<?php endif; ?>			
	</div>
    

    <main>
    <div class="container header-container">
        <div class="row justify-content-center animate__animated animate__fadeInDown">
           
            <h1>Manage your Events</h1>
            <h4>Create or manage your events.</h4>
        
            <!-- Create category form-->
            <div class="col-md-6">
                <div class="card animate__animated animate__zoomIn">
                    <div class="card-header text-center highlight">
                        <h4 class="text-light">Create a new event</h4>
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
                                <input type="text" class="form-control animate__animated animate__fadeInUp" id="event_desc" name="event_desc" required>

                            </div>

                            <div class="mb-3">
                                <label for="event_location" class="form-label">Location</label>
                                <input type="text" class="form-control animate__animated animate__fadeInUp" id="event_location" name="event_location" required>
                            </div>

                            <div class="mb-3">
                                <label for="event_date" class="form-label">Date</label>
                                <input type="date" class="form-control animate__animated animate__fadeInUp" id="event_date" name="cat_name" required>
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
                                <input type="time" class="form-control animate__animated animate__fadeInUp" id="event_cat" name="event_cat" required>
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

            <br> 

            <!-- View Events-->
            <div class="col-md-6">
                <div class="card animate__animated animate__zoomIn">
                    <div class="card-header text-center highlight">
                        <h4>Your current Events</h4>
                    </div>


                    <div class="card-body">
                        <table id="eventTable">
                            <thead>
                                <tr>
                                    <th>Event ID</th>
                                    <th>Event Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <td>No Events available</td>
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

