<?php
session_start();
if (isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$pageTitle = 'Login';
require_once 'config/db.php';

$email = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password =      $_POST['password'] ?? '';
    if (empty($email) || empty($password)) {
        $error = 'Please enter your email and password.';
    } else {
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['name'];
            $_SESSION['user_email'] = $email;
            header('Location: index.php'); exit;
        } else {
            $error = 'Incorrect email or password.';
        }
    }
}
require_once 'includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card animate-fadeInUp">

        <div class="auth-card-top">
            <div class="auth-card-icon">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
            </div>
            <h1 class="auth-card-title">Welcome Back</h1>
            <p class="auth-card-sub">Sign in to your ADBU Canteen account</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error" style="margin-bottom:1rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="login.php" novalidate>
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <div class="input-icon-wrap">
                    <svg class="input-ico" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <input type="email" class="form-control has-icon" id="email" name="email"
                           value="<?= htmlspecialchars($email) ?>"
                           placeholder="you@adbu.ac.in" required autocomplete="email">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-icon-wrap">
                    <svg class="input-ico" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <input type="password" class="form-control has-icon" id="password" name="password"
                           placeholder="Your password" required autocomplete="current-password">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top:1.25rem;">
                Sign In
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
        </form>

        <div class="auth-card-divider"><span>Don't have an account?</span></div>
        <a href="register.php" class="btn btn-soft btn-block">Create a Free Account</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
