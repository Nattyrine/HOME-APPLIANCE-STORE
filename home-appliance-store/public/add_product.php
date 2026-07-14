<?php  
session_start();  

// Only admin can access  
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {  
    header("Location: login.php");  
    exit();  
}  

require_once __DIR__ . '/../config/database.php';  

// If the included file didn't create $conn, create a default PDO connection
if (!isset($conn) || !$conn) {
    try {
        $conn = new PDO('mysql:host=127.0.0.1;dbname=home_appliance_store;charset=utf8mb4', 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        // Fail gracefully if no connection is available
        die('Database connection not found.');
    }
}

// Fetch categories for dropdown  
$catStmt = $conn->prepare("SELECT * FROM categories ORDER BY name ASC");  
$catStmt->execute();  
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);  

$message = '';  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {  
    $name = $_POST['name'] ?? ''; 
    $description= $_POST['description'] ?? '';  
    $price = $_POST['price'] ?? 0;  
    $stock = $_POST['stock'] ?? 0;  
    $category_id = $_POST['category_id'] ?? null;  

    // Handle image upload  
    $image_name = null;  
    if (!empty($_FILES['image']['name'])) {  
        $image_name = time() . '_' . $_FILES['image']['name'];  
        move_uploaded_file(
            $_FILES['image']['tmp_name'], 
            __DIR__ . '/assets/images/' . $image_name
        );  
    }  

    $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, category_id, image) 
                            VALUES (:name, :description, :price, :stock, :category_id, :image)");  
    $stmt->execute([  
        ':name' => $name,
        ':description' => $description,   
        ':price' => $price,  
        ':stock' => $stock,  
        ':category_id' => $category_id,  
        ':image' => $image_name  
    ]);  

    $message = 'Product added successfully!';  
}  
?>  

<!DOCTYPE html>  
<html lang="en">  
<head>  
<meta charset="UTF-8">  
<title>Add Product - nattyrine</title>  

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
</style>
</head>

<body>

<!-- TOP BAR -->
<div class="topbar">

    <div class="logo">
        <a href="admin_dashboard.php">
            <img src="assets/images/logo.png" alt="Logo">
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

<h2>Add New Product</h2>

<?php if($message): ?>  
    <p class="success"><?= $message ?></p>  
<?php endif; ?>  

<form method="post" enctype="multipart/form-data">  

    <label>Name:</label>
    <input type="text" name="name" required>

    <label>Description:</label>
    <textarea name="description" rows="4" required></textarea>

    <label>Price:</label>
    <input type="number" name="price" step="0.01" required>

    <label>Stock:</label>
    <input type="number" name="stock" required>

    <label>Category:</label>
    <select name="category_id" required>  
        <option value="">-- Select Category --</option>  
        <?php foreach($categories as $c): ?>  
            <option value="<?= $c['id'] ?>">
                <?= htmlspecialchars($c['name']) ?>
            </option>  
        <?php endforeach; ?>  
    </select>

    <label>Image:</label>
    <input type="file" name="image" accept="image/*">

    <button type="submit">Add Product</button>

</form>

<br>
<p><a href="manage_products.php">⬅ Back to Manage Products</a></p>

</div>

</body>  
</html>