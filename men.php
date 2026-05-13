<?php
$pageTitle = "Men's Collection — VelvetVogue";
require_once 'includes/header.php';
$db = getDB();
try { $db->exec("ALTER TABLE products ADD COLUMN color VARCHAR(30) DEFAULT NULL"); } catch(PDOException $e) {}

$subcategory = $_GET['sub']   ?? '';
$sort        = $_GET['sort']  ?? 'featured';
$color       = $_GET['color'] ?? '';

$orderBy = match($sort) {
    'price_asc'  => 'COALESCE(sale_price, price) ASC',
    'price_desc' => 'COALESCE(sale_price, price) DESC',
    'newest'     => 'created_at DESC',
    default      => 'featured DESC, rating DESC'
};

$where  = ["p.category = 'men'", "p.id = (SELECT MIN(p2.id) FROM products p2 WHERE p2.name = p.name AND p2.category = p.category)"];
$params = [];
if ($subcategory) { $where[] = "p.subcategory = ?"; $params[] = $subcategory; }
if ($color)       { $where[] = "p.color = ?";       $params[] = $color; }

$whereStr = implode(' AND ', $where);
$stmt = $db->prepare("SELECT p.* FROM products p WHERE $whereStr ORDER BY $orderBy");
$stmt->execute($params);
$products = $stmt->fetchAll();

$subs = array_filter($db->query("SELECT DISTINCT subcategory FROM products WHERE category='men' ORDER BY subcategory")->fetchAll(PDO::FETCH_COLUMN), fn($s) => strlen(trim($s)) > 1);

$colorMap = [
    'white' => '#F8F8F8', 'black' => '#1A1A1A', 'gray'   => '#9E9E9E',
    'beige' => '#D4C5A9', 'navy'  => '#1B2A4A', 'blue'   => '#2980B9',
    'red'   => '#C0392B', 'green' => '#27AE60', 'yellow' => '#F4D03F',
    'pink'  => '#E91E8C', 'purple'=> '#8E44AD', 'orange' => '#E67E22',
    'brown' => '#795548',
];
$availableColors = $db->query("SELECT DISTINCT color FROM products WHERE category='men' AND color IS NOT NULL AND color != '' ORDER BY color")->fetchAll(PDO::FETCH_COLUMN);
?>
<div class="page-header">
    <div class="container">
        <div class="breadcrumb" style="margin-bottom:8px">
            <a href="/velvet vogue/index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>Men's Collection</span>
        </div>
        <h1>Men's Collection</h1>
    </div>
</div>
<div class="container">
    <div class="shop-layout">
        <aside class="filters-sidebar">
            <h3>Filters</h3>
            <div class="filter-group">
                <div class="filter-group-title">Category</div>
                <label class="filter-option"><input type="radio" name="sub" value="" <?= !$subcategory ? 'checked' : '' ?> onchange="applyFilter()"> All</label>
                <?php foreach ($subs as $s): ?>
                <label class="filter-option"><input type="radio" name="sub" value="<?= $s ?>" <?= $subcategory === $s ? 'checked' : '' ?> onchange="applyFilter()"> <?= ucfirst($s) ?>s</label>
                <?php endforeach; ?>
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
                <label class="filter-option"><input type="radio" name="sort" value="price_asc" <?= $sort === 'price_asc' ? 'checked' : '' ?> onchange="applyFilter()"> Price: Low to High</label>
                <label class="filter-option"><input type="radio" name="sort" value="price_desc" <?= $sort === 'price_desc' ? 'checked' : '' ?> onchange="applyFilter()"> Price: High to Low</label>
                <label class="filter-option"><input type="radio" name="sort" value="newest" <?= $sort === 'newest' ? 'checked' : '' ?> onchange="applyFilter()"> Newest</label>
            </div>
        </aside>
        <div class="shop-content">
            <div class="shop-top-bar">
                <span class="shop-result-count"><?= count($products) ?> products</span>
            </div>
            <div class="products-grid" style="grid-template-columns:repeat(3,1fr)">
                <?php foreach ($products as $p): ?>
                <?php include 'includes/product-card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<script>
function applyFilter() {
    const sub  = document.querySelector('input[name="sub"]:checked')?.value || '';
    const sort = document.querySelector('input[name="sort"]:checked')?.value || 'featured';
    const color = '<?= addslashes($color) ?>';
    let url = '/velvet vogue/men.php?';
    if (sub)   url += `sub=${sub}&`;
    if (sort)  url += `sort=${sort}&`;
    if (color) url += `color=${encodeURIComponent(color)}&`;
    window.location.href = url;
}
function applyColorFilter(c) {
    const sub  = document.querySelector('input[name="sub"]:checked')?.value || '';
    const sort = document.querySelector('input[name="sort"]:checked')?.value || 'featured';
    let url = '/velvet vogue/men.php?';
    if (sub)  url += `sub=${sub}&`;
    if (sort) url += `sort=${sort}&`;
    if (c)    url += `color=${encodeURIComponent(c)}&`;
    window.location.href = url;
}
</script>
<?php require_once 'includes/footer.php'; ?>
