<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

/* Block if not logged in */
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => false,
        'message' => 'Login required'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    /* Start transaction */
    $conn->beginTransaction();

    /* 1. Check if cart has items */
    $check = $conn->prepare(
        "SELECT product_id, quantity 
         FROM cart_items 
         WHERE user_id = :user_id"
    );
    $check->execute([':user_id' => $user_id]);
    $cartItems = $check->fetchAll(PDO::FETCH_ASSOC);

    if (!$cartItems) {
        $conn->rollBack();
        echo json_encode([
            'status' => false,
            'message' => 'Cart is empty'
        ]);
        exit;
    }

    /* 2. Create order */
    $order = $conn->prepare(
        "INSERT INTO orders (user_id, order_date, status)
         VALUES (:user_id, NOW(), 'pending')"
    );
    $order->execute([':user_id' => $user_id]);

    $order_id = $conn->lastInsertId();

    /* 3. Insert order items */
    $itemStmt = $conn->prepare(
        "INSERT INTO order_items (order_id, product_id, quantity)
         VALUES (:order_id, :product_id, :quantity)"
    );

    foreach ($cartItems as $item) {
        $itemStmt->execute([
            ':order_id'  => $order_id,
            ':product_id'=> $item['product_id'],
            ':quantity'  => $item['quantity']
        ]);
    }

    /* 4. Clear cart */
    $clear = $conn->prepare(
        "DELETE FROM cart_items WHERE user_id = :user_id"
    );
    $clear->execute([':user_id' => $user_id]);

    /* Commit transaction */
    $conn->commit();

    echo json_encode([
        'status' => true,
        'message' => 'Order placed successfully'
    ]);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode([
        'status' => false,
        'message' => 'Failed to place order'
    ]);
}