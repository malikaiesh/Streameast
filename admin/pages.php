<?php
require_once '../config/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../includes/Page.php';

$page = new Page();
$success = '';
$error = '';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if ($page->delete($_GET['delete'])) {
        $success = 'Page deleted successfully!';
    } else {
        $error = 'Failed to delete page.';
    }
}

// Pagination
$searchQuery = $_GET['search'] ?? '';
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$offset = ($currentPage - 1) * $perPage;

$pages = $page->getAll($searchQuery, $perPage, $offset);
$totalPages = ceil($page->count($searchQuery) / $perPage);

$pageTitle = 'Manage Pages';
include 'views/header.php';
?>

<div class="admin-content">
    <div class="content-header">
        <h1>📄 Manage Pages</h1>
        <a href="edit-page.php" class="btn btn-primary">+ Add New Page</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search pages..." 
                       value="<?= htmlspecialchars($searchQuery) ?>" class="search-input">
                <button type="submit" class="btn btn-primary">🔍 Search</button>
                <?php if ($searchQuery): ?>
                    <a href="pages.php" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pages)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px;">
                                <p>No pages found. <a href="edit-page.php">Create your first page</a></p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pages as $p): ?>
                            <tr>
                                <td><?= $p['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($p['title']) ?></strong>
                                </td>
                                <td>
                                    <code>/<?= htmlspecialchars($p['slug']) ?>.php</code>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $p['status'] === 'published' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($p['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
                                <td class="actions">
                                    <a href="/<?= htmlspecialchars($p['slug']) ?>.php" 
                                       class="btn btn-sm btn-secondary" target="_blank" title="View">👁️</a>
                                    <a href="edit-page.php?id=<?= $p['id'] ?>" 
                                       class="btn btn-sm btn-primary" title="Edit">✏️</a>
                                    <a href="?delete=<?= $p['id'] ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this page?')" 
                                       title="Delete">🗑️</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?><?= $searchQuery ? '&search=' . urlencode($searchQuery) : '' ?>" 
                       class="btn btn-secondary">← Previous</a>
                <?php endif; ?>

                <span class="page-info">Page <?= $currentPage ?> of <?= $totalPages ?></span>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?><?= $searchQuery ? '&search=' . urlencode($searchQuery) : '' ?>" 
                       class="btn btn-secondary">Next →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'views/footer.php'; ?>
