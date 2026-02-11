<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? '';
$name = $_SESSION['name'] ?? 'User';
$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    die("Order ID is required");
}

/* ================= FETCH ORDER ================= */
if ($role === 'admin') {
    $stmt = $conn->prepare("
        SELECT o.order_id, o.order_date, o.status, u.name AS customer_name
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.user_id
        WHERE o.order_id = :order_id
    ");
    $stmt->execute(['order_id' => $order_id]);
} else {
    $stmt = $conn->prepare("
        SELECT o.order_id, o.order_date, o.status
        FROM orders o
        WHERE o.order_id = :order_id AND o.user_id = :user_id
    ");
    $stmt->execute([
        'order_id' => $order_id,
        'user_id'  => $user_id
    ]);
}

$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) {
    die("Order not found");
}

/* ================= TIME CHECK ================= */
$orderTime = !empty($order['order_date']) ? strtotime($order['order_date']) : 0;
$currentTime = time();
$minutesPassed = $orderTime ? (($currentTime - $orderTime) / 60) : 999;

$canCancel = (
    $role !== 'admin' &&
    $order['status'] === 'pending' &&
    $minutesPassed <= 60
);

/* ================= FETCH ITEMS ================= */
$stmt = $conn->prepare("
    SELECT oi.quantity, p.name, p.price, p.image
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = :order_id
");
$stmt->execute(['order_id' => $order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order #<?= htmlspecialchars($order['order_id']) ?> Details</title>
<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

/* Header with logo */
.header {
    display: flex;
    align-items: center;
    padding: 10px 20px;
    background-color: #fff;
    border-bottom: 1px solid #ddd;
}
.logo-img {
    height: 50px;
    width: auto;
    object-fit: contain;
}

/* User profile */
.user-options {
    padding: 10px 20px;
    background: #fff;
    font-size: 14px;
    border-bottom: 1px solid #ddd;
}

/* Order details container */
.container {
    padding: 15px 20px;
}

/* Status styles */
.status-pending { color: orange; font-weight: bold; }
.status-confirmed { color: green; font-weight: bold; }
.status-cancelled { color: red; font-weight: bold; }

/* Order items table */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}
th, td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: center;
}
th { background: #eee; }
img:not(.logo-img) { width: 80px; height: 80px; object-fit: cover; }

/* Buttons */
button {
    padding: 6px 10px;
    cursor: pointer;
    border-radius: 4px;
}
.cancel-btn { background: #f44336; color: #fff; border: none; }
.update-btn { background: #00bcd4; color: #fff; border: none; }

/* Back link */
.back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #007bff; }
.back-link:hover { text-decoration: underline; }
</style>
</head>
<body>

<header class="header">
    <img src="assets/images/logo.png" alt="Logo" class="logo-img">
</header>

<div class="user-options">
    Hello, <?= htmlspecialchars($name) ?> 👋 | Role: <strong><?= htmlspecialchars($role) ?></strong>
</div>

<div class="container">
    <h2>Order Details</h2>
    <p>Status: <span class="status-<?= strtolower($order['status']) ?>"><?= htmlspecialchars($order['status']) ?></span></p>

    <?php if ($canCancel): ?>
        <form method="post" action="api/orders/cancel.php">
            <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
            <button type="submit" class="cancel-btn">Cancel Order</button>
        </form>
    <?php elseif ($role !== 'admin' && $order['status'] === 'pending'): ?>
        <p style="color:gray;">❌ Cancellation time expired (60 minutes passed)</p>
    <?php endif; ?>

    <?php if ($role === 'admin'): ?>
        <p>
        Change Status:
        <select id="statusSelect">
            <option value="pending" <?= $order['status']==='pending'?'selected':'' ?>>Pending</option>
            <option value="confirmed" <?= $order['status']==='confirmed'?'selected':'' ?>>Confirmed</option>
            <option value="cancelled" <?= $order['status']==='cancelled'?'selected':'' ?>>Cancelled</option>
        </select>
        <button type="button" onclick="updateStatus()" class="update-btn">Update</button>
        </p>
        <p>Customer: <?= htmlspecialchars($order['customer_name'] ?? 'Unknown') ?></p>
    <?php else: ?>
        <p>Customer: <?= htmlspecialchars($_SESSION['name'] ?? 'Customer') ?></p>
    <?php endif; ?>

    <p>Date: <?= htmlspecialchars($order['order_date']) ?></p>

    <h3>Items in this order:</h3>

    <?php if (empty($items)): ?>
        <p>No items found for this order.</p>
    <?php else: ?>
    <table>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
        </tr>
        <?php foreach ($items as $i): ?>
        <tr>
            <td><img src="assets/images/<?= htmlspecialchars($i['image'] ?? 'default.png') ?>" alt="<?= htmlspecialchars($i['name']) ?>"></td>
            <td><?= htmlspecialchars($i['name']) ?></td>
            <td>TSH <?= number_format($i['price'], 2) ?></td>
            <td><?= $i['quantity'] ?></td>
            <td>TSH <?= number_format($i['price'] * $i['quantity'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>

    <a href="<?= $role === 'admin' ? 'manage_orders.php' : 'orders.php' ?>" class="back-link">⬅ Back to Orders</a>
</div>

<script>
function updateStatus() {
    fetch('api/orders/update_status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            order_id: <?= (int)$order['order_id'] ?>,
            status: document.getElementById('statusSelect').value
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.status) location.reload();
    })
    .catch(err => console.error(err));
}
</script>

</body>
</html>