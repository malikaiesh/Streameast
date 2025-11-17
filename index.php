<?php
require_once 'config/config.php';

$video = new Video();
$category = new Category();
$settings = new Settings();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$categorySlug = $_GET['category'] ?? '';
$search = $_GET['s'] ?? '';

$filters = [];
if ($categorySlug) {
    $cat = $category->getBySlug($categorySlug);
    if ($cat) {
        $filters['category_id'] = $cat['id'];
        $pageTitle = Security::output($cat['name']) . ' Videos';
    }
}

if ($search) {
    $filters['search'] = $search;
    $pageTitle = 'Search: ' . Security::output($search);
}

if (!isset($pageTitle)) {
    $pageTitle = 'Home';
}

$videos = $video->getAll($page, VIDEOS_PER_PAGE, $filters);
$totalVideos = $video->getCount($filters);
$totalPages = ceil($totalVideos / VIDEOS_PER_PAGE);
$categories = $category->getAll();

include 'views/header.php';
?>

<div class="content-area">
    <div class="videos-grid">
        <?php if (empty($videos)): ?>
            <div class="no-videos">
                <h2>No videos found</h2>
                <p>Try searching for something else or check back later.</p>
            </div>
        <?php else: ?>
            <?php foreach ($videos as $v): ?>
                <div class="video-card">
                    <a href="watch.php?v=<?= $v['slug'] ?>" class="video-thumbnail">
                        <?php if ($v['thumbnail']): ?>
                            <img src="<?= htmlspecialchars($v['thumbnail']) ?>" alt="<?= Security::output($v['title']) ?>">
                        <?php else: ?>
                            <div class="no-thumbnail">No Thumbnail</div>
                        <?php endif; ?>
                        <?php if ($v['duration']): ?>
                            <span class="duration"><?= Security::output($v['duration']) ?></span>
                        <?php endif; ?>
                        <?php if ($v['video_type'] === 'live'): ?>
                            <span class="live-badge">LIVE</span>
                        <?php endif; ?>
                    </a>
                    <div class="video-info">
                        <h3><a href="watch.php?v=<?= $v['slug'] ?>"><?= Security::output($v['title']) ?></a></h3>
                        <div class="video-meta">
                            <?php if ($v['category_name']): ?>
                                <span class="category"><?= Security::output($v['category_name']) ?></span>
                            <?php endif; ?>
                            <span class="views"><?= number_format($v['views']) ?> views</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?><?= $categorySlug ? '&category=' . $categorySlug : '' ?><?= $search ? '&s=' . urlencode($search) : '' ?>" class="page-link">← Previous</a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= min($totalPages, 10); $i++): ?>
            <a href="?page=<?= $i ?><?= $categorySlug ? '&category=' . $categorySlug : '' ?><?= $search ? '&s=' . urlencode($search) : '' ?>" class="page-link <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?><?= $categorySlug ? '&category=' . $categorySlug : '' ?><?= $search ? '&s=' . urlencode($search) : '' ?>" class="page-link">Next →</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'views/footer.php'; ?>
