<?php

session_start();

require_once __DIR__ . '/../../../config/database.php';


header("Content-Type: application/json");


// Check login

$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;


if(!$user_id){

    echo json_encode([]);

    exit;

}



// Admin request

$is_admin_request = isset($_GET['admin']) && $_GET['admin'] == 1;



if($is_admin_request && $role === 'admin'){


    // Get all orders

    $stmt = $conn->prepare(

    "SELECT 
        o.id,
        o.user_id,
        o.total,
        o.status,
        o.created_at,
        u.name AS customer_name

     FROM orders o

     LEFT JOIN users u 
     ON o.user_id = u.id

     ORDER BY o.created_at DESC"

    );


    $stmt->execute();


    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);



}else{


    // Customer orders

    $stmt = $conn->prepare(

    "SELECT 
        id,
        total,
        status,
        created_at

     FROM orders

     WHERE user_id = :user_id

     ORDER BY created_at DESC"

    );


    $stmt->execute([

        ':user_id'=>$user_id

    ]);


    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);


}



echo json_encode($orders);


?>