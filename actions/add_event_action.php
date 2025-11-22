<?php
require_once '../controllers/event_controller.php';
require_once '../settings/core.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //recieve data

    $eventCat = $_POST['eventCat'] ?? '';
    $eventDes = $_POST['eventDes'] ?? '';
    $eventPrice = $_POST['eventPrice'] ?? '';
    $eventLocation = $_POST['eventLocation'] ?? '';
    $eventStart = $_POST['eventStart'] ?? ''; 
    $eventEnd = $_POST['eventEnd'] ?? '';
    $eventKey = $_POST['eventKey'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    $flyer = '';
    if (empty($eventCat) || empty($eventDes) || $eventPrice === '' || empty($eventLocation) || empty($eventStart) || empty($eventEnd) || empty($eventKey)) {
        $response['status'] = 'error';
        $response['message'] = 'Please fill in all fields!';
        echo json_encode($response);
        exit();
    }


    // For image upload 
    if (isset($_FILES['flyer']) && $_FILES['flyer']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
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
            $response['status'] = 'error';
            $response['message'] = 'Image upload failed';
            echo json_encode($response);
            exit();
        }
    }

    // Insert event
    $result = add_event_ctr($eventCat, $eventDes, $eventPrice, $eventLocation, $eventStart, $eventEnd, $flyer, $eventKey, $user_id);
    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'event added successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add event']);
    }
}
?>