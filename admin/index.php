<?php
require_once '../config/config.php';
Security::requireAdmin();

$db = Database::getInstance();
$video = new Video();

// Get statistics
$totalVideos = $db->fetchOne("SELECT COUNT(*) as count FROM videos")['count'];
$totalViews = $db->fetchOne("SELECT SUM(views) as total FROM videos")['total'] ?? 0;
$totalCategories = $db->fetchOne("SELECT COUNT(*) as count FROM categories")['count'];
$recentVideos = $video->getAll(1, 5);

include 'views/header.php';
?>

<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-icon">🎥</div>
        <div class="stat-info">
            <h3><?= number_format($totalVideos) ?></h3>
            <p>Total Videos</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">👁️</div>
        <div class="stat-info">
            <h3><?= number_format($totalViews) ?></h3>
            <p>Total Views</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📁</div>
        <div class="stat-info">
            <h3><?= number_format($totalCategories) ?></h3>
            <p>Categories</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📈</div>
        <div class="stat-info">
            <h3><?= date('Y-m-d') ?></h3>
            <p>Today</p>
        </div>
    </div>
</div>

<div class="dashboard-section">
    <h2>Recent Videos</h2>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Views</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentVideos as $v): ?>
                <tr>
                    <td><?= Security::output($v['title']) ?></td>
                    <td><span class="badge"><?= ucfirst($v['video_type']) ?></span></td>
                    <td><?= number_format($v['views']) ?></td>
                    <td><?= date('M d, Y', strtotime($v['created_at'])) ?></td>
                    <td>
                        <a href="edit-video.php?id=<?= $v['id'] ?>" class="btn-small">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'views/footer.php'; ?>
