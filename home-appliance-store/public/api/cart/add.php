<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status'=>false,'message'=>'Login required']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $_SESSION['user_id'];
$product_id = $data['product_id'] ?? null;

if (!$product_id) {
    echo json_encode(['status'=>false,'message'=>'Invalid product']);
    exit;
}

/* Check if product already in cart */
$sql = "SELECT cart_item_id, quantity 
        FROM cart_items 
        WHERE user_id = :user_id AND product_id = :product_id";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':user_id' => $user_id,
    ':product_id' => $product_id
]);

$item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($item) {
    /* Increase quantity */
    $update = $conn->prepare(
        "UPDATE cart_items 
         SET quantity = quantity + 1 
         WHERE cart_item_id = :id"
    );
    $update->execute([':id' => $item['cart_item_id']]);

    echo json_encode(['status'=>true,'message'=>'Quantity updated']);
} else {
    /* Add new item */
    $insert = $conn->prepare(
        "INSERT INTO cart_items (user_id, product_id, quantity)
         VALUES (:user_id, :product_id, 1)"
    );
    $insert->execute([
        ':user_id' => $user_id,
        ':product_id' => $product_id
    ]);

    echo json_encode(['status'=>true,'message'=>'Added to cart']);
    exit;
}