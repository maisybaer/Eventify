<?php
require_once '../controllers/brand_controller.php';
require_once '../settings/core.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand_name = $_POST['brand_name'] ?? '';
    $user_id = $_POST['user_id'] ?? '';

    if (!empty($brand_name) && !empty($user_id)) {
        $result = add_brand_ctr(brand_name: $brand_name, user_id: $user_id);
        if ($result) {
            echo json_encode(["status" => "success", "message" => "Brand added successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to add brand."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Missing brand name or user ID."]);
    }
}

