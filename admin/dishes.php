<?php
/**
 * admin/dishes.php — Add / Edit / Delete dishes
 */
$pageTitle = 'Manage Dishes';
require_once '../config/db.php';
require_once 'includes/admin_header.php';

$msg = $msgType = '';

// ── DELETE ──
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $del = $conn->prepare("DELETE FROM dishes WHERE id=?");
    $del->bind_param('i', $id); $del->execute(); $del->close();
    $msg = 'Dish deleted successfully.'; $msgType = 'success';
}

// ── ADD / EDIT ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = isset($_POST['dish_id']) && is_numeric($_POST['dish_id']) ? (int)$_POST['dish_id'] : 0;
    $name  = trim($_POST['name']  ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $image = trim($_POST['image'] ?? '');

    if (empty($name) || empty($desc) || $price <= 0) {
        $msg = 'Please fill all required fields with valid values.'; $msgType = 'error';
    } else {
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE dishes SET name=?, description=?, price=?, image=? WHERE id=?");
            $stmt->bind_param('ssdsi', $name, $desc, $price, $image, $id);
            $stmt->execute(); $stmt->close();
            $msg = 'Dish updated successfully.'; $msgType = 'success';
        } else {
            $stmt = $conn->prepare("INSERT INTO dishes (name, description, price, image) VALUES (?,?,?,?)");
            $stmt->bind_param('ssds', $name, $desc, $price, $image);
            $stmt->execute(); $stmt->close();
            $msg = 'New dish added successfully.'; $msgType = 'success';
        }
    }
}

// ── FETCH for edit ──
$editDish = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM dishes WHERE id=?");
    $stmt->bind_param('i', (int)$_GET['edit']);
    $stmt->execute();
    $editDish = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// ── FETCH all dishes ──
$dishes = $conn->query("
    SELECT d.*, COALESCE(AVG(r.rating),0) AS avg_r, COUNT(r.id) AS rev_cnt
    FROM dishes d LEFT JOIN reviews r ON d.id = r.dish_id
    GROUP BY d.id ORDER BY d.id
");

$dishImages = [
    'momos'=>'momos.svg','fried rice'=>'fried_rice.svg','maggi'=>'maggi.svg',
    'samosa'=>'samosa.svg','sandwich'=>'sandwich.svg','tea'=>'tea.svg','coffee'=>'coffee.svg',
];
function getThumb(string $name, ?string $img): ?string {
    global $dishImages;
    if (!empty($img) && file_exists($_SERVER['DOCUMENT_ROOT'].'/canteen_project/images/'.$img))
        return '/canteen_project/images/'.$img;
    $lower = strtolower($name);
    foreach ($dishImages as $k => $f) {
        if (str_contains($lower, $k)) return '/canteen_project/images/'.$f;
    }
    return null;
}
?>

<?php if ($msg): ?>
<div class="alert alert-<?= $msgType === 'success' ? 'success' : 'error' ?>" style="margin-bottom:1.25rem;" data-auto-dismiss>
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <?php if ($msgType === 'success'): ?><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
        <?php else: ?><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        <?php endif; ?>
    </svg>
    <?= htmlspecialchars($msg) ?>
</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 380px;gap:1.75rem;align-items:start;">

    <!-- Dishes Table -->
    <div>
        <div class="admin-section-header">
            <div class="admin-section-title">All Dishes</div>
            <button class="btn btn-primary btn-sm" onclick="openAddModal()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Dish
            </button>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Dish</th>
                        <th>Price</th>
                        <th>Rating</th>
                        <th>Reviews</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dishes && $dishes->num_rows > 0):
                        while ($d = $dishes->fetch_assoc()):
                            $thumb = getThumb($d['name'], $d['image']);
                            $avg   = round((float)$d['avg_r'], 1);
                    ?>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="admin-dish-thumb">
                                    <?php if ($thumb): ?>
                                        <img src="<?= $thumb ?>" alt="<?= htmlspecialchars($d['name']) ?>">
                                    <?php else: ?>
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--pink-accent)" stroke-width="1.8"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div style="font-weight:800;"><?= htmlspecialchars($d['name']) ?></div>
                                    <div style="font-size:.75rem;color:var(--text-muted);"><?= htmlspecialchars(substr($d['description'],0,40)) ?>...</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="admin-badge badge-pink">&#8377;<?= number_format($d['price'],2) ?></span></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:4px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="<?= $avg>0?'#f5c842':'none' ?>" stroke="#f5c842" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                <span style="font-weight:700;font-size:.85rem;"><?= $avg > 0 ? $avg : 'N/A' ?></span>
                            </div>
                        </td>
                        <td><span class="admin-badge badge-blue"><?= $d['rev_cnt'] ?></span></td>
                        <td>
                            <div style="display:flex;gap:.4rem;">
                                <button class="btn btn-soft btn-sm"
                                    onclick="openEditModal(<?= $d['id'] ?>, '<?= addslashes($d['name']) ?>', '<?= addslashes($d['description']) ?>', '<?= $d['price'] ?>', '<?= addslashes($d['image'] ?? '') ?>')">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Edit
                                </button>
                                <button class="btn btn-sm" style="background:#fde8ec;color:#c03040;border:none;"
                                    onclick="confirmDelete(<?= $d['id'] ?>, '<?= addslashes($d['name']) ?>')">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="5" class="admin-empty">No dishes found. Add one!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick add / instructions -->
    <div class="admin-table-wrap" style="padding:1.5rem;">
        <div class="admin-section-title" style="font-size:1rem;margin-bottom:1rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--pink-accent)" stroke-width="2" style="margin-right:6px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            Quick Add Dish
        </div>
        <form method="POST" id="quickAddForm">
            <input type="hidden" name="dish_id" value="0">
            <div class="form-group">
                <label class="form-label">Dish Name *</label>
                <input type="text" class="form-control" name="name" placeholder="e.g. Biryani" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description *</label>
                <textarea class="form-control" name="description" rows="2" placeholder="Short description..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Price (&#8377;) *</label>
                <input type="number" class="form-control" name="price" min="1" step="0.01" placeholder="e.g. 50">
            </div>
            <div class="form-group">
                <label class="form-label">Image filename <span style="color:var(--text-muted);font-weight:400;">(optional)</span></label>
                <input type="text" class="form-control" name="image" placeholder="e.g. biryani.jpg">
                <div style="font-size:.75rem;color:var(--text-muted);margin-top:4px;">Place image in <code>images/</code> folder first</div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Dish
            </button>
        </form>
    </div>
</div>

<!-- ── EDIT MODAL ── -->
<div class="admin-modal-overlay" id="editModal">
    <div class="admin-modal">
        <div class="admin-modal-title">
            <div class="admin-modal-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </div>
            Edit Dish
        </div>
        <form method="POST" id="editForm">
            <input type="hidden" name="dish_id" id="edit_id">
            <div class="form-group">
                <label class="form-label">Dish Name</label>
                <input type="text" class="form-control" name="name" id="edit_name" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" id="edit_desc" rows="2"></textarea>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                <div class="form-group">
                    <label class="form-label">Price (&#8377;)</label>
                    <input type="number" class="form-control" name="price" id="edit_price" min="1" step="0.01">
                </div>
                <div class="form-group">
                    <label class="form-label">Image file</label>
                    <input type="text" class="form-control" name="image" id="edit_image" placeholder="filename.jpg">
                </div>
            </div>
            <div class="admin-modal-actions">
                <button type="button" class="btn btn-soft" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ── DELETE CONFIRM ── -->
<div class="admin-confirm-overlay" id="deleteConfirm">
    <div class="admin-confirm-box">
        <div class="admin-confirm-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#c03040" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
        </div>
        <div class="admin-confirm-title">Delete Dish?</div>
        <div class="admin-confirm-desc" id="deleteMsg">This will permanently remove the dish and all its reviews.</div>
        <div class="admin-confirm-actions">
            <button class="btn btn-soft" onclick="closeModal('deleteConfirm')">Cancel</button>
            <a id="deleteConfirmBtn" href="#" class="btn" style="background:#e83050;color:white;">Delete</a>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    // Reset quick add form and scroll to it
    document.getElementById('quickAddForm').reset();
    document.getElementById('quickAddForm').scrollIntoView({behavior:'smooth'});
}
function openEditModal(id, name, desc, price, image) {
    document.getElementById('edit_id').value    = id;
    document.getElementById('edit_name').value  = name;
    document.getElementById('edit_desc').value  = desc;
    document.getElementById('edit_price').value = price;
    document.getElementById('edit_image').value = image;
    document.getElementById('editModal').classList.add('open');
}
function confirmDelete(id, name) {
    document.getElementById('deleteMsg').textContent = 'Delete "' + name + '"? This will also remove all its reviews.';
    document.getElementById('deleteConfirmBtn').href = 'dishes.php?delete=' + id;
    document.getElementById('deleteConfirm').classList.add('open');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('open');
}
// Close modals on overlay click
['editModal','deleteConfirm'].forEach(function(id) {
    document.getElementById(id)?.addEventListener('click', function(e) {
        if (e.target === this) closeModal(id);
    });
});
</script>

<style>
@media (max-width: 900px) {
    .admin-content > div[style*="grid-template-columns:1fr 380px"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once 'includes/admin_footer.php'; ?>
