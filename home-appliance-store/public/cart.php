<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "
SELECT c.cart_item_id, c.product_id, p.name, p.price, c.quantity, p.stock, p.image
FROM cart_items c
JOIN products p ON c.product_id = p.product_id
WHERE c.user_id = :user_id
";

$stmt = $conn->prepare($sql);
$stmt->execute([':user_id'=>$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Cart</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 0 20px; }
        h2 { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: #fff; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #eee; }
        img { width: 60px; height: 60px; object-fit: cover; }
        button { padding: 4px 8px; margin: 0 2px; cursor: pointer; }
        .cart-controls { display: flex; justify-content: center; align-items: center; }
        .remove-btn { background: #ff4d4f; color: #fff; border: none; border-radius: 3px; }
        #grandTotal { font-size: 18px; font-weight: bold; margin-top: 10px; }
        #placeOrderBtn { padding: 8px 12px; background: #00bcd4; color: #fff; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px; }
        #cartMsg { display:none; background: #e6fffa; color: #065f46; padding: 8px; margin: 10px 0; border: 1px solid #34d399; border-radius: 5px; }
    </style>
</head>
<body>

<h2>My Cart</h2>
<div id="cartMsg"></div>

<?php if (empty($items)): ?>
    <p>Your cart is empty</p>
<?php else: ?>
<table>
    <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Qty</th>
        <th>Total</th>
        <th>Actions</th>
    </tr>

<?php $grand = 0; ?>
<?php foreach ($items as $item): ?>
<?php $total = $item['price'] * $item['quantity']; ?>
<?php $grand += $total; ?>
<tr data-cart-id="<?= $item['cart_item_id'] ?>">
    <td>
        <?php if($item['image']): ?>
            <img src="assets/images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
        <?php endif; ?>
        <br><?= htmlspecialchars($item['name']) ?>
    </td>
    <td>TSH <?= $item['price'] ?></td>
    <td class="cart-controls"><?= $item['quantity'] ?>
        
    <td class="item-total"><?= $total ?></td>
    <td>
        <button class="remove-btn" onclick="removeItem(<?= $item['cart_item_id'] ?>)">Remove</button>
    </td>
</tr>
<?php endforeach; ?>
</table>

<p id="grandTotal">Total TSH: $<?= $grand ?></p>
<button id="placeOrderBtn" onclick="placeOrder()">Place Order</button>

<?php endif; ?>

<p><a href="index.php">Back to Shop</a></p>
<p><a href="orders.php">My Orders</a></p>

<script>
function updateQty(cartId, change) {
    fetch('api/cart/update.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({cart_item_id: cartId, change: change})
    })
    .then(res => res.json())
    .then(data => {
        const msgEl = document.getElementById('cartMsg');
        if(data.status) {
            const row = document.querySelector(`tr[data-cart-id='${cartId}']`);
            if(data.quantity === 0) {
                row.remove();
            } else {
                row.querySelector('.qty').innerText = data.quantity;
                row.querySelector('.item-total').innerText = (data.price * data.quantity).toFixed(2);
            }
            document.getElementById('grandTotal').innerText = 'Total TSH: $' + data.grand.toFixed(2);
            msgEl.innerText = data.message || 'Cart updated';
            msgEl.style.display='block';
            setTimeout(()=>{msgEl.style.display='none'}, 3000);
        } else {
            msgEl.innerText = data.message || 'Failed';
            msgEl.style.display='block';
            setTimeout(()=>{msgEl.style.display='none'}, 3000);
        }
    })
    .catch(err => console.error(err));
}

function removeItem(cart_item_Id) {
    fetch('api/cart/remove.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({cart_item_id: cart_item_Id})
    })
    .then(res => res.json())
    .then(data => {
        const msgEl = document.getElementById('cartMsg');
        if(data.status) {
            document.querySelector(`tr[data-cart-id='${cart_item_Id}']`).remove();
            document.getElementById('grandTotal').innerText = 'Total TSH: $' + data.grand.toFixed(2);
            msgEl.innerText = data.message || 'Item removed';
            msgEl.style.display='block';
            setTimeout(()=>{msgEl.style.display='none'}, 3000);
        } else {
            msgEl.innerText = data.message || 'Failed to remove';
            msgEl.style.display='block';
            setTimeout(()=>{msgEl.style.display='none'}, 3000);
        }
    })
    .catch(err => console.error(err));
}

function placeOrder() {
    fetch('api/orders/create.php', { method: 'POST' })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if(data.status) window.location.href = 'orders.php';
    })
    .catch(err => {
        console.error(err);
        alert('Something went wrong');
    });
}
</script>

</body>
</html>