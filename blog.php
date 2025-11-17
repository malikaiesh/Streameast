<?php
require_once 'config/config.php';
require_once 'includes/Blog.php';

$blog = new Blog();
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 12;

$posts = $blog->getPublished($page, $perPage);
$totalPosts = $blog->count('published');
$totalPages = ceil($totalPosts / $perPage);

$pageTitle = 'Blog';
include 'views/header.php';
?>

<style>
.blog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 32px;
    padding: 12px 0;
}

.blog-card {
    background: var(--bg-secondary);
    border-radius: 16px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid var(--border);
}

.blog-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
}

.blog-image {
    width: 100%;
    height: 220px;
    object-fit: cover;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.blog-content {
    padding: 24px;
}

.blog-category {
    display: inline-block;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.blog-title {
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 12px;
    color: var(--text-primary);
    line-height: 1.3;
}

.blog-excerpt {
    color: var(--text-secondary);
    font-size: 15px;
    line-height: 1.6;
    margin-bottom: 16px;
}

.blog-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 13px;
    color: var(--text-secondary);
    padding-top: 16px;
    border-top: 1px solid var(--border);
}

.blog-date {
    display: flex;
    align-items: center;
    gap: 6px;
}

.read-more {
    background: transparent;
    border: 2px solid rgba(102, 126, 234, 0.5);
    color: var(--text-primary);
    padding: 10px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    display: inline-block;
    transition: all 0.3s ease;
    margin-top: 12px;
}

.read-more:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: transparent;
    color: white;
    transform: translateX(5px);
}
</style>

<div class="page-title">
    <h1>📝 Our Blog</h1>
    <p style="color: var(--text-secondary); margin-top: 8px;">Stay updated with the latest news, tips, and insights about video streaming</p>
</div>

<?php if (empty($posts)): ?>
<div class="empty-state" style="text-align: center; padding: 80px 20px; margin: 40px auto; max-width: 600px;">
    <div style="font-size: 64px; margin-bottom: 16px;">📝</div>
    <h3 style="font-size: 24px; margin-bottom: 12px;">No blog posts yet</h3>
    <p style="color: var(--text-secondary);">Check back soon for the latest updates and articles!</p>
</div>
<?php else: ?>
<div class="blog-grid">
    <?php foreach ($posts as $post): ?>
    <article class="blog-card">
        <?php if ($post['featured_image']): ?>
            <img src="<?= Security::output($post['featured_image']) ?>" alt="<?= Security::output($post['title']) ?>" class="blog-image">
        <?php else: ?>
            <div class="blog-image" style="display: flex; align-items: center; justify-content: center; font-size: 48px; color: white;">
                📝
            </div>
        <?php endif; ?>
        <div class="blog-content">
            <span class="blog-category"><?= Security::output($post['category'] ?? 'General') ?></span>
            <h2 class="blog-title"><?= Security::output($post['title']) ?></h2>
            <p class="blog-excerpt"><?= Security::output($post['excerpt'] ?? substr(strip_tags($post['content']), 0, 150) . '...') ?></p>
            <div class="blog-meta">
                <span class="blog-date">📅 <?= $post['published_at'] ? date('M d, Y', strtotime($post['published_at'])) : date('M d, Y', strtotime($post['created_at'])) ?></span>
                <span>•</span>
                <span><?= Security::output($post['author_name'] ?? 'Admin') ?></span>
            </div>
            <a href="blog-post.php?slug=<?= Security::output($post['slug']) ?>" class="read-more">Read More →</a>
        </div>
    </article>
    <?php endforeach; ?>
</div>

<?php if ($totalPages > 1): ?>
<div class="pagination" style="display: flex; gap: 8px; justify-content: center; margin-top: 40px; padding: 20px 0;">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>" 
           class="page-link" 
           style="padding: 10px 16px; background: var(--bg-secondary); color: var(--text-primary); text-decoration: none; border-radius: 8px; font-weight: 600; border: 1px solid var(--border); <?= $i === $page ? 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>
</div>
<?php endif; ?>
<?php endif; ?>

<?php include 'views/footer.php'; ?>
