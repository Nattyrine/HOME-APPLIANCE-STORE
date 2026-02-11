<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => false,
        'message' => 'Unauthorized'
    ]);
    exit();
}

$user_id = $_SESSION['user_id'];

$name  = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($name === '' || $email === '') {
    echo json_encode([
        'status' => false,
        'message' => 'All fields are required'
    ]);
    exit();
}

try {
    $stmt = $conn->prepare("
        UPDATE users 
        SET name = :name, email = :email 
        WHERE user_id = :id
    ");

    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':id' => $user_id
    ]);

    echo json_encode([
        'status' => true,
        'message' => 'Profile updated successfully'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status' => false,
        'message' => 'Update failed'
    ]);
}