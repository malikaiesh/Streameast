<?php
require_once '../config/config.php';
require_once '../includes/SitemapGenerator.php';

Security::requireAdmin();

$sitemapGen = new SitemapGenerator();
$success = '';
$error = '';

// Handle sitemap generation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_sitemap'])) {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (Security::verifyCSRFToken($csrf_token)) {
        try {
            $sitemapGen->saveSitemap();
            $success = 'Sitemap generated successfully!';
        } catch (Exception $e) {
            $error = 'Error generating sitemap: ' . $e->getMessage();
        }
    }
}

$stats = $sitemapGen->getStats();
$csrf_token = Security::generateCSRFToken();
include 'views/header.php';
?>

<style>
.seo-container {
    max-width: 1200px;
}

.seo-section {
    background: white;
    padding: 24px;
    margin-bottom: 24px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.seo-section h3 {
    margin: 0 0 20px 0;
    padding-bottom: 12px;
    border-bottom: 2px solid #f0f0f0;
    color: #333;
    font-size: 18px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.stat-card {
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.stat-card h4 {
    margin: 0 0 8px 0;
    font-size: 14px;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-card .stat-value {
    font-size: 32px;
    font-weight: 700;
    margin: 0;
}

.stat-card.neutral {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stat-card.success {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.schema-examples {
    background: #f9f9f9;
    padding: 16px;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.schema-examples h4 {
    margin-top: 0;
    color: #667eea;
    font-size: 14px;
}

.schema-examples ul {
    margin: 8px 0;
    padding-left: 24px;
}

.schema-examples li {
    margin: 4px 0;
    color: #666;
}

.action-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 20px;
}

.info-box {
    background: #e3f2fd;
    border-left: 4px solid #2196f3;
    padding: 16px;
    border-radius: 4px;
    margin: 16px 0;
}

.info-box strong {
    color: #1976d2;
}

.code-block {
    background: #2d2d2d;
    color: #f8f8f2;
    padding: 16px;
    border-radius: 8px;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    overflow-x: auto;
    margin: 12px 0;
}
</style>

<div class="seo-container">
    <h2>🔍 SEO Manager - Sitemap & Schema Generator</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= Security::output($success) ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= Security::output($error) ?></div>
    <?php endif; ?>

    <!-- Stats Section -->
    <div class="seo-section">
        <h3>📊 Sitemap Statistics</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <h4>Total Videos</h4>
                <p class="stat-value"><?= number_format($stats['videos']) ?></p>
            </div>
            <div class="stat-card neutral">
                <h4>Blog Posts</h4>
                <p class="stat-value"><?= number_format($stats['blog_posts']) ?></p>
            </div>
            <div class="stat-card success">
                <h4>Pages</h4>
                <p class="stat-value"><?= number_format($stats['pages']) ?></p>
            </div>
            <div class="stat-card">
                <h4>Categories</h4>
                <p class="stat-value"><?= number_format($stats['categories']) ?></p>
            </div>
        </div>
        
        <div class="info-box">
            <strong>Last Generated:</strong> <?= $stats['last_generated'] ?>
        </div>
    </div>

    <!-- XML Sitemap Section -->
    <div class="seo-section">
        <h3>🗺️ XML Sitemap Generator</h3>
        <p>Generate an XML sitemap for search engines like Google, Bing, and Yahoo. The sitemap includes all your videos, blog posts, pages, and categories.</p>
        
        <div class="info-box">
            <strong>Sitemap URL:</strong> <a href="<?= SITE_URL ?>/sitemap.xml" target="_blank"><?= SITE_URL ?>/sitemap.xml</a>
        </div>
        
        <h4>What's Included:</h4>
        <ul>
            <li>✅ Homepage</li>
            <li>✅ All published videos</li>
            <li>✅ All published blog posts</li>
            <li>✅ All active pages (Privacy Policy, Terms, etc.)</li>
            <li>✅ All categories</li>
            <li>✅ Static pages (Trending, Shorts, Movies, Live Sports, Blog)</li>
        </ul>
        
        <div class="action-buttons">
            <form method="POST" style="display: inline;">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <button type="submit" name="generate_sitemap" class="btn btn-primary">
                    🔄 Generate Sitemap Now
                </button>
            </form>
            
            <?php if (file_exists('../sitemap.xml')): ?>
            <a href="<?= SITE_URL ?>/sitemap.xml" target="_blank" class="btn btn-secondary">
                👁️ View Current Sitemap
            </a>
            <?php endif; ?>
        </div>
        
        <div class="info-box" style="margin-top: 20px;">
            <strong>💡 Pro Tip:</strong> After generating your sitemap, submit it to <a href="https://search.google.com/search-console" target="_blank">Google Search Console</a> and <a href="https://www.bing.com/webmasters" target="_blank">Bing Webmaster Tools</a> for better SEO.
        </div>
    </div>

    <!-- Schema.org Section -->
    <div class="seo-section">
        <h3>📋 Schema.org Structured Data</h3>
        <p>Schema.org structured data helps search engines understand your content better, leading to rich snippets in search results.</p>
        
        <div class="schema-examples">
            <h4>Automatically Generated Schema Types:</h4>
            <ul>
                <li><strong>Organization Schema:</strong> Your website's basic information and social media links</li>
                <li><strong>VideoObject Schema:</strong> For all video pages (already implemented)</li>
                <li><strong>BlogPosting Schema:</strong> For blog posts with author, publish date, and images</li>
                <li><strong>WebPage Schema:</strong> For static pages like Privacy Policy, Terms, etc.</li>
                <li><strong>BreadcrumbList Schema:</strong> For navigation breadcrumbs</li>
            </ul>
        </div>
        
        <h4>Example Organization Schema:</h4>
        <div class="code-block"><?= htmlspecialchars($sitemapGen->generateOrganizationSchema()) ?></div>
        
        <div class="info-box">
            <strong>✅ Auto-Implementation:</strong> Schema is automatically injected into your pages. No action needed!
        </div>
    </div>

    <!-- Robots.txt Section -->
    <div class="seo-section">
        <h3>🤖 Robots.txt Configuration</h3>
        <p>Your robots.txt file tells search engines which pages they can crawl.</p>
        
        <div class="info-box">
            <strong>Robots.txt URL:</strong> <a href="<?= SITE_URL ?>/robots.txt" target="_blank"><?= SITE_URL ?>/robots.txt</a>
        </div>
        
        <h4>Current Configuration:</h4>
        <div class="code-block">User-agent: *
Allow: /
Disallow: /admin/
Disallow: /includes/
Disallow: /config/

Sitemap: <?= SITE_URL ?>/sitemap.xml</div>
        
        <div class="info-box">
            <strong>💡 Note:</strong> The robots.txt file is already configured to allow search engines to crawl your site while protecting admin areas.
        </div>
    </div>

    <!-- SEO Best Practices -->
    <div class="seo-section">
        <h3>✨ SEO Best Practices</h3>
        <div class="stats-grid">
            <div style="padding: 16px; background: #f0f4f8; border-radius: 8px;">
                <h4 style="margin-top: 0; color: #667eea;">📝 Content</h4>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>Use unique titles</li>
                    <li>Write meta descriptions</li>
                    <li>Add alt text to images</li>
                </ul>
            </div>
            <div style="padding: 16px; background: #f0f4f8; border-radius: 8px;">
                <h4 style="margin-top: 0; color: #667eea;">🔗 Technical</h4>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>Update sitemap regularly</li>
                    <li>Use HTTPS</li>
                    <li>Optimize page speed</li>
                </ul>
            </div>
            <div style="padding: 16px; background: #f0f4f8; border-radius: 8px;">
                <h4 style="margin-top: 0; color: #667eea;">📊 Monitoring</h4>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>Track with Analytics</li>
                    <li>Monitor Search Console</li>
                    <li>Check Core Web Vitals</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>
