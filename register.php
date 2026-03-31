<?php
session_start();
if (isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$pageTitle = 'Register';
require_once 'config/db.php';

$name = $email = $success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password =      $_POST['password'] ?? '';
    $confirm  =      $_POST['confirm']  ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $error = 'All fields are required.';
    } elseif (strlen($name) < 2) {
        $error = 'Name must be at least 2 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $chk = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $chk->bind_param('s', $email); $chk->execute(); $chk->store_result();
        if ($chk->num_rows > 0) {
            $error = 'An account with this email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins  = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?,?,?)");
            $ins->bind_param('sss', $name, $email, $hash);
            if ($ins->execute()) { $success = 'Account created! You can now sign in.'; $name = $email = ''; }
            else { $error = 'Something went wrong. Please try again.'; }
            $ins->close();
        }
        $chk->close();
    }
}
require_once 'includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card animate-fadeInUp">

        <div class="auth-card-top">
            <div class="auth-card-icon">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <line x1="19" y1="8" x2="19" y2="14"/>
                    <line x1="22" y1="11" x2="16" y2="11"/>
                </svg>
            </div>
            <h1 class="auth-card-title">Create Account</h1>
            <p class="auth-card-sub">Join the ADBU Canteen community for free</p>
        </div>

        <?php if ($success): ?>
        <div class="alert alert-success" style="margin-bottom:1rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <?= htmlspecialchars($success) ?>
            <a href="login.php" style="font-weight:800;margin-left:4px;">Sign in &rarr;</a>
        </div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert alert-error" style="margin-bottom:1rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="register.php" novalidate>
            <div class="form-group">
                <label class="form-label" for="name">Full Name</label>
                <div class="input-icon-wrap">
                    <svg class="input-ico" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <input type="text" class="form-control has-icon" id="name" name="name"
                           value="<?= htmlspecialchars($name) ?>"
                           placeholder="e.g. Priya Sharma" required autocomplete="name">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <div class="input-icon-wrap">
                    <svg class="input-ico" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <input type="email" class="form-control has-icon" id="email" name="email"
                           value="<?= htmlspecialchars($email) ?>"
                           placeholder="you@adbu.ac.in" required autocomplete="email">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-icon-wrap">
                        <svg class="input-ico" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input type="password" class="form-control has-icon" id="password" name="password"
                               placeholder="Min. 6 chars" required autocomplete="new-password">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="confirm">Confirm</label>
                    <div class="input-icon-wrap">
                        <svg class="input-ico" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <input type="password" class="form-control has-icon" id="confirm" name="confirm"
                               placeholder="Re-enter" required autocomplete="new-password">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top:1.1rem;">
                Create Account
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
        </form>

        <div class="auth-card-divider"><span>Already have an account?</span></div>
        <a href="login.php" class="btn btn-soft btn-block">Sign In Instead</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
