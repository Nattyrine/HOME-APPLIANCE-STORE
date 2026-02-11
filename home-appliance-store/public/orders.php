<?php    
session_start();    
require_once __DIR__ . '/../config/database.php';    
  
// Block access if not logged in    
if (!isset($_SESSION['user_id'])) {    
    header("Location: login.php");    
    exit();    
}    
  
$name = $_SESSION['name'];    
$role = $_SESSION['role'];    
?>    <!DOCTYPE html>  <html lang="en">  
<head>  
<meta charset="UTF-8">  
<meta name="viewport" content="width=device-width, initial-scale=1.0">  
<title>My Orders - Home Appliance Store</title>  <style>  
body {  
    font-family: Arial, sans-serif;  
    margin: 0;  
    padding: 0;  
    background-color: #f4f4f4;  
}  
  
/* Header with menu & logo */  
.header {  
    display: flex;  
    align-items: center;  
    padding: 10px 20px;  
    background-color: #fff;  
    border-bottom: 1px solid #ddd;  
}  
.logo-img {  
    height: 50px;  
}  
  
/* Greeting */  
.user-options {  
    padding: 10px 20px;  
    background: #fff;  
    font-size: 14px;  
    border-bottom: 1px solid #ddd;  
}  
  
/* Orders container */  
#orders {  
    padding: 15px 20px;  
    display: flex;  
    flex-direction: column;  
    gap: 10px;  
}  
  
/* Order card style */  
.order-card {  
    background: #fff;  
    padding: 12px;  
    border-radius: 6px;  
    border: 1px solid #ddd;  
    display: grid;  
    grid-template-columns: 1fr auto;  
    align-items: center;  
    gap: 10px;  
}  
.order-info {  
    display: flex;  
    flex-direction: column;  
    gap: 4px;  
}  
.order-info p {  
    margin: 0;  
    font-size: 14px;  
}  
.status-pending { color: orange; font-weight: bold; }  
.status-confirmed { color: green; font-weight: bold; }  
.status-cancelled { color: red; font-weight: bold; }  
.view-items-btn {  
    padding: 6px 10px;  
    background: #00bcd4;  
    color: #fff;  
    text-decoration: none;  
    border-radius: 4px;  
    font-size: 13px;  
}  
</style>  </head>  <body>  <header class="header">  
    <img src="assets/images/logo.png" alt="Logo" class="logo-img">  
</header>  <div class="user-options">  
    Hello, <?php echo htmlspecialchars($name); ?> 👋 | Role: <strong><?php echo htmlspecialchars($role); ?></strong>  
</div>  <div id="orders">Loading orders...</div>  <script>  
// Load orders  
fetch('api/orders/read.php')  
.then(res => res.json())  
.then(orders => {  
    const container = document.getElementById('orders');  
    container.innerHTML = '';  
  
    if(orders.length === 0){  
        container.innerHTML = '<p>You have not placed any orders yet.</p>';  
        return;  
    }  
  
    orders.forEach(o => {  
        const div = document.createElement('div');  
        div.className = 'order-card';  
        div.innerHTML = `  
            <div class="order-info">  
                <p><strong>Order</p>  
                <p>Date: ${o.order_date}</p>  
                <p>Status: <span class="status-${o.status}">${o.status}</span></p>  
            </div>  
            <a href="order_details.php?order_id=${o.order_id}" class="view-items-btn">View Items</a>  
        `;  
        container.appendChild(div);  
    });  
})  
.catch(err => {  
    console.error(err);  
    document.getElementById('orders').innerHTML = '<p>Error loading orders</p>';  
});  
</script>  <p style="padding: 15px 20px;"><a href="index.php">🏠 Back to Home</a></p>  </body>  
</html>