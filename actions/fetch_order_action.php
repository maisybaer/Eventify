<?php
/**
 * Fetch Order Action
 * Handles all order-related fetch operations
 * Usage: ?action=user_orders or ?action=order_products&order_id=X or ?action=by_creator
 */

require_once '../controllers/order_controller.php';
require_once '../classes/order_class.php';
require_once '../settings/db_class.php';
require_once '../settings/core.php';
header('Content-Type: application/json');

try {
    $user_id = getUserID();
    if (!$user_id) {
        echo json_encode([]);
        exit;
    }

    $action = $_GET['action'] ?? 'user_orders';

    switch ($action) {
        case 'user_orders':
            // Fetch all orders for the current user
            $orders = get_user_orders_ctr($user_id);
            if (!is_array($orders)) {
                $orders = [];
            }
            echo json_encode($orders);
            break;

        case 'order_products':
            // Fetch products for a specific order
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
            break;

        case 'by_creator':
            // Fetch orders for events created by the user
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
                    GROUP BY o.order_id, o.invoice_no, o.customer_id, o.order_date, o.order_status, p.amt
                    ORDER BY o.order_date DESC, o.order_id DESC";

            $rows = $order->db_fetch_all($sql);
            if ($rows === false) $rows = [];
            echo json_encode($rows);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
} catch (Throwable $e) {
    http_response_code(500);
    error_log('Fetch order error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
