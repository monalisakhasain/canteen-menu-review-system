<?php
/**
 * includes/header.php
 */
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$siteTitle   = 'Canteen Menu & Review System';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$pageTitle   = isset($pageTitle) ? $pageTitle . ' — ' . $siteTitle : $siteTitle;
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,700;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/canteen_project/css/style.css">
    <script src="/canteen_project/js/theme.js"></script>
</head>
<body>

<header>
    <div class="header-inner">

        <!-- Brand -->
        <a href="/canteen_project/index.php" class="header-brand">
            <?php if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/canteen_project/images/logo.png')): ?>
                <img src="/canteen_project/images/logo.png" alt="ADBU Logo" class="header-logo">
            <?php else: ?>
                <div class="header-logo-placeholder">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--pink-accent)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8h1a4 4 0 0 1 0 8h-1"/>
                        <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
                        <line x1="6" y1="1" x2="6" y2="4"/>
                        <line x1="10" y1="1" x2="10" y2="4"/>
                        <line x1="14" y1="1" x2="14" y2="4"/>
                    </svg>
                </div>
            <?php endif; ?>
            <div class="brand-text">
                <span class="brand-name">Canteen Menu &amp; Review System</span>
                <span class="brand-sub">ADBU &middot; Campus Dining</span>
            </div>
        </a>

        <!-- Desktop nav -->
        <nav class="header-nav" aria-label="Main navigation">
            <a href="/canteen_project/index.php" class="nav-link <?= $currentPage==='index'?'active':'' ?>">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Home
            </a>
            <a href="/canteen_project/menu.php" class="nav-link <?= $currentPage==='menu'?'active':'' ?>">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                Menu
            </a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="nav-user">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <?= htmlspecialchars($_SESSION['user_name']) ?>
                </span>
                <a href="/canteen_project/logout.php" class="nav-link logout-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Sign Out
                </a>
            <?php else: ?>
                <a href="/canteen_project/login.php" class="nav-link <?= $currentPage==='login'?'active':'' ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    Login
                </a>
                <a href="/canteen_project/register.php" class="nav-link nav-link-cta <?= $currentPage==='register'?'active':'' ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                    Register
                </a>
            <?php endif; ?>

            <a href="/canteen_project/admin/login.php" class="nav-link" style="font-size:.8rem;opacity:.7;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Admin
            </a>

            <button class="theme-toggle" id="themeToggle" title="Toggle theme" aria-label="Toggle theme">
                <span class="icon-sun"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg></span>
                <span class="icon-moon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg></span>
            </button>
        </nav>

        <!-- Hamburger -->
        <button class="hamburger" id="hamburger" aria-expanded="false" aria-controls="mobileNav" aria-label="Open menu">
            <span></span><span></span><span></span>
        </button>
    </div>

    <!-- Mobile nav -->
    <nav class="mobile-nav" id="mobileNav" aria-label="Mobile navigation">
        <a href="/canteen_project/index.php" class="nav-link">Home</a>
        <a href="/canteen_project/menu.php"  class="nav-link">Menu</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <span class="nav-link" style="font-weight:800;color:var(--pink-accent);">
                <?= htmlspecialchars($_SESSION['user_name']) ?>
            </span>
            <a href="/canteen_project/logout.php" class="nav-link logout-link">Sign Out</a>
        <?php else: ?>
            <a href="/canteen_project/login.php"    class="nav-link">Login</a>
            <a href="/canteen_project/register.php" class="nav-link">Register</a>
        <?php endif; ?>
        <a href="/canteen_project/admin/login.php" class="nav-link" style="opacity:.7;font-size:.85rem;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Admin Login
        </a>
        <div style="padding:8px 16px 14px;">
            <button class="theme-toggle" style="width:100%;border-radius:10px;gap:8px;justify-content:center;"
                    onclick="document.getElementById('themeToggle').click()">
                <svg class="icon-sun" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/></svg>
                <svg class="icon-moon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                <span style="font-size:.85rem;font-weight:700;">Toggle Theme</span>
            </button>
        </div>
    </nav>
</header>

<main>
