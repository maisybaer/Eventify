<?php
require_once '../controllers/event_controller.php';
require_once '../settings/core.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $user_id = getUserID();

    $success = delete_event_ctr($event_id);

    echo json_encode([
        "status" => $success ? "success" : "error",
        "message" => $success ? "Event deleted successfully!" : "Failed to delete event."
    ]);
}
