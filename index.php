<?php
/**
 * index.php — Homepage
 */
session_start();
require_once 'config/db.php';
$pageTitle = 'Home';

$dish_count   = $conn->query("SELECT COUNT(*) FROM dishes")->fetch_row()[0]  ?? 0;
$review_count = $conn->query("SELECT COUNT(*) FROM reviews")->fetch_row()[0] ?? 0;
$user_count   = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0]   ?? 0;

$top_dishes = $conn->query("
    SELECT d.*, COALESCE(AVG(r.rating),0) AS avg_rating, COUNT(r.id) AS review_count
    FROM dishes d LEFT JOIN reviews r ON d.id = r.dish_id
    GROUP BY d.id ORDER BY avg_rating DESC, d.id ASC LIMIT 3
");

require_once 'includes/header.php';
?>

<!-- ============================================================
     HERO
     ============================================================ -->
<section class="idx-hero">
    <div class="idx-hero-bg"></div>
    <div class="idx-hero-inner">
        <div class="idx-hero-label">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            ADBU Campus Canteen
        </div>

        <h1 class="idx-hero-title">
            Discover &amp; Review<br>
            <span>Delicious Campus Food</span>
        </h1>

        <p class="idx-hero-desc">
            Explore today's canteen menu, rate your favourite dishes, and help your
            fellow students make the best food choices every day.
        </p>

        <div class="idx-hero-actions">
            <a href="menu.php" class="btn btn-primary btn-lg">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                View Today's Menu
            </a>
            <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="register.php" class="btn btn-outline btn-lg">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                Join Free
            </a>
            <?php else: ?>
            <a href="menu.php" class="btn btn-outline btn-lg">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                Rate a Dish
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- scroll cue -->
    <a href="#stats" class="idx-scroll-cue" aria-label="Scroll down">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
    </a>
</section>

<!-- ============================================================
     STATS STRIP
     ============================================================ -->
<section id="stats" class="idx-stats">
    <div class="idx-stats-inner">
        <div class="idx-stat">
            <div class="idx-stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            </div>
            <div class="idx-stat-num"><?= $dish_count ?>+</div>
            <div class="idx-stat-lbl">Menu Items</div>
        </div>
        <div class="idx-stat-divider"></div>
        <div class="idx-stat">
            <div class="idx-stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div class="idx-stat-num"><?= $review_count ?>+</div>
            <div class="idx-stat-lbl">Student Reviews</div>
        </div>
        <div class="idx-stat-divider"></div>
        <div class="idx-stat">
            <div class="idx-stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="idx-stat-num"><?= $user_count ?>+</div>
            <div class="idx-stat-lbl">Registered Students</div>
        </div>
    </div>
</section>

<!-- ============================================================
     SPLIT SECTION 1 — About
     ============================================================ -->
<section class="idx-split reveal-section" id="about">
    <div class="idx-split-visual idx-visual-about">
        <div class="idx-visual-grid">
            <div class="idx-vg-card" style="--d:0s">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--pink-accent)" stroke-width="1.8"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                <span>Daily Menu</span>
            </div>
            <div class="idx-vg-card" style="--d:.15s">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--blue-accent)" stroke-width="1.8"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <span>Star Ratings</span>
            </div>
            <div class="idx-vg-card" style="--d:.3s">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--pink-accent)" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <span>Reviews</span>
            </div>
            <div class="idx-vg-card" style="--d:.45s">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--blue-accent)" stroke-width="1.8"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                <span>Dark Mode</span>
            </div>
        </div>
    </div>

    <div class="idx-split-text">
        <div class="idx-section-label">About the System</div>
        <h2 class="idx-split-title">Everything You Need,<br>All in One Place</h2>
        <p class="idx-split-desc">
            The Canteen Menu &amp; Review System is built specifically for ADBU students.
            Browse the full menu, see dish prices and descriptions, and make smarter
            food choices guided by real reviews from your peers.
        </p>
        <a href="menu.php" class="btn btn-primary" style="margin-top:1.5rem;">
            Explore the Menu
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
    </div>
</section>

<!-- ============================================================
     SPLIT SECTION 2 — Ratings & Reviews (reversed)
     ============================================================ -->
<section class="idx-split idx-split-reverse reveal-section idx-split-alt">
    <div class="idx-split-text">
        <div class="idx-section-label">Community Ratings</div>
        <h2 class="idx-split-title">Rate Dishes, Shape<br>the Menu Experience</h2>
        <p class="idx-split-desc">
            Every student voice counts. Give a 1&ndash;5 star rating, write an honest
            review, and help your classmates decide what to order. Your feedback
            helps the canteen improve every single day.
        </p>
        <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="register.php" class="btn btn-primary" style="margin-top:1.5rem;">
            Create an Account
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
        <?php else: ?>
        <a href="menu.php" class="btn btn-primary" style="margin-top:1.5rem;">
            Rate a Dish
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
        <?php endif; ?>
    </div>

    <div class="idx-split-visual idx-visual-reviews">
        <!-- Mock review card -->
        <div class="idx-mock-card">
            <div class="idx-mock-header">
                <div class="idx-mock-avatar">A</div>
                <div>
                    <div class="idx-mock-name">Anjali Sharma</div>
                    <div class="idx-mock-stars">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#f5c842" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#f5c842" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#f5c842" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#f5c842" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#f5c842" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                </div>
            </div>
            <p class="idx-mock-text">"Best momos on campus! The chilli sauce is incredible. Highly recommend to everyone."</p>
            <div class="idx-mock-dish">Momos &mdash; Verified Review</div>
        </div>
        <div class="idx-mock-card idx-mock-card-2">
            <div class="idx-mock-header">
                <div class="idx-mock-avatar" style="background:linear-gradient(135deg,var(--blue),var(--blue-dark))">R</div>
                <div>
                    <div class="idx-mock-name">Rohan Das</div>
                    <div class="idx-mock-stars">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#f5c842" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#f5c842" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#f5c842" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#f5c842" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#f5c842" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                </div>
            </div>
            <p class="idx-mock-text">"Chai here is just perfect. The right amount of ginger every single time."</p>
            <div class="idx-mock-dish">Tea &mdash; Verified Review</div>
        </div>
    </div>
</section>

<!-- ============================================================
     TOP RATED DISHES
     ============================================================ -->
<section class="idx-top-dishes reveal-section">
    <div class="idx-section-header">
        <div class="idx-section-label">Student Favourites</div>
        <h2 class="idx-split-title" style="margin-bottom:.5rem;">Top Rated Dishes</h2>
        <p style="color:var(--text-muted);font-size:.95rem;">Highest-rated dishes voted by ADBU students</p>
    </div>

    <?php if ($top_dishes && $top_dishes->num_rows > 0): ?>
    <div class="idx-dishes-grid">
        <?php
        $dishIcons = [
            'momos'    => '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>',
            'default'  => '<path d="M3 6h18M3 12h18M3 18h18"/>',
        ];
        while ($dish = $top_dishes->fetch_assoc()):
            $avg = round((float)$dish['avg_rating'], 1);
            $avgInt = round($avg);
        ?>
        <div class="idx-dish-card">
            <div class="idx-dish-visual">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--pink-accent)" stroke-width="1.5">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M8 12s1.5 2 4 2 4-2 4-2"/>
                    <line x1="9" y1="9" x2="9.01" y2="9"/>
                    <line x1="15" y1="9" x2="15.01" y2="9"/>
                </svg>
            </div>
            <div class="idx-dish-body">
                <div class="idx-dish-name"><?= htmlspecialchars($dish['name']) ?></div>
                <div class="idx-dish-desc"><?= htmlspecialchars($dish['description']) ?></div>
                <div class="idx-dish-footer">
                    <div class="idx-dish-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <svg width="14" height="14" viewBox="0 0 24 24"
                             fill="<?= $i <= $avgInt ? '#f5c842' : 'none' ?>"
                             stroke="#f5c842" stroke-width="2">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                        <?php endfor; ?>
                        <span class="idx-dish-rcount"><?= $avg ?> &bull; <?= $dish['review_count'] ?> review<?= $dish['review_count'] != 1 ? 's' : '' ?></span>
                    </div>
                    <div class="idx-dish-price">&#8377;<?= number_format($dish['price'], 2) ?></div>
                </div>
            </div>
            <div class="idx-dish-action">
                <a href="review.php?dish_id=<?= $dish['id'] ?>" class="btn btn-soft btn-sm" style="width:100%;justify-content:center;">
                    View Reviews
                </a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>

    <div style="text-align:center;margin-top:2.5rem;">
        <a href="menu.php" class="btn btn-primary">
            See Full Menu
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
    </div>
</section>

<!-- ============================================================
     CTA BANNER
     ============================================================ -->
<?php if (!isset($_SESSION['user_id'])): ?>
<section class="idx-cta reveal-section">
    <div class="idx-cta-inner">
        <div class="idx-cta-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <h2 class="idx-cta-title">Join Your Classmates Today</h2>
        <p class="idx-cta-desc">Create a free account to rate dishes, write reviews, and be part of the ADBU food community.</p>
        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;margin-top:2rem;">
            <a href="register.php" class="btn" style="background:white;color:var(--pink-accent);font-weight:800;">
                Create Free Account
            </a>
            <a href="login.php" class="btn btn-outline" style="border-color:rgba(255,255,255,0.6);color:white;">
                Log In
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================
     PAGE STYLES (scoped to index)
     ============================================================ -->
<style>
/* ── HERO ── */
.idx-hero {
    min-height: 88vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 5rem 2rem 4rem;
    position: relative;
    overflow: hidden;
}
.idx-hero-bg {
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 60% 50% at 20% 30%, var(--pink) 0%, transparent 70%),
        radial-gradient(ellipse 50% 40% at 80% 70%, var(--blue) 0%, transparent 70%);
    opacity: .55;
    pointer-events: none;
}
.idx-hero-inner { position: relative; z-index: 1; max-width: 680px; }
.idx-hero-label {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: var(--surface);
    border: 1.5px solid var(--pink-dark);
    border-radius: 99px;
    padding: 6px 18px;
    font-size: .78rem;
    font-weight: 700;
    color: var(--pink-accent);
    letter-spacing: .6px;
    text-transform: uppercase;
    margin-bottom: 1.5rem;
}
.idx-hero-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.2rem, 5.5vw, 3.8rem);
    font-weight: 700;
    line-height: 1.18;
    color: var(--text);
    margin-bottom: 1.25rem;
}
.idx-hero-title span {
    background: linear-gradient(135deg, var(--pink-accent), var(--blue-accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.idx-hero-desc {
    font-size: 1.05rem;
    color: var(--text-muted);
    line-height: 1.75;
    max-width: 520px;
    margin: 0 auto 2.25rem;
}
.idx-hero-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
.idx-scroll-cue {
    position: absolute;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    width: 40px; height: 40px;
    border-radius: 50%;
    background: var(--surface);
    border: 1.5px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    animation: bounce 2s ease-in-out infinite;
    transition: all .25s;
    z-index: 2;
}
.idx-scroll-cue:hover { background: var(--pink); border-color: var(--pink-dark); color: var(--text); }
@keyframes bounce { 0%,100%{transform:translateX(-50%) translateY(0)} 50%{transform:translateX(-50%) translateY(6px)} }

/* ── STATS ── */
.idx-stats {
    background: linear-gradient(135deg, var(--pink-accent), var(--blue-accent));
    padding: 2.5rem 2rem;
}
.idx-stats-inner {
    max-width: 700px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    flex-wrap: wrap;
}
.idx-stat {
    flex: 1;
    min-width: 140px;
    text-align: center;
    padding: 0 1.5rem;
}
.idx-stat-icon {
    width: 44px; height: 44px;
    background: rgba(255,255,255,.2);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto .7rem;
    color: white;
}
.idx-stat-num {
    font-family: 'Playfair Display', serif;
    font-size: 2.2rem;
    font-weight: 700;
    color: white;
    line-height: 1;
    margin-bottom: .25rem;
}
.idx-stat-lbl { font-size: .82rem; color: rgba(255,255,255,.85); font-weight: 600; letter-spacing: .4px; }
.idx-stat-divider { width: 1px; height: 60px; background: rgba(255,255,255,.25); flex-shrink: 0; }

/* ── SPLIT SECTIONS ── */
.idx-split {
    display: flex;
    align-items: center;
    gap: 4rem;
    padding: 5rem 4rem;
    max-width: 1200px;
    margin: 0 auto;
}
.idx-split-reverse { flex-direction: row-reverse; }
.idx-split-alt { background: var(--surface2); max-width: 100%; }
.idx-split-alt > .idx-split-text,
.idx-split-alt > .idx-split-visual { max-width: 560px; }
.idx-split-alt { padding: 5rem 4rem; }
.idx-split-alt .idx-split-text,
.idx-split-alt .idx-split-visual { margin: 0 auto; }
.idx-split-alt { display: flex; align-items: center; justify-content: center; gap: 4rem; flex-wrap: wrap; }

.idx-split-visual, .idx-split-text { flex: 1; min-width: 280px; }
.idx-section-label {
    display: inline-block;
    font-size: .75rem;
    font-weight: 800;
    color: var(--pink-accent);
    letter-spacing: 1px;
    text-transform: uppercase;
    margin-bottom: .75rem;
    padding: 4px 12px;
    background: var(--pink);
    border-radius: 99px;
}
[data-theme="dark"] .idx-section-label { background: var(--surface2); }
.idx-split-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.7rem, 3vw, 2.4rem);
    font-weight: 700;
    line-height: 1.25;
    color: var(--text);
    margin-bottom: 1.25rem;
}
.idx-split-desc {
    font-size: .98rem;
    color: var(--text-muted);
    line-height: 1.8;
    max-width: 460px;
}

/* Visual grid (section 1) */
.idx-visual-about {
    background: linear-gradient(135deg, var(--pink), var(--blue));
    border-radius: var(--radius);
    padding: 2.5rem;
    min-height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.idx-visual-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    width: 100%;
}
.idx-vg-card {
    background: var(--surface);
    border-radius: var(--radius-sm);
    padding: 1.25rem 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .6rem;
    font-size: .85rem;
    font-weight: 700;
    color: var(--text);
    box-shadow: 0 4px 12px var(--shadow);
    animation: fadeInUp .5s ease both;
    animation-delay: var(--d, 0s);
    transition: transform .25s;
}
.idx-vg-card:hover { transform: translateY(-3px); }

/* Visual mock reviews (section 2) */
.idx-visual-reviews {
    position: relative;
    min-height: 280px;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    justify-content: center;
}
.idx-mock-card {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    padding: 1.25rem 1.5rem;
    box-shadow: 0 6px 24px var(--shadow);
}
.idx-mock-card-2 { margin-left: 2rem; }
.idx-mock-header { display: flex; align-items: center; gap: 10px; margin-bottom: .6rem; }
.idx-mock-avatar {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--pink), var(--pink-dark));
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: .85rem; color: var(--text); flex-shrink: 0;
}
.idx-mock-name  { font-weight: 800; font-size: .9rem; }
.idx-mock-stars { display: flex; gap: 2px; margin-top: 2px; }
.idx-mock-text  { font-size: .85rem; color: var(--text-muted); line-height: 1.6; font-style: italic; }
.idx-mock-dish  { font-size: .75rem; font-weight: 700; color: var(--pink-accent); margin-top: .5rem; }

/* ── TOP DISHES ── */
.idx-top-dishes {
    padding: 5rem 2rem;
    max-width: 1200px;
    margin: 0 auto;
}
.idx-section-header { text-align: center; margin-bottom: 3rem; }
.idx-dishes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1.5rem;
}
.idx-dish-card {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: 0 4px 16px var(--shadow);
    transition: transform .3s, box-shadow .3s;
    display: flex;
    flex-direction: column;
}
.idx-dish-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px var(--shadow-md); }
.idx-dish-visual {
    height: 140px;
    background: linear-gradient(135deg, var(--pink), var(--blue));
    display: flex;
    align-items: center;
    justify-content: center;
}
.idx-dish-body { padding: 1.25rem; flex: 1; display: flex; flex-direction: column; gap: .5rem; }
.idx-dish-name  { font-weight: 800; font-size: 1.05rem; }
.idx-dish-desc  { font-size: .82rem; color: var(--text-muted); line-height: 1.5; flex: 1; }
.idx-dish-footer { display: flex; align-items: center; justify-content: space-between; margin-top: .5rem; flex-wrap: wrap; gap: .5rem; }
.idx-dish-stars { display: flex; align-items: center; gap: 3px; }
.idx-dish-rcount { font-size: .75rem; color: var(--text-muted); font-weight: 600; margin-left: 4px; }
.idx-dish-price { font-weight: 800; color: var(--pink-accent); font-size: 1rem; }
.idx-dish-action { padding: 0 1.25rem 1.25rem; }

/* ── CTA ── */
.idx-cta {
    background: linear-gradient(135deg, var(--pink-accent), var(--blue-accent));
    padding: 5rem 2rem;
    text-align: center;
}
.idx-cta-inner { max-width: 560px; margin: 0 auto; }
.idx-cta-icon {
    width: 64px; height: 64px;
    background: rgba(255,255,255,.2);
    border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.5rem;
}
.idx-cta-title { font-family: 'Playfair Display', serif; font-size: clamp(1.6rem, 3vw, 2.2rem); font-weight: 700; color: white; margin-bottom: .75rem; }
.idx-cta-desc  { color: rgba(255,255,255,.88); font-size: .98rem; line-height: 1.7; }

/* ── SCROLL REVEAL ── */
.reveal-section { opacity: 0; transform: translateY(30px); transition: opacity .7s ease, transform .7s ease; }
.reveal-section.in-view { opacity: 1; transform: translateY(0); }

/* ── RESPONSIVE ── */
@media (max-width: 900px) {
    .idx-split        { flex-direction: column !important; padding: 3rem 1.5rem; gap: 2.5rem; }
    .idx-split-alt    { padding: 3rem 1.5rem; }
    .idx-split-visual { min-height: 220px; }
    .idx-mock-card-2  { margin-left: .5rem; }
}
@media (max-width: 600px) {
    .idx-hero    { min-height: 80vh; padding: 4rem 1.25rem 3rem; }
    .idx-stats-inner { gap: 0; }
    .idx-stat-divider { display: none; }
    .idx-stat    { flex: 0 0 50%; padding: 1rem; }
    .idx-top-dishes { padding: 3rem 1rem; }
    .idx-cta     { padding: 3.5rem 1.25rem; }
    .idx-hero-actions { flex-direction: column; align-items: center; }
    .btn-lg { width: 100%; max-width: 320px; justify-content: center; }
}
</style>

<script>
// Scroll-reveal using IntersectionObserver
(function () {
    const els = document.querySelectorAll('.reveal-section');
    if (!els.length) return;
    const io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (e.isIntersecting) {
                e.target.classList.add('in-view');
                io.unobserve(e.target);
            }
        });
    }, { threshold: 0.12 });
    els.forEach(function (el) { io.observe(el); });
})();
</script>

<?php require_once 'includes/footer.php'; ?>
