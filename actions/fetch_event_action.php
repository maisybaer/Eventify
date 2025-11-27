<?php
require_once '../controllers/event_controller.php';
require_once '../settings/core.php';

// Get the logged-in user's ID (if required)
$user_id = getUserID();

// Fetch all events
$events = view_all_event_ctr();

// Debug: Check if there's an error
if ($events === false) {
    // Log error and return empty array with debug info
    error_log('fetch_event_action.php: view_all_event_ctr returned false');
    echo json_encode(['error' => 'Database query failed', 'events' => []]);
    exit;
}

if (empty($events)) {
    echo json_encode([]);
    exit;
}


$out = [];
// determine site base (e.g. /E-commerce_Labs) to build site-relative URLs
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$siteBase = dirname($scriptDir);
if ($siteBase === '/' || $siteBase === '.') $siteBase = '';

foreach ($events as $p) {
    $item = $p;
    // event rows use 'event_image' as the column name; also accept legacy 'flyer'
    $img = trim((string)($p['event_image'] ?? $p['flyer'] ?? ''));
    $imageUrl = '';

    if ($img !== '') {
        // absolute URL?
        if (preg_match('#^https?://#i', $img)) {
            $imageUrl = $img;
        } else {
            // treat as filename or relative path under uploads
            $filename = basename($img);
            $fs = realpath(__DIR__ . '/../uploads/' . $filename);
            if ($fs && file_exists($fs)) {
                // site-relative URL (includes site base if app is in a subfolder)
                $imageUrl = $siteBase . '/uploads/' . $filename;
            } else {
                // fallback to a local placeholder inside uploads
                $imageUrl = $siteBase . '/uploads/no-image.svg';
            }
        }
    } else {
        $imageUrl = $siteBase . '/uploads/no-image.svg';
    }

    $item['image_url'] = $imageUrl;
    $out[] = $item;
}

header('Content-Type: application/json');
echo json_encode($out);
exit;
