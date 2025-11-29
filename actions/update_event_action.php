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
    // accept either 'eventDate' or legacy 'updateEventDate'
    $eventDate = $_POST['eventDate'] ?? $_POST['updateEventDate'] ?? null;
    $eventKey = $_POST['eventKey'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    $flyer = '';

    // Handle file upload if present
    if (isset($_FILES['flyer']) && $_FILES['flyer']['error'] === UPLOAD_ERR_OK) {
        // store uploads in the uploads/ folder (project root)
        $uploadDir = __DIR__ . '/../uploads/';

        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                error_log("Failed to create uploads directory: $uploadDir");
                echo json_encode(['status' => 'error', 'message' => 'Failed to create uploads directory. Please contact administrator.']);
                exit();
            }
        }

        // Check if directory is writable
        if (!is_writable($uploadDir)) {
            error_log("Uploads directory is not writable: $uploadDir (permissions: " . substr(sprintf('%o', fileperms($uploadDir)), -4) . ')');
            echo json_encode(['status' => 'error', 'message' => 'Uploads directory is not writable. Please contact administrator.']);
            exit();
        }

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
            } else {
                $upload_error = error_get_last();
                error_log("move_uploaded_file failed: " . print_r($upload_error, true));
                error_log("Source: $fileTmp, Destination: $destPath");
                echo json_encode(['status' => 'error', 'message' => 'Image upload failed. Check server error logs for details.']);
                exit();
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Only JPG, JPEG, PNG, and GIF images are allowed.']);
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
