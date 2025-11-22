<?php
require_once '../controllers/event_controller.php';
require_once '../settings/core.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect POST fields
    $event_id = $_POST['event_id'] ?? '';
    $eventCat = $_POST['eventCat'] ?? '';
    $eventDes = $_POST['eventDes'] ?? '';
    $eventPrice = $_POST['eventPrice'] ?? '';
    $eventLocation = $_POST['eventLocation'] ?? '';
    $eventStart = $_POST['eventStart'] ?? ''; 
    $eventEnd = $_POST['eventEnd'] ?? '';
    $eventKey = $_POST['eventKey'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    $flyer = '';

    // Handle file upload if present
    if (isset($_FILES['flyer']) && $_FILES['flyer']['error'] === UPLOAD_ERR_OK) {
        // store uploads in the uploads/ folder (project root)
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileTmp  = $_FILES['flyer']['tmp_name'];
        $fileName = basename($_FILES['flyer']['name']);
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExts = ['jpg','jpeg','png','gif'];
        if (in_array($fileExt, $allowedExts)) {
            $newFileName = uniqid('IMG_', true) . '.' . $fileExt;
            $destPath = $uploadDir . $newFileName;
            if (move_uploaded_file($fileTmp, $destPath)) {
                // store only the filename in DB; frontend resolves to /uploads/<filename>
                $flyer = $newFileName;
            }
        }
    }

    if (!empty($event_id)) {
        $result = update_event_ctr($event_id, $eventCat, $eventDes, $eventPrice, $eventLocation, $eventStart, $eventEnd,  $flyer, $eventKey);

        if ($result) {
            echo json_encode(["status" => "success", "message" => "Event updated successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update event."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Missing fields."]);
    }

}
