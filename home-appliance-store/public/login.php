<?php
session_start();
require_once '../config/database.php';
require_once '../classes/User.php';

$database = new Database();
$conn = $database->connect();

$userModel = new User($conn);

$message = "";

// Handle login POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($email && $password) {
        $user = $userModel->findByEmail($email);
        if ($user && password_verify($password, $user['password_hash'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Redirect
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $message = "Invalid email or password";
        }
    } else {
        $message = "All fields are required";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Home Appliance Store</title>
<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    min-height: 100vh;
    background: url('assets/images/login-bg.png') no-repeat center center fixed;
    background-size: contain;
}

/* Header */
.header {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    background-color: #002b5c;
    justify-content: flex-start;
    gap: 15px;
}

/* Menu icon */
.menu-icon {
    font-size: 26px;
    color: white;
    cursor: pointer;
}

/* Logo image */
.logo-png {
    height: 45px;
    cursor: pointer;
    transition: transform 0.3s ease;
}

/* Expanded logo */
.logo-png.expanded {
    transform: scale(2);
    z-index: 5;
    position: relative;
}

/* Menu content */
.menu-content {
    display: none;
    position: absolute;
    top: 60px;
    left: 20px;
    background: white;
    border: 1px solid #ccc;
    padding: 15px;
    z-index: 5;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
}
.menu-content a {
    display: block;
    text-decoration: none;
    margin-bottom: 10px;
    color: #002b5c;
}

/* Sub promo bar */
.sub-promo-bar {
    background-color: #f2f2f2;
    text-align: center;
    padding: 20px;
    font-size: 30px;
    border-bottom: 1px solid #ddd;
    color: #002b5c;
    font-weight: bold;
    transition: all 0.5s ease, opacity 0.5s ease;
    min-height: 50px;
}

/* Login form */
.login-section {
    max-width: 400px;
    margin: 180px auto;
    background: rgba(255,255,255,0.92);
    padding: 35px;
    box-shadow: 0 0 15px rgba(0,0,0,0.15);
    border-radius: 8px;
    position: relative;
    z-index: 1;
}
.login-section h2 {
    text-align: center;
    color: #002b5c;
    margin-bottom: 20px;
}
.login-section form input {
    width: 100%;
    padding: 12px;
    font-size: 18px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
}
.login-section form button {
    width: 100%;
    padding: 12px;
    background-color: #e30613;
    border: none;
    color: white;
    font-weight: bold;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 10px;
}
.login-section form button:hover {
    background-color: #c1050f;
}
.error-message {
    color: red;
    text-align: center;
    margin-bottom: 15px;
}
.login-link {
    text-align: center;
    margin-top: 15px;
}
.login-link a {
    color: #002b5c;
    text-decoration: none;
    font-weight: bold;
}
</style>
</head>
<body>

<header class="header">
    <span class="menu-icon" id="menu-toggle">☰</span>
    <img src="assets/images/logo.png" alt="Natty Home Appliances" class="logo-png" id="logo">
</header>

<div class="menu-content" id="menu-content">
    <a href="register.php">Home</a>
    <a href="about.php">About</a>
    <a href="contact.php">Contact</a>
</div>

<div class="sub-promo-bar" id="dynamic-promo">
    Welcome back to Natty Home Appliances Store...👋| Shop now with us
</div>

<section class="login-section">
    <h2>Login</h2>
    <?php if($message): ?>
        <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <div class="login-link">
        Don't have an account? <a href="register.php">Register here</a>
    </div>
</section>

<script>
// Logo click: expand/shrink
const logo = document.getElementById('logo');
logo.addEventListener('click', () => {
    logo.classList.toggle('expanded');
});

// Menu toggle
const menuToggle = document.getElementById('menu-toggle');
const menuContent = document.getElementById('menu-content');
menuToggle.addEventListener('click', () => {
    menuContent.style.display = (menuContent.style.display === 'block') ? 'none' : 'block';
});

// Rotate promos
const promos = [
    '🚚 FREE Next Day Delivery on Kitchen Appliances',
    '🏷️ 10% off when you buy 2 or more products',
    '💳 Shop Now, Pay Later',
    '⚠️ Product Safety Issue'
];
let index = 0;
const promoElement = document.getElementById('dynamic-promo');

function rotatePromos() {
    promoElement.style.opacity = 0;
    setTimeout(() => {
        promoElement.innerHTML = promos[index];
        promoElement.style.opacity = 1;
        index = (index + 1) % promos.length;
    }, 300);
}

setInterval(rotatePromos, 4500);
</script>

</body>
</html>