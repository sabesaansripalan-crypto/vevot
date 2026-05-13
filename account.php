<?php
$pageTitle = 'My Account — VelvetVogue';
require_once 'includes/header.php';
requireLogin();
$db = getDB();

// Handle order cancellation
$cancelMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order_id'])) {
    $cancelId = (int)$_POST['cancel_order_id'];
    $stmt = $db->prepare("SELECT id, status FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$cancelId, $_SESSION['user_id']]);
    $toCancel = $stmt->fetch();
    if ($toCancel && in_array($toCancel['status'], ['pending', 'processing'])) {
        $db->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?")->execute([$cancelId]);
        $cancelMsg = 'success';
    } else {
        $cancelMsg = 'error';
    }
}

$user = $db->prepare("SELECT * FROM users WHERE id = ?");
$user->execute([$_SESSION['user_id']]);
$user = $user->fetch();

$orders = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orders->execute([$_SESSION['user_id']]);
$orders = $orders->fetchAll();

$wishlistCount = getWishlistCount();
$newOrder = $_GET['order'] ?? '';
?>
<div class="page-header">
    <div class="container">
        <div class="breadcrumb" style="margin-bottom:8px">
            <a href="/velvet vogue/index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>My Account</span>
        </div>
        <h1>My Account</h1>
        <p style="font-size:14px;color:var(--text);margin-top:8px">Welcome back, <a href="#" style="color:var(--gold-dark);font-weight:600"><?= htmlspecialchars($user['name']) ?></a> — manage your orders and wishlist below.</p>
    </div>
</div>

<div class="container" style="padding:60px 24px">
    <?php if ($newOrder): ?>
    <div class="alert alert-success" style="margin-bottom:28px">
        <i class="fas fa-check-circle"></i>
        Your order <strong><?= htmlspecialchars($newOrder) ?></strong> has been placed successfully! We'll start processing it right away.
    </div>
    <?php endif; ?>
    <?php if ($cancelMsg === 'success'): ?>
    <div class="alert alert-success" style="margin-bottom:28px">
        <i class="fas fa-check-circle"></i>
        Your order has been <strong>cancelled</strong> successfully.
    </div>
    <?php elseif ($cancelMsg === 'error'): ?>
    <div class="alert alert-error" style="margin-bottom:28px">
        <i class="fas fa-exclamation-circle"></i>
        This order cannot be cancelled (it may already be shipped or delivered).
    </div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:320px 1fr;gap:32px;align-items:start">
        <!-- PROFILE SIDEBAR -->
        <div style="background:var(--white);border:1px solid var(--border);border-radius:16px;padding:36px;text-align:center">
            <div style="width:80px;height:80px;background:var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-family:'Cormorant Garamond',serif;font-size:36px;font-weight:700;color:#fff">
                <?= strtoupper(substr($user['name'], 0, 1)) ?>
            </div>
            <h2 style="font-family:'Cormorant Garamond',serif;font-size:26px;font-weight:600;margin-bottom:4px"><?= htmlspecialchars($user['name']) ?></h2>
            <p style="font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--gold-dark);margin-bottom:24px;padding-bottom:24px;border-bottom:1px solid var(--border)">VelvetVogue Member</p>

            <div style="display:flex;align-items:center;gap:10px;font-size:13px;color:var(--text);margin-bottom:24px;padding-bottom:24px;border-bottom:1px solid var(--border);justify-content:center">
                <i class="fas fa-envelope" style="color:var(--text-light)"></i>
                <?= htmlspecialchars($user['email']) ?>
            </div>

            <div style="display:flex;flex-direction:column;gap:4px;text-align:left;margin-bottom:28px">
                <a href="#orders" style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-radius:8px;font-size:14px;font-weight:500;color:var(--dark);transition:background 0.2s" onmouseover="this.style.background='var(--cream)'" onmouseout="this.style.background='transparent'">
                    <span><i class="fas fa-bag-shopping" style="width:20px;color:var(--text-light);margin-right:8px"></i>My Orders</span>
                    <span style="background:var(--cream-dark);padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600"><?= count($orders) ?></span>
                </a>
                <a href="/velvet vogue/wishlist.php" style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-radius:8px;font-size:14px;font-weight:500;color:var(--dark);transition:background 0.2s" onmouseover="this.style.background='var(--cream)'" onmouseout="this.style.background='transparent'">
                    <span><i class="far fa-heart" style="width:20px;color:var(--text-light);margin-right:8px"></i>My Wishlist</span>
                    <?php if ($wishlistCount > 0): ?>
                    <span style="background:var(--cream-dark);padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600"><?= $wishlistCount ?></span>
                    <?php endif; ?>
                </a>
                <a href="/velvet vogue/track-order.php" style="display:flex;align-items:center;padding:12px 16px;border-radius:8px;font-size:14px;font-weight:500;color:var(--dark);transition:background 0.2s" onmouseover="this.style.background='var(--cream)'" onmouseout="this.style.background='transparent'">
                    <i class="fas fa-clock-rotate-left" style="width:20px;color:var(--text-light);margin-right:8px"></i>Track Order
                </a>
            </div>

            <a href="/velvet vogue/logout.php" style="display:block;width:100%;padding:12px;border:1.5px solid var(--error);border-radius:30px;font-family:'Jost',sans-serif;font-size:11px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--error);text-align:center;transition:all 0.2s;text-decoration:none" onmouseover="this.style.background='var(--error)';this.style.color='#fff'" onmouseout="this.style.background='transparent';this.style.color='var(--error)'">
                Sign Out
            </a>
        </div>

        <!-- ORDER HISTORY -->
        <div id="orders" style="background:var(--white);border:1px solid var(--border);border-radius:16px;padding:36px">
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:28px">
                <h2 style="font-family:'Cormorant Garamond',serif;font-size:28px;font-weight:600">Order History</h2>
                <span style="background:var(--cream-dark);border:1px solid var(--border);padding:4px 14px;border-radius:20px;font-size:12px;font-weight:600"><?= count($orders) ?> orders</span>
            </div>

            <?php if (empty($orders)): ?>
            <div class="empty-state">
                <i class="fas fa-bag-shopping"></i>
                <h3>No orders yet</h3>
                <p>When you place an order, it will appear here.</p>
                <a href="/velvet vogue/shop.php" class="btn-primary">Start Shopping</a>
            </div>
            <?php else: ?>
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr>
                        <th style="text-align:left;font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-light);padding-bottom:16px;border-bottom:1px solid var(--border)">Order ID</th>
                        <th style="text-align:left;font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-light);padding-bottom:16px;border-bottom:1px solid var(--border)">Date</th>
                        <th style="text-align:left;font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-light);padding-bottom:16px;border-bottom:1px solid var(--border)">Items</th>
                        <th style="text-align:left;font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-light);padding-bottom:16px;border-bottom:1px solid var(--border)">Total</th>
                        <th style="text-align:left;font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--text-light);padding-bottom:16px;border-bottom:1px solid var(--border)">Status</th>
                        <th style="border-bottom:1px solid var(--border)"></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order):
                    $itemCount = $db->prepare("SELECT SUM(quantity) FROM order_items WHERE order_id = ?");
                    $itemCount->execute([$order['id']]);
                    $items = $itemCount->fetchColumn();
                    $badgeClass = match($order['status']) {
                        'processing' => 'background:#FFF5E0;color:#D4880A',
                        'shipped' => 'background:#EDE5F5;color:#8E44AD',
                        'delivered' => 'background:#E8F8EC;color:#27AE60',
                        'cancelled' => 'background:#FEF0F0;color:#E74C3C',
                        'paid' => 'background:#F0F0F0;color:#7F8C8D',
                        default => 'background:#EAF2FC;color:#2980B9'
                    };
                ?>
                <tr>
                    <td style="padding:20px 0;border-bottom:1px solid #F5F2EE">
                        <a href="/velvet vogue/track-order.php?order=<?= urlencode($order['order_number']) ?>" style="color:var(--gold-dark);font-weight:600;font-size:13px"><?= $order['order_number'] ?></a>
                    </td>
                    <td style="padding:20px 0;border-bottom:1px solid #F5F2EE;font-size:13px;color:var(--text)"><?= date('Y-m-d H:i', strtotime($order['created_at'])) ?></td>
                    <td style="padding:20px 0;border-bottom:1px solid #F5F2EE;font-size:13px;color:var(--text)"><?= $items ?> <?= $items == 1 ? 'item' : 'items' ?></td>
                    <td style="padding:20px 0;border-bottom:1px solid #F5F2EE;font-weight:700;font-size:14px"><?= formatPrice($order['total']) ?></td>
                    <td style="padding:20px 0;border-bottom:1px solid #F5F2EE">
                        <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 14px;border-radius:30px;font-size:11px;font-weight:600;<?= $badgeClass ?>">
                            <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block"></span>
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </td>
                    <td style="padding:20px 0;border-bottom:1px solid #F5F2EE">
                        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                            <a href="/velvet vogue/track-order.php?order=<?= urlencode($order['order_number']) ?>" style="padding:7px 16px;border:1.5px solid var(--border);border-radius:20px;font-size:11px;font-weight:600;color:var(--dark);text-decoration:none;transition:all 0.2s" onmouseover="this.style.borderColor='var(--dark)';this.style.background='var(--dark)';this.style.color='#fff'" onmouseout="this.style.borderColor='var(--border)';this.style.background='transparent';this.style.color='var(--dark)'">Track</a>
                            <?php if (in_array($order['status'], ['pending', 'processing'])): ?>
                            <form method="POST" onsubmit="return confirm('Cancel order <?= $order['order_number'] ?>? This cannot be undone.')">
                                <input type="hidden" name="cancel_order_id" value="<?= $order['id'] ?>">
                                <button type="submit" style="padding:7px 16px;border:1.5px solid #E74C3C;border-radius:20px;font-size:11px;font-weight:600;color:#E74C3C;background:transparent;cursor:pointer;transition:all 0.2s" onmouseover="this.style.background='#E74C3C';this.style.color='#fff'" onmouseout="this.style.background='transparent';this.style.color='#E74C3C'">Cancel</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
