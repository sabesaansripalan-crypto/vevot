<?php
$pageTitle = 'My Wishlist — VelvetVogue';
require_once 'includes/header.php';
requireLogin();
$db = getDB();
$stmt = $db->prepare("SELECT p.*, w.id as wishlist_id FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.user_id = ? ORDER BY w.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();
?>
<div class="page-header">
    <div class="container">
        <div class="breadcrumb" style="margin-bottom:8px">
            <a href="/velvet vogue/index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>My Wishlist</span>
        </div>
        <h1>My Wishlist</h1>
    </div>
</div>
<div class="container products-section">
    <?php if (empty($items)): ?>
    <div class="empty-state">
        <i class="far fa-heart"></i>
        <h3>Your wishlist is empty</h3>
        <p>Save items you love by clicking the heart icon on any product.</p>
        <a href="/velvet vogue/shop.php" class="btn-primary">Browse Products</a>
    </div>
    <?php else: ?>
    <div style="margin-bottom:28px;font-size:14px;color:var(--text-light)"><?= count($items) ?> saved items</div>
    <div class="products-grid">
        <?php foreach ($items as $p): ?>
        <?php include 'includes/product-card.php'; ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php require_once 'includes/footer.php'; ?>
