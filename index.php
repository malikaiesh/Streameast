<?php
require_once 'config/config.php';
require_once 'includes/HomeContent.php';
require_once 'includes/FAQ.php';

$videoObj = new Video();
$categoryObj = new Category();
$settingsObj = new Settings();
$homeContentObj = new HomeContent();
$faqObj = new FAQ();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$categorySlug = $_GET['category'] ?? '';
$search = $_GET['s'] ?? '';

$filters = [];
if ($categorySlug) {
    $cat = $categoryObj->getBySlug($categorySlug);
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

$videos = $videoObj->getAll($page, VIDEOS_PER_PAGE, $filters);
$totalVideos = $videoObj->getCount($filters);
$totalPages = ceil($totalVideos / VIDEOS_PER_PAGE);
$categories = $categoryObj->getAll();

// Fetch Shorts for homepage
$shorts = [];
if ($page == 1 && !$categorySlug && !$search) {
    $shorts = $videoObj->getAll(1, 10, ['video_type' => 'short']);
}

// Fetch home content sections and FAQs for homepage
$homeContent = [];
$faqs = [];
if ($page == 1 && !$categorySlug && !$search) {
    $homeContent = $homeContentObj->getActive();
    $faqs = $faqObj->getActive();
}

include 'views/header.php';
?>

<!-- Home Content Sections -->
<?php if (!empty($homeContent)): ?>
<div class="home-content-sections">
    <?php foreach ($homeContent as $section): ?>
    <div class="content-section-card">
        <h2 class="section-title">📚 <?= Security::output($section['title']) ?></h2>
        <div class="section-content scrollable-content">
            <?= $section['content'] ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- FAQs Section -->
<?php if (!empty($faqs)): ?>
<div class="faqs-section">
    <h2 class="faqs-title">❓ Frequently Asked Questions</h2>
    <div class="faqs-container">
        <?php foreach ($faqs as $faq): ?>
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                <span><?= Security::output($faq['question']) ?></span>
                <span class="faq-icon">▼</span>
            </div>
            <div class="faq-answer">
                <?= $faq['answer'] ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="content-area">
    <div class="videos-grid">
        <?php if (empty($videos)): ?>
            <div class="no-videos">
                <h2>No videos found</h2>
                <p>Try searching for something else or check back later.</p>
            </div>
        <?php else: ?>
            <?php 
            $videoCount = 0;
            $shortsShown = false;
            foreach ($videos as $v): 
                // Show Shorts section after 8 videos (2 rows)
                if ($videoCount == 8 && !$shortsShown && !empty($shorts)):
                    $shortsShown = true;
            ?>
            </div>
            
            <!-- Shorts Section -->
            <div class="shorts-section">
                <div class="shorts-header">
                    <h2>📱 Shorts</h2>
                    <a href="<?= SITE_URL ?>/shorts.php" class="view-all">View All →</a>
                </div>
                <div class="shorts-container">
                    <button class="scroll-btn scroll-left" onclick="scrollShorts('left')">‹</button>
                    <div class="shorts-scroll" id="shortsScroll">
                        <?php foreach ($shorts as $short): ?>
                            <div class="short-card">
                                <a href="watch.php?v=<?= $short['slug'] ?>" class="short-thumbnail">
                                    <?php if ($short['thumbnail']): ?>
                                        <img src="<?= htmlspecialchars($short['thumbnail']) ?>" alt="<?= Security::output($short['title']) ?>">
                                    <?php else: ?>
                                        <div class="no-thumbnail">No Thumbnail</div>
                                    <?php endif; ?>
                                    <span class="shorts-badge">SHORTS</span>
                                </a>
                                <div class="short-info">
                                    <h3><a href="watch.php?v=<?= $short['slug'] ?>"><?= Security::output($short['title']) ?></a></h3>
                                    <span class="short-views"><?= number_format($short['views']) ?> views</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="scroll-btn scroll-right" onclick="scrollShorts('right')">›</button>
                </div>
            </div>
            
            <div class="videos-grid">
            <?php endif; ?>
            
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
            <?php 
                $videoCount++;
            endforeach; ?>
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

<script>
function toggleFAQ(element) {
    const faqItem = element.parentElement;
    faqItem.classList.toggle('active');
}
</script>

<?php include 'views/footer.php'; ?>
