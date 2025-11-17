<?php
require_once 'config/config.php';

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

<div class="blog-grid">
    <article class="blog-card">
        <img src="https://images.unsplash.com/photo-1611162616475-46b635cb6868?w=800&h=600&fit=crop" alt="Blog post" class="blog-image">
        <div class="blog-content">
            <span class="blog-category">Streaming</span>
            <h2 class="blog-title">The Future of Video Streaming in 2025</h2>
            <p class="blog-excerpt">Discover the latest trends in video streaming technology and how Stream East is leading the way with innovative features...</p>
            <div class="blog-meta">
                <span class="blog-date">📅 Nov 17, 2025</span>
                <span>•</span>
                <span>5 min read</span>
            </div>
            <a href="#" class="read-more">Read More →</a>
        </div>
    </article>

    <article class="blog-card">
        <img src="https://images.unsplash.com/photo-1536240478700-b869070f9279?w=800&h=600&fit=crop" alt="Blog post" class="blog-image">
        <div class="blog-content">
            <span class="blog-category">Technology</span>
            <h2 class="blog-title">How to Get the Best Streaming Quality</h2>
            <p class="blog-excerpt">Learn expert tips and tricks to optimize your streaming experience and enjoy crystal-clear video quality on any device...</p>
            <div class="blog-meta">
                <span class="blog-date">📅 Nov 15, 2025</span>
                <span>•</span>
                <span>4 min read</span>
            </div>
            <a href="#" class="read-more">Read More →</a>
        </div>
    </article>

    <article class="blog-card">
        <img src="https://images.unsplash.com/photo-1522869635100-9f4c5e86aa37?w=800&h=600&fit=crop" alt="Blog post" class="blog-image">
        <div class="blog-content">
            <span class="blog-category">Entertainment</span>
            <h2 class="blog-title">Top 10 Must-Watch Movies This Month</h2>
            <p class="blog-excerpt">Our curated list of the best movies currently streaming on Stream East. Don't miss these incredible films...</p>
            <div class="blog-meta">
                <span class="blog-date">📅 Nov 12, 2025</span>
                <span>•</span>
                <span>6 min read</span>
            </div>
            <a href="#" class="read-more">Read More →</a>
        </div>
    </article>

    <article class="blog-card">
        <img src="https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?w=800&h=600&fit=crop" alt="Blog post" class="blog-image">
        <div class="blog-content">
            <span class="blog-category">Sports</span>
            <h2 class="blog-title">Live Sports Streaming: A Complete Guide</h2>
            <p class="blog-excerpt">Everything you need to know about watching live sports on Stream East, including schedules, quality settings, and more...</p>
            <div class="blog-meta">
                <span class="blog-date">📅 Nov 10, 2025</span>
                <span>•</span>
                <span>7 min read</span>
            </div>
            <a href="#" class="read-more">Read More →</a>
        </div>
    </article>

    <article class="blog-card">
        <img src="https://images.unsplash.com/photo-1533563906091-fdfdffc3e3c4?w=800&h=600&fit=crop" alt="Blog post" class="blog-image">
        <div class="blog-content">
            <span class="blog-category">Updates</span>
            <h2 class="blog-title">New Features Coming to Stream East</h2>
            <p class="blog-excerpt">We're excited to announce several new features that will enhance your streaming experience. Here's what's coming soon...</p>
            <div class="blog-meta">
                <span class="blog-date">📅 Nov 8, 2025</span>
                <span>•</span>
                <span>3 min read</span>
            </div>
            <a href="#" class="read-more">Read More →</a>
        </div>
    </article>

    <article class="blog-card">
        <img src="https://images.unsplash.com/photo-1485846234645-a62644f84728?w=800&h=600&fit=crop" alt="Blog post" class="blog-image">
        <div class="blog-content">
            <span class="blog-category">Tutorial</span>
            <h2 class="blog-title">Creating the Perfect Watchlist</h2>
            <p class="blog-excerpt">Organize your favorite content and never miss a new release with these watchlist management tips and best practices...</p>
            <div class="blog-meta">
                <span class="blog-date">📅 Nov 5, 2025</span>
                <span>•</span>
                <span>5 min read</span>
            </div>
            <a href="#" class="read-more">Read More →</a>
        </div>
    </article>
</div>

<?php include 'views/footer.php'; ?>
