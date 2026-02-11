<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

// Check if user is logged in
$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;

if (!$user_id) {
    echo json_encode([]);
    exit;
}

// Check if admin requested all orders
$is_admin_request = isset($_GET['admin']) && $_GET['admin'] == 1;

if ($is_admin_request && $role === 'admin') {
    // Admin: return all orders with customer names
    $stmt = $conn->prepare("
        SELECT o.order_id, o.user_id, o.order_date, o.status, u.name AS customer_name
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.user_id
        ORDER BY o.order_date DESC
    ");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Customer: return only their own orders
    $stmt = $conn->prepare("
        SELECT order_id, order_date, status
        FROM orders
        WHERE user_id = :user_id
        ORDER BY order_date DESC
    ");
    $stmt->execute([':user_id' => $user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Return JSON
echo json_encode($orders);