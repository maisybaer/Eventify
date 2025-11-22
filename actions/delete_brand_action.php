<?php
require_once '../controllers/brand_controller.php';
require_once '../settings/core.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cat_id = $_POST['brand_id'];
    $user_id = getUserID();

    $success = delete_brand_ctr($brand_id);

    echo json_encode([
        "status" => $success ? "success" : "error",
        "message" => $success ? "Brand deleted successfully!" : "Failed to brand category."
    ]);
}
