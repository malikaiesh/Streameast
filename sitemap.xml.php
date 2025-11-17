<?php
require_once 'config/config.php';
require_once 'includes/SitemapGenerator.php';

// Set XML headers
header('Content-Type: application/xml; charset=utf-8');

// Check if sitemap file exists
if (file_exists('sitemap.xml')) {
    // Serve the cached sitemap
    readfile('sitemap.xml');
} else {
    // Generate sitemap on the fly
    $generator = new SitemapGenerator();
    echo $generator->generateSitemap();
}
