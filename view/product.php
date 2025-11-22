<?php
//session_start();

require_once '../settings/core.php';
require_once '../controllers/event_controller.php';

$events = get_all_events_ctr();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>See events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css">
</head>

<style>
.container {
  display: grid;
  grid-template-columns: auto auto auto;
  background-color: dodgerblue;
  padding: 10px;
}
.container div {
  background-color: #f1f1f1;
  border: 1px solid black;
  padding: 10px;
  font-size: 30px;
  text-align: center;
}
</style>

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
        <h2 class="text-center mb-4">event Management</h2>

        <table class="table table-bordered table-striped" id="eventTable">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Location</th>
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
    <script src="../js/event.js"></script>

    
</body>

</html>