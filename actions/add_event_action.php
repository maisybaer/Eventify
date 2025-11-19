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
    $flyer          = $_POST['flyer'] ?? ''; // If uploaded via <input type="file">, handle $_FILES instead
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
