<?php
require_once '../config/config.php';
require_once '../includes/HomeContent.php';

Security::requireAdmin();

$homeContent = new HomeContent();
$success = '';
$error = '';

// Handle POST requests (add/edit/delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (Security::verifyCSRFToken($csrf_token)) {
        // Handle delete
        if (isset($_POST['delete'])) {
            $homeContent->delete($_POST['delete']);
            $success = 'Content section deleted successfully!';
        }
        // Handle add/edit
        elseif (isset($_POST['title'])) {
            $data = [
                'title' => $_POST['title'] ?? '',
                'content' => $_POST['content'] ?? '',
                'display_order' => $_POST['display_order'] ?? 0,
                'is_active' => isset($_POST['is_active'])
            ];
            
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                // Update
                $homeContent->update($_POST['id'], $data);
                $success = 'Content section updated successfully!';
            } else {
                // Create
                $homeContent->create($data);
                $success = 'Content section created successfully!';
            }
        }
    }
}

$sections = $homeContent->getAll();
$csrf_token = Security::generateCSRFToken();
include 'views/header.php';
?>

<style>
.content-list {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.content-item {
    padding: 20px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.content-item:last-child {
    border-bottom: none;
}

.content-info h3 {
    margin: 0 0 8px 0;
    color: #333;
    font-size: 18px;
}

.content-meta {
    color: #666;
    font-size: 14px;
}

.content-actions {
    display: flex;
    gap: 8px;
}

.badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.badge-success {
    background: #d4edda;
    color: #155724;
}

.badge-secondary {
    background: #e2e3e5;
    color: #383d41;
}

.form-section {
    background: white;
    padding: 24px;
    border-radius: 8px;
    margin-bottom: 24px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
}

textarea.form-control {
    min-height: 200px;
    font-family: inherit;
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.checkbox-group input[type="checkbox"] {
    width: auto;
}

#contentForm {
    display: none;
}

#contentForm.show {
    display: block;
}
</style>

<div class="admin-header-row">
    <h2>📝 Home Content Sections</h2>
    <button onclick="showForm()" class="btn btn-primary">+ Add New Section</button>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?= Security::output($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= Security::output($error) ?></div>
<?php endif; ?>

<!-- Add/Edit Form -->
<div id="contentForm" class="form-section">
    <h3 id="formTitle">Add New Content Section</h3>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <input type="hidden" name="id" id="sectionId" value="">
        
        <div class="form-group">
            <label for="title">Title *</label>
            <input type="text" id="title" name="title" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="content">Content * (HTML allowed)</label>
            <textarea id="content" name="content" class="form-control" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="display_order">Display Order</label>
            <input type="number" id="display_order" name="display_order" class="form-control" value="0">
            <small>Lower numbers appear first</small>
        </div>
        
        <div class="form-group">
            <div class="checkbox-group">
                <input type="checkbox" id="is_active" name="is_active" checked>
                <label for="is_active" style="margin: 0;">Active (Show on homepage)</label>
            </div>
        </div>
        
        <div style="display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary">Save Section</button>
            <button type="button" onclick="hideForm()" class="btn btn-secondary">Cancel</button>
        </div>
    </form>
</div>

<!-- Content List -->
<?php if (empty($sections)): ?>
<div class="empty-state">
    <h3>No content sections yet</h3>
    <p>Click "Add New Section" to create your first home content section.</p>
</div>
<?php else: ?>
<div class="content-list">
    <?php foreach ($sections as $section): ?>
    <div class="content-item">
        <div class="content-info">
            <h3>
                <?= Security::output($section['title']) ?>
                <?php if ($section['is_active']): ?>
                    <span class="badge badge-success">Active</span>
                <?php else: ?>
                    <span class="badge badge-secondary">Inactive</span>
                <?php endif; ?>
            </h3>
            <div class="content-meta">
                Order: <?= $section['display_order'] ?> | 
                Created: <?= date('M d, Y', strtotime($section['created_at'])) ?>
            </div>
        </div>
        <div class="content-actions">
            <button onclick="editSection(<?= htmlspecialchars(json_encode($section), ENT_QUOTES, 'UTF-8') ?>)" class="btn btn-secondary">Edit</button>
            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this section?')">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="delete" value="<?= $section['id'] ?>">
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
function showForm() {
    document.getElementById('contentForm').classList.add('show');
    document.getElementById('formTitle').textContent = 'Add New Content Section';
    document.getElementById('sectionId').value = '';
    document.getElementById('title').value = '';
    document.getElementById('content').value = '';
    document.getElementById('display_order').value = '0';
    document.getElementById('is_active').checked = true;
}

function hideForm() {
    document.getElementById('contentForm').classList.remove('show');
}

function editSection(section) {
    document.getElementById('contentForm').classList.add('show');
    document.getElementById('formTitle').textContent = 'Edit Content Section';
    document.getElementById('sectionId').value = section.id;
    document.getElementById('title').value = section.title;
    document.getElementById('content').value = section.content;
    document.getElementById('display_order').value = section.display_order;
    document.getElementById('is_active').checked = section.is_active;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<?php include 'views/footer.php'; ?>
