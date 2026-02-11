<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if(!isset($_SESSION['user_id'])){
    echo json_encode(['status'=>false,'message'=>'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$cart_item_id = $data['cart_item_id'] ?? 0;

$stmt = $conn->prepare("DELETE FROM cart_items WHERE cart_item_id=:cid AND user_id=:uid");
$stmt->execute([
    ':cid'=>$cart_item_id,
    ':uid'=>$_SESSION['user_id']
]);

echo json_encode(['status'=>true,'message'=>'Item removed from cart']);