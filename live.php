<?php
require_once 'config/config.php';

$video = new Video();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$videos = $video->getAll($page, VIDEOS_PER_PAGE, ['type' => 'live']);
$totalVideos = $video->getCount(['type' => 'live']);
$totalPages = ceil($totalVideos / VIDEOS_PER_PAGE);
$pageTitle = 'Live Sports';

include 'views/header.php';
?>

<h2 class="page-title">📺 Live Sports</h2>

<div class="content-area">
    <div class="videos-grid">
        <?php if (empty($videos)): ?>
            <div class="no-videos">
                <h2>No live streams available</h2>
            </div>
        <?php else: ?>
            <?php foreach ($videos as $v): ?>
                <div class="video-card">
                    <a href="watch.php?v=<?= $v['slug'] ?>" class="video-thumbnail">
                        <img src="<?= htmlspecialchars($v['thumbnail']) ?>" alt="<?= Security::output($v['title']) ?>">
                        <span class="live-badge">LIVE</span>
                    </a>
                    <div class="video-info">
                        <h3><a href="watch.php?v=<?= $v['slug'] ?>"><?= Security::output($v['title']) ?></a></h3>
                        <div class="video-meta">
                            <span class="views"><?= number_format($v['views']) ?> watching</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'views/footer.php'; ?>
