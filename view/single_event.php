<?php
require_once '../settings/core.php';
require_once '../controllers/event_controller.php';

$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;
$event = null;
if ($event_id > 0) {
    $event = view_single_event_ctr($event_id);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=1.1">

</head>

<body>
	<div class="menu-tray">
		<?php if (isset($_SESSION['user_id'])): ?>
			
			<a href="../index.php" class="btn btn-sm btn-outline-secondary">Home</a>
			<a href=" ../login/logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>
            <a href="cart.php" class="btn btn-sm btn-outline-secondary">Basket</a>

		<?php else: ?>
			<a href="../login/register.php" class="btn btn-sm btn-outline-primary">Register</a>
			<a href="../login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
		<?php endif; ?>	
    </div>

    <div>
        <div class="container" style="padding-top:120px;">

            <div class="text-center">
                <h1>Event Details</h1>
            </div>
        </div>
    </div>


    <main class="container product-container">

        <?php if (!$event): ?>
            <div class="alert alert-warning">Event not found.</div>
        <?php else: ?>

            <?php
                // Prepare flyer URL (site-base aware)
                $imgField = trim((string)($event['flyer'] ?? ''));
                $imgUrl = '';
                $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
                $siteBase = dirname($scriptDir);
                if ($siteBase === '/' || $siteBase === '.') $siteBase = '';
                if ($imgField !== '') {
                    // if stored as 'uploads/filename' or just filename
                    $filename = basename($imgField);
                    $fs = realpath(__DIR__ . '/../uploads/' . $filename);
                    if ($fs && file_exists($fs)) {
                        $imgUrl = $siteBase . '/uploads/' . $filename;
                    } else {
                        // try raw path
                        $imgUrl = $imgField;
                    }
                }
            ?>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <?php if ($imgUrl): ?>
                        <img src="<?php echo htmlspecialchars($imgUrl); ?>" class="img-fluid product-image" alt="<?php echo htmlspecialchars($event['event_name']); ?>" onerror="this.onerror=null;this.src='<?php echo htmlspecialchars($siteBase . '/uploads/no-image.svg'); ?>'">
                    <?php else: ?>
                        <img src="<?php echo htmlspecialchars($siteBase . '/uploads/no-image.svg'); ?>" class="img-fluid product-image" alt="No image">
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header text-center highlight">
                            <h2 class="mb-0"><?php echo htmlspecialchars($event['event_name'] ?? ''); ?></h2>
                        </div>
                        <div class="card-body">
                            <p><strong>Event ID:</strong> <?php echo (int)$event['event_id']; ?></p>
                            <p><strong>Category:</strong> <?php echo htmlspecialchars($event['category'] ?? $event['event_cat'] ?? ''); ?></p>
                            <p><strong>Date:</strong> <?php echo htmlspecialchars($event['event_date'] ?? ''); ?></p>
                            <p><strong>Time:</strong> <?php echo htmlspecialchars(($event['event_start'] ?? '') . (($event['event_end'] ?? '') ? ' - ' . $event['event_end'] : '')); ?></p>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($event['event_location'] ?? ''); ?></p>
                            <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($event['event_desc'] ?? '')); ?></p>
                            <div class="mt-3">
                                <label for="ticketQuantity" class="form-label fw-semibold">Number of Tickets</label>
                                <input
                                    type="number"
                                    id="ticketQuantity"
                                    name="quantity"
                                    class="form-control"
                                    min="1"
                                    value="1"
                                >
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <button id="addToCartBtn" class="btn btn-primary" data-id="<?php echo (int)$event['event_id']; ?>">Add to Cart</button>
                            <a href="../view/all_events.php" class="btn btn-outline-secondary ms-2">Back to events</a>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function(){
            $('#addToCartBtn').on('click', function(e){
                e.preventDefault();
                const eventId = $(this).data('id');
                const quantity = parseInt($('#ticketQuantity').val(), 10) || 1;

                if (quantity <= 0) {
                    Swal.fire({ icon: 'warning', title: 'Invalid quantity', text: 'Please enter at least 1 ticket.' });
                    return;
                }

                $.ajax({
                    url: '../actions/add_to_cart_action.php',
                    method: 'POST',
                    data: { event_id: eventId, quantity: quantity },
                    dataType: 'json',
                    success: function(response) {
                        if(response.status === 'success'){
                            Swal.fire({ icon: 'success', title: 'Added to Cart!', text: response.message, timer: 1500, showConfirmButton: false });
                        } else if (response.status === 'error') {
                            Swal.fire({ icon: 'error', title: 'Error', text: response.message });
                        } else {
                            Swal.fire({ icon: 'info', title: 'Notice', text: response.message || 'Added' });
                        }
                    },
                    error: function(){
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to add to cart. Please try again.' });
                    }
                });
            });
        });
    </script>

</body>
</html>
