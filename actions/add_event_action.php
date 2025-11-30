<?php
require_once '../controllers/event_controller.php';
require_once '../controllers/category_controller.php';
require_once '../settings/core.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // receive data

    $eventCat = $_POST['eventCat'] ?? '';
    $eventDes = $_POST['eventDes'] ?? '';
    $eventPrice = $_POST['eventPrice'] ?? '';
    $eventLocation = $_POST['eventLocation'] ?? '';
    $eventDate = $_POST['eventDate'] ?? null;
    $eventStart = $_POST['eventStart'] ?? '';
    $eventEnd = $_POST['eventEnd'] ?? '';
    $eventKey = $_POST['eventKey'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    $flyer = '';

    // Basic required fields - keywords and price are optional
    if (empty($eventCat) || empty($eventDes) || empty($eventLocation) || empty($eventStart) || empty($eventEnd)) {
        $response['status'] = 'error';
        $response['message'] = 'Please fill in all required fields!';
        echo json_encode($response);
        exit();
    }

    // Normalize optional fields
    if ($eventPrice === '' || $eventPrice === null) {
        $eventPrice = 0.00;
    }
    if ($eventKey === null) {
        $eventKey = '';
    }

    // If user_id not passed, fall back to session
    if (empty($user_id)) {
        $user_id = getUserID();
    }


    // For image upload
    if (isset($_FILES['flyer']) && $_FILES['flyer']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/';

        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                error_log('Failed to create uploads directory: ' . $uploadDir);
                $response['status'] = 'error';
                $response['message'] = 'Failed to create uploads directory. Please contact administrator.';
                echo json_encode($response);
                exit();
            }
        }

        // Check if directory is writable
        if (!is_writable($uploadDir)) {
            error_log('Uploads directory is not writable: ' . $uploadDir . ' (permissions: ' . substr(sprintf('%o', fileperms($uploadDir)), -4) . ')');
            $response['status'] = 'error';
            $response['message'] = 'Uploads directory is not writable. Please contact administrator.';
            echo json_encode($response);
            exit();
        }

        $fileTmp   = $_FILES['flyer']['tmp_name'];
        $fileName  = basename($_FILES['flyer']['name']);
        $fileExt   = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Allowed extensions
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileExt, $allowedExts)) {
            $response['status'] = 'error';
            $response['message'] = 'Only JPG, JPEG, PNG, and GIF images are allowed.';
            echo json_encode($response);
            exit();
        }

        // Unique filename
        $newFileName = uniqid("IMG_", true) . "." . $fileExt;
        $destPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmp, $destPath)) {
            // store filename only (consistent with update action)
            $flyer = $newFileName;
        } else {
            $upload_error = error_get_last();
            error_log('move_uploaded_file failed: ' . print_r($upload_error, true));
            error_log('Source: ' . $fileTmp . ', Destination: ' . $destPath);
            $response['status'] = 'error';
            $response['message'] = 'Image upload failed. Check server error logs for details.';
            echo json_encode($response);
            exit();
        }
    }

    // Validate category exists
    $allCats = get_all_cat_ctr();
    $catIds = array_map(function($c){ return (string)$c['cat_id']; }, $allCats ?: []);
    if (!in_array((string)$eventCat, $catIds, true)) {
        echo json_encode(['status' => 'error', 'message' => 'Selected category does not exist.']);
        exit();
    }

    // Validate user exists (if provided)
    if (!empty($user_id)) {
        require_once '../settings/db_class.php';
        $db = new db_connection();
        $userRow = $db->db_fetch_one("SELECT customer_id FROM eventify_customer WHERE customer_id = " . intval($user_id));
        if (!$userRow) {
            echo json_encode(['status' => 'error', 'message' => 'User not found for provided user_id.']);
            exit();
        }
    }

    // Build payload snapshot for debugging
    $payload = [
        'eventCat' => $eventCat,
        'eventDes' => $eventDes,
        'eventPrice' => $eventPrice,
        'eventLocation' => $eventLocation,
        'eventStart' => $eventStart,
        'eventEnd' => $eventEnd,
        'eventKey' => $eventKey,
        'user_id' => $user_id,
        'flyer_present' => isset($_FILES['flyer']) ? $_FILES['flyer']['name'] : null,
    ];

    // Insert event with exception handling so we return JSON instead of HTML on DB errors
    try {
        $result = add_event_ctr($eventCat, $eventDes, $eventPrice, $eventLocation, $eventDate, $eventStart, $eventEnd, $flyer, $eventKey, $user_id);
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Event added successfully!', 'payload' => $payload]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add event', 'payload' => $payload]);
        }
    } catch (mysqli_sql_exception $e) {
        // Return DB error message to help debug foreign-key problems (temporary)
        error_log('Add event DB error: ' . $e->getMessage());
        $msg = 'Database error while adding event. ' . $e->getMessage();
        echo json_encode(['status' => 'error', 'message' => $msg, 'payload' => $payload]);
    }
}
?>