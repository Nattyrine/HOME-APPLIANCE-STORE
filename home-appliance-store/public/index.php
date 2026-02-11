<?php    
session_start();    
require_once __DIR__ . '/../config/database.php';    

if (!isset($_SESSION['user_id'])) {    
    header("Location: register.php");    
    exit();    
}    

$name = $_SESSION['name'];    
$role = $_SESSION['role'];    
?>    

<!DOCTYPE html>  
<html lang="en">  
<head>  
<meta charset="UTF-8">  
<meta name="viewport" content="width=device-width, initial-scale=1.0">  
<title>Natty Home Appliances Store</title>  
<style>  
body {  
    font-family: 'Helvetica Neue', Arial, sans-serif;  
    margin: 0;  
    padding: 0;  
    background-color: #f4f4f4;  
}  

/* Promo bar */  
.promo-bar {  
    background-color: #00bcd4;  
    color: white;  
    text-align: center;  
    padding: 20px 5px;  
    font-size: 30px;  
    font-weight: bold;  
    transition: opacity 0.5s ease;  
}  

/* Header */  
.header {  
    display: flex;  
    align-items: center;  
    justify-content: space-between;  
    padding: 8px 8px;  
    background-color: #fff;  
    border-bottom: 1px solid #ddd;  
    position: relative; 
}  
.menu-logo {  
    display: flex;  
    align-items: center;  
}  
.menu-icon {  
    font-size: 26px;  
    cursor: pointer;  
    margin-right: 8px;  
}  
.logo-img {  
    height: 50px;  
    cursor: pointer;  
    transition: transform 0.3s ease;  
}  
.logo-img.expanded { transform: scale(3); }  

/* Search box */  
.search-box {  
    flex: 0.5;  
    display: flex;  
    align-items: center;  
    margin: 0 15px;  
    position: relative;  
    background: #fff;  
    border: 1px solid #ccc;  
    border-radius: 4px;  
    padding: 2px 5px;  
}  
.search-box input {  
    flex: 1;  
    border: none;  
    padding: 5px 8px;  
    font-size: 14px;  
    outline: none;  
}  
.search-box .search-icon, .search-box .clear-icon {  
    cursor: pointer;  
    padding: 0 5px;  
    font-size: 16px;  
}  

/* Nav Icons */  
.nav-icons {  
    display: flex;  
    align-items: center;  
    gap: 12px;  
    font-size: 18px;  
}  
.nav-icons a {  
    color: #333;  
    text-decoration: none;  
    position: relative;  
}  
#cartCountHeader {  
    background: red;  
    color: white;  
    font-size: 12px;  
    padding: 1px 5px;  
    border-radius: 50%;  
    position: absolute;  
    top: -8px;  
    right: -10px;  
}  

/* Menu content */  
.menu-content {  
    display: none;  
    position: absolute;  
    top: 60px;  
    left: 10px;  
    background: white;  
    border: 1px solid #ccc;  
    padding: 10px;  
    z-index: 10;  
    border-radius: 5px;  
    box-shadow: 0 0 10px rgba(0,0,0,0.2);  
}  
.menu-content a {  
    display: block;  
    margin-bottom: 6px;  
    color: #002b5c;  
    text-decoration: none;  
    font-size: 14px;  
}  

/* User info */  
.user-options {  
    padding: 10px 20px;  
    background: #fff;  
    font-size: 14px;  
}  

/* PRODUCT GRID */  
#products {  
    display: flex;  
    flex-wrap: wrap;  
    gap: 15px;  
    padding: 10px 20px;  
}  

/* PRODUCT CARD - SQUARE & COMPACT */  
.product-card {  
    width: 300px;  
    border: 1px solid #ccc;  
    padding: 8px;  
    box-sizing: border-box;  
    text-align: center;  
    background: #fff;  
    display: flex;  
    flex-direction: column;  
    align-items: center;  
    justify-content: space-between;  
    font-size: 12px;  
}  
.img-box {  
    width: 100%;  
    height: 300px;  
    overflow: hidden;  
    margin-bottom: 5px;  
    background: #f5f5f5;  
}  
.img-box img {  
    width: auto;  
    max-width: 100%;  
    height: 100%;  
    object-fit: contain;  
}  
.product-card h4 {  
    margin: 2px 0;  
    font-size: 14px;  
}  
.product-card p {  
    margin: 2px 0;  
    font-size: 12px;  
}  
button {  
    padding: 5px 8px;  
    font-size: 12px;  
    cursor: pointer;  
}  
button:disabled {  
    background: #aaa;  
    cursor: not-allowed;  
}  

/* Cart message */  
#cartMsg {  
    display: none;  
    background: #e6fffa;  
    color: #065f46;  
    padding: 8px;  
    margin: 10px 20px;  
    border: 1px solid #34d399;  
    border-radius: 5px;  
}  
</style>  
</head>  
<body>  

<div class="promo-bar" id="dynamicPromo">Welcome Natty home appliances store | Get your offer| Shop Now</div>  

<header class="header">  
    <div class="menu-logo">  
        <div class="menu-icon" id="menuToggle">☰</div>  
        <img src="assets/images/logo.png" alt="Logo" class="logo-img" id="logo">  
        <div class="menu-content" id="menuContent">  
            <a href="orders.php">My orders</a>
            <a href="profile.php">About you</a>  
            <a href="contact.php">Contact us</a>  
        </div>  
    </div>  

    <div class="search-box">  
        <span class="search-icon" onclick="searchProducts()">🔎</span>  
        <input type="text" id="searchInput" placeholder="Search products..." oninput="searchProducts()">  
        <span class="clear-icon" id="clearSearch" onclick="clearSearch()" style="display:none;">❌</span>  
    </div>  

    <div class="nav-icons">  
        <span>✉️ nattyhomeappliances@gmail.com</span>  
        <a href="profile.php" title="Profile">👤</a>  
        <a href="cart.php" title="Cart">🛒 <span id="cartCountHeader">0</span></a>  
        <a href="logout.php" title="Logout">🔓</a>  
    </div>  
</header>  

<div class="user-options">  
    Hello, <?php echo htmlspecialchars($name); ?> 👋 | Role: <strong><?php echo htmlspecialchars($role); ?></strong>  
</div>  

<div id="cartMsg"></div>  
<div id="products"></div>  

<script>  
// Dynamic promo rotation  
const promos = [  
    '🚚 FREE Next Day Delivery on Kitchen Appliances',  
    '🏷️ 10% off when you buy 2 or more products',  
    '💳 Shop Now, Pay Later',  
    '⚠️ Product Safety Alert'  
];  
let promoIndex = 0;  
const promoEl = document.getElementById('dynamicPromo');  
function rotatePromo(){  
    promoEl.style.opacity=0;  
    setTimeout(()=>{  
        promoEl.innerText=promos[promoIndex];  
        promoEl.style.opacity=1;  
        promoIndex=(promoIndex+1)%promos.length;  
    },300);  
}  
setInterval(rotatePromo,4000);  

// Menu toggle  
const menuToggle = document.getElementById('menuToggle');  
const menuContent = document.getElementById('menuContent');  
menuToggle.addEventListener('click', ()=> {  
    menuContent.style.display = menuContent.style.display==='block' ? 'none' : 'block';  
});  

// Logo click expand  
const logo = document.getElementById('logo');  
logo.addEventListener('click', ()=>{ logo.classList.toggle('expanded'); });  

// Load products  
let allProducts = [];  
function loadProducts() {  
    fetch('api/products/read.php')  
        .then(res => res.json())  
        .then(products => {  
            allProducts = products;  
            displayProducts(products);  
        })  
        .catch(err=>console.error(err));  
}  

// Display products (filtered or all)  
function displayProducts(products) {  
    const container = document.getElementById('products');  
    container.innerHTML='';  
    if(products.length===0){   
        container.innerHTML='<p style="padding:10px;">No products available</p>';   
        return;   
    }  
    products.forEach(p=>{  
        const div=document.createElement('div');  
        div.className='product-card';  
        div.innerHTML=`  
            <div class="img-box">  
                <img src="assets/images/${p.image || 'default.png'}" alt="${p.name}">  
            </div>  
            <h4>${p.name}</h4>  
            <p>${p.description || ''}</p>  
            <p><strong>TSH ${p.price}</strong></p>  
            <p>Stock: ${p.stock}</p>  
            <p>${p.category_name || 'Uncategorized'}</p>  
            <button ${p.stock===0?'disabled':''} onclick="addToCart(${p.product_id})">  
                ${p.stock===0?'Out of Stock':'Add to Cart'}  
            </button>  
        `;  
        container.appendChild(div);  
    });  
}  

// Add to Cart function  
function addToCart(productId) {  
    fetch('api/cart/add.php', {  
        method: 'POST',  
        headers: {'Content-Type': 'application/json'},  
        body: JSON.stringify({ product_id: productId, quantity: 1 })  
    })  
    .then(res => res.json())  
    .then(data => {  
        const msgEl = document.getElementById('cartMsg');  
        if(data.status){  
            msgEl.innerText = data.message || 'Product added to cart!';  
            msgEl.style.display = 'block';  
            setTimeout(()=>{ msgEl.style.display='none'; }, 3000);  
            updateCartCount();  
        } else {  
            msgEl.innerText = data.message || 'Failed to add to cart';  
            msgEl.style.display = 'block';  
            setTimeout(()=>{ msgEl.style.display='none'; }, 3000);  
        }  
    })  
    .catch(err => console.error(err));  
}  

// Search products  
function searchProducts() {  
    const term = document.getElementById('searchInput').value.trim().toLowerCase();  
    const clearBtn = document.getElementById('clearSearch');  
    clearBtn.style.display = term ? 'inline' : 'none';  
    if(!term){ displayProducts(allProducts); return; }  

    const filtered = allProducts.filter(p =>   
        p.name.toLowerCase().includes(term) ||  
        (p.category_name && p.category_name.toLowerCase().includes(term))  
    );  
    displayProducts(filtered);  
}  

// Clear search  
function clearSearch() {  
    document.getElementById('searchInput').value = '';  
    document.getElementById('clearSearch').style.display = 'none';  
    displayProducts(allProducts);  
}  

// Cart count  
function updateCartCount(){  
    fetch('api/cart/count.php')  
    .then(res=>res.json())  
    .then(data=>{  
        if(data.status){ document.getElementById('cartCountHeader').innerText=data.count; }  
    }).catch(err=>console.error(err));  
}  

// Initial load  
loadProducts();  
updateCartCount();  
</script>  

<hr>  

<?php if($role==='admin'): ?>  
<h3>Admin Options</h3>  
<ul>  
<li><a href="manage_products.php">Manage Products</a></li>  
<li><a href="manage_orders.php">View Orders</a></li>  
</ul>  
<?php else: ?>  
<h3>Customer Options</h3>  
<ul>  
<li><a href="orders.php">My Orders</a></li>  
<li><a href="profile.php">My Profile</a></li>  
</ul>  
<?php endif; ?>  

<p><a href="cart.php">🛒 View Cart</a></p>  
<p><a href="logout.php">Logout</a></p>  

</body>  
</html>