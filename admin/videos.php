<?php
require_once '../config/config.php';
Security::requireAdmin();

$video = new Video();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$videos = $video->getAll($page, 20);
$totalVideos = $video->getCount();
$totalPages = ceil($totalVideos / 20);

// Handle delete
if (isset($_GET['delete']) && isset($_GET['csrf'])) {
    if (Security::verifyCSRFToken($_GET['csrf'])) {
        $video->delete($_GET['delete']);
        header('Location: videos.php');
        exit;
    }
}

$csrf_token = Security::generateCSRFToken();
include 'views/header.php';
?>

<div class="page-header">
    <h2>All Videos (<?= number_format($totalVideos) ?>)</h2>
    <a href="add-video.php" class="btn btn-primary">+ Add New Video</a>
</div>

<div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Thumbnail</th>
                <th>Title</th>
                <th>Type</th>
                <th>Category</th>
                <th>Views</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($videos as $v): ?>
            <tr>
                <td><?= $v['id'] ?></td>
                <td>
                    <?php if ($v['thumbnail']): ?>
                        <img src="<?= SITE_URL ?>/<?= Security::output($v['thumbnail']) ?>" alt="" style="width:80px;height:45px;object-fit:cover;">
                    <?php endif; ?>
                </td>
                <td><strong><?= Security::output($v['title']) ?></strong></td>
                <td><span class="badge"><?= ucfirst($v['video_type']) ?></span></td>
                <td><?= Security::output($v['category_name'] ?? 'None') ?></td>
                <td><?= number_format($v['views']) ?></td>
                <td>
                    <?php if ($v['is_active']): ?>
                        <span class="badge badge-success">Active</span>
                    <?php else: ?>
                        <span class="badge badge-danger">Inactive</span>
                    <?php endif; ?>
                    <?php if ($v['is_trending']): ?>
                        <span class="badge badge-warning">Trending</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit-video.php?id=<?= $v['id'] ?>" class="btn-small">Edit</a>
                    <a href="?delete=<?= $v['id'] ?>&csrf=<?= $csrf_token ?>" class="btn-small btn-danger" onclick="return confirm('Delete this video?')">Delete</a>
                    <a href="<?= SITE_URL ?>/watch.php?v=<?= $v['slug'] ?>" class="btn-small" target="_blank">View</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPages > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<?php include 'views/footer.php'; ?>
