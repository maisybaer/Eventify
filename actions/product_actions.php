<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../settings/core.php';
require_once '../controllers/event_controller.php';
header('Content-Type: application/json');

// Backwards-compatible proxy for product endpoints — maps to events
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'view_all':
        $events = view_all_events_ctr();
        echo json_encode($events);
        break;

    case 'search':
        if (!isset($_GET['query']) || empty(trim($_GET['query']))) {
            echo json_encode(["status" => "error", "message" => "Search query missing."]);
            exit;
        }

        $query = trim($_GET['query']);
        $results = search_events_ctr($query);

        echo json_encode($results);
        break;

    case 'filter_by_category':
        if (!isset($_GET['cat_id']) || !is_numeric($_GET['cat_id'])) {
            echo json_encode(["status" => "error", "message" => "Invalid category ID"]);
            exit;
        }

        $cat_id = intval($_GET['cat_id']);
        $events = filter_by_category_ctr($cat_id);
        echo json_encode($events);
        break;

    case 'view_single':
        if (!isset($_GET['product_id']) && !isset($_GET['event_id'])) {
            echo json_encode(["status" => "error", "message" => "Invalid product/event ID"]);
            exit;
        }

        $id = isset($_GET['event_id']) ? intval($_GET['event_id']) : intval($_GET['product_id']);
        $event = view_single_event_ctr($id);
        echo json_encode($event);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action."]);
        break;
}
?>

