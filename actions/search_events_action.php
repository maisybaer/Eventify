<?php
require_once '../controllers/event_controller.php';
require_once '../controllers/category_controller.php';
require_once '../settings/core.php';

header('Content-Type: application/json');

try {
    // Get search parameters
    $search_query = trim($_GET['q'] ?? $_POST['q'] ?? '');
    $category_filter = $_GET['category'] ?? $_POST['category'] ?? '';
    
    $events = [];
    
    if (!empty($search_query) || !empty($category_filter)) {
        // Search for events
        if (!empty($category_filter) && empty($search_query)) {
            // Filter by category only
            $events = filter_by_category_ctr($category_filter);
        } else if (!empty($search_query) && empty($category_filter)) {
            // General text search
            $events = search_events_ctr($search_query);
        } else {
            // Search with both query and category
            $events = search_events_with_category_ctr($search_query, $category_filter);
        }
    } else {
        // No search criteria, return all events
        $events = view_all_events_ctr();
    }
    
    if (!is_array($events)) {
        $events = [];
    }
    
    // Add category names to events
    foreach ($events as &$event) {
        if (isset($event['cat_id'])) {
            $categories = get_all_cat_ctr();
            foreach ($categories as $cat) {
                if ($cat['cat_id'] == $event['cat_id']) {
                    $event['cat_name'] = $cat['cat_name'];
                    break;
                }
            }
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $events,
        'count' => count($events)
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Search failed: ' . $e->getMessage()
    ]);
}

/**
 * Search events by text across multiple fields
 */
function search_events_ctr($query) {
    try {
        require_once '../classes/event_class.php';
        $event = new Event();
        return $event->searchEvents($query);
    } catch (Exception $e) {
        error_log("Error searching events: " . $e->getMessage());
        return [];
    }
}

/**
 * Search events with category filter
 */
function search_events_with_category_ctr($query, $category_id) {
    try {
        require_once '../classes/event_class.php';
        $event = new Event();
        return $event->searchEventsWithCategory($query, $category_id);
    } catch (Exception $e) {
        error_log("Error searching events with category: " . $e->getMessage());
        return [];
    }
}
?>