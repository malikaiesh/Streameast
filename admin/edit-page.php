<?php
require_once '../config/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../includes/Page.php';
require_once '../includes/Security.php';

$pageModel = new Page();
$success = '';
$error = '';
$isEdit = false;
$pageData = [
    'title' => '',
    'slug' => '',
    'content' => '',
    'meta_description' => '',
    'status' => 'published'
];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $isEdit = true;
    $pageData = $pageModel->getById($_GET['id']);
    if (!$pageData) {
        header('Location: pages.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $content = $_POST['content'] ?? '';
    $meta_description = trim($_POST['meta_description'] ?? '');
    $status = $_POST['status'] ?? 'published';

    if (empty($title)) {
        $error = 'Page title is required.';
    } elseif (empty($slug)) {
        $slug = $pageModel->generateSlug($title, $isEdit ? $pageData['id'] : null);
    } else {
        $slug = $pageModel->generateSlug($slug, $isEdit ? $pageData['id'] : null);
    }

    if (!$error) {
        $sanitizedContent = Security::sanitizeHtml($content);
        
        $data = [
            'title' => $title,
            'slug' => $slug,
            'content' => $sanitizedContent,
            'meta_description' => $meta_description,
            'status' => $status
        ];

        if ($isEdit) {
            if ($pageModel->update($pageData['id'], $data)) {
                $success = 'Page updated successfully!';
                $pageData = $pageModel->getById($pageData['id']);
            } else {
                $error = 'Failed to update page.';
            }
        } else {
            if ($pageModel->create($data)) {
                $success = 'Page created successfully!';
                $pageData = $data;
                $isEdit = true;
            } else {
                $error = 'Failed to create page.';
            }
        }
    }
}

$pageTitle = $isEdit ? 'Edit Page' : 'Add New Page';
include 'views/header.php';
?>

<div class="admin-content">
    <div class="content-header">
        <h1><?= $isEdit ? '✏️ Edit Page' : '➕ Add New Page' ?></h1>
        <a href="pages.php" class="btn btn-secondary">← Back to Pages</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="page-form">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label for="title">Page Title *</label>
                    <input type="text" id="title" name="title" required 
                           value="<?= htmlspecialchars($pageData['title']) ?>"
                           placeholder="e.g., Privacy Policy">
                </div>

                <div class="form-group">
                    <label for="slug">URL Slug *</label>
                    <div class="slug-preview">
                        <span class="slug-prefix">yoursite.com/</span>
                        <input type="text" id="slug" name="slug" 
                               value="<?= htmlspecialchars($pageData['slug']) ?>"
                               placeholder="privacy-policy">
                        <span class="slug-suffix">.php</span>
                    </div>
                    <small>Leave empty to auto-generate from title</small>
                </div>

                <div class="form-group">
                    <label for="meta_description">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" rows="2"
                              placeholder="Brief description for search engines (150-160 characters)"><?= htmlspecialchars($pageData['meta_description']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="content">Page Content *</label>
                    <textarea id="content" name="content" rows="20" required
                              placeholder="Enter your page content here (HTML allowed)"><?= htmlspecialchars($pageData['content']) ?></textarea>
                    <small>HTML is allowed. Dangerous tags and scripts will be automatically removed for security.</small>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="published" <?= $pageData['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                        <option value="draft" <?= $pageData['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?= $isEdit ? '💾 Update Page' : '➕ Create Page' ?>
                    </button>
                    <a href="pages.php" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.slug-preview {
    display: flex;
    align-items: center;
    gap: 5px;
}

.slug-prefix, .slug-suffix {
    color: var(--text-secondary);
    font-size: 14px;
}

.slug-preview input {
    flex: 1;
}

.page-form textarea {
    font-family: 'Courier New', monospace;
    font-size: 14px;
}
</style>

<?php include 'views/footer.php'; ?>
