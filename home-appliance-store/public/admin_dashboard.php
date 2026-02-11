<?php  
session_start();  

// Only admin can access  
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {  
    header("Location: login.php");  
    exit();  
}  

require_once __DIR__ . '/../config/database.php';  

$name = $_SESSION['name'];  

// Fetch product count
$stmtProd = $conn->query("SELECT COUNT(*) as total_products FROM products");
$total_products = $stmtProd->fetch(PDO::FETCH_ASSOC)['total_products'];

// Fetch orders info
$stmtOrders = $conn->query("
    SELECT o.order_id, u.name AS customer_name, u.email AS customer_email
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
");
$orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);
$total_orders = count($orders);
?>
<!DOCTYPE html>
<html lang="en">  
<head>  
<meta charset="UTF-8">  
<title>Admin Dashboard - nattyrine Home Appliances Store</title>  

<style>
body { 
    font-family: Arial, sans-serif; 
    margin: 0; 
    background: #f4f6f9;
}

/* TOP BAR */
.topbar {
    background: #1e3a8a;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
}

/* Expandable Logo */
.logo img {
    height: 45px;
    transition: 0.3s;
}
.logo img:hover { height: 60px; }

/* Profile */
.profile {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: white;
}
.profile-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: white;
    color: #1e3a8a;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 10px;
}

/* Container */
.container { padding: 30px; }

h1 { margin-bottom: 5px; }
h2 { margin-top: 0; }

/* Dashboard Cards */
.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.card {
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    transition: 0.3s;
}
.card:hover { transform: translateY(-5px); }

.card a { text-decoration: none; font-weight: bold; color: #1e3a8a; font-size: 18px; }
.card-icon { font-size: 35px; margin-bottom: 10px; }

.card ul { padding-left: 20px; margin: 5px 0 0 0; }
.card li { margin-bottom: 5px; font-size: 14px; }
.card li a { color: #1e3a8a; text-decoration: none; display: block; }

.card-logout {
    background: #fff;
    padding: 50px 5px;       /* smaller than regular cards */
    border-radius: 16px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    text-align: center;
    transition: 0.2s;
    width: 300px;             /* optional: set fixed width */
    margin: 0 auto;           /* center it */
}

.card-logout:hover {
    transform: translateY(-3px);
}

.card-logout a {
    font-size: 20px;          /* smaller text */
    font-weight: bold;
    color: #1e3a8a;
    text-decoration: none;
}
.card-logout .card-icon {
    font-size: 24px;          /* smaller icon */
    margin-bottom: 5px;
}

</style>
</head>

<body>

<!-- TOP BAR -->
<div class="topbar">

    <div class="logo">
        <img src="../assets/images/logo.png" alt="Logo">
    </div>

    <a href="profile.php" class="profile">
        <div class="profile-icon"><?= strtoupper(substr($name, 0, 1)) ?></div>
        <span><?= htmlspecialchars($name) ?></span>
    </a>

</div>

<div class="container">

<h1>Welcome <?= htmlspecialchars($name) ?>|role : <?= $_SESSION['role'] ?></h1>  
<h2>Admin Dashboard</h2>  

<div class="dashboard-cards">

    <!-- Manage Products Card -->
    <div class="card">
        <div class="card-icon">🛠</div>
        <a href="manage_products.php">MANAGE PRODUCTS</a>
        <ul>
            <li><a href="manage_products.php">View products</a></li>
            <li><a href="add_product.php">Add New Product</a></li>
            <li><a href="edit_product.php">Edit Products</a></li>
            <li><a href="delete_product.php">Delete Products</a></li>
        </ul>
    </div>

    <!-- Manage Orders Card -->
    <div class="card">
        <div class="card-icon">📦</div>
        <a href="manage_orders.php">MANAGE ORDERS</a>
        <ul>
            <li>ORDERS FROM <?= $total_orders ?> CUSTOMERS</li>
            <?php foreach($orders as $order): ?>
                <li>
                    <a href="order_details.php?order_id=<?= $order['order_id'] ?>">
                        <?= htmlspecialchars($order['customer_name']) ?>  (<?= htmlspecialchars($order['customer_email']) ?>)
                    </a>
                    &nbsp;|&nbsp;
                    <a href="delete_order.php?order_id=<?= $order['order_id'] ?>" style="color: red;" onclick="return confirm('Are you sure you want to delete this order?');">
                        Delete
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Logout Card -->
    <div class="card-logout">
        <div class="card-icon">🔓</div>
        <a href="logout.php">Logout</a>
    </div>

</div>

</div>
</body>
</html>