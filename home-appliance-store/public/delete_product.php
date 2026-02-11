<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';

$product_id = $_GET['id'] ?? null;

if ($product_id) {
    // Delete product
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id=:id");
    $stmt->execute([':id' => $product_id]);
}

header("Location: manage_products.php");
exit();