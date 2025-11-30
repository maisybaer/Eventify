<?php
require_once '../controllers/event_controller.php';
require_once '../settings/core.php';
require_once '../helpers/upload_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect POST fields
    $event_id = $_POST['event_id'] ?? '';
    $eventCat = $_POST['eventCat'] ?? '';
    $eventDes = $_POST['eventDes'] ?? '';
    $eventPrice = $_POST['eventPrice'] ?? '';
    $eventLocation = $_POST['eventLocation'] ?? '';
    $eventStart = $_POST['eventStart'] ?? ''; 
    $eventEnd = $_POST['eventEnd'] ?? '';
    // accept either 'eventDate' or legacy 'updateEventDate'
    $eventDate = $_POST['eventDate'] ?? $_POST['updateEventDate'] ?? null;
    $eventKey = $_POST['eventKey'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    $flyer = '';

    // Handle file upload if present - using remote upload API
    if (isset($_FILES['flyer']) && $_FILES['flyer']['error'] === UPLOAD_ERR_OK) {
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
        $uploadResult = upload_file_to_api($_FILES['flyer'], $allowedExts);

        if ($uploadResult['success']) {
            // Store the full URL returned from the API
            $flyer = $uploadResult['url'];
        } else {
            echo json_encode(['status' => 'error', 'message' => $uploadResult['error']]);
            exit();
        }
    }

    if (!empty($event_id)) {
        $result = update_event_ctr($event_id, $eventCat, $eventDes, $eventPrice, $eventLocation, $eventDate, $eventStart, $eventEnd,  $flyer, $eventKey);

        if ($result) {
            echo json_encode(["status" => "success", "message" => "Event updated successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update event."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Missing fields."]);
    }

}
