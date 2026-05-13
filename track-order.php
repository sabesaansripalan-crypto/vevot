<?php
$pageTitle = 'Track Order — VelvetVogue';
require_once 'includes/header.php';
$db = getDB();
$orderNumber = trim($_GET['order'] ?? '');
$order = null;
$orderItems = [];

if ($orderNumber) {
    $stmt = $db->prepare("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.order_number = ?");
    $stmt->execute([$orderNumber]);
    $order = $stmt->fetch();
    if ($order && isLoggedIn() && $order['user_id'] == $_SESSION['user_id']) {
        $itemStmt = $db->prepare("SELECT oi.*, p.name, p.image, p.category FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
        $itemStmt->execute([$order['id']]);
        $orderItems = $itemStmt->fetchAll();
    } elseif ($order && !isLoggedIn()) {
        $order = null;
    }
}

$steps = ['pending' => 0, 'processing' => 1, 'shipped' => 2, 'delivered' => 3];
$currentStep = $order ? ($steps[$order['status']] ?? 0) : 0;
?>
<div class="page-header">
    <div class="container">
        <div class="breadcrumb" style="margin-bottom:8px">
            <a href="/velvet vogue/index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>Track Order</span>
        </div>
        <h1>Track Your Order</h1>
    </div>
</div>

<div class="container" style="padding:60px 24px;max-width:800px">
    <!-- SEARCH FORM -->
    <div style="background:var(--cream);border:1px solid var(--border);border-radius:12px;padding:32px;margin-bottom:32px;text-align:center">
        <p style="font-size:13px;color:var(--text-light);margin-bottom:16px">Enter your order number to track your shipment</p>
        <form method="GET" style="display:flex;gap:0;max-width:480px;margin:0 auto;border-radius:6px;overflow:hidden;border:1.5px solid var(--border)">
            <input type="text" name="order" value="<?= htmlspecialchars($orderNumber) ?>" placeholder="e.g. VEL-2026-1016"
                style="flex:1;padding:14px 20px;border:none;font-family:'Jost',sans-serif;font-size:14px;background:var(--white);outline:none;color:var(--dark)">
            <button type="submit" style="background:var(--gold);color:#fff;border:none;padding:14px 28px;font-family:'Jost',sans-serif;font-size:12px;font-weight:600;letter-spacing:1px;text-transform:uppercase;cursor:pointer">Track</button>
        </form>
    </div>

    <?php if ($orderNumber && !$order): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i>Order not found. Please check your order number.</div>
    <?php endif; ?>

    <?php if ($order): ?>
    <!-- ORDER INFO -->
    <div style="background:var(--white);border:1px solid var(--border);border-radius:12px;padding:32px;margin-bottom:24px">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;margin-bottom:28px">
            <div>
                <h2 style="font-family:'Cormorant Garamond',serif;font-size:26px;font-weight:600;margin-bottom:4px"><?= $order['order_number'] ?></h2>
                <p style="font-size:13px;color:var(--text-light)">Placed on <?= date('F j, Y \a\t g:i A', strtotime($order['created_at'])) ?></p>
            </div>
            <?php
            $badgeStyle = match($order['status']) {
                'processing' => 'background:#FFF5E0;color:#D4880A',
                'shipped' => 'background:#EDE5F5;color:#8E44AD',
                'delivered' => 'background:#E8F8EC;color:#27AE60',
                'cancelled' => 'background:#FEF0F0;color:#E74C3C',
                default => 'background:#EAF2FC;color:#2980B9'
            };
            ?>
            <span style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:30px;font-size:12px;font-weight:600;<?= $badgeStyle ?>">
                <span style="width:7px;height:7px;border-radius:50%;background:currentColor"></span>
                <?= ucfirst($order['status']) ?>
            </span>
        </div>

        <?php if ($order['status'] !== 'cancelled'): ?>
        <!-- PROGRESS TRACKER -->
        <div style="position:relative;margin:32px 0 40px">
            <div style="position:absolute;top:22px;left:11%;right:11%;height:2px;background:var(--border);z-index:0"></div>
            <div style="position:absolute;top:22px;left:11%;width:<?= min(100, $currentStep * 33.3) ?>%;height:2px;background:var(--gold);z-index:1;transition:width 0.3s"></div>
            <div style="display:grid;grid-template-columns:repeat(4,1fr);position:relative;z-index:2">
                <?php
                $trackSteps = [
                    ['icon' => 'fas fa-box', 'label' => 'Order Placed'],
                    ['icon' => 'fas fa-gear', 'label' => 'Processing'],
                    ['icon' => 'fas fa-truck', 'label' => 'Shipped'],
                    ['icon' => 'fas fa-house', 'label' => 'Delivered'],
                ];
                foreach ($trackSteps as $i => $step):
                    $done = $i <= $currentStep;
                ?>
                <div style="text-align:center">
                    <div style="width:44px;height:44px;border-radius:50%;<?= $done ? 'background:var(--gold)' : 'background:var(--white);border:2px solid var(--border)' ?>;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;font-size:16px;color:<?= $done ? '#fff' : 'var(--text-light)' ?>">
                        <i class="<?= $step['icon'] ?>"></i>
                    </div>
                    <div style="font-size:12px;font-weight:<?= $done ? '600' : '400' ?>;color:<?= $done ? 'var(--dark)' : 'var(--text-light)' ?>"><?= $step['label'] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- ORDER DETAILS -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;padding-top:24px;border-top:1px solid var(--border)">
            <div>
                <h4 style="font-size:11px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-light);margin-bottom:10px">Shipping To</h4>
                <p style="font-size:14px;font-weight:600;margin-bottom:4px"><?= htmlspecialchars($order['shipping_name']) ?></p>
                <p style="font-size:13px;color:var(--text)"><?= htmlspecialchars($order['shipping_address']) ?></p>
                <p style="font-size:13px;color:var(--text)"><?= htmlspecialchars($order['shipping_city']) ?></p>
                <p style="font-size:13px;color:var(--text)"><?= htmlspecialchars($order['shipping_phone']) ?></p>
            </div>
            <div>
                <h4 style="font-size:11px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-light);margin-bottom:10px">Order Summary</h4>
                <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px"><span style="color:var(--text)">Subtotal</span><span><?= formatPrice($order['subtotal']) ?></span></div>
                <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px"><span style="color:var(--text)">Shipping</span><span><?= $order['shipping'] == 0 ? '<span style="color:var(--success)">Free</span>' : formatPrice($order['shipping']) ?></span></div>
                <div style="display:flex;justify-content:space-between;font-size:15px;font-weight:700;padding-top:8px;border-top:1px solid var(--border)"><span>Total</span><span style="color:var(--gold-dark)"><?= formatPrice($order['total']) ?></span></div>
            </div>
        </div>
    </div>

    <!-- ORDER ITEMS -->
    <div style="background:var(--white);border:1px solid var(--border);border-radius:12px;padding:28px">
        <h3 style="font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:600;margin-bottom:20px">Items Ordered</h3>
        <?php foreach ($orderItems as $item): ?>
        <div style="display:flex;gap:16px;align-items:center;padding:16px 0;border-bottom:1px solid var(--border)">
            <img src="/velvet vogue/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width:60px;height:76px;object-fit:cover;object-position:top;border-radius:6px;border:1px solid var(--border)">
            <div style="flex:1">
                <div style="font-weight:600;font-size:15px;margin-bottom:4px"><?= htmlspecialchars($item['name']) ?></div>
                <div style="font-size:12px;color:var(--text-light)">Size: <?= $item['size'] ?> × <?= $item['quantity'] ?></div>
            </div>
            <div style="font-weight:600;color:var(--gold-dark)"><?= formatPrice($item['price'] * $item['quantity']) ?></div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
