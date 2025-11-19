<?php
require_once '../controllers/event_controller.php';
require_once '../settings/core.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the event ID from POST
    $event_id = $_POST['event_id'] ?? null;

    if (!$event_id) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing event ID."
        ]);
        exit;
    }

    // Call controller function to delete the event
    $success = delete_event_ctr($event_id);

    echo json_encode([
        "status" => $success ? "success" : "error",
        "message" => $success ? "Event deleted successfully!" : "Failed to delete event."
    ]);
}
