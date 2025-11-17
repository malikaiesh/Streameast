<?php
require_once '../config/config.php';
Security::requireAdmin();

$db = Database::getInstance();

// Get top videos by views
$topVideos = $db->fetchAll("SELECT id, title, views FROM videos ORDER BY views DESC LIMIT 10");

// Get recent views
$recentViews = $db->fetchAll("SELECT v.title, a.viewed_at, a.user_ip 
    FROM analytics_views a 
    JOIN videos v ON a.video_id = v.id 
    ORDER BY a.viewed_at DESC LIMIT 20");

// Get total stats
$totalViews = $db->fetchOne("SELECT SUM(views) as total FROM videos")['total'] ?? 0;
$totalVideos = $db->fetchOne("SELECT COUNT(*) as count FROM videos")['count'];
$viewsToday = $db->fetchOne("SELECT COUNT(*) as count FROM analytics_views WHERE DATE(viewed_at) = DATE('now')")['count'] ?? 0;

include 'views/header.php';
?>

<h2>Analytics & Statistics</h2>

<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-icon">👁️</div>
        <div class="stat-info">
            <h3><?= number_format($totalViews) ?></h3>
            <p>Total Views</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📊</div>
        <div class="stat-info">
            <h3><?= number_format($viewsToday) ?></h3>
            <p>Views Today</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🎥</div>
        <div class="stat-info">
            <h3><?= number_format($totalVideos) ?></h3>
            <p>Total Videos</p>
        </div>
    </div>
</div>

<div class="dashboard-section">
    <h2>Top Videos by Views</h2>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Views</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topVideos as $v): ?>
                <tr>
                    <td><?= $v['id'] ?></td>
                    <td><?= Security::output($v['title']) ?></td>
                    <td><strong><?= number_format($v['views']) ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="dashboard-section">
    <h2>Recent Activity</h2>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Video</th>
                    <th>IP Address</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentViews as $rv): ?>
                <tr>
                    <td><?= Security::output($rv['title']) ?></td>
                    <td><?= Security::output($rv['user_ip']) ?></td>
                    <td><?= date('M d, Y H:i', strtotime($rv['viewed_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'views/footer.php'; ?>
