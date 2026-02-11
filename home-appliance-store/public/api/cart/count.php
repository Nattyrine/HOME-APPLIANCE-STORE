<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if(!isset($_SESSION['user_id'])){
    echo json_encode(['status'=>false,'count'=>0]);
    exit;
}

$user_id = $_SESSION['user_id'];

$countStmt = $conn->prepare("SELECT SUM(quantity) AS total FROM cart_items WHERE user_id = :user_id");
$countStmt->execute([':user_id' => $user_id]);
$count = $countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

echo json_encode([
    'status' => true,
    'message' => 'Cart count retrieved',
    'count' => $count
]);

?>