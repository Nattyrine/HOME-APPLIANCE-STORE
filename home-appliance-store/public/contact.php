<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Guests or logged-in users
$isLoggedIn = isset($_SESSION['user_id']);
$name = $_SESSION['name'] ?? '';
$role = $_SESSION['role'] ?? '';

// Admin contact info
$adminInfo = [
    'name' => 'Nattyrine Admin',
    'email' => 'nattyhomeappliances@gmail.com',
    'phone' => '+255 780972777',
    'address' => '07 Kingstone St, Dar es Salaam, Tanzania',
    'website' => 'https://www.nattyhomeappliances.com',
    'working_hours' => 'Mon - Fri: 8:00 AM - 6:00 PM',
    'socials' => [
        'facebook' => 'https://www.facebook.com/nattyhomeappliances',
        'twitter' => 'https://twitter.com/nattyhomeappl',
        'instagram' => 'https://instagram.com/nattyhomeappliances'
    ]
];

// Dynamic motivational promo
$messages = [
    "Welcome! Reach out to us for assistance or inquiries.",
    "Contact us now and discover our exclusive offers!",
    "Our team is here to help you shop smart.",
    "Have questions? Let Nattyrine Home Appliances assist you!"
];
$msgIndex = array_rand($messages);
$promoMessage = $messages[$msgIndex];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Natty Home Appliances</title>
<style>
body { 
    font-family: Arial,sans-serif; 
    margin:0; 
    background: url('assets/images/contact-bg.png') no-repeat center center fixed; 
    background-size: cover; 
}

/* Header */
header { display:flex; justify-content:space-between; align-items:center; padding:10px 20px; background:#fff; border-bottom:1px solid #ddd; }
header img { height:50px; cursor:pointer; transition:0.3s; }
header img.expanded { transform:scale(2); }
nav a { margin-left:15px; text-decoration:none; color:#002b5c; font-weight:bold; }

/* Promo bar */
.promo-bar { background:#e6f7ff; color:#065f46; padding:15px 20px; font-size:16px; border:1px solid #34d399; border-radius:5px; margin:15px 20px; text-align:center; }

/* Container */
.container { max-width:900px; margin:30px auto; background:rgba(255,255,255,0.95); padding:25px; border-radius:8px; box-shadow:0 0 15px rgba(0,0,0,0.15); }

/* Headings */
h1 { color:#1e3a8a; text-align:center; margin-bottom:20px; }
.info-row { display:flex; flex-wrap:wrap; margin-bottom:15px; }
.info-label { flex:0 0 150px; font-weight:bold; color:#333; }
.info-value { flex:1; color:#555; }

/* Social links */
.social-links a { margin-right:15px; text-decoration:none; color:#1e3a8a; font-weight:bold; }

/* Login prompt for guests */
.login-prompt { background:#fff3cd; padding:15px; border:1px solid #ffeeba; border-radius:5px; color:#856404; text-align:center; margin-top:20px; font-weight:bold; }
</style>
</head>
<body>

<header>
    <div>
        <img src="assets/images/logo.png" alt="Natty Home Appliances Logo" id="logo">
    </div>
    <nav>
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <?php if($isLoggedIn): ?>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>

<div class="promo-bar"><?= htmlspecialchars($promoMessage) ?></div>

<div class="container">
    <h1>Contact Natty Home Appliances</h1>

    <p>We at Natty Home Appliances are committed to providing high-quality appliances and excellent customer service. Feel free to reach out to us for inquiries, support, or to learn more about our services.</p>
    
    <h2>Admin Contact Information</h2>

    <div class="info-row">
        <div class="info-label">Admin Name:</div>
        <div class="info-value"><?= htmlspecialchars($adminInfo['name']) ?></div>
    </div>

    <div class="info-row">
        <div class="info-label">Email:</div>
        <div class="info-value"><a href="mailto:<?= htmlspecialchars($adminInfo['email']) ?>"><?= htmlspecialchars($adminInfo['email']) ?></a></div>
    </div>

    <div class="info-row">
        <div class="info-label">Phone:</div>
        <div class="info-value"><a href="tel:<?= htmlspecialchars($adminInfo['phone']) ?>"><?= htmlspecialchars($adminInfo['phone']) ?></a></div>
    </div>

    <div class="info-row">
        <div class="info-label">Address:</div>
        <div class="info-value"><?= htmlspecialchars($adminInfo['address']) ?></div>
    </div>

    <div class="info-row">
        <div class="info-label">Website:</div>
        <div class="info-value"><a href="<?= htmlspecialchars($adminInfo['website']) ?>" target="_blank"><?= htmlspecialchars($adminInfo['website']) ?></a></div>
    </div>

    <div class="info-row">
        <div class="info-label">Working Hours:</div>
        <div class="info-value"><?= htmlspecialchars($adminInfo['working_hours']) ?></div>
    </div>

    <div class="info-row social-links">
        <div class="info-label">Socials:</div>
        <div class="info-value">
            <a href="<?= htmlspecialchars($adminInfo['socials']['facebook']) ?>" target="_blank">Facebook</a>
            <a href="<?= htmlspecialchars($adminInfo['socials']['twitter']) ?>" target="_blank">Twitter</a>
            <a href="<?= htmlspecialchars($adminInfo['socials']['instagram']) ?>" target="_blank">Instagram</a>
        </div>
    </div>

    <?php if(!$isLoggedIn): ?>
        <div class="login-prompt">
            To place orders or interact with products, please <a href="login.php">Login</a> or <a href="register.php">Register</a>.
        </div>
    <?php endif; ?>
</div>

<script>
const logo = document.getElementById('logo');
logo.addEventListener('click', () => { logo.classList.toggle('expanded'); });
</script>

</body>
</html>