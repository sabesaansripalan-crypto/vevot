<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/functions.php';
$db = getDB();

$db->exec("CREATE TABLE IF NOT EXISTS inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20),
    type VARCHAR(50) DEFAULT 'general',
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new','read') DEFAULT 'new',
    user_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $type    = trim($_POST['type'] ?? 'general');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$subject || !$message) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $uid = isLoggedIn() ? $_SESSION['user_id'] : null;
        $stmt = $db->prepare("INSERT INTO inquiries (name, email, phone, type, subject, message, user_id) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$name, $email, $phone, $type, $subject, $message, $uid]);
        $success = true;
    }
}

$pageTitle = 'Contact Us — VelvetVogue';
require_once 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <div class="breadcrumb" style="margin-bottom:8px">
            <a href="/velvet vogue/index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>Contact Us</span>
        </div>
        <h1>Contact Us</h1>
    </div>
</div>

<section style="padding:60px 0 80px;background:var(--white)">
    <div class="container">
        <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:60px;align-items:start">

            <div>
                <span style="font-size:10px;font-weight:600;letter-spacing:3px;text-transform:uppercase;color:var(--gold-dark)">Get In Touch</span>
                <h2 style="font-family:'Cormorant Garamond',serif;font-size:36px;font-weight:600;margin:12px 0 20px;line-height:1.2">We'd Love to<br>Hear From You</h2>
                <p style="font-size:14px;color:var(--text);line-height:1.8;margin-bottom:36px">Have a question about an order, a product, or just want to say hello? Our team is here to help. Fill out the form and we'll get back to you within 24 hours.</p>

                <div style="display:flex;flex-direction:column;gap:24px;margin-bottom:40px">
                    <div style="display:flex;align-items:flex-start;gap:16px">
                        <div style="width:44px;height:44px;background:var(--cream);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-envelope" style="color:var(--gold-dark);font-size:16px"></i>
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:14px;margin-bottom:4px">Email Us</div>
                            <div style="font-size:13px;color:var(--text-light)">support@velvetvogue.com</div>
                            <div style="font-size:12px;color:var(--text-light);margin-top:2px">We reply within 24 hours</div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:flex-start;gap:16px">
                        <div style="width:44px;height:44px;background:var(--cream);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-phone" style="color:var(--gold-dark);font-size:16px"></i>
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:14px;margin-bottom:4px">Call Us</div>
                            <div style="font-size:13px;color:var(--text-light)">+94 77 000 0000</div>
                            <div style="font-size:12px;color:var(--text-light);margin-top:2px">Mon – Sat, 9am – 6pm</div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:flex-start;gap:16px">
                        <div style="width:44px;height:44px;background:var(--cream);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-map-marker-alt" style="color:var(--gold-dark);font-size:16px"></i>
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:14px;margin-bottom:4px">Visit Us</div>
                            <div style="font-size:13px;color:var(--text-light)">Colombo, Sri Lanka</div>
                            <div style="font-size:12px;color:var(--text-light);margin-top:2px">By appointment only</div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:flex-start;gap:16px">
                        <div style="width:44px;height:44px;background:var(--cream);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-clock" style="color:var(--gold-dark);font-size:16px"></i>
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:14px;margin-bottom:4px">Business Hours</div>
                            <div style="font-size:13px;color:var(--text-light)">Monday – Saturday</div>
                            <div style="font-size:12px;color:var(--text-light);margin-top:2px">9:00 AM – 6:00 PM</div>
                        </div>
                    </div>
                </div>

                <div style="background:var(--cream);border-radius:12px;padding:24px">
                    <div style="font-weight:600;font-size:13px;margin-bottom:16px;color:var(--dark)">Common Questions</div>
                    <div style="display:flex;flex-direction:column;gap:10px">
                        <?php foreach ([
                            ['fas fa-undo-alt',      'How do I return an item?'],
                            ['fas fa-shipping-fast', 'When will my order arrive?'],
                            ['fas fa-ruler',         'How do I find my size?'],
                            ['fas fa-tag',           'Do you offer discounts?'],
                        ] as $faq): ?>
                        <div style="display:flex;align-items:center;gap:10px;font-size:13px;color:var(--text)">
                            <i class="<?= $faq[0] ?>" style="color:var(--gold-dark);font-size:12px;width:14px;text-align:center"></i>
                            <?= $faq[1] ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div style="background:var(--white);border:1.5px solid var(--border);border-radius:16px;padding:40px">
                <?php if ($success): ?>
                <div style="text-align:center;padding:40px 20px">
                    <div style="width:72px;height:72px;background:#f0faf0;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
                        <i class="fas fa-check" style="font-size:28px;color:#3aaa5c"></i>
                    </div>
                    <h3 style="font-family:'Cormorant Garamond',serif;font-size:26px;margin-bottom:12px">Message Sent!</h3>
                    <p style="font-size:14px;color:var(--text-light);line-height:1.7;margin-bottom:28px">Thank you for reaching out. Our team will review your inquiry and get back to you within 24 hours.</p>
                    <a href="/velvet vogue/inquiry.php" class="btn-primary">Send Another</a>
                </div>
                <?php else: ?>
                <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;margin-bottom:6px">Send Us a Message</h3>
                <p style="font-size:13px;color:var(--text-light);margin-bottom:28px">Fields marked with * are required.</p>

                <?php if ($error): ?>
                <div class="alert alert-error" style="margin-bottom:20px"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" style="display:flex;flex-direction:column;gap:18px">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;letter-spacing:1.2px;text-transform:uppercase;margin-bottom:8px">Full Name *</label>
                            <input type="text" name="name" required placeholder="Your full name"
                                value="<?= htmlspecialchars(isLoggedIn() ? $_SESSION['user_name'] : ($_POST['name'] ?? '')) ?>"
                                style="width:100%;padding:12px 16px;border:1.5px solid var(--border);border-radius:8px;font-family:'Jost',sans-serif;font-size:13px;background:var(--cream);outline:none;transition:border-color .2s"
                                onfocus="this.style.borderColor='var(--gold)';this.style.background='#fff'"
                                onblur="this.style.borderColor='var(--border)';this.style.background='var(--cream)'">
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;letter-spacing:1.2px;text-transform:uppercase;margin-bottom:8px">Email Address *</label>
                            <input type="email" name="email" required placeholder="you@example.com"
                                value="<?= htmlspecialchars(isLoggedIn() ? $_SESSION['email'] : ($_POST['email'] ?? '')) ?>"
                                style="width:100%;padding:12px 16px;border:1.5px solid var(--border);border-radius:8px;font-family:'Jost',sans-serif;font-size:13px;background:var(--cream);outline:none;transition:border-color .2s"
                                onfocus="this.style.borderColor='var(--gold)';this.style.background='#fff'"
                                onblur="this.style.borderColor='var(--border)';this.style.background='var(--cream)'">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;letter-spacing:1.2px;text-transform:uppercase;margin-bottom:8px">Phone <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--text-light)">(optional)</span></label>
                            <input type="tel" name="phone" placeholder="+94 77 000 0000"
                                value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                                style="width:100%;padding:12px 16px;border:1.5px solid var(--border);border-radius:8px;font-family:'Jost',sans-serif;font-size:13px;background:var(--cream);outline:none;transition:border-color .2s"
                                onfocus="this.style.borderColor='var(--gold)';this.style.background='#fff'"
                                onblur="this.style.borderColor='var(--border)';this.style.background='var(--cream)'">
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;letter-spacing:1.2px;text-transform:uppercase;margin-bottom:8px">Inquiry Type *</label>
                            <select name="type" style="width:100%;padding:12px 16px;border:1.5px solid var(--border);border-radius:8px;font-family:'Jost',sans-serif;font-size:13px;background:var(--cream);outline:none;color:var(--dark);cursor:pointer">
                                <?php foreach ([
                                    'general'  => 'General Inquiry',
                                    'order'    => 'Order Issue',
                                    'product'  => 'Product Question',
                                    'return'   => 'Return / Exchange',
                                    'shipping' => 'Shipping & Delivery',
                                    'feedback' => 'Feedback',
                                    'other'    => 'Other',
                                ] as $val => $label): ?>
                                <option value="<?= $val ?>" <?= ($_POST['type'] ?? 'general') === $val ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;letter-spacing:1.2px;text-transform:uppercase;margin-bottom:8px">Subject *</label>
                        <input type="text" name="subject" required placeholder="Brief subject of your inquiry"
                            value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>"
                            style="width:100%;padding:12px 16px;border:1.5px solid var(--border);border-radius:8px;font-family:'Jost',sans-serif;font-size:13px;background:var(--cream);outline:none;transition:border-color .2s"
                            onfocus="this.style.borderColor='var(--gold)';this.style.background='#fff'"
                            onblur="this.style.borderColor='var(--border)';this.style.background='var(--cream)'">
                    </div>

                    <div>
                        <label style="display:block;font-size:11px;font-weight:600;letter-spacing:1.2px;text-transform:uppercase;margin-bottom:8px">Message *</label>
                        <textarea name="message" required rows="6" placeholder="Describe your inquiry in detail..."
                            style="width:100%;padding:12px 16px;border:1.5px solid var(--border);border-radius:8px;font-family:'Jost',sans-serif;font-size:13px;background:var(--cream);outline:none;resize:vertical;min-height:140px;transition:border-color .2s"
                            onfocus="this.style.borderColor='var(--gold)';this.style.background='#fff'"
                            onblur="this.style.borderColor='var(--border)';this.style.background='var(--cream)'"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn-primary" style="width:100%;justify-content:center;padding:16px;font-size:12px;border-radius:8px">
                        <i class="fas fa-paper-plane" style="margin-right:8px"></i> Send Message
                    </button>
                </form>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
