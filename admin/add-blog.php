<?php
require_once '../config/config.php';
Security::requireAdmin();
require_once '../includes/Blog.php';

$blog = new Blog();
$message = '';
$post = null;

$isEdit = isset($_GET['id']);
if ($isEdit) {
    $post = $blog->getById($_GET['id']);
    if (!$post) {
        header('Location: blogs.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::checkCSRF();
    
    $data = [
        'title' => $_POST['title'],
        'content' => $_POST['content'],
        'excerpt' => $_POST['excerpt'],
        'author_name' => $_POST['author_name'],
        'category' => $_POST['category'],
        'status' => $_POST['status'],
        'meta_title' => $_POST['meta_title'],
        'meta_description' => $_POST['meta_description']
    ];
    
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $upload = $blog->uploadFeaturedImage($_FILES['featured_image']);
        if ($upload['success']) {
            $data['featured_image'] = $upload['path'];
        } else {
            $message = '<div class="alert alert-error">' . $upload['error'] . '</div>';
        }
    } elseif ($isEdit && $post['featured_image']) {
        $data['featured_image'] = $post['featured_image'];
    }
    
    if (empty($message)) {
        if ($isEdit) {
            if ($blog->update($_GET['id'], $data)) {
                header('Location: blogs.php');
                exit;
            } else {
                $message = '<div class="alert alert-error">Failed to update blog post.</div>';
            }
        } else {
            if ($blog->create($data)) {
                header('Location: blogs.php');
                exit;
            } else {
                $message = '<div class="alert alert-error">Failed to create blog post.</div>';
            }
        }
    }
}

include 'views/header.php';
?>

<?php if ($message) echo $message; ?>

<div class="page-header">
    <h1><?= $isEdit ? 'Edit' : 'Add New' ?> Blog Post</h1>
    <a href="blogs.php" class="btn btn-secondary">← Back to Posts</a>
</div>

<form method="POST" enctype="multipart/form-data" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRF() ?>">
    
    <div class="form-row">
        <div class="col-8">
            <div class="card">
                <h3>Content</h3>
                
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" id="title" name="title" required 
                           value="<?= Security::output($post['title'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="excerpt">Excerpt</label>
                    <textarea id="excerpt" name="excerpt" rows="3" 
                              placeholder="Short summary of the blog post"><?= Security::output($post['excerpt'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="content">Content *</label>
                    <textarea id="content" name="content" rows="20" required><?= Security::output($post['content'] ?? '') ?></textarea>
                    <small>Supports HTML formatting</small>
                </div>
            </div>
            
            <div class="card">
                <h3>SEO Settings</h3>
                
                <div class="form-group">
                    <label for="meta_title">Meta Title</label>
                    <input type="text" id="meta_title" name="meta_title" 
                           value="<?= Security::output($post['meta_title'] ?? $post['title'] ?? '') ?>">
                    <small>Recommended: 50-60 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="meta_description">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" rows="3"><?= Security::output($post['meta_description'] ?? $post['excerpt'] ?? '') ?></textarea>
                    <small>Recommended: 150-160 characters</small>
                </div>
            </div>
        </div>
        
        <div class="col-4">
            <div class="card">
                <h3>Publish</h3>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="draft" <?= ($post['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= ($post['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="author_name">Author Name</label>
                    <input type="text" id="author_name" name="author_name" 
                           value="<?= Security::output($post['author_name'] ?? 'Admin') ?>">
                </div>
                
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        <option value="General" <?= ($post['category'] ?? 'General') === 'General' ? 'selected' : '' ?>>General</option>
                        <option value="Streaming" <?= ($post['category'] ?? '') === 'Streaming' ? 'selected' : '' ?>>Streaming</option>
                        <option value="Technology" <?= ($post['category'] ?? '') === 'Technology' ? 'selected' : '' ?>>Technology</option>
                        <option value="Entertainment" <?= ($post['category'] ?? '') === 'Entertainment' ? 'selected' : '' ?>>Entertainment</option>
                        <option value="Sports" <?= ($post['category'] ?? '') === 'Sports' ? 'selected' : '' ?>>Sports</option>
                        <option value="Tutorial" <?= ($post['category'] ?? '') === 'Tutorial' ? 'selected' : '' ?>>Tutorial</option>
                        <option value="News" <?= ($post['category'] ?? '') === 'News' ? 'selected' : '' ?>>News</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <?= $isEdit ? 'Update' : 'Publish' ?> Post
                </button>
            </div>
            
            <div class="card">
                <h3>Featured Image</h3>
                
                <?php if ($isEdit && $post['featured_image']): ?>
                    <div class="featured-image-preview">
                        <img src="../<?= Security::output($post['featured_image']) ?>" alt="Featured Image">
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="featured_image">Upload Image</label>
                    <input type="file" id="featured_image" name="featured_image" accept="image/*">
                    <small>JPG, PNG, GIF, or WebP. Max 5MB.</small>
                </div>
            </div>
        </div>
    </div>
</form>

<?php include 'views/footer.php'; ?>
