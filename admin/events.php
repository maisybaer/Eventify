<?php
require_once '../settings/core.php';
require_once '../settings/db_class.php';
require_once '../controllers/category_controller.php';

$user_id = getUserID();
$role = getUserRole();


$db = new db_connection();

// fetch categories created by this user (fallback to all categories if not per-user)
$allCat = get_cat_ctr($user_id);

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
        
            <!-- Create event form -->
            <div class="col-md-6 mb-4">
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

            <!-- View Events -->
            <div class="col-md-6 mb-4">
                <div class="card animate__animated animate__zoomIn">
                    <div class="card-header text-center highlight">
                        <h4>Your current Events</h4>
                    </div>


                    <div class="card-body">
                        <div class="table-responsive">
                        <table id="eventTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Event ID</th>
                                    <th>Event Name</th>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="5">Loading events...</td></tr>
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

