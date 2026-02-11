<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Unauthorized');
}

$name = trim($_POST['name']);
$description = trim($_POST['description']);
$price = $_POST['price'];
$stock = $_POST['stock'];

$imageName = 'default.png';

if (!empty($_FILES['image']['name'])) {
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $imageName = uniqid() . '.' . $ext;

    move_uploaded_file(
        $_FILES['image']['tmp_name'],
        __DIR__ . '/../../assets/images/' . $imageName
    );
}

$sql = "INSERT INTO products (name, description, price, stock, image)
        VALUES (:name, :description, :price, :stock, :image)";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':name' => $name,
    ':description' => $description,
    ':price' => $price,
    ':stock' => $stock,
    ':image' => $imageName
]);

header("Location: /manage_products.php");
exit();