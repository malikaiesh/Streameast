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
                            <?php if (!empty($rv['category_name'])): ?>
                                <span><?= Security::output($rv['category_name']) ?></span>
                            <?php endif; ?>
                            <span><?= number_format($rv['views']) ?> views</span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modern Share Modal -->
<div id="shareModal" class="share-modal">
    <div class="share-modal-content">
        <div class="share-modal-header">
            <h2>Share Video</h2>
            <button class="close-modal" onclick="closeShareModal()">✕</button>
        </div>
        
        <div class="share-link-container">
            <input type="text" class="share-link-input" id="shareLink" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" readonly>
            <button class="copy-btn" onclick="copyLink()">Copy</button>
        </div>
        
        <div class="share-platforms">
            <a href="#" class="share-platform facebook" onclick="shareToFacebook(); return false;">
                <div class="share-platform-icon">
                    <svg width="24" height="24" fill="white" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </div>
                <span class="share-platform-name">Facebook</span>
            </a>
            
            <a href="#" class="share-platform twitter" onclick="shareToTwitter(); return false;">
                <div class="share-platform-icon">
                    <svg width="24" height="24" fill="white" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                </div>
                <span class="share-platform-name">Twitter</span>
            </a>
            
            <a href="#" class="share-platform whatsapp" onclick="shareToWhatsApp(); return false;">
                <div class="share-platform-icon">
                    <svg width="24" height="24" fill="white" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                </div>
                <span class="share-platform-name">WhatsApp</span>
            </a>
            
            <a href="#" class="share-platform telegram" onclick="shareToTelegram(); return false;">
                <div class="share-platform-icon">
                    <svg width="24" height="24" fill="white" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                </div>
                <span class="share-platform-name">Telegram</span>
            </a>
            
            <a href="#" class="share-platform reddit" onclick="shareToReddit(); return false;">
                <div class="share-platform-icon">
                    <svg width="24" height="24" fill="white" viewBox="0 0 24 24"><path d="M12 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0zm5.01 4.744c.688 0 1.25.561 1.25 1.249a1.25 1.25 0 0 1-2.498.056l-2.597-.547-.8 3.747c1.824.07 3.48.632 4.674 1.488.308-.309.73-.491 1.207-.491.968 0 1.754.786 1.754 1.754 0 .716-.435 1.333-1.01 1.614a3.111 3.111 0 0 1 .042.52c0 2.694-3.13 4.87-7.004 4.87-3.874 0-7.004-2.176-7.004-4.87 0-.183.015-.366.043-.534A1.748 1.748 0 0 1 4.028 12c0-.968.786-1.754 1.754-1.754.463 0 .898.196 1.207.49 1.207-.883 2.878-1.43 4.744-1.487l.885-4.182a.342.342 0 0 1 .14-.197.35.35 0 0 1 .238-.042l2.906.617a1.214 1.214 0 0 1 1.108-.701zM9.25 12C8.561 12 8 12.562 8 13.25c0 .687.561 1.248 1.25 1.248.687 0 1.248-.561 1.248-1.249 0-.688-.561-1.249-1.249-1.249zm5.5 0c-.687 0-1.248.561-1.248 1.25 0 .687.561 1.248 1.249 1.248.688 0 1.249-.561 1.249-1.249 0-.687-.562-1.249-1.25-1.249zm-5.466 3.99a.327.327 0 0 0-.231.094.33.33 0 0 0 0 .463c.842.842 2.484.913 2.961.913.477 0 2.105-.056 2.961-.913a.361.361 0 0 0 .029-.463.33.33 0 0 0-.464 0c-.547.533-1.684.73-2.512.73-.828 0-1.979-.196-2.512-.73a.326.326 0 0 0-.232-.095z"/></svg>
                </div>
                <span class="share-platform-name">Reddit</span>
            </a>
            
            <a href="#" class="share-platform linkedin" onclick="shareToLinkedIn(); return false;">
                <div class="share-platform-icon">
                    <svg width="24" height="24" fill="white" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                </div>
                <span class="share-platform-name">LinkedIn</span>
            </a>
            
            <a href="#" class="share-platform email" onclick="shareViaEmail(); return false;">
                <div class="share-platform-icon">
                    <svg width="24" height="24" fill="white" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                </div>
                <span class="share-platform-name">Email</span>
            </a>
            
            <a href="#" class="share-platform copy" onclick="copyLink(); return false;">
                <div class="share-platform-icon">
                    <svg width="24" height="24" fill="white" viewBox="0 0 24 24"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>
                </div>
                <span class="share-platform-name">Copy Link</span>
            </a>
        </div>
    </div>
</div>

<script>
const shareModal = document.getElementById('shareModal');
const shareUrl = window.location.href;
const videoTitle = '<?= addslashes($video['title']) ?>';

function shareVideo() {
    shareModal.classList.add('active');
}

function closeShareModal() {
    shareModal.classList.remove('active');
}

// Close modal when clicking outside
shareModal.addEventListener('click', function(e) {
    if (e.target === shareModal) {
        closeShareModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && shareModal.classList.contains('active')) {
        closeShareModal();
    }
});

function copyLink() {
    const input = document.getElementById('shareLink');
    input.select();
    document.execCommand('copy');
    
    const btn = event.target;
    const originalText = btn.textContent;
    btn.textContent = '✓ Copied!';
    btn.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
    
    setTimeout(() => {
        btn.textContent = originalText;
        btn.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
    }, 2000);
}

function shareToFacebook() {
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`, '_blank', 'width=600,height=400');
}

function shareToTwitter() {
    window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(videoTitle)}`, '_blank', 'width=600,height=400');
}

function shareToWhatsApp() {
    window.open(`https://wa.me/?text=${encodeURIComponent(videoTitle + ' ' + shareUrl)}`, '_blank');
}

function shareToTelegram() {
    window.open(`https://t.me/share/url?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(videoTitle)}`, '_blank');
}

function shareToReddit() {
    window.open(`https://reddit.com/submit?url=${encodeURIComponent(shareUrl)}&title=${encodeURIComponent(videoTitle)}`, '_blank', 'width=600,height=600');
}

function shareToLinkedIn() {
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(shareUrl)}`, '_blank', 'width=600,height=600');
}

function shareViaEmail() {
    window.location.href = `mailto:?subject=${encodeURIComponent(videoTitle)}&body=${encodeURIComponent('Check out this video: ' + shareUrl)}`;
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
