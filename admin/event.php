<?php
require_once '../settings/core.php';
require_once '../controllers/category_controller.php';

$user_id = getUserID();
$role = getUserRole();

// Get categories for dropdown
$allCat = get_all_cat_ctr();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - Eventify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../settings/styles.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="../settings/favicon.ico"/>
    
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            text-align: center;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            background: var(--gradient-primary);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 1.5rem;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--brand);
        }
        
        .stat-label {
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        
        .form-section {
            background: white;
            padding: 2rem;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
        }
        
        .table-container {
            background: white;
            padding: 2rem;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            overflow-x: auto;
        }
        
        .event-image-preview {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: var(--radius-md);
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
    </style>
</head>

<body>
    <header>
    <!-- Navigation -->
        <div class="menu-tray">
            
            <a href="../home.php" class="logo">
                <div class="logo-icon"><img src="../settings/logo.png" alt="eventify logo" style="height:30px;"></div>
            </a>
            

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../index.php"><i class="fas fa-home"></i> Home</a>
                <a href="../login/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="../index.php" class="btn btn-sm btn-primary">Home</a>
                <a href="../login/login.php" class="btn btn-sm btn-secondary">Login</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="container header-container">
        <!-- Header -->
        <div class="text-center mb-5 fade-in">
            <span class="badge mb-3">
                <i class="fas fa-calendar-alt"></i> Event Management
            </span>
            <h1 class="mb-3">My Events</h1>
            <p class="text-muted" style="font-size: 1.125rem;">
                Create, manage, and track all your events in one place
            </p>
        </div>


        <!-- Create Event Form -->
        <div class="form-section slide-up">
            <h3 class="mb-4">
                <i class="fas fa-plus-circle"></i> Create New Event
            </h3>
            
            <form id="addEventForm" enctype="multipart/form-data">
                <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION['user_id'] ?? ''; ?>">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="event_desc" class="form-label">
                            <i class="fas fa-heading"></i> Event Name *
                        </label>
                        <input type="text" class="form-control" id="event_desc" name="event_desc" placeholder="Enter event name..." required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="event_cat" class="form-label">
                            <i class="fas fa-tag"></i> Category *
                        </label>
                        <select class="form-control" id="event_cat" name="event_cat" required>
                            <option value="">Select Category</option>
                            <?php foreach ($allCat as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['cat_id']); ?>">
                                    <?php echo htmlspecialchars($cat['cat_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="event_location" class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Location *
                        </label>
                        <input type="text" class="form-control" id="event_location" name="event_location" placeholder="Event location..." required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="eventPrice" class="form-label">
                            <i class="fas fa-dollar-sign"></i> Ticket Price (GHS)
                        </label>
                        <input type="number" class="form-control" id="eventPrice" name="eventPrice" step="0.01" placeholder="0.00">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="event_start" class="form-label">
                            <i class="fas fa-clock"></i> Start Time *
                        </label>
                        <input type="time" class="form-control" id="event_start" name="event_start" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="event_end" class="form-label">
                            <i class="fas fa-clock"></i> End Time *
                        </label>
                        <input type="time" class="form-control" id="event_end" name="event_end" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="event_date" class="form-label">
                            <i class="fas fa-calendar-day"></i> Event Date
                        </label>
                        <input type="date" class="form-control" id="event_date" name="event_date">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="flyer" class="form-label">
                        <i class="fas fa-image"></i> Event Flyer
                    </label>
                    <input type="file" class="form-control" id="flyer" name="flyer" accept="image/*">
                    <small class="text-muted">Recommended: 800x600px, Max 5MB</small>
                </div>

                <div class="mb-3">
                    <label for="eventKey" class="form-label">
                        <i class="fas fa-tags"></i> Keywords (comma separated)
                    </label>
                    <input type="text" class="form-control" id="eventKey" name="eventKey" placeholder="music, concert, outdoor...">
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="fas fa-plus-circle"></i> Create Event
                </button>
            </form>
        </div>

        <!-- Events Table -->
        <div class="table-container slide-up">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    <i class="fas fa-list"></i> My Events
                </h3>
                <div class="d-flex gap-2">
                    <input type="text" id="searchEvents" class="form-control" placeholder="Search events..." style="max-width: 300px;">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table" id="eventTable">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Event Name</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th>Event Date</th>
                            <th>Time</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" class="text-center" style="padding: 3rem;">
                                <div style="font-size: 3rem; margin-bottom: 1rem;">🎭</div>
                                <p class="text-muted">No events yet. Create your first event above!</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Expose current user id and role to client-side scripts
        window.currentUserId = <?php echo json_encode($user_id ?? null); ?>;
        window.currentUserRole = <?php echo json_encode($role ?? null); ?>;
    </script>
    <script src="../js/event.js?v=<?php echo time(); ?>"></script>
    
    <script>
        // Search functionality
        $('#searchEvents').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('#eventTable tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
        
        // Update stats
        function updateStats() {
            const totalEvents = $('#eventTable tbody tr').length - $('#eventTable tbody tr:contains("No events")').length;
            $('#totalEvents').text(totalEvents);
        }
        
        // Call updateStats after events load
        setTimeout(updateStats, 1000);
    </script>

<?php
$footer_base = '../';
include '../includes/footer.php';
?>
</body>
</html>

