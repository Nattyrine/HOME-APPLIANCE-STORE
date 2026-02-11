<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Guests only (redirect logged-in users to main index)
if(isset($_SESSION['user_id'])){
    header("Location: register.php"); // your real home page after login
    exit();
}

// Fetch categories and products
$catStmt = $conn->prepare("SELECT * FROM categories ORDER BY name ASC");
$catStmt->execute();
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

$categoryProducts = [];
foreach($categories as $cat) {
    $prodStmt = $conn->prepare("SELECT * FROM products WHERE category_id=:cat_id ORDER BY name ASC");
    $prodStmt->execute([':cat_id'=>$cat['category_id']]);
    $categoryProducts[$cat['category_id']] = $prodStmt->fetchAll(PDO::FETCH_ASSOC);
}

// Motivational messages for new users
$messages = [
    "Welcome! Discover quality home appliances today.",
    "Sign up to explore our exclusive offers!",
    "Register now and start shopping smart.",
    "Don't miss out! Join Natty Home Appliances."
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Natty Home Appliances</title>
<style>
body { font-family: Arial, sans-serif; margin:0; background:#f4f4f4; }
header { display:flex; align-items:center; justify-content:space-between; padding:10px 20px; background:#fff; border-bottom:1px solid #ddd; }
header img { height:50px; cursor:pointer; transition:0.3s; }
header img.expanded { transform:scale(3); }
nav a { margin-left:15px; text-decoration:none; color:#002b5c; font-weight:bold; }

/* Promo / Motivation */
.promo-bar { background:#e6f7ff; color:#065f46; padding:30px 20px; font-size:20px; border:1px solid #34d399; border-radius:5px; margin:15px 20px; text-align:center; transition:opacity 0.5s ease; }

/* Container */
.container { padding:20px; }

/* About / Info Sections */
.about-section { margin-bottom:30px; background:#fff; padding:15px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.05); }
.about-section h2 { color:#1e3a8a; margin-bottom:10px; }
.about-section p { margin-bottom:8px; line-height:1.5; }

/* Category Section */
.category { margin-bottom:30px; }
.category h3 { background:#1e3a8a; color:white; padding:5px 10px; border-radius:5px; }
.products { display:flex; flex-wrap:wrap; gap:15px; margin-top:10px; }
.product-card { width:220px; border:1px solid #ccc; padding:8px; background:#fff; text-align:center; }
.product-card img { width:100%; height:150px; object-fit:contain; }

/* Buttons */
.btn { display:inline-block; padding:5px 8px; margin-top:5px; background:#1e3a8a; color:white; text-decoration:none; border-radius:3px; }
</style>
</head>
<body>

<header>
    <div>
        <img src="assets/images/logo.png" id="logo" alt="Logo">
     </div>
     <nav>
    <?php if(isset($_SESSION['user_id'])): ?>
        <!-- Logged-in user menu -->
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact us</a>
        <a href="profile.php">👤 <?= htmlspecialchars($_SESSION['name']) ?></a>
        <a href="logout.php">🔓 Logout</a>
    <?php else: ?>
        <!-- Guest menu -->
        <a href="register.php">Home</a>
        <a href="contact.php">Contact us</a>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    <?php endif; ?>
</nav>

</header>

<div class="promo-bar" id="dynamic-promo">
    <?= htmlspecialchars($messages[array_rand($messages)]) ?>
</div>

<div class="container">

    <!-- About / Services Info -->
    <div class="about-section">
        <h2>About Natty Home Appliances</h2>
        <p>At Natty Home Appliances, we provide top-quality home appliances designed to make your daily life easier and more comfortable. Our commitment is to bring you reliable, durable, and energy-efficient products.</p>

        <h2>Our Services</h2>
        <p>We offer expert guidance on choosing appliances, delivery to your doorstep, installation support, and post-purchase customer service. Your satisfaction is our priority.</p>

        <h2>Categories</h2>
        <p>We cover a wide range of categories: kitchen appliances, cleaning devices, air conditioners, refrigerators, and more. Each category is carefully curated for quality and value.</p>

        <h2>Special Offers</h2>
        <p>Take advantage of seasonal discounts, bundle deals, and exclusive online promotions. Sign up to get early access to our offers and stay updated with our latest arrivals.</p>

        <h2>How We Work</h2>
        <p>Our process is simple: browse products, register or login to order, receive your appliance quickly, and enjoy reliable support throughout. We believe in transparency, fair pricing, and trust.</p>

        <div style="margin-top:15px; background:#e6f7ff; padding:10px; border:1px solid #34d399; border-radius:5px; color:#065f46;">
            Excited to explore our products? <a href="login.php">Login</a> or <a href="register.php">Register</a> now!
        </div>
    </div>

    <!-- Categories & Products -->
    <?php foreach($categories as $cat): ?>
        <div class="category">
            <h3><?= htmlspecialchars($cat['name']) ?></h3>
            <div class="products">
                <?php if(isset($categoryProducts[$cat['category_id']]) && count($categoryProducts[$cat['category_id']])>0): ?>
                    <?php foreach($categoryProducts[$cat['category_id']] as $prod): ?>
                        <div class="product-card">
                            <div class="img-box">
                                <img src="assets/images/<?= $prod['image'] ?? 'default.png' ?>" alt="<?= htmlspecialchars($prod['name']) ?>">
                            </div>
                            <h4><?= htmlspecialchars($prod['name']) ?></h4>
                            <p><strong>TSH <?= $prod['price'] ?></strong></p>
                            <p>Stock: <?= $prod['stock'] ?></p>
                            <a class="btn" href="login.php">Login to Order</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No products available in this category.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
// Logo expand
const logo = document.getElementById('logo');
logo.addEventListener('click', ()=>{ logo.classList.toggle('expanded'); });

// Dynamic rotating promo messages
const promoMessages = <?= json_encode($messages) ?>;
let promoIndex = 0;
const promoEl = document.getElementById('dynamic-promo');

function rotatePromo(){
    promoEl.style.opacity = 0;
    setTimeout(() => {
        promoIndex = (promoIndex + 1) % promoMessages.length;
        promoEl.innerText = promoMessages[promoIndex];
        promoEl.style.opacity = 1;
    }, 300);
}

setInterval(rotatePromo, 4000);
</script>

</body>
</html>