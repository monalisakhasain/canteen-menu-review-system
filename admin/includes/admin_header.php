<?php
/**
 * admin/includes/admin_header.php
 * Reusable admin panel header. Guards every admin page.
 */
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Auth guard — redirect to admin login if not admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: /canteen_project/admin/login.php'); exit;
}

$adminName   = $_SESSION['admin_name'] ?? 'Admin';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$pageTitle   = isset($pageTitle) ? $pageTitle . ' — Admin' : 'Admin Panel';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/canteen_project/css/style.css">
    <link rel="stylesheet" href="/canteen_project/admin/admin.css">
    <script src="/canteen_project/js/theme.js"></script>
</head>
<body class="admin-body">

<div class="admin-layout">

    <!-- ── SIDEBAR ── -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-sidebar-brand">
           <div class="admin-brand-icon" style="background:none;padding:2px;">
    <img src="/canteen_project/images/logo.png" alt="ADBU Logo" style="width:34px;height:34px;object-fit:contain;">
</div>
            <div>
                <div class="admin-brand-name">Canteen Admin</div>
                <div class="admin-brand-sub">ADBU</div>
            </div>
        </div>

        <nav class="admin-nav">
            <div class="admin-nav-label">Main</div>

            <a href="/canteen_project/admin/dashboard.php"
               class="admin-nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>

            <a href="/canteen_project/admin/dishes.php"
               class="admin-nav-link <?= $currentPage === 'dishes' ? 'active' : '' ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                Manage Dishes
            </a>

            <a href="/canteen_project/admin/reviews.php"
               class="admin-nav-link <?= $currentPage === 'reviews' ? 'active' : '' ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Manage Reviews
            </a>

            <div class="admin-nav-label" style="margin-top:1rem;">Account</div>

            <a href="/canteen_project/index.php" class="admin-nav-link" target="_blank">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                View Site
            </a>

            <a href="/canteen_project/admin/logout.php" class="admin-nav-link admin-nav-logout">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Sign Out
            </a>
        </nav>

        <div class="admin-sidebar-user">
            <div class="admin-user-avatar"><?= strtoupper(substr($adminName, 0, 1)) ?></div>
            <div>
                <div class="admin-user-name"><?= htmlspecialchars($adminName) ?></div>
                <div class="admin-user-role">Administrator</div>
            </div>
        </div>
    </aside>

    <!-- ── MAIN CONTENT ── -->
    <div class="admin-main">

        <!-- Top bar -->
        <div class="admin-topbar">
            <button class="admin-menu-btn" id="adminMenuBtn" aria-label="Toggle sidebar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <div class="admin-topbar-title"><?= $pageTitle ?></div>
            <div class="admin-topbar-right">
                <button class="theme-toggle" id="themeToggle" title="Toggle theme">
                    <svg class="icon-sun" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/></svg>
                    <svg class="icon-moon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
            </div>
        </div>

        <div class="admin-content">
