<?php
session_start();

require_once __DIR__ . '/../../../config/database.php';


header("Content-Type: application/json");


if (!isset($_SESSION['user_id'])) {

    echo json_encode([
        'status'=>false,
        'message'=>'Login required'
    ]);

    exit;
}


$user_id = $_SESSION['user_id'];


try {


$conn->beginTransaction();



/* Get cart items with product price */

$check = $conn->prepare(
"SELECT 
c.product_id,
c.quantity,
p.price

FROM cart_items c

JOIN products p 
ON c.product_id = p.id

WHERE c.user_id = :user_id"
);


$check->execute([
':user_id'=>$user_id
]);


$cartItems = $check->fetchAll(PDO::FETCH_ASSOC);



if(!$cartItems){

$conn->rollBack();

echo json_encode([
'status'=>false,
'message'=>'Cart is empty'
]);

exit;

}




/* Calculate total */

$total = 0;


foreach($cartItems as $item){

$total += $item['price'] * $item['quantity'];

}




/* Create order */

$order = $conn->prepare(

"INSERT INTO orders
(user_id,total,status)

VALUES
(:user_id,:total,'Pending')"

);


$order->execute([

':user_id'=>$user_id,
':total'=>$total

]);


$order_id = $conn->lastInsertId();




/* Insert order items */

$itemStmt = $conn->prepare(

"INSERT INTO order_items
(order_id,product_id,quantity,price,subtotal)

VALUES

(:order_id,:product_id,:quantity,:price,:subtotal)"

);



foreach($cartItems as $item){


$subtotal = $item['price'] * $item['quantity'];


$itemStmt->execute([

':order_id'=>$order_id,
':product_id'=>$item['product_id'],
':quantity'=>$item['quantity'],
':price'=>$item['price'],
':subtotal'=>$subtotal

]);


}




/* Clear cart */

$clear = $conn->prepare(

"DELETE FROM cart_items 
WHERE user_id=:user_id"

);


$clear->execute([

':user_id'=>$user_id

]);




$conn->commit();



echo json_encode([

'status'=>true,
'message'=>'Order placed successfully'

]);



}catch(Exception $e){


if($conn->inTransaction()){

$conn->rollBack();

}


echo json_encode([

'status'=>false,
'message'=>$e->getMessage()

]);


}

?>