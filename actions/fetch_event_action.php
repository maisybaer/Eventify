<?php
/**
 * Fetch Event Action
 * Handles all event-related fetch operations
 * Usage: ?action=all or ?action=single&event_id=X or ?action=by_customer
 */

require_once '../controllers/event_controller.php';
require_once '../classes/event_class.php';
require_once '../settings/core.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? 'all';

try {
    switch ($action) {
        case 'all':
            // Fetch all events
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
                error_log('Both controller and direct fetch failed');
                echo json_encode(['status' => 'error', 'message' => 'Server error', 'data' => []]);
            } else {
                echo json_encode(['status' => 'success', 'data' => $events]);
            }
            break;

        case 'single':
            // Fetch single event with image processing
            $events = view_all_event_ctr();

            if ($events === false) {
                error_log('fetch_event_action.php: view_all_event_ctr returned false');
                echo json_encode(['error' => 'Database query failed', 'events' => []]);
                exit;
            }

            if (empty($events)) {
                echo json_encode([]);
                exit;
            }

            $out = [];
            $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
            $siteBase = dirname($scriptDir);
            if ($siteBase === '/' || $siteBase === '.') $siteBase = '';

            foreach ($events as $p) {
                $item = $p;
                $img = trim((string)($p['event_image'] ?? $p['flyer'] ?? ''));
                $imageUrl = '';

                if ($img !== '') {
                    if (preg_match('#^https?://#i', $img)) {
                        $imageUrl = $img;
                    } else {
                        $filename = basename($img);
                        $fs = realpath(__DIR__ . '/../uploads/' . $filename);
                        if ($fs && file_exists($fs)) {
                            $imageUrl = $siteBase . '/uploads/' . $filename;
                        } else {
                            $imageUrl = $siteBase . '/uploads/no-image.svg';
                        }
                    }
                } else {
                    $imageUrl = $siteBase . '/uploads/no-image.svg';
                }

                $item['image_url'] = $imageUrl;
                $out[] = $item;
            }

            echo json_encode($out);
            break;

        case 'by_customer':
            // Fetch events created by the logged-in customer
            $customer_id = getUserID();
            if (!$customer_id) {
                echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
                exit;
            }

            $events = get_event_ctr($customer_id);

            if ($events === false || $events === null) {
                echo json_encode(['status' => 'success', 'data' => []]);
            } else {
                echo json_encode(['status' => 'success', 'data' => $events]);
            }
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
} catch (Throwable $e) {
    error_log('Fetch event error: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server error', 'data' => []]);
}
?>
