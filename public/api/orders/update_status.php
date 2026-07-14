<?php

session_start();

require_once __DIR__ . '/../../../config/database.php';


header("Content-Type: application/json");


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {

    echo json_encode([
        'status'=>false,
        'message'=>'Access denied'
    ]);

    exit();

}



$data = json_decode(file_get_contents('php://input'), true);


$order_id = $data['order_id'] ?? null;
$status = $data['status'] ?? null;



$allowed_status = [
    'Pending',
    'Processing',
    'Completed',
    'Cancelled'
];



if(!$order_id || !in_array($status,$allowed_status)){

    echo json_encode([
        'status'=>false,
        'message'=>'Invalid status'
    ]);

    exit();

}




$stmt = $conn->prepare(

"UPDATE orders

 SET status = :status

 WHERE id = :order_id"

);



if($stmt->execute([

    ':status'=>$status,
    ':order_id'=>$order_id

])){


    echo json_encode([

        'status'=>true,
        'message'=>'Status updated'

    ]);


}else{


    echo json_encode([

        'status'=>false,
        'message'=>'Update failed'

    ]);

}


?>