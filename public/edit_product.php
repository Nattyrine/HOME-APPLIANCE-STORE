<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';

// Ensure $conn (PDO) is available. If the included file did not define it,
// attempt a sensible default local XAMPP MySQL connection.
if (!isset($conn)) {
    try {
        $conn = new PDO('mysql:host=127.0.0.1;dbname=home_appliance_store;charset=utf8', 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        // If we cannot connect, stop with a simple message.
        echo 'Database connection error.';
        exit();
    }
}

$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    header("Location: manage_products.php");
    exit();
}

// Fetch product
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = :id");
$stmt->execute([':id' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch categories
$catStmt = $conn->prepare("SELECT * FROM categories ORDER BY name ASC");
$catStmt->execute();
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $category_id = $_POST['category_id'] ?? null;

    // Handle image upload
    $image_name = $product['image'];
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/assets/images/' . $image_name);
    }

    $update = $conn->prepare("UPDATE products 
        SET name=:name, price=:price, stock=:stock, category_id=:category_id, image=:image 
        WHERE product_id=:id");

    $update->execute([
        ':name' => $name,
        ':price' => $price,
        ':stock' => $stock,
        ':category_id' => $category_id,
        ':image' => $image_name,
        ':id' => $product_id
    ]);

    $message = 'Product updated successfully!';

    // Refresh product info
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit Product - nattyrine</title>

<style>
body { 
    font-family: Arial, sans-serif; 
    margin: 0; 
    background: #f4f6f9;
}

/* TOP BAR */
.topbar {
    background: #1e3a8a;
    padding: 10px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

/* Expandable Logo */
.logo img {
    height: 45px;
    transition: 0.3s;
}

.logo img:hover {
    height: 60px;
}

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
.container {
    padding: 30px;
}

h2 {
    margin-bottom: 20px;
}

/* Form Styling */
form {
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    max-width: 600px;
}

label {
    font-weight: bold;
}

input, select {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    margin-bottom: 15px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

button {
    background: #1e3a8a;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
}

button:hover {
    background: #162c6e;
}

.success {
    color: green;
    margin-bottom: 15px;
}

.product-preview {
    margin-top: 10px;
}
</style>
</head>

<body>

<!-- TOP BAR -->
<div class="topbar">

    <div class="logo">
        <a href="admin_dashboard.php">
            <img src="../assets/images/logo.png" alt="Logo">
        </a>
    </div>

    <a href="profile.php" class="profile">
        <div class="profile-icon">
            <?= strtoupper(substr($_SESSION['name'], 0, 1)) ?>
        </div>
        <span><?= htmlspecialchars($_SESSION['name']) ?></span>
    </a>

</div>

<div class="container">

<h2>Edit Product</h2>

<?php if($message): ?>
    <p class="success"><?= $message ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">

    <label>Name:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

    <label>Price:</label>
    <input type="number" name="price" step="0.01" value="<?= $product['price'] ?>" required>

    <label>Stock:</label>
    <input type="number" name="stock" value="<?= $product['stock'] ?>" required>

    <label>Category:</label>
    <select name="category_id" required>
        <?php foreach($categories as $c): ?>
            <option value="<?= $c['category_id'] ?>"
                <?= $c['category_id'] == $product['category_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Image:</label>
    <input type="file" name="image" accept="image/*">

    <?php if($product['image']): ?>
        <div class="product-preview">
            <img src="../assets/images/<?= $product['image'] ?>" width="120">
        </div>
    <?php endif; ?>

    <button type="submit">Update Product</button>

</form>

<br>
<p><a href="manage_products.php">⬅ Back to Manage Products</a></p>

</div>

</body>
</html>