<?php
$pageTitle = 'Checkout — VelvetVogue';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/functions.php';
requireLogin();
$db = getDB();

$stmt = $db->prepare("
    SELECT c.id, c.quantity, c.size, p.id as product_id, p.name, p.image,
           COALESCE(p.sale_price, p.price) as unit_price
    FROM cart c JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cartItems = $stmt->fetchAll();
if (empty($cartItems)) { header('Location: /velvet vogue/cart.php'); exit; }

$subtotal = array_sum(array_map(fn($i) => $i['unit_price'] * $i['quantity'], $cartItems));
$shipping = $subtotal >= 5000 ? 0 : 350;
$total = $subtotal + $shipping;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['shipping_name'] ?? '');
    $address = trim($_POST['shipping_address'] ?? '');
    $city = trim($_POST['shipping_city'] ?? '');
    $phone = trim($_POST['shipping_phone'] ?? '');
    $payment = $_POST['payment_method'] ?? 'cod';

    if (!$name || !$address || !$city || !$phone) {
        $error = 'Please fill in all shipping details.';
    } else {
        $orderNumber = generateOrderNumber();
        while ($db->prepare("SELECT id FROM orders WHERE order_number = ?")->execute([$orderNumber]) &&
               $db->query("SELECT id FROM orders WHERE order_number = '$orderNumber'")->fetchColumn()) {
            $orderNumber = generateOrderNumber();
        }

        $stmt = $db->prepare("INSERT INTO orders (user_id, order_number, subtotal, shipping, total, status, shipping_name, shipping_address, shipping_city, shipping_phone, payment_method) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$_SESSION['user_id'], $orderNumber, $subtotal, $shipping, $total, 'processing', $name, $address, $city, $phone, $payment]);
        $orderId = $db->lastInsertId();

        $itemStmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, size) VALUES (?,?,?,?,?)");
        foreach ($cartItems as $item) {
            $itemStmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['unit_price'], $item['size']]);
        }

        $db->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$_SESSION['user_id']]);
        header('Location: /velvet vogue/account.php?order=' . $orderNumber);
        exit;
    }
}

require_once 'includes/header.php';
?>
<div class="page-header">
    <div class="container">
        <div class="breadcrumb" style="margin-bottom:8px">
            <a href="/velvet vogue/index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <a href="/velvet vogue/cart.php">Cart</a>
            <i class="fas fa-chevron-right"></i>
            <span>Checkout</span>
        </div>
        <h1>Checkout</h1>
    </div>
</div>
<div class="container checkout-page">
    <?php if ($error): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="checkout-layout">
            <div class="checkout-form-section">
                <h2>Shipping Details</h2>
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input class="form-input" type="text" name="shipping_name" value="<?= htmlspecialchars($_SESSION['user_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number *</label>
                    <input class="form-input" type="tel" name="shipping_phone" placeholder="+94 77 000 0000" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Street Address *</label>
                    <input class="form-input" type="text" name="shipping_address" placeholder="123 Main Street" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">City *</label>
                        <input class="form-input" type="text" name="shipping_city" placeholder="Colombo" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Postal Code</label>
                        <input class="form-input" type="text" placeholder="00100">
                    </div>
                </div>

                <h2 style="margin-top:40px">Payment Method</h2>
                <div style="display:flex;flex-direction:column;gap:12px">
                    <label style="display:flex;align-items:center;gap:12px;padding:16px;border:1.5px solid var(--border);border-radius:6px;cursor:pointer;transition:all 0.2s" id="cod-label">
                        <input type="radio" name="payment_method" value="cod" checked onchange="updatePayLabel()">
                        <i class="fas fa-money-bill-wave" style="color:var(--gold-dark);font-size:18px"></i>
                        <div>
                            <div style="font-weight:600;font-size:14px">Cash on Delivery</div>
                            <div style="font-size:12px;color:var(--text-light)">Pay when your order arrives</div>
                        </div>
                    </label>
                    <label style="display:flex;align-items:center;gap:12px;padding:16px;border:1.5px solid var(--border);border-radius:6px;cursor:pointer;transition:all 0.2s" id="card-label">
                        <input type="radio" name="payment_method" value="card" onchange="updatePayLabel()">
                        <i class="fas fa-credit-card" style="color:var(--gold-dark);font-size:18px"></i>
                        <div>
                            <div style="font-weight:600;font-size:14px">Credit / Debit Card</div>
                            <div style="font-size:12px;color:var(--text-light)">Visa, Mastercard accepted</div>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <div class="cart-summary" style="position:sticky;top:90px">
                    <h3>Order Summary</h3>
                    <?php foreach ($cartItems as $item): ?>
                    <div style="display:flex;gap:12px;align-items:center;padding:10px 0;border-bottom:1px solid var(--border)">
                        <img src="/velvet vogue/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width:50px;height:64px;object-fit:cover;object-position:top;border-radius:4px">
                        <div style="flex:1;min-width:0">
                            <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars($item['name']) ?></div>
                            <div style="font-size:11px;color:var(--text-light)">Size: <?= $item['size'] ?> × <?= $item['quantity'] ?></div>
                        </div>
                        <div style="font-size:13px;font-weight:600;color:var(--gold-dark);white-space:nowrap"><?= formatPrice($item['unit_price'] * $item['quantity']) ?></div>
                    </div>
                    <?php endforeach; ?>
                    <div class="summary-row" style="margin-top:16px">
                        <span>Subtotal</span>
                        <span><?= formatPrice($subtotal) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span><?= $shipping === 0 ? '<span style="color:var(--success)">Free</span>' : formatPrice($shipping) ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span><?= formatPrice($total) ?></span>
                    </div>
                    <button type="submit" class="btn-checkout" style="border:none;cursor:pointer">
                        <i class="fas fa-lock" style="margin-right:8px"></i>Place Order
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function updatePayLabel() {
    document.querySelectorAll('[id$="-label"]').forEach(l => l.style.borderColor = 'var(--border)');
    const selected = document.querySelector('input[name="payment_method"]:checked');
    if (selected) selected.closest('label').style.borderColor = 'var(--gold)';
}
updatePayLabel();
</script>

<?php require_once 'includes/footer.php'; ?>
