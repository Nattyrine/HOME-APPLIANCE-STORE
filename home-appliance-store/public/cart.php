<?php
session_start();

require_once __DIR__ . '/../config/database.php';

// Create database connection
$db = new Database();
$conn = $db->connect();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


/* Get cart items */
$sql = "
SELECT 
    c.id AS cart_id,
    p.id AS product_id,
    p.name,
    p.price,
    c.quantity,
    p.stock,
    p.image
FROM cart_items c
JOIN products p ON c.product_id = p.id
WHERE c.user_id = :user_id
";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':user_id' => $user_id
]);

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>

<title>My Cart - Natty Home Appliances</title>

<style>

body{
    font-family:Arial;
    background:#f4f4f4;
    padding:20px;
}

table{
    width:100%;
    background:white;
    border-collapse:collapse;
}

th,td{
    border:1px solid #ddd;
    padding:10px;
    text-align:center;
}

th{
    background:#002b5c;
    color:white;
}

img{
    width:70px;
    height:70px;
    object-fit:contain;
}

.remove-btn{
    background:red;
    color:white;
    border:none;
    padding:6px 10px;
    cursor:pointer;
}

.order-btn{
    background:#00bcd4;
    color:white;
    border:none;
    padding:10px 15px;
    cursor:pointer;
}

</style>

</head>


<body>


<h2>🛒 My Cart</h2>


<?php if(empty($items)): ?>

<p>Your cart is empty.</p>


<?php else: ?>


<table>

<tr>

<th>Product</th>
<th>Price</th>
<th>Quantity</th>
<th>Total</th>
<th>Action</th>

</tr>


<?php

$grandTotal = 0;

foreach($items as $item):

$total = $item['price'] * $item['quantity'];

$grandTotal += $total;

?>


<tr>


<td>

<?php if($item['image']): ?>

<img src="assets/images/<?= htmlspecialchars($item['image']) ?>">

<?php endif; ?>


<br>

<?= htmlspecialchars($item['name']) ?>


</td>


<td>

TSH <?= number_format($item['price']) ?>

</td>


<td>

<?= $item['quantity'] ?>

</td>


<td>

TSH <?= number_format($total) ?>

</td>


<td>


<button class="remove-btn"
onclick="removeItem(<?= $item['cart_id'] ?>)">
Remove
</button>


</td>


</tr>


<?php endforeach; ?>


<tr>

<td colspan="3">

<strong>Total</strong>

</td>


<td colspan="2">

<strong>
TSH <?= number_format($grandTotal) ?>
</strong>

</td>

</tr>


</table>


<br>


<button class="order-btn" onclick="placeOrder()">
Place Order
</button>


<?php endif; ?>


<br><br>

<a href="index.php">⬅ Back to Shop</a>


<script>


function removeItem(id){


fetch("api/cart/remove.php",{

method:"POST",

headers:{
"Content-Type":"application/json"
},

body:JSON.stringify({

cart_id:id

})

})


.then(res=>res.json())

.then(data=>{

alert(data.message);

location.reload();

});


}



function placeOrder(){


fetch("api/orders/create.php",{

method:"POST"

})


.then(res=>res.json())

.then(data=>{

alert(data.message);


if(data.status){

window.location.href="orders.php";

}


});


}


</script>


</body>

</html>