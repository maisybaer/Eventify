<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../settings/core.php';
require_once '../controllers/event_controller.php';
header('Content-Type: application/json');

// Handle incoming requests
$action = $_GET['action'] ?? '';

switch ($action) {

//view all events
    case 'view_all':
        $events = view_all_event_ctr();
        echo json_encode($events);
        break;

    case 'search':
        if (!isset($_GET['query']) || empty(trim($_GET['query']))) {
            echo json_encode(["status" => "error", "message" => "Search query missing."]);
            exit;
        }

        $query = trim($_GET['query']);
        $evt = new Event();
        $results = $evt->search($query);

        echo json_encode($results);
        break;

//filter by category
    case 'filter_by_category':
        if (!isset($_GET['cat_id']) || !is_numeric($_GET['cat_id'])) {
            echo json_encode(["status" => "error", "message" => "Invalid category ID"]);
            exit;
        }

        $cat_id = intval($_GET['cat_id']);
        $events = filter_by_cat_ctr($cat_id);

        echo json_encode($events);
        break;


//view single event
    case 'view_single':
        if (!isset($_GET['event_id']) || !is_numeric($_GET['event_id'])) {
            echo json_encode(["status" => "error", "message" => "Invalid event ID"]);
            exit;
        }

        $event_id = intval($_GET['event_id']);
        $event = view_single_event_ctr($event_id);

        echo json_encode($event);
        break;



//default
    default:
        echo json_encode(["status" => "error", "message" => "Invalid action."]);
        break;
}
?>

