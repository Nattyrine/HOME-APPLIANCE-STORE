<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Ensure $conn is available (database.php may expose PDO in different variable names)
if (!isset($conn)) {
    if (isset($pdo) && $pdo instanceof PDO) {
        $conn = $pdo;
    } elseif (isset($db) && $db instanceof PDO) {
        $conn = $db;
    } elseif (defined('DB_DSN') && defined('DB_USER')) {
        try {
            $conn = new PDO(DB_DSN, DB_USER, defined('DB_PASS') ? DB_PASS : null);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            die('Database connection error: ' . $e->getMessage());
        }
    } else {
        // Try common environment variables
        $dsn = getenv('DB_DSN') ?: (getenv('DB_DRIVER') && getenv('DB_HOST') && getenv('DB_NAME') ? getenv('DB_DRIVER') . ":host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_NAME') : null);
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');
        if ($dsn) {
            try {
                $conn = new PDO($dsn, $user ?: null, $pass ?: null);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (Exception $e) {
                die('Database connection error: ' . $e->getMessage());
            }
        } else {
            die('Database connection not configured.');
        }
    }
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$name = $_SESSION['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin - Manage Orders</title>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    background: #f4f4f4;
}

/* HEADER */
.header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 20px;
    background: #fff;
    border-bottom: 1px solid #ddd;
}
.header img {
    height: 45px;
}
.header a {
    text-decoration: none;
    color: #333;
    font-weight: bold;
}

/* PAGE CONTENT */
.container {
    padding: 20px;
}

h2 {
    margin-bottom: 15px;
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
}

th, td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
    font-size: 14px;
}

th {
    background: #f0f0f0;
}

tr:nth-child(even) {
    background: #fafafa;
}

/* STATUS COLORS */
.status-pending { color: orange; font-weight: bold; }
.status-shipped { color: #0d6efd; font-weight: bold; }
.status-delivered { color: green; font-weight: bold; }

/* ACTION */
.action a {
    color: #0d6efd;
    text-decoration: none;
    font-weight: bold;
}
.action a:hover {
    text-decoration: underline;
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <div>
        <img src="assets/images/logo.png" alt="Logo">
    </div>
    <div>
        Admin: <?= htmlspecialchars($name) ?> |
        <a href="admin_dashboard.php">Dashboard</a> |
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- CONTENT -->
<div class="container">
<h2>📦 Manage Orders</h2>

<table>
    <tr>
        
        <th>Order ID</th>
        <th>Customer</th>
        <th>Date</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

<?php
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
ORDER BY o.id DESC
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$counter = 1;
foreach ($orders as $o):
?>
<tr>
    
    <td><?= $o['id'] ?></td>
    <td><?= htmlspecialchars($o['customer_name'] ?? 'Guest') ?></td>
    <td><?= $o['created_at'] ?></td>
    <td>
        <span class="status-<?= strtolower($o['status']) ?>">
            <?= htmlspecialchars($o['status']) ?>
        </span>
    </td>
    <td class="action">
        <a href="order_details.php?order_id=<?= $o['id'] ?>">View Items</a>
    </td>
</tr>
<?php endforeach; ?>

<?php if (count($orders) === 0): ?>
<tr>
    <td colspan="6">No orders found</td>
</tr>
<?php endif; ?>
</table>
</div>

</body>
</html>