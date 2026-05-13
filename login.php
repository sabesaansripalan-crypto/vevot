<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /velvet vogue/index.php');
    exit;
}


//my login pageeeee

$error = '';
$redirect = $_GET['redirect'] ?? '/velvet vogue/index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['role'] === 'admin') {
                $error = 'Admin accounts must use the <a href="/velvet vogue/admin/login.php" style="color:#a82d2d;font-weight:600;text-decoration:underline">Admin Login</a> page.';
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                header('Location: ' . $redirect);
                exit;
            }
        } else {
            $error = 'Invalid email or password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In — VelvetVogue</title>
<link rel="stylesheet" href="/velvet vogue/assets/css/auth.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="auth-left">
    <div class="auth-brand">
        <div class="auth-brand-icon">V</div>
        <span class="auth-brand-name">VelvetVogue</span>
    </div>
    <h1 class="auth-left-title">Welcome<br>Back</h1>
    <p class="auth-left-desc">Sign in to access your orders, wishlist, and exclusive member offers.</p>
    <div class="auth-features">
        <div class="auth-feature"><i class="fas fa-star"></i><span>Exclusive Deals</span></div>
        <div class="auth-feature"><i class="fas fa-undo-alt"></i><span>Easy Returns</span></div>
        <div class="auth-feature"><i class="fas fa-shield-alt"></i><span>Premium Quality</span></div>
    </div>
</div>

<div class="auth-right">
    <h1>Sign In</h1>
    <p class="auth-subtitle">Access your VelvetVogue customer account</p>

    <?php if ($error): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i><?= $error ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['registered'])): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i>Account created successfully! Please sign in.</div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

        <div class="form-field">
            <label>Email Address</label>
            <div class="input-wrap">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
        </div>

        <div class="form-field">
            <label>Password</label>
            <div style="position:relative;display:flex;align-items:center;width:100%;border:1.5px solid #E2DAD0;border-radius:6px;background:#F5F0E8;padding:0 46px 0 44px;transition:border-color 0.2s,background 0.2s;" id="pw-box">
                <i class="fas fa-lock" style="position:absolute;left:16px;top:50%;transform:translateY(-50%);color:#8C8680;font-size:14px;pointer-events:none;"></i>
                <input type="password" name="password" id="password" placeholder="Enter your password" required
                    style="flex:1;border:none;background:none;outline:none;padding:14px 0;font-family:'Jost',sans-serif;font-size:14px;color:#1C1C1C;width:100%;"
                    onfocus="document.getElementById('pw-box').style.borderColor='#C9A870';document.getElementById('pw-box').style.background='#fff';"
                    onblur="document.getElementById('pw-box').style.borderColor='#E2DAD0';document.getElementById('pw-box').style.background='#F5F0E8';">
                <button type="button" onclick="togglePassword()" style="background:none;border:none;cursor:pointer;color:#8C8680;font-size:15px;padding:0;display:flex;align-items:center;flex-shrink:0;">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </button>
            </div>
            <div class="form-field-footer">
                <a href="#" class="forgot-link">Forgot password?</a>
            </div>
        </div>

        <button type="submit" class="btn-auth">Sign In</button>
    </form>

    <p class="auth-switch">Don't have an account? <a href="/velvet vogue/register.php">Register here</a></p>
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
