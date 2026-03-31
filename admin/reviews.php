<?php
/**
 * admin/reviews.php — View and delete reviews
 */
$pageTitle = 'Manage Reviews';
require_once '../config/db.php';
require_once 'includes/admin_header.php';

$msg = $msgType = '';

// Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id  = (int)$_GET['delete'];
    $del = $conn->prepare("DELETE FROM reviews WHERE id=?");
    $del->bind_param('i', $id); $del->execute(); $del->close();
    $msg = 'Review deleted.'; $msgType = 'success';
}

// Filter
$filterDish = isset($_GET['dish_id']) && is_numeric($_GET['dish_id']) ? (int)$_GET['dish_id'] : 0;
$filterStar = isset($_GET['stars'])   && is_numeric($_GET['stars'])   ? (int)$_GET['stars']   : 0;

$where = 'WHERE 1=1';
$params = []; $types = '';
if ($filterDish > 0) { $where .= ' AND r.dish_id=?'; $types .= 'i'; $params[] = $filterDish; }
if ($filterStar > 0) { $where .= ' AND r.rating=?';  $types .= 'i'; $params[] = $filterStar; }

$sql = "SELECT r.*, u.name AS user_name, d.name AS dish_name
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        JOIN dishes d ON r.dish_id = d.id
        $where ORDER BY r.created_at DESC";

if ($types) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $reviews = $stmt->get_result();
    $stmt->close();
} else {
    $reviews = $conn->query($sql);
}

$dishes = $conn->query("SELECT id, name FROM dishes ORDER BY name");
$totalReviews = $conn->query("SELECT COUNT(*) FROM reviews")->fetch_row()[0];
?>

<?php if ($msg): ?>
<div class="alert alert-<?= $msgType === 'success' ? 'success' : 'error' ?>" data-auto-dismiss style="margin-bottom:1.25rem;">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    <?= htmlspecialchars($msg) ?>
</div>
<?php endif; ?>

<!-- Filter bar -->
<form method="GET" action="reviews.php" style="display:flex;gap:.75rem;align-items:flex-end;flex-wrap:wrap;margin-bottom:1.5rem;">
    <div class="form-group" style="margin:0;flex:1;min-width:160px;">
        <label class="form-label" style="margin-bottom:4px;">Filter by Dish</label>
        <select name="dish_id" class="form-control" style="height:40px;">
            <option value="0">All Dishes</option>
            <?php $dishes->data_seek(0); while ($d = $dishes->fetch_assoc()): ?>
            <option value="<?= $d['id'] ?>" <?= $filterDish === $d['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($d['name']) ?>
            </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="form-group" style="margin:0;flex:1;min-width:130px;">
        <label class="form-label" style="margin-bottom:4px;">Filter by Stars</label>
        <select name="stars" class="form-control" style="height:40px;">
            <option value="0">All Ratings</option>
            <?php for ($i=5;$i>=1;$i--): ?>
            <option value="<?= $i ?>" <?= $filterStar === $i ? 'selected' : '' ?>><?= $i ?> Star<?= $i>1?'s':'' ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary btn-sm" style="height:40px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        Filter
    </button>
    <?php if ($filterDish || $filterStar): ?>
    <a href="reviews.php" class="btn btn-soft btn-sm" style="height:40px;">Clear</a>
    <?php endif; ?>
</form>

<div class="admin-section-header">
    <div class="admin-section-title">All Reviews <span style="font-size:.8rem;color:var(--text-muted);font-weight:600;font-family:var(--font-body);">(<?= $totalReviews ?> total)</span></div>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Dish</th>
                <th>Rating</th>
                <th>Review</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($reviews && $reviews->num_rows > 0):
                while ($rev = $reviews->fetch_assoc()):
                    $initials = strtoupper(substr($rev['user_name'], 0, 1));
            ?>
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div class="rv-avatar" style="width:30px;height:30px;font-size:.75rem;"><?= $initials ?></div>
                        <span style="font-weight:700;"><?= htmlspecialchars($rev['user_name']) ?></span>
                    </div>
                </td>
                <td><span class="admin-badge badge-pink"><?= htmlspecialchars($rev['dish_name']) ?></span></td>
                <td>
                    <div style="display:flex;gap:2px;">
                        <?php for ($i=1;$i<=5;$i++): ?>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="<?= $i<=(int)$rev['rating']?'#f5c842':'none' ?>" stroke="#f5c842" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <?php endfor; ?>
                    </div>
                </td>
                <td style="max-width:260px;color:var(--text-muted);font-size:.82rem;">
                    <?= htmlspecialchars(substr($rev['review_text'], 0, 80)) ?><?= strlen($rev['review_text']) > 80 ? '...' : '' ?>
                </td>
                <td style="white-space:nowrap;font-size:.8rem;color:var(--text-muted);">
                    <?= date('d M Y', strtotime($rev['created_at'])) ?>
                </td>
                <td>
                    <button class="btn btn-sm" style="background:#fde8ec;color:#c03040;border:none;"
                        onclick="confirmDelete(<?= $rev['id'] ?>, '<?= addslashes($rev['user_name']) ?>')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                        Delete
                    </button>
                </td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="6" class="admin-empty">No reviews found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Confirm delete -->
<div class="admin-confirm-overlay" id="deleteConfirm">
    <div class="admin-confirm-box">
        <div class="admin-confirm-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#c03040" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
        </div>
        <div class="admin-confirm-title">Delete Review?</div>
        <div class="admin-confirm-desc" id="deleteMsg">This action cannot be undone.</div>
        <div class="admin-confirm-actions">
            <button class="btn btn-soft" onclick="document.getElementById('deleteConfirm').classList.remove('open')">Cancel</button>
            <a id="deleteBtn" href="#" class="btn" style="background:#e83050;color:white;">Delete</a>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteMsg').textContent = 'Delete review by "' + name + '"? This cannot be undone.';
    document.getElementById('deleteBtn').href = 'reviews.php?delete=' + id + '<?= $filterDish ? "&dish_id=$filterDish" : "" ?><?= $filterStar ? "&stars=$filterStar" : "" ?>';
    document.getElementById('deleteConfirm').classList.add('open');
}
document.getElementById('deleteConfirm')?.addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
});
</script>

<?php require_once 'includes/admin_footer.php'; ?>
