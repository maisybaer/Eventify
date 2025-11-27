<?php
require_once '../controllers/order_controller.php';
require_once '../settings/core.php';
header('Content-Type: application/json');

try {
    $user_id = getUserID();
    if (!$user_id) {
        echo json_encode([]);
        exit;
    }

    // Use a direct DB query via Order class for a custom report
    require_once '../classes/order_class.php';
    $order = new Order();

    $uid = intval($user_id);
    $sql = "SELECT o.order_id, o.invoice_no, o.customer_id, o.order_date, o.order_status,
                   COALESCE(p.amt, SUM(od.qty * pr.event_price)) AS total_price,
                   COUNT(od.event_id) AS item_count
            FROM eventify_orders o
            LEFT JOIN eventify_payment p ON o.order_id = p.order_id
            JOIN eventify_orderdetails od ON o.order_id = od.order_id
            JOIN eventify_products pr ON od.event_id = pr.event_id
            WHERE pr.added_by = $uid
            GROUP BY o.order_id
            ORDER BY o.order_date DESC, o.order_id DESC";

    $rows = $order->db_fetch_all($sql);
    if ($rows === false) $rows = [];

    echo json_encode($rows);
} catch (Throwable $e) {
    http_response_code(500);
    error_log('Fetch orders by creator error: ' . $e->getMessage());
    echo json_encode([]);
}

?>
