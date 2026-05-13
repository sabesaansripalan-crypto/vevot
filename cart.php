<?php
$pageTitle = 'Shopping Cart — VelvetVogue';
require_once 'includes/header.php';
requireLogin();
$db = getDB();
$stmt = $db->prepare("
    SELECT c.id, c.quantity, c.size, p.id as product_id, p.name, p.image, p.category,
           COALESCE(p.sale_price, p.price) as unit_price, p.stock
    FROM cart c JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ? ORDER BY c.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$cartItems = $stmt->fetchAll();
$subtotal = array_sum(array_map(fn($i) => $i['unit_price'] * $i['quantity'], $cartItems));
$shipping = $subtotal >= 5000 ? 0 : 350;
$total = $subtotal + $shipping;
?>
<div class="page-header">
    <div class="container">
        <div class="breadcrumb" style="margin-bottom:8px">
            <a href="/velvet vogue/index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>Shopping Cart</span>
        </div>
        <h1>Shopping Cart</h1>
    </div>
</div>

<div class="container cart-page">
    <?php if (empty($cartItems)): ?>
    <div class="empty-state">
        <i class="fas fa-shopping-bag"></i>
        <h3>Your cart is empty</h3>
        <p>Looks like you haven't added anything yet.</p>
        <a href="/velvet vogue/shop.php" class="btn-primary">Start Shopping</a>
    </div>
    <?php else: ?>
    <div class="cart-layout">
        <div class="cart-items">
            <div class="cart-header">
                <span>Product</span>
                <span>Price</span>
                <span>Quantity</span>
                <span>Total</span>
                <span></span>
            </div>

            <?php foreach ($cartItems as $item): ?>
            <div class="cart-item" data-item="<?= $item['id'] ?>">
                <div class="cart-item-info">
                    <img class="cart-item-img" src="/velvet vogue/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                    <div>
                        <div class="cart-item-name"><?= htmlspecialchars($item['name']) ?></div>
                        <div class="cart-item-size">Size: <?= $item['size'] ?></div>
                        <div style="font-size:12px;color:var(--text-light);margin-top:4px"><?= ucfirst($item['category']) ?></div>
                    </div>
                </div>
                <div style="font-weight:600;color:var(--gold-dark)"><?= formatPrice($item['unit_price']) ?></div>
                <div class="cart-qty">
                    <button class="cqty-btn" onclick="updateCartQty(<?= $item['id'] ?>, -1)">−</button>
                    <input class="cqty-input" type="number" value="<?= $item['quantity'] ?>" min="1" readonly>
                    <button class="cqty-btn" onclick="updateCartQty(<?= $item['id'] ?>, 1)">+</button>
                </div>
                <div style="font-weight:600"><?= formatPrice($item['unit_price'] * $item['quantity']) ?></div>
                <button class="cart-remove" onclick="removeCartItem(<?= $item['id'] ?>)" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="cart-summary">
            <h3>Order Summary</h3>
            <div class="summary-row">
                <span>Subtotal</span>
                <span id="cart-subtotal"><?= formatPrice($subtotal) ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping</span>
                <span><?= $shipping === 0 ? '<span style="color:var(--success)">Free</span>' : formatPrice($shipping) ?></span>
            </div>
            <?php if ($shipping > 0): ?>
            <div style="font-size:11px;color:var(--text-light);margin-bottom:8px">Add Rs. <?= number_format(5000 - $subtotal) ?> more for free shipping</div>
            <?php endif; ?>
            <div class="summary-row total">
                <span>Total</span>
                <span id="cart-total"><?= formatPrice($total) ?></span>
            </div>
            <a href="/velvet vogue/checkout.php" class="btn-checkout">Proceed to Checkout</a>
            <a href="/velvet vogue/shop.php" style="display:block;text-align:center;font-size:12px;color:var(--text-light);margin-top:14px">
                <i class="fas fa-arrow-left" style="margin-right:4px"></i>Continue Shopping
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
