<?php
require_once '../controllers/event_controller.php';
require_once '../settings/core.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'] ?? null;
    if (!$event_id) {
        echo json_encode(["status" => "error", "message" => "Missing event id"]);
        exit();
    }

    $user_id = getUserID();
    $role = getUserRole();

    // Load event and check ownership or admin privileges
    $event = view_single_event_ctr($event_id);
    if (!$event) {
        echo json_encode(["status" => "error", "message" => "Event not found"]);
        exit();
    }

    // Allow delete if current user is the owner or has admin role (role == 1)
    if ((isset($event['added_by']) && intval($event['added_by']) !== intval($user_id)) && intval($role) !== 1) {
        echo json_encode(["status" => "error", "message" => "You are not authorized to delete this event"]);
        exit();
    }

    $success = delete_event_ctr($event_id);

    echo json_encode([
        "status" => $success ? "success" : "error",
        "message" => $success ? "Event deleted successfully!" : "Failed to delete event."
    ]);
}
