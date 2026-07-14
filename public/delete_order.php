<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';

if (!isset($conn) && isset($pdo)) {
    $conn = $pdo;
}

if (!isset($conn)) {
    die('Database connection not available.');
}

$order_id = $_GET['order_id'] ?? null;

if ($order_id) {

    // Delete order items first
    $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = :id");
    $stmt->execute([':id' => $order_id]);

    // Delete the order
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = :id");
    $stmt->execute([':id' => $order_id]);
}

header("Location: admin_dashboard.php");
exit();
?>