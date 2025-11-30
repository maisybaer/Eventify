<?php
require_once '../controllers/event_controller.php';
require_once '../controllers/category_controller.php';
require_once '../settings/core.php';
require_once '../helpers/upload_helper.php';
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


    // For image upload - using remote upload API
    if (isset($_FILES['flyer']) && $_FILES['flyer']['error'] === UPLOAD_ERR_OK) {
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
        $uploadResult = upload_file_to_api($_FILES['flyer'], $allowedExts);

        if ($uploadResult['success']) {
            // Store the full URL returned from the API
            $flyer = $uploadResult['url'];
        } else {
            $response['status'] = 'error';
            $response['message'] = $uploadResult['error'];
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