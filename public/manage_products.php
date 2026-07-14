<?php
session_start();

// Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';

if (!isset($conn)) {
    if (isset($pdo)) {
        $conn = $pdo;
    } elseif (isset($db)) {
        $conn = $db;
    }
}

// Ensure we have a valid PDO connection
if (!isset($conn) || !($conn instanceof PDO)) {
    // If the included config used a different variable name, try common ones
    if (isset($mysqli) && $mysqli instanceof mysqli) {
        // convert mysqli to PDO is non-trivial; show an error instead
        die('Database connection is using mysqli. Please provide a PDO connection in config/database.php');
    }
    die('Database connection not found. Please check config/database.php');
}

// Fetch products with categories
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$name = $_SESSION['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin - Manage Products</title>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    background: #f4f4f4;
}

/* HEADER */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
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
    margin-left: 10px;
}

/* CONTENT */
.container {
    padding: 20px;
}

h2 {
    margin-bottom: 10px;
}

/* TOP ACTION */
.top-actions {
    margin-bottom: 15px;
}
.top-actions a {
    background: #0d6efd;
    color: #fff;
    padding: 8px 12px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
}
.top-actions a:hover {
    background: #0b5ed7;
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

/* IMAGE */
.product-img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #ddd;
}

/* ACTION LINKS */
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
<h2>🛠️ Manage Products</h2>

<div class="top-actions">
    <a href="add_product.php">➕ Add New Product</a>
</div>

<?php if (empty($products)): ?>
    <p>No products found.</p>
<?php else: ?>
<table>
    <tr>
        <th>#</th>
        <th>Image</th>
        <th>Name</th>
        <th>Category</th>
        <th>Price (TSH)</th>
        <th>Stock</th>
        <th>Actions</th>
    </tr>

<?php
$counter = 1;
foreach ($products as $p):
?>
<tr>
    <td><?= $counter++ ?></td>
    <td>
        <img 
            src="assets/images/<?= htmlspecialchars($p['image'] ?? 'default.png') ?>" 
            alt="<?= htmlspecialchars($p['name']) ?>" 
            class="product-img">
    </td>
    <td><?= htmlspecialchars($p['name']) ?></td>
    <td><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?></td>
    <td><?= number_format($p['price']) ?></td>
    <td><?= $p['stock'] ?></td>
    <td class="action">
        <a href="edit_product.php?id=<?= $p['id'] ?>">Edit</a> |
        <a href="delete_product.php?id=<?= $p['id'] ?>"
           onclick="return confirm('Are you sure you want to delete this product?')">
           Delete
        </a>
    </td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
</div>

</body>
</html>