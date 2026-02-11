<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];
$order_id = $_POST['order_id'] ?? null;

if (!$order_id) {
    die("Order ID required");
}

/* Fetch order */
$stmt = $conn->prepare("
    SELECT status, order_date 
    FROM orders 
    WHERE order_id = :order_id AND user_id = :user_id
");
$stmt->execute([
    ':order_id' => $order_id,
    ':user_id' => $user_id
]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found");
}

/* Check time limit */
$orderTime = strtotime($order['order_date']);
$currentTime = time();
$minutesPassed = ($currentTime - $orderTime) / 60;

if ($order['status'] !== 'pending' || $minutesPassed > 30) {
    die("Cancellation not allowed");
}

/* Cancel order */
$update = $conn->prepare("
    UPDATE orders 
    SET status = 'cancelled' 
    WHERE order_id = :order_id
");
$update->execute([':order_id' => $order_id]);

header("Location: ../../order_details.php?order_id=" . $order_id);
exit();