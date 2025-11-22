<?php
require_once '../controllers/brand_controller.php';
require_once '../settings/core.php';

header('Content-Type: application/json');

try {
    $user_id = getUserID();

    if (!$user_id) {
        echo json_encode([]);
        exit;
    }

    $brands = get_brand_ctr($user_id);

    if (!is_array($brands)) {
        $brands = [];
    }

    echo json_encode($brands);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch brands.'
    ]);
}
