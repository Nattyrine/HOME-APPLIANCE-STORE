<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])){
    echo json_encode([
        'status'=>false,
        'message'=>'Not logged in'
    ]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$cart_id = $data['cart_id'] ?? 0;

if(!$cart_id){
    echo json_encode([
        'status'=>false,
        'message'=>'Invalid cart item'
    ]);
    exit;
}


$stmt = $conn->prepare(
    "DELETE FROM cart_items 
     WHERE id=:cid 
     AND user_id=:uid"
);

$stmt->execute([
    ':cid'=>$cart_id,
    ':uid'=>$_SESSION['user_id']
]);


echo json_encode([
    'status'=>true,
    'message'=>'Item removed from cart'
]);

?>