<?php
require_once 'config/config.php';

header('Content-Type: application/xml; charset=utf-8');

$video = new Video();
$videos = $video->getAll(1, 1000); // Get all videos

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?= SITE_URL ?></loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/trending.php</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/shorts.php</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/movies.php</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= SITE_URL ?>/live.php</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <?php foreach ($videos as $v): ?>
    <url>
        <loc><?= SITE_URL ?>/watch.php?v=<?= $v['slug'] ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($v['updated_at'])) ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>
</urlset>
