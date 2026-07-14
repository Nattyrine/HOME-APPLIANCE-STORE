<?php
session_start();

require_once __DIR__ . '/../config/database.php';

$db = new Database();
$conn = $db->connect();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'customer';
$name = $_SESSION['name'] ?? 'User';

$order_id = $_GET['order_id'] ?? null;


if (!$order_id) {
    die("Order ID is required");
}


/* ================= FETCH ORDER ================= */

if ($role === 'admin') {

    $stmt = $conn->prepare("
        SELECT 
            o.id,
            o.created_at,
            o.status,
            o.total,
            u.name AS customer_name

        FROM orders o

        LEFT JOIN users u 
        ON o.user_id = u.id

        WHERE o.id = :order_id
    ");


    $stmt->execute([
        ':order_id'=>$order_id
    ]);


} else {


    $stmt = $conn->prepare("
        SELECT 
            id,
            created_at,
            status,
            total

        FROM orders

        WHERE id = :order_id
        AND user_id = :user_id
    ");


    $stmt->execute([
        ':order_id'=>$order_id,
        ':user_id'=>$user_id
    ]);

}


$order = $stmt->fetch(PDO::FETCH_ASSOC);



if (!$order) {
    die("Order not found");
}



/* ================= CANCEL CHECK ================= */


$orderTime = strtotime($order['created_at']);

$currentTime = time();

$minutesPassed = ($currentTime - $orderTime) / 60;


$canCancel = (

    $role !== 'admin'
    &&
    $order['status'] === 'Pending'
    &&
    $minutesPassed <= 30

);



/* ================= FETCH ORDER ITEMS ================= */


$stmt = $conn->prepare("

    SELECT 

    oi.quantity,
    oi.price,
    oi.subtotal,

    p.name,
    p.image


    FROM order_items oi


    JOIN products p

    ON oi.product_id = p.id


    WHERE oi.order_id = :order_id

");


$stmt->execute([

    ':order_id'=>$order_id

]);


$items = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>


<!DOCTYPE html>

<html>

<head>

<title>Order Details</title>


<style>

body{
font-family:Arial;
background:#f4f4f4;
padding:20px;
}


.container{

background:white;
padding:20px;
border-radius:8px;

}


table{

width:100%;
border-collapse:collapse;
margin-top:20px;

}


th,td{

border:1px solid #ddd;
padding:10px;
text-align:center;

}


img{

width:70px;
height:70px;
object-fit:cover;

}


.pending{

color:orange;
font-weight:bold;

}


.processing{

color:blue;
font-weight:bold;

}


.completed{

color:green;
font-weight:bold;

}


.cancelled{

color:red;
font-weight:bold;

}


button{

padding:8px;
cursor:pointer;

}


.cancel-btn{

background:red;
color:white;
border:none;

}


.update-btn{

background:#00bcd4;
color:white;
border:none;

}


</style>


</head>


<body>


<div class="container">


<h2>Order Details</h2>


<p>
Order ID:
<strong>
<?= $order['id'] ?>
</strong>
</p>


<p>
Status:

<span class="<?= strtolower($order['status']) ?>">

<?= htmlspecialchars($order['status']) ?>

</span>

</p>



<p>
Date:
<?= $order['created_at'] ?>
</p>


<p>
Total:
TSH <?= number_format($order['total'],2) ?>
</p>



<?php if($role === 'admin'): ?>


<p>
Customer:

<?= htmlspecialchars($order['customer_name']) ?>

</p>



<select id="statusSelect">


<option value="Pending">
Pending
</option>


<option value="Processing">
Processing
</option>


<option value="Completed">
Completed
</option>


<option value="Cancelled">
Cancelled
</option>


</select>



<button onclick="updateStatus()" class="update-btn">
Update Status
</button>


<?php endif; ?>



<?php if($canCancel): ?>


<form method="post" action="api/orders/cancel.php">

<input type="hidden" 
name="order_id"
value="<?= $order['id'] ?>">


<button class="cancel-btn">

Cancel Order

</button>


</form>


<?php endif; ?>



<h3>Products</h3>



<table>


<tr>

<th>Image</th>
<th>Name</th>
<th>Price</th>
<th>Quantity</th>
<th>Total</th>

</tr>



<?php foreach($items as $item): ?>


<tr>


<td>

<img src="assets/images/<?= htmlspecialchars($item['image'] ?? 'default.png') ?>">

</td>


<td>

<?= htmlspecialchars($item['name']) ?>

</td>


<td>

TSH <?= number_format($item['price'],2) ?>

</td>


<td>

<?= $item['quantity'] ?>

</td>


<td>

TSH <?= number_format($item['subtotal'],2) ?>

</td>


</tr>


<?php endforeach; ?>


</table>



<br>


<a href="<?= $role==='admin'?'manage_orders.php':'orders.php' ?>">

⬅ Back

</a>


</div>



<script>


function updateStatus(){


fetch('api/orders/update_status.php',{

method:'POST',

headers:{
'Content-Type':'application/json'
},


body:JSON.stringify({

order_id: <?= $order['id'] ?>,

status:
document.getElementById('statusSelect').value

})


})


.then(res=>res.json())

.then(data=>{

alert(data.message);


if(data.status){

location.reload();

}


});


}


</script>


</body>


</html>