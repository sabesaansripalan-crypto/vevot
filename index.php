<?php
$pageTitle = 'VelvetVogue — Dress with Purpose. Live with Style.';
require_once 'includes/header.php';

/*
 * HOMEPAGE IMAGES — drop your photos into:
 *   assets/images/homepage/
 *
 * Expected filenames:
 *   hero-1.jpg  hero-2.jpg  hero-3.jpg  hero-4.jpg   (hero grid, top-left → top-right → bottom-left → bottom-right)
 *   about-main.jpg   about-top.jpg   about-bottom.jpg (about section)
 *   col-men.jpg   col-women.jpg   col-kids.jpg        (Shop by Collection)
 *
 * Until you upload, the site uses product images as fallbacks.
 */
if (!function_exists('hp')) {
    function hp($file, $fallback) {
        $path = __DIR__ . '/assets/images/homepage/' . $file;
        if (file_exists($path)) {
            return '/velvet vogue/assets/images/homepage/' . rawurlencode($file);
        }
        $parts = explode('/', $fallback);
        return '/velvet vogue/' . implode('/', array_map('rawurlencode', $parts));
    }
}

$heroImages = [
    hp('hero-1.jpg', 'Men/1.jpeg'),
    hp('hero-2.jpg', 'Men/2.jpeg'),
    hp('hero-3.jpg', 'Men/3.jpeg'),
    hp('hero-4.jpg', 'Men/4.jpeg'),
];

$aboutMain   = hp('about-main.jpg',   'Men/menjean1.jpeg');
$aboutTop    = hp('about-top.jpg',    'Men/shirt2 (2).jpeg');
$aboutBottom = hp('about-bottom.jpg', 'Women/womenofficail.jpeg');

$colMen   = hp('col-men.jpg',   'newcollections/mencort2.jpeg');
$colWomen = hp('col-women.jpg', 'newcollections/cortfull.jpeg');
$colKids  = hp('col-kids.jpg',  'newcollections/frockdress9.jpeg');

$db = getDB();
$totalProductCount = $db->query("SELECT COUNT(DISTINCT name) FROM products")->fetchColumn();
$featuredProducts  = $db->query("SELECT * FROM products WHERE featured = 1 AND id = (SELECT MIN(p2.id) FROM products p2 WHERE p2.name = products.name) ORDER BY rating DESC LIMIT 4")->fetchAll();
?>

<!-- ============================================================
     HERO
     ============================================================ -->
<section class="hero">
    <div class="hero-content">
        <span class="hero-label">New Collection 2026</span>
        <h1 class="hero-title">Dress with Purpose.<br>Live with Style.</h1>
        <p class="hero-desc">Discover a curated wardrobe of timeless silhouettes and contemporary edge. Each piece is crafted with care — designed to move with you, made to last.</p>
        <a href="/velvet vogue/shop.php" class="btn-primary">Shop Now</a>
    </div>

    <div class="hero-images">
        <?php
        $heroCards = [
            ['img' => $heroImages[0], 'label' => 'Denim Jeans'],
            ['img' => $heroImages[1], 'label' => "Casual Shirt"],
            ['img' => $heroImages[2], 'label' => "Graphic Tee"],
            ['img' => $heroImages[3], 'label' => "Corduroy Jacket"],
        ];
        foreach ($heroCards as $card): ?>
        <div class="hero-img-wrap">
            <img src="<?= $card['img'] ?>" alt="<?= htmlspecialchars($card['label']) ?>">
            <div class="hero-img-label"><?= htmlspecialchars($card['label']) ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ============================================================
     STATS BAR
     ============================================================ -->
<section class="stats-bar">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">8<span class="stat-suffix">+</span></div>
                <div class="stat-label">Years of Excellence</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">50K<span class="stat-suffix">+</span></div>
                <div class="stat-label">Happy Clients</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">98<span class="stat-suffix">%</span></div>
                <div class="stat-label">Customer Satisfaction</div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     FEATURES TICKER — running marquee
     ============================================================ -->
<div class="features-ticker">
    <div class="ticker-track">
        <!-- Items duplicated so the loop is seamless -->
        <div class="ticker-items">
            <div class="ticker-item">Easy Returns</div>
            <div class="ticker-item">New Arrivals Every Week</div>
            <div class="ticker-item">Exclusive Members Deals</div>
            <div class="ticker-item">Free Shipping Over Rs. 5,000</div>
            <!-- duplicate set -->
            <div class="ticker-item">Easy Returns</div>
            <div class="ticker-item">New Arrivals Every Week</div>
            <div class="ticker-item">Exclusive Members Deals</div>
            <div class="ticker-item">Free Shipping Over Rs. 5,000</div>
        </div>
    </div>
</div>

<!-- ============================================================
     ABOUT
     ============================================================ -->
<section class="about-section">
    <div class="container">
        <div class="about-grid">

            <!-- LEFT — TEXT -->
            <div class="about-content">
                <span class="about-label">Our Story</span>
                <h2 class="about-title">About VelvetVogue</h2>
                <p class="about-text">VelvetVogue was born from a simple belief: Fashion should feel personal, intentional, and joyful. Since our founding, we have curated collections that celebrate individuality — blending refined tailoring with modern silhouettes that speak to every chapter of your life.</p>
                <p class="about-text">We work with skilled artisans and sustainable mills to bring you pieces worth keeping. From everyday essentials to occasion-ready looks, every stitch carries our commitment to quality, comfort, and conscious style.</p>

                <div class="about-quote">
                    <p>"Style is a way to say who you are without having to speak."</p>
                    <cite>— Rachel Zoe</cite>
                </div>

                <div class="about-stats">
                    <div class="about-stat">
                        <div class="about-stat-num"><?= $totalProductCount ?>+</div>
                        <div class="about-stat-label">Products</div>
                    </div>
                    <div class="about-stat">
                        <div class="about-stat-num">3</div>
                        <div class="about-stat-label">Collections</div>
                    </div>
                    <div class="about-stat">
                        <div class="about-stat-num">25+</div>
                        <div class="about-stat-label">Cities Served</div>
                    </div>
                    <div class="about-stat">
                        <div class="about-stat-num">4.8</div>
                        <div class="about-stat-label">Avg. Rating</div>
                    </div>
                </div>
            </div>

            <!-- RIGHT — IMAGES -->
            <div class="about-img-grid">
                <div class="about-img-main">
                    <img src="<?= $aboutMain ?>" alt="Our Craftsmanship">
                    <span class="about-img-badge">Our Craftsmanship</span>
                </div>
                <div class="about-img-top">
                    <img src="<?= $aboutTop ?>" alt="Our Collection">
                </div>
                <div class="about-img-bottom">
                    <img src="<?= $aboutBottom ?>" alt="New Season">
                    <span class="about-img-badge">New Season</span>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ============================================================
     WHY CHOOSE US
     ============================================================ -->
<section class="features-section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Why Choose Us</span>
            <h2 class="section-title">Everything You Deserve</h2>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-shipping-fast"></i></div>
                <h3 class="feature-title">Free Shipping</h3>
                <p class="feature-desc">Enjoy free shipping on all orders above Rs. 5,000. Fast dispatch and reliable tracking, every step of the way.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-undo-alt"></i></div>
                <h3 class="feature-title">Easy Returns</h3>
                <p class="feature-desc">Shop with confidence using our hassle-free returns within 30 days — no questions asked.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-gem"></i></div>
                <h3 class="feature-title">Premium Quality</h3>
                <p class="feature-desc">Enjoy premium materials and rigorously quality-checked fabrics before they reach you.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-lock"></i></div>
                <h3 class="feature-title">Secure Payment</h3>
                <p class="feature-desc">Shop with confidence using industry-standard encryption. Your data is always safe with us.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-headset"></i></div>
                <h3 class="feature-title">24/7 Support</h3>
                <p class="feature-desc">Our dedicated support team is available around the clock to assist and resolve any issue.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-star"></i></div>
                <h3 class="feature-title">Exclusive Deals</h3>
                <p class="feature-desc">Members get first access to sales, exclusive drops and loyalty rewards designed to celebrate you.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     SHOP BY COLLECTION
     ============================================================ -->
<section class="collection-section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Explore</span>
            <h2 class="section-title" style="color:#fff">Shop by Collection</h2>
            <p class="section-desc">Curated styles for every member of the family.</p>
        </div>
        <div class="collections-grid">
            <div class="collection-card">
                <img src="<?= $colMen ?>" alt="Men's Collection">
                <div class="collection-overlay"></div>
                <div class="collection-info">
                    <p class="collection-season">New Season</p>
                    <h3 class="collection-name">Men</h3>
                    <p class="collection-desc">Sharp fits, bold styles — from casual tees to tailored formals built for every occasion.</p>
                    <a href="/velvet vogue/men.php" class="btn-collection">Shop Now <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="collection-card">
                <img src="<?= $colWomen ?>" alt="Women's Collection">
                <div class="collection-overlay"></div>
                <div class="collection-info">
                    <p class="collection-season">New Season</p>
                    <h3 class="collection-name">Women</h3>
                    <p class="collection-desc">Elegant, effortless, and empowering — discover styles that move with your story.</p>
                    <a href="/velvet vogue/women.php" class="btn-collection">Shop Now <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="collection-card">
                <img src="<?= $colKids ?>" alt="Kids' Collection">
                <div class="collection-overlay"></div>
                <div class="collection-info">
                    <p class="collection-season">New Season</p>
                    <h3 class="collection-name">Kids</h3>
                    <p class="collection-desc">Playful, comfy, and colorful — little styles made for big adventures.</p>
                    <a href="/velvet vogue/kids.php" class="btn-collection">Shop Now <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     FEATURED PRODUCTS
     ============================================================ -->
<?php if (!empty($featuredProducts)): ?>
<section style="padding:80px 0;background:var(--white)">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Hand Picked</span>
            <h2 class="section-title">Featured Products</h2>
            <p class="section-desc">Our most loved styles — curated just for you.</p>
        </div>
        <div class="products-grid" style="margin-top:40px">
            <?php foreach ($featuredProducts as $p): ?>
            <?php include 'includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center;margin-top:40px">
            <a href="/velvet vogue/shop.php" class="btn-outline">View All Products</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================
     SHOP IN 4 SIMPLE STEPS
     ============================================================ -->
<section class="steps-section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">How It Works</span>
            <h2 class="section-title">Shop in 4 Simple Steps</h2>
        </div>
        <div class="steps-grid">
            <div class="steps-connector"><div class="steps-connector-fill"></div></div>
            <div class="step-card">
                <div class="step-icon"><i class="fas fa-search"></i></div>
                <h3 class="step-title">Browse</h3>
                <p class="step-desc">Explore our curated collections. Filter by category, style, or occasion with a single tap.</p>
            </div>
            <div class="step-card">
                <div class="step-icon"><i class="fas fa-shopping-cart"></i></div>
                <h3 class="step-title">Add to Cart</h3>
                <p class="step-desc">Found something you love? Add your favorites to the cart with a single tap.</p>
            </div>
            <div class="step-card">
                <div class="step-icon"><i class="fas fa-credit-card"></i></div>
                <h3 class="step-title">Checkout</h3>
                <p class="step-desc">Seamless, secure checkout. Multiple payment options, fast and easy.</p>
            </div>
            <div class="step-card">
                <div class="step-icon"><i class="fas fa-box-open"></i></div>
                <h3 class="step-title">Delivered</h3>
                <p class="step-desc">Your order is carefully packed and delivered straight to your door.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     TESTIMONIALS
     ============================================================ -->
<section class="testimonials-section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Customer Love</span>
            <h2 class="section-title">What Our Clients Say</h2>
        </div>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="testimonial-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    <i class="fas fa-star"></i><i class="fas fa-star"></i>
                    <span class="rating-num">5.0</span>
                </div>
                <p class="testimonial-text">"Absolutely in love with my new wardrobe from VelvetVogue! The fabric quality is incredible and the life is perfect. Top quality and incredible, I've been very satisfied and love the fit."</p>
                <div class="testimonial-author">
                    <div class="author-avatar">P</div>
                    <div>
                        <div class="author-name">Priya Kandaasamy</div>
                        <div class="author-badge"><i class="fas fa-circle-check" style="color:#3aaa5c;font-size:10px;margin-right:4px"></i>Verified Customer</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    <i class="fas fa-star"></i><i class="fas fa-star"></i>
                    <span class="rating-num">5.0</span>
                </div>
                <p class="testimonial-text">"I had absolutely beautiful packaging and the team was amazing and the customer support was very responsive when I had a query about my order. Couldn't be happier with my purchase!"</p>
                <div class="testimonial-author">
                    <div class="author-avatar">R</div>
                    <div>
                        <div class="author-name">Rohan Sivakumar</div>
                        <div class="author-badge"><i class="fas fa-circle-check" style="color:#3aaa5c;font-size:10px;margin-right:4px"></i>Verified Customer</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    <i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                    <span class="rating-num">4.5</span>
                </div>
                <p class="testimonial-text">"Great range of styles and very reasonable prices! Delivery was super fast and I was impressed when I received help with fitting. I've been recommending VelvetVogue to all my friends every month!"</p>
                <div class="testimonial-author">
                    <div class="author-avatar">A</div>
                    <div>
                        <div class="author-name">Ananya Nithyanandam</div>
                        <div class="author-badge"><i class="fas fa-circle-check" style="color:#3aaa5c;font-size:10px;margin-right:4px"></i>Verified Customer</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     NEWSLETTER
     ============================================================ -->
<section class="newsletter-section">
    <div class="container">
        <span class="newsletter-label">Stay Updated</span>
        <h2 class="newsletter-title">Stay in Style</h2>
        <p class="newsletter-desc">Subscribe to our newsletter and be the first to know about new arrivals, exclusive offers, and style inspiration delivered to your inbox.</p>
        <form class="newsletter-form" onsubmit="handleNewsletter(event)">
            <input type="email" placeholder="Enter your email address..." required>
            <button type="submit">Subscribe</button>
        </form>
    </div>
</section>

<script>
function handleNewsletter(e) {
    e.preventDefault();
    const input = e.target.querySelector('input');
    if (input.value) {
        showToast('Thanks for subscribing! Stay stylish. ✨', 'success');
        input.value = '';
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
