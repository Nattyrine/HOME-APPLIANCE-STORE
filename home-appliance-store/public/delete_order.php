<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';

$order_id = $_GET['order_id'] ?? null;

if ($order_id) {
    $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = :id");
    $stmt->execute([':id' => $order_id]);
}

header("Location: admin_dashboard.php"); // redirect back to dashboard
exit();
?>