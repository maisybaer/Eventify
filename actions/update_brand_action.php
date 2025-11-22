<?php
require_once '../controllers/brand_controller.php';
require_once '../settings/core.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand_id = $_POST['brand_id'];
    $brand_name = $_POST['brand_name'];

    if ($brand_id && $cat_name) {
        $result = update_brand_ctr(brand_id: $brand_id, brand_name: $brand_name);

        if ($result) {
            echo json_encode(["status" => "success", "message" => "Brand updated successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update brand."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Missing fields."]);
    }

}
