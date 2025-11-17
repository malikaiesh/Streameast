<?php
require_once '../config/config.php';
Security::requireAdmin();

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
$totalCount = $page->count();

$pageTitle = 'Pages Manager';
include 'views/header.php';
?>

<style>
.page-header-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 30px 40px;
    border-radius: 12px;
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.page-header-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

.page-icon-badge {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
}

.page-header-info h1 {
    margin: 0;
    color: white;
    font-size: 32px;
    font-weight: 700;
}

.page-header-subtitle {
    color: rgba(255,255,255,0.9);
    font-size: 14px;
    margin-top: 5px;
}

.page-header-right {
    display: flex;
    align-items: center;
    gap: 20px;
}

.user-info-widget {
    background: rgba(255,255,255,0.15);
    padding: 10px 20px;
    border-radius: 8px;
    color: white;
    font-size: 14px;
}

.btn-add-new {
    background: white;
    color: #667eea;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: transform 0.2s;
}

.btn-add-new:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.content-section {
    background: var(--bg-secondary);
    border-radius: 12px;
    padding: 25px;
}

.section-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border);
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    color: #ffffff;
}

.section-description {
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
    margin-top: 5px;
}

.search-bar {
    display: flex;
    gap: 10px;
}

.search-input-modern {
    padding: 10px 16px;
    background: var(--bg-primary);
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--text-primary);
    width: 300px;
}

.btn-search {
    padding: 10px 20px;
    background: #667eea;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
}

.btn-clear {
    padding: 10px 20px;
    background: var(--bg-hover);
    color: var(--text-primary);
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.modern-table {
    width: 100%;
    border-collapse: collapse;
}

.modern-table thead {
    background: var(--bg-primary);
}

.modern-table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.7);
    letter-spacing: 0.5px;
}

.modern-table td {
    padding: 18px 15px;
    border-bottom: 1px solid var(--border);
}

.modern-table tbody tr:hover {
    background: var(--bg-hover);
}

.page-icon-col {
    width: 50px;
    text-align: center;
    font-size: 24px;
}

.page-title-col strong {
    font-size: 15px;
    color: var(--text-primary);
}

.page-slug-text {
    color: var(--text-secondary);
    font-size: 13px;
    font-family: 'Courier New', monospace;
    margin-top: 3px;
}

.status-pill {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-published {
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
}

.status-draft {
    background: rgba(251, 191, 36, 0.2);
    color: #fbbf24;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-action {
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s;
    display: inline-block;
}

.btn-edit {
    background: #8b5cf6;
    color: white;
}

.btn-edit:hover {
    background: #7c3aed;
}

.btn-delete {
    background: #ef4444;
    color: white;
}

.btn-delete:hover {
    background: #dc2626;
}

.pagination-modern {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid var(--border);
}

.page-link-modern {
    padding: 8px 16px;
    background: var(--bg-primary);
    color: var(--text-primary);
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.2s;
}

.page-link-modern:hover {
    background: #667eea;
    color: white;
}

.page-info-modern {
    color: var(--text-secondary);
    font-size: 14px;
}
</style>

<!-- Header Section -->
<div class="page-header-section">
    <div class="page-header-left">
        <div class="page-icon-badge">📄</div>
        <div class="page-header-info">
            <h1>Pages Manager</h1>
            <div class="page-header-subtitle">Manage all website pages and content</div>
        </div>
    </div>
    <div class="page-header-right">
        <div class="user-info-widget">
            Logged in as: <strong><?= Security::output($_SESSION['admin_username']) ?></strong>
        </div>
        <a href="edit-page.php" class="btn-add-new">+ Add New Page</a>
    </div>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Content Section -->
<div class="content-section">
    <div class="section-toolbar">
        <div>
            <div class="section-title">All Pages (<?= $totalCount ?>)</div>
            <div class="section-description">Manage and edit your website pages</div>
        </div>
        <form method="GET" class="search-bar">
            <input type="text" name="search" placeholder="Search pages..." 
                   value="<?= htmlspecialchars($searchQuery) ?>" class="search-input-modern">
            <button type="submit" class="btn-search">🔍 Search</button>
            <?php if ($searchQuery): ?>
                <a href="pages.php" class="btn-clear">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <table class="modern-table">
        <thead>
            <tr>
                <th>ICON</th>
                <th>TITLE</th>
                <th>SLUG</th>
                <th>STATUS</th>
                <th>CREATED</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pages)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 60px 20px;">
                        <div style="font-size: 48px; margin-bottom: 15px;">📄</div>
                        <div style="font-size: 16px; color: var(--text-secondary);">
                            No pages found. <a href="edit-page.php" style="color: #667eea;">Create your first page</a>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pages as $p): ?>
                    <tr>
                        <td class="page-icon-col">
                            <div style="width: 40px; height: 40px; background: <?= $p['status'] === 'published' ? 'rgba(16, 185, 129, 0.2)' : 'rgba(251, 191, 36, 0.2)' ?>; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                📄
                            </div>
                        </td>
                        <td class="page-title-col">
                            <strong><?= htmlspecialchars($p['title']) ?></strong>
                            <div class="page-slug-text">/<?= htmlspecialchars($p['slug']) ?>.php</div>
                        </td>
                        <td>
                            <code style="background: var(--bg-primary); padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                <?= htmlspecialchars($p['slug']) ?>
                            </code>
                        </td>
                        <td>
                            <span class="status-pill status-<?= $p['status'] ?>">
                                <?= ucfirst($p['status']) ?>
                            </span>
                        </td>
                        <td style="color: var(--text-secondary);">
                            <?= date('M d, Y', strtotime($p['created_at'])) ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="edit-page.php?id=<?= $p['id'] ?>" class="btn-action btn-edit">
                                    Edit
                                </a>
                                <a href="?delete=<?= $p['id'] ?>" 
                                   class="btn-action btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this page?')">
                                    Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
        <div class="pagination-modern">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?= $currentPage - 1 ?><?= $searchQuery ? '&search=' . urlencode($searchQuery) : '' ?>" 
                   class="page-link-modern">← Previous</a>
            <?php endif; ?>

            <span class="page-info-modern">Page <?= $currentPage ?> of <?= $totalPages ?></span>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?= $currentPage + 1 ?><?= $searchQuery ? '&search=' . urlencode($searchQuery) : '' ?>" 
                   class="page-link-modern">Next →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'views/footer.php'; ?>
