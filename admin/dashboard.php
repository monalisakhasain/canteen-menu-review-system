<?php
/**
 * admin/dashboard.php
 */
$pageTitle = 'Dashboard';
require_once '../config/db.php';
require_once 'includes/admin_header.php';

// Stats
$totalDishes  = $conn->query("SELECT COUNT(*) FROM dishes")->fetch_row()[0];
$totalReviews = $conn->query("SELECT COUNT(*) FROM reviews")->fetch_row()[0];
$totalUsers   = $conn->query("SELECT COUNT(*) FROM users WHERE is_admin = 0")->fetch_row()[0];
$avgRating    = round($conn->query("SELECT COALESCE(AVG(rating),0) FROM reviews")->fetch_row()[0], 1);

// Top rated dishes
$topDishes = $conn->query("
    SELECT d.name, COALESCE(AVG(r.rating),0) AS avg_r, COUNT(r.id) AS cnt
    FROM dishes d LEFT JOIN reviews r ON d.id = r.dish_id
    GROUP BY d.id ORDER BY avg_r DESC LIMIT 5
");

// Recent reviews
$recentReviews = $conn->query("
    SELECT r.rating, r.review_text, r.created_at, u.name AS user_name, d.name AS dish_name
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN dishes d ON r.dish_id = d.id
    ORDER BY r.created_at DESC LIMIT 5
");
?>

<!-- Stat cards -->
<div class="admin-stats animate-fadeInUp">
    <div class="admin-stat-card">
        <div class="admin-stat-icon stat-pink">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--pink-accent)" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
        </div>
        <div>
            <div class="admin-stat-num"><?= $totalDishes ?></div>
            <div class="admin-stat-lbl">Total Dishes</div>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon stat-blue">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--blue-accent)" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </div>
        <div>
            <div class="admin-stat-num"><?= $totalReviews ?></div>
            <div class="admin-stat-lbl">Total Reviews</div>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon stat-green">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2e8040" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div>
            <div class="admin-stat-num"><?= $totalUsers ?></div>
            <div class="admin-stat-lbl">Registered Students</div>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon stat-yell">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#9a7020" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        <div>
            <div class="admin-stat-num"><?= $avgRating ?></div>
            <div class="admin-stat-lbl">Avg Rating / 5</div>
        </div>
    </div>
</div>

<!-- Two columns -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start;" class="animate-fadeInUp animate-delay-1">

    <!-- Top rated dishes bar chart -->
    <div class="admin-table-wrap" style="padding:1.5rem;">
        <div class="admin-section-title" style="margin-bottom:1.25rem;font-size:1rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--pink-accent)" stroke-width="2" style="margin-right:8px;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            Top Rated Dishes
        </div>
        <?php if ($topDishes && $topDishes->num_rows > 0):
            $maxRating = 5; ?>
        <div class="admin-bar-chart">
            <?php while ($d = $topDishes->fetch_assoc()):
                $pct = ($maxRating > 0) ? round((float)$d['avg_r'] / $maxRating * 100) : 0;
            ?>
            <div class="admin-bar-row">
                <div class="admin-bar-label"><?= htmlspecialchars(substr($d['name'],0,10)) ?></div>
                <div class="admin-bar-track">
                    <div class="admin-bar-fill" style="width:<?= $pct ?>%"></div>
                </div>
                <div class="admin-bar-val"><?= round((float)$d['avg_r'],1) ?></div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <p style="color:var(--text-muted);font-size:.875rem;">No ratings yet.</p>
        <?php endif; ?>
    </div>

    <!-- Recent reviews -->
    <div class="admin-table-wrap" style="padding:1.5rem;">
        <div class="admin-section-title" style="margin-bottom:1.25rem;font-size:1rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--blue-accent)" stroke-width="2" style="margin-right:8px;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Recent Reviews
        </div>
        <?php if ($recentReviews && $recentReviews->num_rows > 0): ?>
        <div style="display:flex;flex-direction:column;gap:.75rem;">
            <?php while ($rev = $recentReviews->fetch_assoc()): ?>
            <div style="padding:.85rem;background:var(--surface2);border-radius:10px;border:1px solid var(--border);">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.3rem;flex-wrap:wrap;gap:.3rem;">
                    <span style="font-weight:800;font-size:.85rem;"><?= htmlspecialchars($rev['user_name']) ?></span>
                    <span class="admin-badge badge-pink"><?= htmlspecialchars($rev['dish_name']) ?></span>
                </div>
                <div style="display:flex;gap:2px;margin-bottom:.3rem;">
                    <?php for ($i=1;$i<=5;$i++): ?>
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="<?= $i<=(int)$rev['rating']?'#f5c842':'none' ?>" stroke="#f5c842" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <?php endfor; ?>
                </div>
                <p style="font-size:.8rem;color:var(--text-muted);margin:0;line-height:1.5;"><?= htmlspecialchars(substr($rev['review_text'],0,80)) ?>...</p>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <p style="color:var(--text-muted);font-size:.875rem;">No reviews yet.</p>
        <?php endif; ?>
    </div>

</div>

<style>
@media (max-width: 700px) {
    .admin-content > div[style*="grid-template-columns:1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once 'includes/admin_footer.php'; ?>
