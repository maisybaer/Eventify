<?php
require_once '../settings/core.php';
require_once '../settings/db_class.php';
header('Content-Type: application/json');

try {
    $user_id = getUserID();
    if (!$user_id) {
        echo json_encode([]);
        exit;
    }

    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    if ($order_id <= 0) {
        echo json_encode([]);
        exit;
    }

    $db = new db_connection();
    $db->db_connect();

    // Fetch only items that belong to events created by the current user
    $sql = "SELECT od.event_id, od.qty, p.event_desc AS product_title, p.event_price AS product_price, p.flyer AS product_image
            FROM eventify_orderdetails od
            INNER JOIN eventify_products p ON od.event_id = p.event_id
            INNER JOIN eventify_orders o ON od.order_id = o.order_id
            WHERE od.order_id = ? AND p.added_by = ?";

    $stmt = $db->db->prepare($sql);
    if (!$stmt) {
        // fallback: try a simple query
        $rows = $db->db_fetch_all("SELECT od.event_id, od.qty, p.event_desc AS product_title, p.event_price AS product_price, p.flyer AS product_image
            FROM eventify_orderdetails od
            INNER JOIN eventify_products p ON od.event_id = p.event_id
            WHERE od.order_id = $order_id AND p.added_by = " . intval($user_id));
        if ($rows === false) $rows = [];
        echo json_encode($rows);
        exit;
    }

    $stmt->bind_param('ii', $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if ($rows === null) $rows = [];

    echo json_encode($rows);
} catch (Throwable $e) {
    http_response_code(500);
    error_log('Fetch order products error: ' . $e->getMessage());
    echo json_encode([]);
}
