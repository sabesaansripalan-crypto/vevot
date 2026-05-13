<?php
require_once 'config/db.php';
$db = getDB();

$products = [
    ['Men\'s Formal Coat',  'men',   'coat',  'A sharp, tailored formal coat perfect for every occasion.',         4999, null, 'newcollections/mencort2.jpeg',    30, 4.5, 1],
    ['Women\'s Long Coat',  'women', 'coat',  'An elegant long coat — effortless style for every season.',         5499, null, 'newcollections/cortfull.jpeg',    25, 4.6, 1],
    ['Kids Frock Dress',    'kids',  'dress', 'A beautiful frock dress for little ones — colorful and comfortable.', 1999, null, 'newcollections/frockdress9.jpeg', 40, 4.7, 1],
];

$stmt = $db->prepare("INSERT INTO products (name, category, subcategory, description, price, sale_price, image, stock, rating, featured) VALUES (?,?,?,?,?,?,?,?,?,?)");

foreach ($products as $p) {
    $stmt->execute($p);
    echo "✅ Added: " . $p[0] . "<br>";
}

echo "<br><strong>Done! Delete this file now.</strong>";

// Auto delete this file after running
unlink(__FILE__);
?>
