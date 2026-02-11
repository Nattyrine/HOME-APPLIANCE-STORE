<?php
session_start();
require_once '../config/database.php';

$message = "";

// Redirect logged-in users to index (optional)
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle registration
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($name && $email && $password) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);

        if ($stmt->rowCount() > 0) {
            $message = "Email already registered";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password)");
            if ($stmt->execute([':name' => $name, ':email' => $email, ':password' => $hashed])) {
                header("Location: login.php");
                exit();
            } else {
                $message = "Registration failed. Try again.";
            }
        }
    } else {
        $message = "All fields are required.";
    }
}

// Fetch categories and products
$catStmt = $conn->prepare("SELECT * FROM categories ORDER BY name ASC");
$catStmt->execute();
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

$categoryProducts = [];
foreach ($categories as $cat) {
    $prodStmt = $conn->prepare("SELECT * FROM products WHERE category_id=:cat_id ORDER BY name ASC");
    $prodStmt->execute([':cat_id'=>$cat['category_id']]);
    $categoryProducts[$cat['category_id']] = $prodStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register & Explore - Natty Home Appliances</title>
<style>
body {
    font-family: Arial, sans-serif;
    margin:0;
    background: url('assets/images/register.png') no-repeat center center fixed;
    background-size: cover;
}

/* Header */
header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:10px 20px;
    background:#002b5c;
    color:white;
}
header img { height:50px; cursor:pointer; transition:0.3s; }
header img.expanded { transform:scale(2); }
nav a { margin-left:15px; text-decoration:none; color:white; font-weight:bold; }

/* Dynamic Promo */
.sub-promo-bar {
    background:#f2f2f2;
    text-align:center;
    padding:30px;
    font-weight:bold;
    font-size: 25px;
    color:#1e3a8a;
    border-bottom:1px solid #ddd;
    transition: opacity 0.5s ease;
}

/* Registration form */
.register-section {
    max-width:400px;
    margin:20px auto;
    background: rgba(255,255,255,0.95);
    padding:25px;
    border-radius:8px;
    box-shadow:0 0 15px rgba(0,0,0,0.1);
}
.register-section h2 { text-align:center; margin-bottom:15px; }
.register-section form input {
    width:100%; padding:10px; margin:8px 0; font-size:16px; border-radius:5px; border:1px solid #ccc;
}
.register-section form button {
    width:100%; padding:12px; background:#e30613; color:white; font-weight:bold; border:none; border-radius:5px; cursor:pointer; margin-top:10px;
}
.register-section form button:hover { background:#c1050f; }
.error-message { color:red; text-align:center; margin-bottom:10px; }
.login-link { text-align:center; margin-top:10px; }
.login-link a { color:#002b5c; font-weight:bold; text-decoration:none; }

/* Categories & Products */
.container { padding:20px; max-width:1200px; margin:auto; }
.category { margin-bottom:30px; }
.category h3 { background:#1e3a8a; color:white; padding:5px 10px; border-radius:5px; }
.products { display:flex; flex-wrap:wrap; gap:15px; margin-top:10px; }
.product-card { width:220px; border:1px solid #ccc; padding:8px; background:#fff; text-align:center; border-radius:5px; }
.product-card img { width:100%; height:150px; object-fit:contain; }
.btn { display:inline-block; padding:5px 8px; margin-top:5px; background:#1e3a8a; color:white; text-decoration:none; border-radius:3px; }
.login-prompt { background:#e6f7ff; padding:10px; margin-bottom:20px; border:1px solid #34d399; border-radius:5px; color:#065f46; }
</style>
</head>
<body>

<header>
    <div><img src="assets/images/logo.png" id="logo" alt="Logo"></div>
    <nav>
        <a href="about.php">About</a>
        <a href="contact.php">Contact us</a>
        <a href="login.php">Login</a>
    </nav>
</header>

<!-- Dynamic rotating promo -->
<div class="sub-promo-bar" id="dynamic-promo">
    Welcome to Natty Home Appliances! 👋 | Register now to explore exclusive deals and offers!
</div>

<!-- Registration Form -->
<section class="register-section">
    <h2>User Registration</h2>
    <?php if($message): ?>
        <p class="error-message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>
    <div class="login-link">
        Already have an account? <a href="login.php">Login here</a>
    </div>
</section>

<!-- Categories & Products -->
<div class="container">
    <h1>Explore Categories & Products</h1>
    <p>Discover our products before signing up! Register to purchase.</p>
    
    <div class="login-prompt">
        Excited about our products? <a href="login.php">Login</a> or <a href="register.php">Register</a> to buy!
    </div>

    <?php foreach($categories as $cat): ?>
        <div class="category">
            <h3><?= htmlspecialchars($cat['name']) ?></h3>
            <div class="products">
                <?php if(isset($categoryProducts[$cat['category_id']]) && count($categoryProducts[$cat['category_id']])>0): ?>
                    <?php foreach($categoryProducts[$cat['category_id']] as $prod): ?>
                        <div class="product-card">
                            <img src="assets/images/<?= $prod['image'] ?? 'default.png' ?>" alt="<?= htmlspecialchars($prod['name']) ?>">
                            <h4><?= htmlspecialchars($prod['name']) ?></h4>
                            <p><strong>TSH <?= $prod['price'] ?></strong></p>
                            <p>Stock: <?= $prod['stock'] ?></p>
                            <a class="btn" href="login.php">Login to Purchase</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No products in this category yet.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
// Logo click: expand
const logo = document.getElementById('logo');
logo.addEventListener('click', () => { logo.classList.toggle('expanded'); });

// Dynamic rotating promo
const promos = [
    '🚚 FREE Next Day Delivery on Kitchen Appliances',
    '🏷️ 10% off when you buy 2 or more products',
    '💳 Shop Now, Pay Later',
    '⚠️ Product Safety Alert'
];
let promoIndex = 0;
const promoEl = document.getElementById('dynamic-promo');
function rotatePromo() {
    promoEl.style.opacity = 0;
    setTimeout(() => {
        promoEl.innerText = promos[promoIndex];
        promoEl.style.opacity = 1;
        promoIndex = (promoIndex + 1) % promos.length;
    }, 300);
}
setInterval(rotatePromo, 4000);
</script>

</body>
</html>