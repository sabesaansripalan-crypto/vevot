<?php
$pageTitle = 'Shop All — VelvetVogue';
require_once 'includes/header.php';
$db = getDB();
try { $db->exec("ALTER TABLE products ADD COLUMN color VARCHAR(30) DEFAULT NULL"); } catch(PDOException $e) {}

$category    = $_GET['cat']   ?? '';
$subcategory = $_GET['sub']   ?? '';
$q           = trim($_GET['q'] ?? '');
$sort        = $_GET['sort']  ?? 'featured';
$color       = $_GET['color'] ?? '';
$page        = max(1, (int)($_GET['page'] ?? 1));
$perPage     = 12;

$where  = ["1=1"];
$params = [];

if ($category)    { $where[] = "category = ?";                       $params[] = $category; }
if ($subcategory) { $where[] = "subcategory = ?";                    $params[] = $subcategory; }
if ($q)           { $where[] = "(name LIKE ? OR description LIKE ?)"; $params[] = "%$q%"; $params[] = "%$q%"; }
if ($color)       { $where[] = "color = ?";                          $params[] = $color; }

$orderBy = match($sort) {
    'price_asc'  => 'COALESCE(sale_price, price) ASC',
    'price_desc' => 'COALESCE(sale_price, price) DESC',
    'newest'     => 'created_at DESC',
    'rating'     => 'rating DESC',
    default      => 'featured DESC, rating DESC'
};

$whereStr       = implode(' AND ', $where);
$total          = $db->prepare("SELECT COUNT(*) FROM products p WHERE $whereStr AND p.id = (SELECT MIN(p2.id) FROM products p2 WHERE p2.name = p.name AND p2.category = p.category)");
$total->execute($params);
$totalProducts  = $total->fetchColumn();
$totalPages     = ceil($totalProducts / $perPage);
$offset         = ($page - 1) * $perPage;

$stmt = $db->prepare("SELECT p.* FROM products p WHERE $whereStr AND p.id = (SELECT MIN(p2.id) FROM products p2 WHERE p2.name = p.name AND p2.category = p.category) ORDER BY $orderBy LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$products = $stmt->fetchAll();

$colorMap = [
    'white' => '#F8F8F8', 'black' => '#1A1A1A', 'gray'   => '#9E9E9E',
    'beige' => '#D4C5A9', 'navy'  => '#1B2A4A', 'blue'   => '#2980B9',
    'red'   => '#C0392B', 'green' => '#27AE60', 'yellow' => '#F4D03F',
    'pink'  => '#E91E8C', 'purple'=> '#8E44AD', 'orange' => '#E67E22',
    'brown' => '#795548',
];
$availableColors = $db->query("SELECT DISTINCT color FROM products WHERE color IS NOT NULL AND color != '' ORDER BY color")->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="page-header">
    <div class="container">
        <div class="breadcrumb" style="margin-bottom:8px">
            <a href="/velvet vogue/index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span><?= $q ? "Search: \"$q\"" : ($category ? ucfirst($category) : 'All Products') ?></span>
        </div>
        <h1><?= $q ? "Search Results" : ($category ? ucfirst($category) . "'s Collection" : 'Shop All') ?></h1>
    </div>
</div>

<div class="container">
    <div class="shop-layout">
        <aside class="filters-sidebar">
            <h3>Filters</h3>
            <div class="filter-group">
                <div class="filter-group-title">Category</div>
                <label class="filter-option"><input type="radio" name="cat" value="" <?= !$category ? 'checked' : '' ?> onchange="applyFilter()"> All</label>
                <label class="filter-option"><input type="radio" name="cat" value="men" <?= $category === 'men' ? 'checked' : '' ?> onchange="applyFilter()"> Men</label>
                <label class="filter-option"><input type="radio" name="cat" value="women" <?= $category === 'women' ? 'checked' : '' ?> onchange="applyFilter()"> Women</label>
                <label class="filter-option"><input type="radio" name="cat" value="kids" <?= $category === 'kids' ? 'checked' : '' ?> onchange="applyFilter()"> Kids</label>
            </div>
            <div class="filter-group">
                <div class="filter-group-title">Color</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:6px">
                    <?php foreach ($availableColors as $c):
                        $hex      = $colorMap[$c] ?? '#ccc';
                        $isActive = $color === $c;
                    ?>
                    <button type="button" onclick="applyColorFilter('<?= $c ?>')" title="<?= ucfirst($c) ?>"
                        style="width:28px;height:28px;border-radius:50%;background:<?= $hex ?>;
                               border:2.5px solid <?= $isActive ? '#C8720A' : ($c === 'white' ? '#D0C8BC' : 'transparent') ?>;
                               outline:<?= $isActive ? '2px solid #C8720A' : 'none' ?>;outline-offset:2px;
                               cursor:pointer;transition:all 0.2s"></button>
                    <?php endforeach; ?>
                    <?php if ($color): ?>
                    <button type="button" onclick="applyColorFilter('')" title="Clear color"
                        style="width:28px;height:28px;border-radius:50%;background:#F0ECE6;border:1.5px solid #D0C8BC;
                               cursor:pointer;font-size:15px;font-weight:700;color:#7A6A52;
                               display:flex;align-items:center;justify-content:center">×</button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="filter-group">
                <div class="filter-group-title">Sort By</div>
                <label class="filter-option"><input type="radio" name="sort" value="featured" <?= $sort === 'featured' ? 'checked' : '' ?> onchange="applyFilter()"> Featured</label>
                <label class="filter-option"><input type="radio" name="sort" value="newest" <?= $sort === 'newest' ? 'checked' : '' ?> onchange="applyFilter()"> Newest</label>
                <label class="filter-option"><input type="radio" name="sort" value="price_asc" <?= $sort === 'price_asc' ? 'checked' : '' ?> onchange="applyFilter()"> Price: Low to High</label>
                <label class="filter-option"><input type="radio" name="sort" value="price_desc" <?= $sort === 'price_desc' ? 'checked' : '' ?> onchange="applyFilter()"> Price: High to Low</label>
                <label class="filter-option"><input type="radio" name="sort" value="rating" <?= $sort === 'rating' ? 'checked' : '' ?> onchange="applyFilter()"> Top Rated</label>
            </div>
        </aside>

        <div class="shop-content">
            <div class="shop-top-bar">
                <span class="shop-result-count"><?= $totalProducts ?> products found</span>
                <?php if ($q): ?>
                <a href="/velvet vogue/shop.php" style="font-size:12px;color:var(--gold-dark)">Clear search</a>
                <?php endif; ?>
            </div>

            <?php if (empty($products)): ?>
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h3>No products found</h3>
                <p>Try adjusting your filters or search term.</p>
                <a href="/velvet vogue/shop.php" class="btn-primary">View All Products</a>
            </div>
            <?php else: ?>
            <div class="products-grid" style="grid-template-columns:repeat(3,1fr)">
                <?php foreach ($products as $p): ?>
                <?php include 'includes/product-card.php'; ?>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="pagination" style="margin-top:40px">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?cat=<?= urlencode($category) ?>&sort=<?= urlencode($sort) ?>&q=<?= urlencode($q) ?>&color=<?= urlencode($color) ?>&page=<?= $i ?>"
                   class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function applyFilter() {
    const cat  = document.querySelector('input[name="cat"]:checked')?.value || '';
    const sort = document.querySelector('input[name="sort"]:checked')?.value || 'featured';
    const q    = '<?= addslashes($q) ?>';
    const color = '<?= addslashes($color) ?>';
    let url = '/velvet vogue/shop.php?';
    if (cat)   url += `cat=${cat}&`;
    if (sort)  url += `sort=${sort}&`;
    if (q)     url += `q=${encodeURIComponent(q)}&`;
    if (color) url += `color=${encodeURIComponent(color)}&`;
    window.location.href = url;
}
function applyColorFilter(c) {
    const cat  = document.querySelector('input[name="cat"]:checked')?.value || '';
    const sort = document.querySelector('input[name="sort"]:checked')?.value || 'featured';
    const q    = '<?= addslashes($q) ?>';
    let url = '/velvet vogue/shop.php?';
    if (cat)  url += `cat=${cat}&`;
    if (sort) url += `sort=${sort}&`;
    if (q)    url += `q=${encodeURIComponent(q)}&`;
    if (c)    url += `color=${encodeURIComponent(c)}&`;
    window.location.href = url;
}
</script>

<?php require_once 'includes/footer.php'; ?>
