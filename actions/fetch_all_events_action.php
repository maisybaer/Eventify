<?php
require_once '../controllers/event_controller.php';
header('Content-Type: application/json');

try {
    // Fetch all events (no user restriction)
    $events = viewAllEvent_ctr();

    if ($events === false || $events === null) {
        echo json_encode(['status' => 'success', 'data' => []]);
    } else {
        echo json_encode(['status' => 'success', 'data' => $events]);
    }
} catch (Throwable $e) {
    error_log('Fetch all events error: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server error', 'data' => []]);
}
?>
