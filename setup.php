<?php
// Run this file once to initialize the database
try {
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS velvet_vogue CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE velvet_vogue");

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) UNIQUE NOT NULL,
        phone VARCHAR(20),
        password VARCHAR(255) NOT NULL,
        role ENUM('admin','user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        category ENUM('men','women','kids') NOT NULL,
        subcategory VARCHAR(50),
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        sale_price DECIMAL(10,2),
        image VARCHAR(255) NOT NULL,
        stock INT DEFAULT 50,
        rating DECIMAL(2,1) DEFAULT 4.0,
        featured TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        order_number VARCHAR(20) UNIQUE NOT NULL,
        subtotal DECIMAL(10,2) NOT NULL,
        shipping DECIMAL(10,2) DEFAULT 0,
        total DECIMAL(10,2) NOT NULL,
        status ENUM('pending','processing','shipped','delivered','cancelled','paid') DEFAULT 'pending',
        shipping_name VARCHAR(100),
        shipping_address TEXT,
        shipping_city VARCHAR(100),
        shipping_phone VARCHAR(20),
        payment_method VARCHAR(50) DEFAULT 'cod',
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        size VARCHAR(10),
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    )");

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT DEFAULT 1,
        size VARCHAR(10) DEFAULT 'M',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (product_id) REFERENCES products(id),
        UNIQUE KEY unique_cart (user_id, product_id, size)
    )");

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS wishlist (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (product_id) REFERENCES products(id),
        UNIQUE KEY unique_wishlist (user_id, product_id)
    )");

    // Insert admin user
    $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("INSERT IGNORE INTO users (name, email, password, role) VALUES ('Admin', 'admin@velvetvogue.com', '$adminPass', 'admin')");

    // Insert products
    $products = [
        // MEN - Hoodies
        ['Classic Comfort Hoodie', 'men', 'hoodie', 'Premium cotton-blend hoodie with a relaxed fit, perfect for casual days.', 8500, 6500, 'Men/hoodie1.jpeg', 4.8, 1],
        ['Signature Zip Hoodie', 'men', 'hoodie', 'Full-zip hoodie in soft fleece with kangaroo pocket.', 9200, 7200, 'Men/hoodie2.jpeg', 4.6, 0],
        ['Essential Pullover Hoodie', 'men', 'hoodie', 'Everyday hoodie crafted from premium fleece fabric.', 7800, null, 'Men/hoodie3.jpeg', 4.5, 0],
        ['Luxe Oversized Hoodie', 'men', 'hoodie', 'Oversized silhouette for ultimate comfort and modern style.', 10500, 8500, 'Men/hoodie4.jpeg', 4.7, 1],
        ['Athletic Performance Hoodie', 'men', 'hoodie', 'Lightweight moisture-wicking hoodie for active lifestyle.', 6500, null, 'Men/hoodie5.jpeg', 4.4, 0],
        // MEN - Jeans
        ['Slim Fit Dark Denim', 'men', 'jeans', 'Tailored slim fit jeans in premium dark wash denim.', 7000, 5500, 'Men/menjean1.jpeg', 4.7, 1],
        ['Classic Straight Cut Jeans', 'men', 'jeans', 'Timeless straight cut in medium blue wash.', 6500, null, 'Men/menjean2.jpeg', 4.5, 0],
        ['Relaxed Fit Cargo Jeans', 'men', 'jeans', 'Comfortable relaxed fit with functional cargo pockets.', 7500, 6000, 'Men/menjean3.jpeg', 4.3, 0],
        ['Skinny Stretch Jeans', 'men', 'jeans', 'Stretch denim for maximum comfort and sleek silhouette.', 5500, null, 'Men/menjean4.jpeg', 4.6, 0],
        ['Washed Casual Jeans', 'men', 'jeans', 'Vintage washed finish for a lived-in, effortless look.', 6000, 4800, 'Men/menjean5.jpeg', 4.4, 0],
        ['Premium Black Denim', 'men', 'jeans', 'Sharp black denim that transitions from day to night.', 8000, null, 'Men/menjean6.jpeg', 4.8, 1],
        ['Distressed Fashion Jeans', 'men', 'jeans', 'Trendy distressed look with comfortable stretch fabric.', 7200, 5800, 'Men/menjean7.jpeg', 4.3, 0],
        ['Smart Casual Cargo Denim', 'men', 'jeans', 'Versatile cargo denim perfect for any casual occasion.', 6800, null, 'Men/menjean8.jpeg', 4.5, 0],
        // MEN - Shirts
        ['Oxford Button-Down Shirt', 'men', 'shirt', 'Classic oxford weave shirt with a polished look.', 5000, 3800, 'Men/shirt1.jpeg', 4.7, 1],
        ['Linen Summer Shirt', 'men', 'shirt', 'Lightweight linen shirt ideal for warm weather dressing.', 4500, null, 'Men/shirt2 (2).jpeg', 4.5, 0],
        ['Formal White Dress Shirt', 'men', 'shirt', 'Crisp white dress shirt with mother-of-pearl buttons.', 5500, 4200, 'Men/shirt3.jpeg', 4.8, 0],
        ['Printed Casual Shirt', 'men', 'shirt', 'Contemporary print in a relaxed fit for weekend style.', 3800, null, 'Men/shirt4.jpeg', 4.4, 0],
        ['Striped Heritage Shirt', 'men', 'shirt', 'Classic stripe pattern in premium cotton poplin.', 4200, 3200, 'Men/shirt5.jpeg', 4.6, 1],
        // MEN - T-Shirts
        ['Pima Cotton Crew Tee', 'men', 'tshirt', 'Ultra-soft pima cotton tee with a perfect everyday fit.', 2500, null, 'Men/tshirt1.jpeg', 4.6, 0],
        ['Graphic Print T-Shirt', 'men', 'tshirt', 'Statement graphic print on premium cotton fabric.', 3000, 2200, 'Men/tshirt2.jpeg', 4.4, 0],
        ['V-Neck Essential Tee', 'men', 'tshirt', 'Classic V-neck tee in breathable cotton blend.', 2200, null, 'Men/tshirt3.jpeg', 4.5, 0],
        ['Longline Street Tee', 'men', 'tshirt', 'Extended length tee with a modern street-style aesthetic.', 3500, 2800, 'Men/tshirt4.jpeg', 4.3, 0],
        ['Premium Polo T-Shirt', 'men', 'tshirt', 'Classic polo collar in fine pique cotton.', 4000, null, 'Men/tshirt5.jpeg', 4.7, 1],
        // WOMEN - Salwares
        ['Embroidered Salwar Suit', 'women', 'salwar', 'Elegant embroidered salwar in soft cotton with matching dupatta.', 6500, 5000, 'Women/salware1.jpeg', 4.8, 1],
        ['Contemporary Palazzo Set', 'women', 'salwar', 'Modern palazzo pants with a flowy kurta in block print.', 5500, null, 'Women/salware2.jpeg', 4.6, 0],
        ['Festive Anarkali Suit', 'women', 'salwar', 'Flared anarkali with intricate thread work, perfect for occasions.', 8500, 7000, 'Women/salware3.jpeg', 4.7, 0],
        ['Casual Churidar Set', 'women', 'salwar', 'Comfortable daily-wear churidar in vibrant cotton print.', 4500, null, 'Women/salware4.jpeg', 4.4, 0],
        // WOMEN - Sarees
        ['Silk Kanjivaram Saree', 'women', 'saree', 'Authentic Kanjivaram silk saree with gold zari border.', 15000, 12000, 'Women/saree1.jpeg', 4.9, 1],
        ['Chiffon Georgette Saree', 'women', 'saree', 'Lightweight chiffon saree with floral embroidery.', 7500, null, 'Women/saree2.jpeg', 4.7, 0],
        ['Printed Cotton Saree', 'women', 'saree', 'Vibrant hand-block printed cotton saree for everyday elegance.', 4500, 3500, 'Women/saree3.jpeg', 4.5, 0],
        ['Designer Party Saree', 'women', 'saree', 'Contemporary designer saree with sequin detailing.', 12000, 9500, 'Women/saree4.jpeg', 4.8, 1],
        ['Linen Handloom Saree', 'women', 'saree', 'Breezy linen saree perfect for warm-weather occasions.', 5500, null, 'Women/saree5.jpeg', 4.6, 0],
        // WOMEN - Skirts
        ['Floral Midi Skirt', 'women', 'skirt', 'Flowy floral print midi skirt in lightweight fabric.', 3500, 2800, 'Women/skirt1.jpeg', 4.6, 0],
        ['Pleated A-Line Skirt', 'women', 'skirt', 'Classic pleated A-line skirt in solid pastel shades.', 4000, null, 'Women/skirt2.jpeg', 4.5, 0],
        ['Denim Wrap Skirt', 'women', 'skirt', 'Casual denim wrap skirt with adjustable tie waist.', 3800, 3000, 'Women/skirt3.jpeg', 4.4, 0],
        ['Maxi Boho Skirt', 'women', 'skirt', 'Bohemian-style maxi skirt in breathable cotton voile.', 4500, null, 'Women/skirt4.jpeg', 4.7, 1],
        ['Satin Mini Skirt', 'women', 'skirt', 'Elegant satin finish mini skirt for evening occasions.', 5000, 3800, 'Women/skirt5.jpeg', 4.5, 0],
        ['Printed Tiered Skirt', 'women', 'skirt', 'Trendy tiered skirt with bold ethnic print.', 4200, null, 'Women/skirt6.jpeg', 4.6, 0],
        // WOMEN - Official
        ['Power Blazer Set', 'women', 'official', 'Sharp tailored blazer and trouser set for the modern professional.', 9000, 7500, 'Women/womenofficail.jpeg', 4.8, 1],
        ['Classic Office Dress', 'women', 'official', 'Elegant sheath dress perfect for boardroom to dinner.', 7500, null, 'Women/womenofficail2.jpeg', 4.7, 0],
        ['Smart Formal Blazer', 'women', 'official', 'Structured formal blazer in premium wool blend.', 8500, 6800, 'Women/womenofficail3.jpeg', 4.6, 0],
        // KIDS - Various
        ['Girls Flared Skirt', 'kids', 'skirt', 'Adorable flared skirt in bright colors for little fashionistas.', 1800, 1400, 'Kids/flaredskirt1.jpeg', 4.7, 0],
        ['Princess Frock Dress', 'kids', 'frock', 'Beautiful frock dress with lace detailing for special occasions.', 2500, null, 'Kids/frockdress.1.jpeg', 4.8, 1],
        ['Floral Frock Dress', 'kids', 'frock', 'Sweet floral print frock with a comfortable fit.', 2200, 1800, 'Kids/frockdress.2.jpeg', 4.6, 0],
        ['Kids Casual Jeans', 'kids', 'jeans', 'Durable and comfortable jeans for active kids.', 2000, null, 'Kids/kidsjeans1.jpeg', 4.5, 0],
        ['Jeans & Shirt Combo', 'kids', 'set', 'Smart jeans and shirt set perfect for school or outings.', 3000, 2400, 'Kids/kidsjeansshirt.jpeg', 4.7, 1],
        ['Denim Shirt Set', 'kids', 'set', 'Matching denim shirt and pants for a coordinated look.', 2800, null, 'Kids/kidsjeansshirt2.jpeg', 4.5, 0],
        ['Jeans & T-Shirt Set', 'kids', 'set', 'Casual jeans paired with a fun graphic tee.', 2500, 2000, 'Kids/kidsjeanstshirt1.jpeg', 4.6, 0],
        ['Embroidered Salwar', 'kids', 'salwar', 'Traditional embroidered salwar set for festive occasions.', 3500, null, 'Kids/kidssalware1.jpeg', 4.8, 0],
        ['Cotton Play Shorts', 'kids', 'shorts', 'Comfortable cotton shorts for active playtime.', 1200, 900, 'Kids/kidsshorts1.jpeg', 4.4, 0],
        ['Elastic Waist Shorts', 'kids', 'shorts', 'Easy pull-on shorts in soft jersey fabric.', 1000, null, 'Kids/kidsshorts2.jpeg', 4.5, 0],
        ['Printed Fun Shorts', 'kids', 'shorts', 'Bright printed shorts that kids love to wear.', 1100, 850, 'Kids/kidsshorts3.jpeg', 4.3, 0],
        ['Sport Shorts', 'kids', 'shorts', 'Quick-dry sport shorts for outdoor activities.', 1300, null, 'Kids/kidsshorts4.jpeg', 4.6, 0],
        ['Casual Bermuda Shorts', 'kids', 'shorts', 'Mid-length bermuda shorts in comfortable cotton.', 1400, 1100, 'Kids/kidsshorts5.jpeg', 4.5, 0],
        ['Layered Kids Skirt', 'kids', 'skirt', 'Cute layered skirt in colorful fabric for girls.', 1600, null, 'Kids/kidsskirt1.jpeg', 4.7, 0],
        ['Tutu Skirt', 'kids', 'skirt', 'Playful tutu skirt perfect for parties and dress-up.', 1800, 1400, 'Kids/kidsskirt2.jpeg', 4.8, 1],
        ['Floral Print Skirt', 'kids', 'skirt', 'Soft floral print skirt in lightweight cotton.', 1500, null, 'Kids/kidsskirt3.jpeg', 4.5, 0],
        ['Traditional Vesti', 'kids', 'traditional', 'Classic traditional vesti for cultural occasions.', 2000, 1600, 'Kids/kidsvesti.jpeg', 4.6, 0],
        ['Premium Vesti Set', 'kids', 'traditional', 'Premium quality vesti with matching shirt for celebrations.', 2800, null, 'Kids/kidsvesti1.jpeg', 4.7, 0],
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO products (name, category, subcategory, description, price, sale_price, image, rating, featured) VALUES (?,?,?,?,?,?,?,?,?)");
    foreach ($products as $p) {
        $stmt->execute($p);
    }

    echo "<h2 style='font-family:sans-serif;color:green;padding:20px'>✓ VelvetVogue Database Setup Complete!</h2>";
    echo "<p style='font-family:sans-serif;padding:0 20px'>Database, tables, and products created successfully.</p>";
    echo "<p style='font-family:sans-serif;padding:0 20px'><strong>Admin Login:</strong> admin@velvetvogue.com / admin123</p>";
    echo "<p style='font-family:sans-serif;padding:0 20px'><a href='index.php'>Go to Homepage →</a> | <a href='admin/'>Admin Dashboard →</a></p>";

} catch (PDOException $e) {
    echo "<h2 style='color:red;font-family:sans-serif;padding:20px'>Setup Error</h2>";
    echo "<p style='font-family:sans-serif;padding:0 20px'>" . htmlspecialchars($e->getMessage()) . "</p>";
}
