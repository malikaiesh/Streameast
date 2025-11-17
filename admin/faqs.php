<?php
require_once '../config/config.php';
require_once '../includes/FAQ.php';

Security::requireAdmin();

$faq = new FAQ();
$success = '';
$error = '';

// Handle POST requests (add/edit/delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (Security::verifyCSRFToken($csrf_token)) {
        // Handle delete
        if (isset($_POST['delete'])) {
            $faq->delete($_POST['delete']);
            $success = 'FAQ deleted successfully!';
        }
        // Handle add/edit
        elseif (isset($_POST['question'])) {
            $data = [
                'question' => $_POST['question'] ?? '',
                'answer' => $_POST['answer'] ?? '',
                'display_order' => $_POST['display_order'] ?? 0,
                'is_active' => isset($_POST['is_active'])
            ];
            
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                // Update
                $faq->update($_POST['id'], $data);
                $success = 'FAQ updated successfully!';
            } else {
                // Create
                $faq->create($data);
                $success = 'FAQ created successfully!';
            }
        }
    }
}

$faqs = $faq->getAll();
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
    align-items: flex-start;
}

.content-item:last-child {
    border-bottom: none;
}

.content-info h3 {
    margin: 0 0 8px 0;
    color: #333;
    font-size: 16px;
}

.content-meta {
    color: #666;
    font-size: 14px;
}

.content-actions {
    display: flex;
    gap: 8px;
    flex-shrink: 0;
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
    min-height: 150px;
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

#faqForm {
    display: none;
}

#faqForm.show {
    display: block;
}
</style>

<div class="admin-header-row">
    <h2>❓ FAQs Management</h2>
    <button onclick="showForm()" class="btn btn-primary">+ Add New FAQ</button>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?= Security::output($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= Security::output($error) ?></div>
<?php endif; ?>

<!-- Add/Edit Form -->
<div id="faqForm" class="form-section">
    <h3 id="formTitle">Add New FAQ</h3>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <input type="hidden" name="id" id="faqId" value="">
        
        <div class="form-group">
            <label for="question">Question *</label>
            <input type="text" id="question" name="question" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="answer">Answer * (HTML allowed)</label>
            <textarea id="answer" name="answer" class="form-control" required></textarea>
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
            <button type="submit" class="btn btn-primary">Save FAQ</button>
            <button type="button" onclick="hideForm()" class="btn btn-secondary">Cancel</button>
        </div>
    </form>
</div>

<!-- FAQ List -->
<?php if (empty($faqs)): ?>
<div class="empty-state">
    <h3>No FAQs yet</h3>
    <p>Click "Add New FAQ" to create your first frequently asked question.</p>
</div>
<?php else: ?>
<div class="content-list">
    <?php foreach ($faqs as $faqItem): ?>
    <div class="content-item">
        <div class="content-info" style="flex: 1;">
            <h3>
                <?= Security::output($faqItem['question']) ?>
                <?php if ($faqItem['is_active']): ?>
                    <span class="badge badge-success">Active</span>
                <?php else: ?>
                    <span class="badge badge-secondary">Inactive</span>
                <?php endif; ?>
            </h3>
            <div class="content-meta">
                Order: <?= $faqItem['display_order'] ?> | 
                Created: <?= date('M d, Y', strtotime($faqItem['created_at'])) ?>
            </div>
        </div>
        <div class="content-actions">
            <button onclick="editFAQ(<?= htmlspecialchars(json_encode($faqItem), ENT_QUOTES, 'UTF-8') ?>)" class="btn btn-secondary">Edit</button>
            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this FAQ?')">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="delete" value="<?= $faqItem['id'] ?>">
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
function showForm() {
    document.getElementById('faqForm').classList.add('show');
    document.getElementById('formTitle').textContent = 'Add New FAQ';
    document.getElementById('faqId').value = '';
    document.getElementById('question').value = '';
    document.getElementById('answer').value = '';
    document.getElementById('display_order').value = '0';
    document.getElementById('is_active').checked = true;
}

function hideForm() {
    document.getElementById('faqForm').classList.remove('show');
}

function editFAQ(faqItem) {
    document.getElementById('faqForm').classList.add('show');
    document.getElementById('formTitle').textContent = 'Edit FAQ';
    document.getElementById('faqId').value = faqItem.id;
    document.getElementById('question').value = faqItem.question;
    document.getElementById('answer').value = faqItem.answer;
    document.getElementById('display_order').value = faqItem.display_order;
    document.getElementById('is_active').checked = faqItem.is_active;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<?php include 'views/footer.php'; ?>
