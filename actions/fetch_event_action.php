<?php
require_once '../controllers/event_controller.php';
require_once '../settings/core.php';

header('Content-Type: application/json');

try {
    // Get the current user ID
    $user_id = getUserID();

    if (!$user_id) {
        echo json_encode([]);
        exit;
    }

    // Fetch events for this user
    $events = get_event_ctr($user_id);

    if (!is_array($events)) {
        $events = [];
    }

    echo json_encode($events);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch events.'
    ]);
}
