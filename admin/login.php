<?php
/**
 * admin/login.php
 */
session_start();
if (isset($_SESSION['admin_id'])) { header('Location: /canteen_project/admin/dashboard.php'); exit; }

require_once '../config/db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password =      $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter your email and password.';
    } else {
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ? AND is_admin = 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']   = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            header('Location: /canteen_project/admin/dashboard.php'); exit;
        } else {
            $error = 'Invalid admin credentials.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Canteen</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/canteen_project/css/style.css">
    <script src="/canteen_project/js/theme.js"></script>
</head>
<body>
<div class="auth-page">
    <div class="auth-card animate-fadeInUp">
        <div class="auth-card-top">
            <div class="auth-card-icon">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round">
                    <rect x="3" y="11" width="18" height="11" rx="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <h1 class="auth-card-title">Admin Panel</h1>
            <p class="auth-card-sub">Sign in with your administrator credentials</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error" style="margin-bottom:1rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="login.php" novalidate>
            <div class="form-group">
                <label class="form-label" for="email">Admin Email</label>
                <div class="input-icon-wrap">
                    <svg class="input-ico" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <input type="email" class="form-control has-icon" id="email" name="email"
                           placeholder="admin@adbu.ac.in" required autocomplete="email">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-icon-wrap">
                    <svg class="input-ico" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <input type="password" class="form-control has-icon" id="password" name="password"
                           placeholder="Admin password" required autocomplete="current-password">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="margin-top:1.25rem;">
                Sign In to Admin
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
        </form>

        <div style="text-align:center;margin-top:1.25rem;">
            <a href="/canteen_project/index.php" style="font-size:.85rem;color:var(--text-muted);font-weight:700;">
                &larr; Back to main site
            </a>
        </div>
    </div>
</div>
</body>
</html>
