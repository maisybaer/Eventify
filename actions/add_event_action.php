<?php
require_once '../controllers/event_controller.php';
require_once '../settings/core.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect POST data
    $event_name     = $_POST['event_name'] ?? '';
    $event_desc     = $_POST['event_desc'] ?? '';
    $event_location = $_POST['event_location'] ?? '';
    $event_date     = $_POST['event_date'] ?? '';
    $event_start    = $_POST['event_start'] ?? '';
    $event_end      = $_POST['event_end'] ?? '';
    // Handle uploaded flyer file (if any)
    $flyer = '';
    if (!empty($_FILES['flyer']) && !empty($_FILES['flyer']['tmp_name']) && is_uploaded_file($_FILES['flyer']['tmp_name'])) {
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $origName = basename($_FILES['flyer']['name']);
        $ext = pathinfo($origName, PATHINFO_EXTENSION);
        $safeBase = preg_replace('/[^A-Za-z0-9-_\.]/', '-', pathinfo($origName, PATHINFO_FILENAME));
        $newName = $safeBase . '-' . time() . '.' . $ext;
        $target = $uploadDir . $newName;
        if (move_uploaded_file($_FILES['flyer']['tmp_name'], $target)) {
            // store relative path used by front-end (e.g. 'uploads/filename')
            $flyer = 'uploads/' . $newName;
        }
    } else {
        $flyer = $_POST['flyer'] ?? '';
    }
    $event_cat      = $_POST['event_cat'] ?? '';
    $user_id        = $_POST['user_id'] ?? '';

    // Validate required fields
    if (
        !empty($event_name) &&
        !empty($event_location) &&
        !empty($event_date) &&
        !empty($event_start) &&
        !empty($event_end) &&
        !empty($event_cat) &&
        !empty($user_id)
    ) {

        // Call controller function
        $result = add_event_ctr(
            $event_name,
            $event_desc,
            $event_location,
            $event_date,
            $event_start,
            $event_end,
            $flyer,
            $event_cat,
            $user_id
        );

        if ($result) {
            echo json_encode([
                "status" => "success",
                "message" => "Event added successfully!"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to add event."
            ]);
        }

    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Missing required event fields."
        ]);
    }
}
