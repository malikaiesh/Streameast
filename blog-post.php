<?php
require_once 'config/config.php';
require_once 'includes/Blog.php';

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: blog.php');
    exit;
}

$blog = new Blog();
$post = $blog->getBySlug($slug);

if (!$post || $post['status'] !== 'published') {
    http_response_code(404);
    echo '<!DOCTYPE html><html><head><title>404 - Post Not Found</title></head><body><h1>404 - Post Not Found</h1><p>The blog post you are looking for does not exist.</p></body></html>';
    exit;
}

$pageTitle = $post['title'] . ' - Blog - Stream East';
$pageDescription = $post['excerpt'] ?? substr(strip_tags($post['content']), 0, 160);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Security::output($pageTitle) ?></title>
    <meta name="description" content="<?= Security::output($pageDescription) ?>">
    
    <!-- Schema.org BlogPosting -->
    <?php
    require_once 'includes/SitemapGenerator.php';
    $sitemapGen = new SitemapGenerator();
    echo '<script type="application/ld+json">' . $sitemapGen->generateBlogSchema($post) . '</script>';
    ?>
    
    <link rel="icon" type="image/png" href="assets/favicon.png">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
    .blog-post-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    
    .blog-post-header {
        margin-bottom: 40px;
    }
    
    .blog-post-category {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 8px 20px;
        border-radius: 24px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 16px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .blog-post-title {
        font-size: 42px;
        font-weight: 800;
        line-height: 1.2;
        color: var(--text-primary);
        margin-bottom: 20px;
    }
    
    .blog-post-meta {
        display: flex;
        align-items: center;
        gap: 16px;
        color: var(--text-secondary);
        font-size: 15px;
        padding: 16px 0;
        border-bottom: 2px solid var(--border);
    }
    
    .blog-post-featured-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 16px;
        margin: 32px 0;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    }
    
    .blog-post-content {
        font-size: 18px;
        line-height: 1.8;
        color: var(--text-primary);
    }
    
    .blog-post-content h2 {
        font-size: 32px;
        margin: 40px 0 20px 0;
        color: var(--text-primary);
    }
    
    .blog-post-content h3 {
        font-size: 24px;
        margin: 32px 0 16px 0;
        color: var(--text-primary);
    }
    
    .blog-post-content p {
        margin-bottom: 20px;
    }
    
    .blog-post-content ul,
    .blog-post-content ol {
        margin: 20px 0;
        padding-left: 32px;
    }
    
    .blog-post-content li {
        margin-bottom: 12px;
    }
    
    .blog-post-content blockquote {
        border-left: 4px solid #667eea;
        padding: 20px 24px;
        margin: 32px 0;
        background: var(--bg-secondary);
        border-radius: 8px;
        font-style: italic;
    }
    
    .blog-post-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 24px 0;
    }
    
    .back-to-blog {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--bg-secondary);
        color: var(--text-primary);
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        margin-top: 48px;
        border: 1px solid var(--border);
        transition: all 0.3s ease;
    }
    
    .back-to-blog:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: transparent;
        transform: translateX(-5px);
    }
    </style>
</head>
<body>
    <?php include 'views/header.php'; ?>
    
    <article class="blog-post-container">
        <div class="blog-post-header">
            <?php if (!empty($post['category'])): ?>
                <span class="blog-post-category"><?= Security::output($post['category']) ?></span>
            <?php endif; ?>
            
            <h1 class="blog-post-title"><?= Security::output($post['title']) ?></h1>
            
            <div class="blog-post-meta">
                <span>📅 <?= $post['published_at'] ? date('F d, Y', strtotime($post['published_at'])) : date('F d, Y', strtotime($post['created_at'])) ?></span>
                <span>•</span>
                <span>✍️ <?= Security::output($post['author_name'] ?? 'Admin') ?></span>
            </div>
        </div>
        
        <?php if (!empty($post['featured_image'])): ?>
            <img src="<?= Security::output($post['featured_image']) ?>" 
                 alt="<?= Security::output($post['title']) ?>" 
                 class="blog-post-featured-image">
        <?php endif; ?>
        
        <div class="blog-post-content">
            <?= $post['content'] ?>
        </div>
        
        <a href="blog.php" class="back-to-blog">
            ← Back to Blog
        </a>
    </article>
    
    <?php include 'views/footer.php'; ?>
</body>
</html>
