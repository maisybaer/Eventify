<?php
require_once '../controllers/event_controller.php';
require_once '../classes/event_class.php';
header('Content-Type: application/json');

try {
    // Primary: use controller
    $events = null;
    try {
        $events = view_all_event_ctr();
    } catch (Throwable $inner) {
        error_log('view_all_event_ctr threw: ' . $inner->getMessage());
        $events = false;
    }

    // Fallback: direct class call if controller failed
    if ($events === false || $events === null) {
        error_log('view_all_event_ctr returned false or null, attempting direct DB fetch');
        $ev = new Event();
        $events = $ev->viewAllEvent();
    }

    if ($events === false || $events === null) {
        error_log('Both controller and direct fetch failed in fetch_all_events_action.php');
        echo json_encode(['status' => 'error', 'message' => 'Server error', 'data' => []]);
    } else {
        echo json_encode(['status' => 'success', 'data' => $events]);
    }
} catch (Throwable $e) {
    error_log('Fetch all events error: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server error', 'data' => []]);
}
?>
