<?php
/**
 * menu.php
 */
session_start();
$pageTitle = 'Menu';
require_once 'config/db.php';

$dishImages = [
    'momos'      => 'momo.jpg',
    'fried rice' => 'fried_rice.jpg',
    'maggi'      => 'maggi.jpg',
    'samosa'     => 'samosa.jpg',
    'sandwich'   => 'sandwich.jpg',
    'tea'        => 'tea.jpg',
    'coffee'     => 'coffee.jpg',
];

function getDishImage(string $name): ?string {
    global $dishImages;
    $lower = strtolower($name);
    foreach ($dishImages as $key => $file) {
        if (str_contains($lower, $key)) return '/canteen_project/images/' . $file;
    }
    return null;
}

function renderStars(float $rating): string {
    $html = '<div class="stars-display">';
    for ($i = 1; $i <= 5; $i++) {
        $fill = $rating >= $i ? '#f5c842' : 'none';
        $html .= '<svg width="13" height="13" viewBox="0 0 24 24" fill="'.$fill.'" stroke="#f5c842" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
    }
    return $html . '</div>';
}

$result = $conn->query("
    SELECT d.*, COALESCE(AVG(r.rating),0) AS avg_rating, COUNT(r.id) AS review_count
    FROM dishes d LEFT JOIN reviews r ON d.id = r.dish_id
    GROUP BY d.id ORDER BY d.id
");

require_once 'includes/header.php';
?>

<!-- Banner -->
<div class="menu-banner">
    <div class="menu-banner-label">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Updated Daily
    </div>
    <h1>Today's Menu</h1>
    <p>Fresh dishes from the ADBU canteen</p>
    <p> Tap any card to rate and review</p>
</div>

<?php if ($result && $result->num_rows > 0): ?>
<div class="menu-grid" style="padding-top:2.5rem;">
    <?php while ($dish = $result->fetch_assoc()):
        $avg       = round((float)$dish['avg_rating'], 1);
        $avgInt    = round($avg);
        $autoImg   = getDishImage($dish['name']);
        $customImg = !empty($dish['image']) && file_exists($_SERVER['DOCUMENT_ROOT'].'/canteen_project/images/'.$dish['image'])
                     ? '/canteen_project/images/' . $dish['image'] : null;
        $imgSrc    = $customImg ?? $autoImg;
        $isSvg     = $imgSrc && str_ends_with($imgSrc, '.svg');
    ?>
    <article class="dish-card">
        <div class="dish-img-wrap">
            <?php if ($imgSrc): ?>
                <img src="<?= $imgSrc ?>"
                     alt="<?= htmlspecialchars($dish['name']) ?>"
                     loading="lazy"
                     style="<?= $isSvg ? 'object-fit:cover;' : 'object-fit:cover;' ?>">
            <?php else: ?>
                <div class="dish-img-placeholder">
                    <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="var(--pink-accent)" stroke-width="1.5"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                </div>
            <?php endif; ?>
            <div class="dish-badge">&#8377;<?= number_format($dish['price'], 2) ?></div>
        </div>

        <div class="dish-body">
            <div class="dish-name"><?= htmlspecialchars($dish['name']) ?></div>
            <div class="dish-desc"><?= htmlspecialchars($dish['description']) ?></div>
            <div class="dish-meta">
                <div class="dish-price">&#8377;<?= number_format($dish['price'], 2) ?></div>
                <div class="dish-stars">
                    <?= renderStars($avg) ?>
                    <span style="margin-left:3px;"><?= $avg > 0 ? $avg : 'New' ?></span>
                </div>
            </div>
        </div>

        <div class="dish-footer">
            <a href="review.php?dish_id=<?= $dish['id'] ?>" class="btn btn-primary btn-sm btn-block">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <?= $dish['review_count'] ?> Review<?= $dish['review_count'] != 1 ? 's' : '' ?> &amp; Rate
            </a>
        </div>
    </article>
    <?php endwhile; ?>
</div>

<?php else: ?>
<div class="container" style="padding:5rem 0;text-align:center;">
    <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="var(--text-light)" stroke-width="1.3" style="margin:0 auto 1rem;display:block;">
        <path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
        <line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/>
    </svg>
    <strong style="font-size:1.1rem;">No dishes found.</strong><br>
    <span style="color:var(--text-muted);font-size:.9rem;">Please import database.sql to populate the menu.</span>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
