<?php
require_once __DIR__ . '/../../../config/database.php';

$database = new Database();
$conn = $database->connect();

$query = "
SELECT 
    p.id,
    p.name,
    p.description,
    p.price,
    p.stock,
    p.image,
    p.created_at,
    c.name AS category_name
FROM products p
LEFT JOIN categories c 
ON p.category_id = c.id
ORDER BY p.id DESC
";

$stmt = $conn->prepare($query);
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

header("Content-Type: application/json");
echo json_encode($products);