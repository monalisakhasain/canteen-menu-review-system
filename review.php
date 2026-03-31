<?php
/**
 * review.php — Dish reviews & rating form
 */
session_start();
$pageTitle = 'Reviews';
require_once 'config/db.php';

$dish_id = isset($_GET['dish_id']) ? (int)$_GET['dish_id'] : 0;
if ($dish_id <= 0) { header('Location: menu.php'); exit; }

$stmt = $conn->prepare("SELECT * FROM dishes WHERE id = ?");
$stmt->bind_param('i', $dish_id);
$stmt->execute();
$dish = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$dish) { header('Location: menu.php'); exit; }

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id     = (int)$_SESSION['user_id'];
    $rating      = isset($_POST['rating'])      ? (int)$_POST['rating']      : 0;
    $review_text = isset($_POST['review_text']) ? trim($_POST['review_text']) : '';

    if ($rating < 1 || $rating > 5) {
        $error = 'Please select a star rating (1 to 5).';
    } elseif (empty($review_text)) {
        $error = 'Please write a review before submitting.';
    } else {
        $chk = $conn->prepare("SELECT id FROM reviews WHERE user_id=? AND dish_id=?");
        $chk->bind_param('ii', $user_id, $dish_id);
        $chk->execute(); $chk->store_result();
        if ($chk->num_rows > 0) {
            $upd = $conn->prepare("UPDATE reviews SET rating=?, review_text=?, created_at=NOW() WHERE user_id=? AND dish_id=?");
            $upd->bind_param('isii', $rating, $review_text, $user_id, $dish_id);
            $upd->execute(); $upd->close();
            $success = 'Your review has been updated successfully.';
        } else {
            $ins = $conn->prepare("INSERT INTO reviews (user_id, dish_id, rating, review_text, created_at) VALUES (?,?,?,?,NOW())");
            $ins->bind_param('iiis', $user_id, $dish_id, $rating, $review_text);
            $ins->execute(); $ins->close();
            $success = 'Thank you — your review has been posted!';
        }
        $chk->close();
    }
}

$aggStmt = $conn->prepare("SELECT COALESCE(AVG(rating),0) AS avg_r, COUNT(*) AS cnt FROM reviews WHERE dish_id=?");
$aggStmt->bind_param('i', $dish_id);
$aggStmt->execute();
$agg = $aggStmt->get_result()->fetch_assoc();
$aggStmt->close();
$avgRating   = round((float)$agg['avg_r'], 1);
$reviewCount = (int)$agg['cnt'];

$revStmt = $conn->prepare("SELECT r.*, u.name AS user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.dish_id = ? ORDER BY r.created_at DESC");
$revStmt->bind_param('i', $dish_id);
$revStmt->execute();
$reviews = $revStmt->get_result();
$revStmt->close();

$userReview = null;
if (isset($_SESSION['user_id'])) {
    $urStmt = $conn->prepare("SELECT * FROM reviews WHERE user_id=? AND dish_id=?");
    $urStmt->bind_param('ii', $_SESSION['user_id'], $dish_id);
    $urStmt->execute();
    $userReview = $urStmt->get_result()->fetch_assoc();
    $urStmt->close();
}

// SVG star renderer
function svgStars(float $rating, int $size = 15): string {
    $out = '<div class="stars-display">';
    for ($i = 1; $i <= 5; $i++) {
        $fill = $rating >= $i ? '#f5c842' : 'none';
        $out .= '<svg width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="'.$fill.'" stroke="#f5c842" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
    }
    return $out . '</div>';
}

// Map dish name to SVG illustration
$dishImages = [
    'momos'      => 'momos.jpg',
    'fried rice' => 'fried_rice.jpg',
    'maggi'      => 'maggi.jpg',
    'samosa'     => 'samosa.jpg',
    'sandwich'   => 'sandwich.jpg',
    'tea'        => 'tea.jpg',
    'coffee'     => 'coffee.jpg',
];
function getDishImg(string $name): ?string {
    global $dishImages;
    $lower = strtolower($name);
    foreach ($dishImages as $k => $f) {
        if (str_contains($lower, $k)) return '/canteen_project/images/' . $f;
    }
    return null;
}

$customImg = !empty($dish['image']) && file_exists($_SERVER['DOCUMENT_ROOT'].'/canteen_project/images/'.$dish['image'])
             ? '/canteen_project/images/'.$dish['image'] : null;
$dishImg   = $customImg ?? getDishImg($dish['name']);
$ratingLabels = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];

require_once 'includes/header.php';
?>

<!-- ── BANNER ── -->
<div class="rv-banner">
    <div class="rv-banner-inner">
        <a href="menu.php" class="rv-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Menu
        </a>
    </div>
</div>

<!-- ── DISH HERO ── -->
<div class="rv-wrap animate-fadeInUp">

    <div class="rv-dish-hero">
        <div class="rv-dish-img">
            <?php if ($dishImg): ?>
                <img src="<?= $dishImg ?>" alt="<?= htmlspecialchars($dish['name']) ?>">
            <?php else: ?>
                <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="var(--pink-accent)" stroke-width="1.4">
                    <path d="M18 8h1a4 4 0 0 1 0 8h-1"/>
                    <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
                    <line x1="6" y1="1" x2="6" y2="4"/>
                    <line x1="10" y1="1" x2="10" y2="4"/>
                    <line x1="14" y1="1" x2="14" y2="4"/>
                </svg>
            <?php endif; ?>
        </div>

        <div class="rv-dish-info">
            <div class="rv-dish-label">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                Canteen Dish
            </div>
            <h1 class="rv-dish-name"><?= htmlspecialchars($dish['name']) ?></h1>
            <p class="rv-dish-desc"><?= htmlspecialchars($dish['description']) ?></p>

            <div class="rv-dish-meta">
                <div class="rv-price-pill">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    &#8377;<?= number_format($dish['price'], 2) ?>
                </div>

                <div class="rv-rating-pill">
                    <?= svgStars($avgRating, 13) ?>
                    <span><?= $avgRating > 0 ? $avgRating . ' / 5' : 'No ratings yet' ?></span>
                </div>

                <div class="rv-count-pill">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <?= $reviewCount ?> Review<?= $reviewCount !== 1 ? 's' : '' ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ── ALERTS ── -->
    <?php if ($success): ?>
    <div class="alert alert-success animate-fadeInUp" data-auto-dismiss>
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert alert-error animate-fadeInUp">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <!-- ── TWO COLUMN LAYOUT ── -->
    <div class="rv-columns">

        <!-- LEFT — Review form / login prompt -->
        <div class="rv-left animate-fadeInUp animate-delay-1">
            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="rv-form-card">
                <div class="rv-section-title">
                    <div class="rv-section-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </div>
                    <?= $userReview ? 'Update Your Review' : 'Write a Review' ?>
                </div>

                <form method="POST" action="review.php?dish_id=<?= $dish_id ?>">
                    <!-- Star rating -->
                    <div class="form-group">
                        <label class="form-label">Your Rating</label>
                        <div class="rv-star-row">
                            <div class="star-rating">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>"
                                       <?= ($userReview && (int)$userReview['rating'] === $i) ? 'checked' : '' ?>>
                                <label for="star<?= $i ?>" title="<?= $i ?> star">
                                    <svg viewBox="0 0 24 24" width="28" height="28"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                </label>
                                <?php endfor; ?>
                            </div>
                            <span class="rv-rating-label" id="ratingText">
                                <?php if ($userReview): echo $ratingLabels[(int)$userReview['rating']] ?? ''; endif; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Review text -->
                    <div class="form-group">
                        <label class="form-label" for="review_text">Your Review</label>
                        <textarea class="form-control" id="review_text" name="review_text"
                                  placeholder="Tell other students what you think about this dish..." rows="4"><?= $userReview ? htmlspecialchars($userReview['review_text']) : '' ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <?php if ($userReview): ?>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Update Review
                        <?php else: ?>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            Submit Review
                        <?php endif; ?>
                    </button>
                </form>
            </div>

            <?php else: ?>
            <!-- Not logged in -->
            <div class="rv-login-prompt">
                <div class="rv-login-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--pink-accent)" stroke-width="1.6"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <h3 class="rv-login-title">Sign in to Review</h3>
                <p class="rv-login-desc">Create a free account or log in to rate this dish and share your thoughts with fellow students.</p>
                <div style="display:flex;gap:.75rem;flex-wrap:wrap;justify-content:center;margin-top:1.25rem;">
                    <a href="login.php" class="btn btn-primary btn-sm">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        Sign In
                    </a>
                    <a href="register.php" class="btn btn-soft btn-sm">Create Account</a>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- RIGHT — Reviews list -->
        <div class="rv-right animate-fadeInUp animate-delay-2">
            <div class="rv-section-title">
                <div class="rv-section-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                All Reviews
                <span class="rv-count-badge"><?= $reviewCount ?></span>
            </div>

            <?php if ($reviewCount > 0): ?>
            <div class="rv-list">
                <?php while ($rev = $reviews->fetch_assoc()):
                    $initials = strtoupper(substr($rev['user_name'], 0, 1));
                    $isOwn    = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $rev['user_id'];
                ?>
                <div class="rv-item <?= $isOwn ? 'rv-item-own' : '' ?>">
                    <div class="rv-item-header">
                        <div class="rv-avatar"><?= $initials ?></div>
                        <div class="rv-item-meta">
                            <div class="rv-item-name">
                                <?= htmlspecialchars($rev['user_name']) ?>
                                <?php if ($isOwn): ?>
                                    <span class="rv-you-badge">You</span>
                                <?php endif; ?>
                            </div>
                            <div class="rv-item-stars">
                                <?= svgStars((float)$rev['rating'], 12) ?>
                            </div>
                        </div>
                        <div class="rv-item-date">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <?= date('d M Y', strtotime($rev['created_at'])) ?>
                        </div>
                    </div>
                    <p class="rv-item-text"><?= nl2br(htmlspecialchars($rev['review_text'])) ?></p>
                </div>
                <?php endwhile; ?>
            </div>

            <?php else: ?>
            <div class="rv-empty">
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="var(--text-light)" stroke-width="1.3"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <p><strong>No reviews yet</strong></p>
                <span>Be the first to share your thoughts!</span>
            </div>
            <?php endif; ?>
        </div>

    </div><!-- /rv-columns -->
</div><!-- /rv-wrap -->

<style>
/* ── REVIEW PAGE ── */
.rv-banner {
    background: linear-gradient(135deg, var(--pink-accent), var(--blue-accent));
    padding: .9rem 2rem;
}
.rv-banner-inner { max-width: 1100px; margin: 0 auto; }
.rv-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: rgba(255,255,255,.9);
    font-size: .85rem;
    font-weight: 700;
    text-decoration: none;
    transition: color .2s;
}
.rv-back:hover { color: white; }

.rv-wrap {
    max-width: 1100px;
    margin: 0 auto;
    padding: 2rem 2rem 4rem;
}

/* Dish hero */
.rv-dish-hero {
    display: flex;
    gap: 1.75rem;
    align-items: center;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: 20px;
    padding: 1.75rem;
    margin-bottom: 1.75rem;
    box-shadow: 0 4px 18px var(--shadow);
}
.rv-dish-img {
    width: 130px;
    height: 130px;
    border-radius: 14px;
    overflow: hidden;
    flex-shrink: 0;
    background: linear-gradient(135deg, var(--pink), var(--blue));
    display: flex;
    align-items: center;
    justify-content: center;
}
.rv-dish-img img { width: 100%; height: 100%; object-fit: cover; }
.rv-dish-info { flex: 1; }
.rv-dish-label {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: .7rem;
    font-weight: 800;
    color: var(--pink-accent);
    letter-spacing: .8px;
    text-transform: uppercase;
    background: var(--pink);
    padding: 3px 10px;
    border-radius: 99px;
    margin-bottom: .5rem;
}
[data-theme="dark"] .rv-dish-label { background: var(--surface2); }
.rv-dish-name {
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.4rem, 3vw, 2rem);
    font-weight: 700;
    margin-bottom: .35rem;
}
.rv-dish-desc { font-size: .88rem; color: var(--text-muted); line-height: 1.6; margin-bottom: .85rem; }
.rv-dish-meta { display: flex; align-items: center; gap: .6rem; flex-wrap: wrap; }
.rv-price-pill, .rv-rating-pill, .rv-count-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border-radius: 99px;
    font-size: .8rem;
    font-weight: 700;
}
.rv-price-pill  { background: var(--pink);    color: var(--pink-accent); }
.rv-rating-pill { background: var(--yellow);   color: #9a7020; }
.rv-count-pill  { background: var(--blue);     color: var(--blue-accent); }
[data-theme="dark"] .rv-price-pill  { background: var(--surface2); color: var(--pink-accent); }
[data-theme="dark"] .rv-rating-pill { background: var(--surface2); color: #c8a030; }
[data-theme="dark"] .rv-count-pill  { background: var(--surface2); color: var(--blue-accent); }

/* Two-column layout */
.rv-columns {
    display: grid;
    grid-template-columns: 380px 1fr;
    gap: 1.75rem;
    align-items: start;
}

/* Form card */
.rv-form-card {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: 18px;
    padding: 1.5rem;
    box-shadow: 0 4px 16px var(--shadow);
}

.rv-section-title {
    display: flex;
    align-items: center;
    gap: 9px;
    font-size: 1rem;
    font-weight: 800;
    color: var(--text);
    margin-bottom: 1.25rem;
}
.rv-section-icon {
    width: 32px; height: 32px;
    border-radius: 9px;
    background: linear-gradient(135deg, var(--pink), var(--blue));
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.rv-count-badge {
    margin-left: auto;
    background: var(--pink);
    color: var(--pink-accent);
    font-size: .75rem;
    font-weight: 800;
    padding: 2px 9px;
    border-radius: 99px;
}
[data-theme="dark"] .rv-count-badge { background: var(--surface2); }

/* Star rating row */
.rv-star-row { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
.rv-rating-label {
    font-size: .85rem;
    font-weight: 800;
    color: var(--pink-accent);
    min-width: 70px;
}

/* Star rating input — SVG version */
.star-rating { display: flex; flex-direction: row-reverse; gap: 2px; }
.star-rating input { display: none; }
.star-rating label {
    cursor: pointer;
    color: var(--border);
    transition: transform .15s;
}
.star-rating label svg {
    fill: none;
    stroke: #e0c000;
    transition: fill .15s;
}
.star-rating label:hover svg,
.star-rating label:hover ~ label svg,
.star-rating input:checked ~ label svg { fill: #f5c842; }
.star-rating label:hover { transform: scale(1.15); }

/* Login prompt */
.rv-login-prompt {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: 18px;
    padding: 2rem 1.5rem;
    text-align: center;
    box-shadow: 0 4px 16px var(--shadow);
}
.rv-login-icon {
    width: 60px; height: 60px;
    border-radius: 16px;
    background: linear-gradient(135deg, var(--pink), var(--blue));
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1rem;
}
.rv-login-title { font-family: 'Playfair Display',serif; font-size: 1.25rem; font-weight: 700; margin-bottom: .4rem; }
.rv-login-desc  { font-size: .875rem; color: var(--text-muted); line-height: 1.6; }

/* Reviews list */
.rv-list { display: flex; flex-direction: column; gap: 1rem; }
.rv-item {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: 14px;
    padding: 1.1rem 1.25rem;
    transition: border-color .2s, box-shadow .2s;
    box-shadow: 0 2px 8px var(--shadow);
}
.rv-item:hover { border-color: var(--pink-dark); box-shadow: 0 4px 16px var(--shadow-md); }
.rv-item-own   { border-color: var(--pink-dark); background: var(--surface2); }

.rv-item-header {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: .65rem;
}
.rv-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: linear-gradient(135deg, var(--pink), var(--blue));
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: .85rem; flex-shrink: 0;
}
.rv-item-meta { flex: 1; }
.rv-item-name {
    font-weight: 800;
    font-size: .9rem;
    display: flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 3px;
}
.rv-you-badge {
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .5px;
    background: linear-gradient(135deg, var(--pink-accent), var(--blue-accent));
    color: white;
    padding: 2px 7px;
    border-radius: 99px;
}
.rv-item-date {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: .75rem;
    color: var(--text-muted);
    white-space: nowrap;
    margin-left: auto;
    padding-top: 2px;
}
.rv-item-text {
    font-size: .875rem;
    color: var(--text-muted);
    line-height: 1.65;
}

/* Empty state */
.rv-empty {
    background: var(--surface);
    border: 1.5px dashed var(--border);
    border-radius: 14px;
    padding: 3rem 1.5rem;
    text-align: center;
    color: var(--text-muted);
}
.rv-empty svg { display: block; margin: 0 auto .75rem; }
.rv-empty strong { display: block; font-size: 1rem; color: var(--text); margin-bottom: .3rem; }
.rv-empty span  { font-size: .85rem; }

/* Responsive */
@media (max-width: 860px) {
    .rv-columns { grid-template-columns: 1fr; }
    .rv-dish-hero { flex-direction: column; text-align: center; }
    .rv-dish-meta { justify-content: center; }
    .rv-wrap { padding: 1.25rem 1rem 3rem; }
    .rv-dish-img { margin: 0 auto; }
}
@media (max-width: 480px) {
    .rv-banner { padding: .75rem 1rem; }
}
</style>

<?php require_once 'includes/footer.php'; ?>
