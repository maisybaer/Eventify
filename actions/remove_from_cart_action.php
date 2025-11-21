<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
require_once '../controllers/cart_controller.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!isset($_SESSION['user_id'])) {
		echo json_encode(['status' => 'error', 'message' => 'Please login to remove items from cart']);
		exit;
	}

	$event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
	$customer_id = $_SESSION['user_id'];

	if ($event_id <= 0) {
		echo json_encode(['status' => 'error', 'message' => 'Invalid event id']);
		exit;
	}

	error_log("remove_from_cart_action called - event_id={$event_id}, customer_id={$customer_id}");
	$cartController = new CartController();
	$removed = $cartController->remove_from_cart_ctr($event_id, $customer_id);

	if ($removed) {
		echo json_encode(['status' => 'success', 'message' => 'Item removed from cart']);
	} else {
		$err = method_exists($cartController, 'get_last_error_ctr') ? $cartController->get_last_error_ctr() : '';
		$msg = 'Failed to remove item from cart';
		if ($err) $msg .= ': ' . $err;
		echo json_encode(['status' => 'error', 'message' => $msg]);
	}

} else {
	echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>

