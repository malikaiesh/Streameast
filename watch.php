<?php
require_once 'config/config.php';

$videoObj = new Video();
$slug = $_GET['v'] ?? '';

if (!$slug) {
    header('Location: index.php');
    exit;
}

$video = $videoObj->getBySlug($slug);

if (!$video) {
    header('Location: index.php');
    exit;
}

// Increment views
$videoObj->incrementViews($video['id']);
$video['views']++;

// Get related videos
$relatedVideos = $videoObj->getRelated($video['id'], $video['category_id'], 8);

// Get tags
$tags = $videoObj->getTags($video['id']);

include 'views/header.php';
?>

<div class="watch-page">
    <div class="video-player-section">
        <div class="video-player">
            <?php if ($video['embed_code']): ?>
                <?= $video['embed_code'] ?>
            <?php else: ?>
                <iframe src="<?= htmlspecialchars($video['video_url']) ?>" frameborder="0" allowfullscreen></iframe>
            <?php endif; ?>
        </div>
        
        <div class="video-details">
            <h1><?= Security::output($video['title']) ?></h1>
            
            <div class="video-stats">
                <span class="views"><?= number_format($video['views']) ?> views</span>
                <?php if ($video['category_name']): ?>
                    <span class="category"><?= Security::output($video['category_name']) ?></span>
                <?php endif; ?>
                <span class="date"><?= date('M d, Y', strtotime($video['created_at'])) ?></span>
            </div>
            
            <div class="video-actions">
                <button onclick="shareVideo()" class="action-btn">📤 Share</button>
                <button onclick="reportVideo(<?= $video['id'] ?>)" class="action-btn">🚩 Report</button>
            </div>
            
            <?php if ($video['description']): ?>
            <div class="video-description">
                <h3>Description</h3>
                <p><?= nl2br(Security::output($video['description'])) ?></p>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($tags)): ?>
            <div class="video-tags">
                <?php foreach ($tags as $tag): ?>
                    <span class="tag">#<?= Security::output($tag['name']) ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="sidebar">
        <h3>Related Videos</h3>
        <div class="related-videos">
            <?php foreach ($relatedVideos as $rv): ?>
                <a href="watch.php?v=<?= $rv['slug'] ?>" class="related-video">
                    <div class="related-thumbnail">
                        <?php if ($rv['thumbnail']): ?>
                            <img src="<?= htmlspecialchars($rv['thumbnail']) ?>" alt="<?= Security::output($rv['title']) ?>">
                        <?php endif; ?>
                        <?php if ($rv['duration']): ?>
                            <span class="duration"><?= Security::output($rv['duration']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="related-info">
                        <h4><?= Security::output($rv['title']) ?></h4>
                        <div class="related-meta">
                            <span><?= number_format($rv['views']) ?> views</span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
function shareVideo() {
    const url = window.location.href;
    if (navigator.share) {
        navigator.share({
            title: '<?= addslashes($video['title']) ?>',
            url: url
        });
    } else {
        prompt('Share this link:', url);
    }
}

function reportVideo(videoId) {
    const reason = prompt('Please enter the reason for reporting this video:');
    if (reason) {
        fetch('api/report.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({video_id: videoId, reason: reason})
        }).then(r => r.json()).then(data => {
            alert(data.message || 'Report submitted');
        });
    }
}
</script>

<?php include 'views/footer.php'; ?>
