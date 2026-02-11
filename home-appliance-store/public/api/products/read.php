<?php
require_once __DIR__ . '/../../../config/database.php';

$stmt = $conn->prepare("
    SELECT 
        p.product_id,
        p.name,
        p.description,
        p.price,
        p.stock,
        c.name AS category_name,
        p.image
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
");

$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($products);