<?php
session_start();

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../classes/Product.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Unauthorized');
}


$database = new Database();
$conn = $database->connect();


$product = new Product($conn);


$category_id = $_POST['category_id'];
$name = trim($_POST['name']);
$description = trim($_POST['description']);
$price = $_POST['price'];
$stock = $_POST['stock'];


$imageName = "default.png";


if (!empty($_FILES['image']['name'])) {

    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

    $imageName = uniqid() . "." . $ext;


    move_uploaded_file(
        $_FILES['image']['tmp_name'],
        __DIR__ . '/../../assets/images/' . $imageName
    );

}


$product->create(
    $category_id,
    $name,
    $description,
    $price,
    $stock,
    $imageName
);


header("Location: /manage_products.php");
exit();

?>