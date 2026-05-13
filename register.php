<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /velvet vogue/index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $db = getDB();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'An account with this email already exists.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'user')");
            $stmt->execute([$name, $email, $phone, $hashed]);
            header('Location: /velvet vogue/login.php?registered=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account — VelvetVogue</title>
<link rel="stylesheet" href="/velvet vogue/assets/css/auth.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="auth-left">
    <div class="auth-brand">
        <div class="auth-brand-icon">V</div>
        <span class="auth-brand-name">VelvetVogue</span>
    </div>
    <h1 class="auth-left-title">Join<br>VelvetVogue</h1>
    <p class="auth-left-desc">Create your account and discover curated fashion at your fingertips.</p>
    <div class="auth-features">
        <div class="auth-feature"><i class="fas fa-star"></i><span>Exclusive Deals</span></div>
        <div class="auth-feature"><i class="fas fa-undo-alt"></i><span>Easy Returns</span></div>
        <div class="auth-feature"><i class="fas fa-shield-alt"></i><span>Premium Quality</span></div>
    </div>
</div>

<div class="auth-right">
    <h1>Create Account</h1>
    <p class="auth-subtitle">Join thousands of fashion-forward shoppers</p>

    <?php if ($error): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-field">
            <label>Full Name</label>
            <div class="input-wrap">
                <i class="fas fa-user"></i>
                <input type="text" name="name" placeholder="Your full name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            </div>
        </div>

        <div class="form-field">
            <label>Email Address</label>
            <div class="input-wrap">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
        </div>

        <div class="form-field">
            <label>Phone <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--text-light)">(optional)</span></label>
            <div class="input-wrap">
                <i class="fas fa-phone"></i>
                <input type="tel" name="phone" placeholder="+94 77 000 0000" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>
        </div>

        <div class="form-field">
            <label>Password</label>
            <div style="position:relative;display:flex;align-items:center;width:100%;border:1.5px solid #E2DAD0;border-radius:6px;background:#F5F0E8;padding:0 46px 0 44px;transition:border-color 0.2s,background 0.2s;" id="pw-box">
                <i class="fas fa-lock" style="position:absolute;left:16px;top:50%;transform:translateY(-50%);color:#8C8680;font-size:14px;pointer-events:none;"></i>
                <input type="password" name="password" id="password" placeholder="At least 6 characters" required
                    style="flex:1;border:none;background:none;outline:none;padding:14px 0;font-family:'Jost',sans-serif;font-size:14px;color:#1C1C1C;width:100%;"
                    onfocus="document.getElementById('pw-box').style.borderColor='#C9A870';document.getElementById('pw-box').style.background='#fff';"
                    onblur="document.getElementById('pw-box').style.borderColor='#E2DAD0';document.getElementById('pw-box').style.background='#F5F0E8';">
                <button type="button" onclick="togglePassword()" style="background:none;border:none;cursor:pointer;color:#8C8680;font-size:15px;padding:0;display:flex;align-items:center;flex-shrink:0;">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-auth">Create Account</button>
    </form>

    <p class="auth-switch">Already have an account? <a href="/velvet vogue/login.php">Sign In</a></p>
    <p class="terms-text">By registering you agree to our <a href="#">Terms of Service</a> &amp; <a href="#">Privacy Policy</a>.</p>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
</body>
</html>
