<?php
require_once '../controllers/event_controller.php';
require_once '../settings/core.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect POST data
    $event_id       = $_POST['event_id'] ?? null;
    $event_name     = $_POST['event_name'] ?? '';
    $event_desc     = $_POST['event_desc'] ?? '';
    $event_location = $_POST['event_location'] ?? '';
    $event_date     = $_POST['event_date'] ?? '';
    $event_start    = $_POST['event_start'] ?? '';
    $event_end      = $_POST['event_end'] ?? '';
    $flyer          = $_POST['flyer'] ?? ''; // handle file upload separately if needed
    $event_cat      = $_POST['event_cat'] ?? '';

    // Validate required fields
    if ($event_id && $event_name && $event_location && $event_date && $event_start && $event_end && $event_cat) {

        $result = update_event_ctr(
            $event_id,
            $event_name,
            $event_desc,
            $event_location,
            $event_date,
            $event_start,
            $event_end,
            $flyer,
            $event_cat
        );

        if ($result) {
            echo json_encode([
                "status" => "success",
                "message" => "Event updated successfully!"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to update event."
            ]);
        }

    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Missing required fields."
        ]);
    }
}
