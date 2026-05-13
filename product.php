<?php
require_once 'includes/header.php';
$db = getDB();
$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: /velvet vogue/shop.php'); exit; }
$stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { header('Location: /velvet vogue/shop.php'); exit; }
$pageTitle = $p['name'] . ' — VelvetVogue';
$inWishlist = isInWishlist($p['id']);
$displayPrice = $p['sale_price'] ?? $p['price'];
$related = $db->prepare("SELECT * FROM products WHERE category = ? AND id != ? ORDER BY RAND() LIMIT 4");
$related->execute([$p['category'], $p['id']]);
$relatedProducts = $related->fetchAll();
?>

<div class="container" style="padding-top:20px">
    <div class="breadcrumb" style="margin-bottom:0;padding:20px 0">
        <a href="/velvet vogue/index.php">Home</a>
        <i class="fas fa-chevron-right"></i>
        <a href="/velvet vogue/<?= $p['category'] ?>.php"><?= ucfirst($p['category']) ?></a>
        <i class="fas fa-chevron-right"></i>
        <span><?= htmlspecialchars($p['name']) ?></span>
    </div>
</div>

<div class="container product-detail">
    <div class="product-detail-grid">
        <!-- Gallery -->
        <div class="product-gallery">
            <img class="product-main-img" src="/velvet vogue/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" id="main-img">
        </div>

        <!-- Info -->
        <div class="product-detail-info">
            <span class="product-category-badge"><?= strtoupper($p['category']) ?> / <?= strtoupper($p['subcategory']) ?></span>
            <h1 class="product-detail-name"><?= htmlspecialchars($p['name']) ?></h1>

            <div class="product-rating" style="margin-bottom:16px;font-size:15px">
                <?= renderStars($p['rating']) ?>
                <span style="color:var(--text-light);font-size:13px"><?= number_format($p['rating'], 1) ?> Rating</span>
            </div>

            <div class="product-detail-price">
                <span class="detail-price-sale"><?= formatPrice($displayPrice) ?></span>
                <?php if ($p['sale_price']): ?>
                <span class="detail-price-original"><?= formatPrice($p['price']) ?></span>
                <span style="background:#fef0e0;color:#d4880a;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px">
                    <?= round((($p['price'] - $p['sale_price']) / $p['price']) * 100) ?>% OFF
                </span>
                <?php endif; ?>
            </div>

            <p class="product-detail-desc"><?= htmlspecialchars($p['description']) ?></p>

            <div class="size-selector">
                <div class="size-label">Select Size</div>
                <div class="sizes">
                    <?php foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $size): ?>
                    <button class="size-btn <?= $size === 'M' ? 'active' : '' ?>" data-size="<?= $size ?>"><?= $size ?></button>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" id="selected-size" value="M">
            </div>

            <div style="margin-bottom:28px">
                <div class="size-label">Quantity</div>
                <div class="quantity-selector">
                    <button class="qty-btn" data-dir="-">−</button>
                    <input type="number" class="qty-input" id="qty-input" value="1" min="1" max="<?= $p['stock'] ?>">
                    <button class="qty-btn" data-dir="+">+</button>
                </div>
            </div>

            <div class="detail-actions">
                <button class="btn-add-cart-lg" onclick="addToCartFromDetail()">
                    <i class="fas fa-shopping-bag" style="margin-right:8px"></i>Add to Cart
                </button>
                <button class="btn-wishlist-lg <?= $inWishlist ? 'active' : '' ?>" onclick="toggleWishlist(<?= $p['id'] ?>, this)">
                    <i class="<?= $inWishlist ? 'fas' : 'far' ?> fa-heart"></i>
                </button>
            </div>

            <div style="border-top:1px solid var(--border);padding-top:20px;margin-top:8px">
                <div style="display:flex;gap:20px;flex-wrap:wrap">
                    <span style="font-size:12px;color:var(--text-light)"><i class="fas fa-shield-alt" style="color:var(--gold);margin-right:6px"></i>Secure Payment</span>
                    <span style="font-size:12px;color:var(--text-light)"><i class="fas fa-undo-alt" style="color:var(--gold);margin-right:6px"></i>30-Day Returns</span>
                    <span style="font-size:12px;color:var(--text-light)"><i class="fas fa-shipping-fast" style="color:var(--gold);margin-right:6px"></i>Free Shipping over Rs. 5,000</span>
                </div>
            </div>

            <div style="background:var(--cream);border:1px solid var(--border);border-radius:6px;padding:16px;margin-top:20px">
                <div style="display:flex;justify-content:space-between;font-size:13px">
                    <span style="color:var(--text)">Availability:</span>
                    <span style="color:var(--success);font-weight:600"><i class="fas fa-circle" style="font-size:8px;margin-right:4px"></i>In Stock (<?= $p['stock'] ?> left)</span>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($relatedProducts)): ?>
    <div style="margin-top:80px">
        <div class="section-header" style="margin-bottom:36px">
            <span class="section-label">You May Also Like</span>
            <h2 class="section-title">Related <span>Products</span></h2>
        </div>
        <div class="products-grid">
            <?php foreach ($relatedProducts as $p): ?>
            <?php include 'includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function addToCartFromDetail() {
    const qty = parseInt(document.getElementById('qty-input').value) || 1;
    const size = document.getElementById('selected-size').value;
    addToCart(<?= $p['id'] ?>, qty, size);
}
</script>

<?php require_once 'includes/footer.php'; ?>
