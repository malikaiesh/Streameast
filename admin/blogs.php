<?php
require_once '../config/config.php';
Security::requireAdmin();
require_once '../includes/Blog.php';

$blog = new Blog();
$db = Database::getInstance();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$status = $_GET['status'] ?? null;

$posts = $blog->getAll($page, $perPage, $status);
$totalPosts = $blog->count($status);
$totalPages = ceil($totalPosts / $perPage);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    Security::checkCSRF();
    if ($blog->delete($_POST['delete_id'])) {
        $message = '<div class="alert alert-success">Blog post deleted successfully!</div>';
    } else {
        $message = '<div class="alert alert-error">Failed to delete blog post.</div>';
    }
}

include 'views/header.php';
?>

<?php if (isset($message)) echo $message; ?>

<div class="page-header">
    <h1>Blog Posts</h1>
    <a href="add-blog.php" class="btn btn-primary">✍️ Add New Post</a>
</div>

<div class="filter-tabs">
    <a href="blogs.php" class="filter-tab <?= !$status ? 'active' : '' ?>">All (<?= $blog->count() ?>)</a>
    <a href="blogs.php?status=published" class="filter-tab <?= $status === 'published' ? 'active' : '' ?>">Published (<?= $blog->count('published') ?>)</a>
    <a href="blogs.php?status=draft" class="filter-tab <?= $status === 'draft' ? 'active' : '' ?>">Drafts (<?= $blog->count('draft') ?>)</a>
</div>

<?php if (empty($posts)): ?>
    <div class="empty-state">
        <div class="empty-icon">📝</div>
        <h3>No blog posts found</h3>
        <p>Create your first blog post to get started!</p>
        <a href="add-blog.php" class="btn btn-primary">✍️ Create First Post</a>
    </div>
<?php else: ?>
<div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Author</th>
                <th>Status</th>
                <th>Published</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
            <tr>
                <td>
                    <?php if ($post['featured_image']): ?>
                        <img src="../<?= Security::output($post['featured_image']) ?>" alt="" class="table-thumbnail">
                    <?php else: ?>
                        <div class="table-thumbnail-placeholder">📝</div>
                    <?php endif; ?>
                </td>
                <td>
                    <strong><?= Security::output($post['title']) ?></strong>
                    <div class="text-muted"><?= Security::output($post['slug']) ?></div>
                </td>
                <td><?= Security::output($post['author_name'] ?? 'Admin') ?></td>
                <td>
                    <span class="status-badge status-<?= $post['status'] ?>">
                        <?= ucfirst($post['status']) ?>
                    </span>
                </td>
                <td><?= $post['published_at'] ? date('M d, Y', strtotime($post['published_at'])) : '—' ?></td>
                <td>
                    <a href="edit-blog.php?id=<?= $post['id'] ?>" class="btn-small">Edit</a>
                    <a href="../blog-post.php?slug=<?= $post['slug'] ?>" class="btn-small" target="_blank">View</a>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this blog post?');">
                        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRF() ?>">
                        <input type="hidden" name="delete_id" value="<?= $post['id'] ?>">
                        <button type="submit" class="btn-small btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPages > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?><?= $status ? '&status=' . $status : '' ?>" 
           class="page-link <?= $i === $page ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>
</div>
<?php endif; ?>
<?php endif; ?>

<?php include 'views/footer.php'; ?>
