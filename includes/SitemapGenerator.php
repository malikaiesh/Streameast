<?php

class SitemapGenerator {
    private $db;
    private $baseUrl;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->baseUrl = rtrim(SITE_URL, '/');
    }
    
    // Generate complete XML sitemap
    public function generateSitemap() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Homepage
        $xml .= $this->addUrl($this->baseUrl . '/', date('Y-m-d'), '1.0', 'daily');
        
        // Static pages
        $staticPages = ['trending', 'shorts', 'movies', 'live', 'blog'];
        foreach ($staticPages as $page) {
            $xml .= $this->addUrl($this->baseUrl . '/' . $page . '.php', date('Y-m-d'), '0.8', 'daily');
        }
        
        // Videos
        $videos = $this->getVideos();
        foreach ($videos as $video) {
            $url = $this->baseUrl . '/watch.php?id=' . $video['id'];
            $xml .= $this->addUrl($url, $video['updated_at'] ?? $video['created_at'], '0.7', 'weekly');
        }
        
        // Blog posts
        $posts = $this->getBlogPosts();
        foreach ($posts as $post) {
            $url = $this->baseUrl . '/blog-post.php?slug=' . $post['slug'];
            $xml .= $this->addUrl($url, $post['updated_at'] ?? $post['created_at'], '0.6', 'weekly');
        }
        
        // Static legal pages (these have physical PHP files)
        $staticLegalPages = ['privacy-policy', 'terms', 'dmca', 'contact'];
        foreach ($staticLegalPages as $pageSlug) {
            $url = $this->baseUrl . '/' . $pageSlug . '.php';
            $xml .= $this->addUrl($url, date('Y-m-d'), '0.5', 'monthly');
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }
    
    // Generate schema.org JSON-LD for blogs
    public function generateBlogSchema($post) {
        $settings = new Settings();
        $siteName = $settings->get('site_name', 'Stream East');
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post['title'],
            'description' => substr(strip_tags($post['content']), 0, 200),
            'author' => [
                '@type' => 'Organization',
                'name' => $siteName
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $siteName,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $this->baseUrl . '/assets/logo.png'
                ]
            ],
            'datePublished' => $post['created_at'],
            'dateModified' => $post['updated_at'] ?? $post['created_at']
        ];
        
        if (!empty($post['featured_image'])) {
            $schema['image'] = $this->baseUrl . '/' . $post['featured_image'];
        }
        
        return json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    
    // Generate schema.org JSON-LD for pages
    public function generatePageSchema($page) {
        $settings = new Settings();
        $siteName = $settings->get('site_name', 'Stream East');
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $page['title'],
            'description' => $page['meta_description'] ?? substr(strip_tags($page['content']), 0, 200),
            'publisher' => [
                '@type' => 'Organization',
                'name' => $siteName
            ]
        ];
        
        return json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    
    // Generate Organization schema
    public function generateOrganizationSchema() {
        $settings = new Settings();
        $siteName = $settings->get('site_name', 'Stream East');
        $siteDesc = $settings->get('site_description', '');
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $siteName,
            'description' => $siteDesc,
            'url' => $this->baseUrl
        ];
        
        // Add logo if exists
        $logo = $settings->get('site_logo');
        if ($logo) {
            $schema['logo'] = $this->baseUrl . '/' . $logo;
        }
        
        // Add social media links
        $socialLinks = [];
        $socialPlatforms = ['facebook', 'twitter', 'instagram', 'youtube', 'linkedin'];
        foreach ($socialPlatforms as $platform) {
            $link = $settings->get('social_' . $platform);
            if ($link) {
                $socialLinks[] = $link;
            }
        }
        if (!empty($socialLinks)) {
            $schema['sameAs'] = $socialLinks;
        }
        
        return json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    
    // Generate BreadcrumbList schema
    public function generateBreadcrumbSchema($items) {
        $listItems = [];
        $position = 1;
        
        foreach ($items as $item) {
            $listItems[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $item['name'],
                'item' => $item['url']
            ];
        }
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $listItems
        ];
        
        return json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    
    // Save sitemap to file
    public function saveSitemap() {
        $xml = $this->generateSitemap();
        // Save to web root (project root directory) - use dirname to get parent of includes folder
        $webRoot = dirname(__DIR__);
        $filePath = $webRoot . '/sitemap.xml';
        file_put_contents($filePath, $xml);
        
        // Update robots.txt with correct sitemap URL
        $this->updateRobotsTxt();
        
        return true;
    }
    
    // Update robots.txt with sitemap URL
    private function updateRobotsTxt() {
        $robotsTxt = "User-agent: *\n";
        $robotsTxt .= "Allow: /\n";
        $robotsTxt .= "Disallow: /admin/\n";
        $robotsTxt .= "Disallow: /includes/\n";
        $robotsTxt .= "Disallow: /config/\n\n";
        $robotsTxt .= "Sitemap: " . $this->baseUrl . "/sitemap.xml\n";
        
        // Save to web root (project root directory) - use dirname to get parent of includes folder
        $webRoot = dirname(__DIR__);
        $robotsTxtPath = $webRoot . '/robots.txt';
        file_put_contents($robotsTxtPath, $robotsTxt);
    }
    
    // Get sitemap stats
    public function getStats() {
        $webRoot = dirname(__DIR__);
        $sitemapPath = $webRoot . '/sitemap.xml';
        $staticPagesCount = 4; // privacy-policy, terms, dmca, contact
        return [
            'videos' => count($this->getVideos()),
            'blog_posts' => count($this->getBlogPosts()),
            'pages' => $staticPagesCount,
            'last_generated' => file_exists($sitemapPath) ? date('Y-m-d H:i:s', filemtime($sitemapPath)) : 'Never'
        ];
    }
    
    // Helper method to create URL entry
    private function addUrl($loc, $lastmod, $priority, $changefreq) {
        $url = "  <url>\n";
        $url .= "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
        $url .= "    <lastmod>" . $lastmod . "</lastmod>\n";
        $url .= "    <priority>" . $priority . "</priority>\n";
        $url .= "    <changefreq>" . $changefreq . "</changefreq>\n";
        $url .= "  </url>\n";
        return $url;
    }
    
    // Get all published videos
    private function getVideos() {
        $sql = "SELECT id, created_at, updated_at FROM videos WHERE status = 'published' ORDER BY created_at DESC LIMIT 5000";
        return $this->db->fetchAll($sql);
    }
    
    // Get all published blog posts (with all fields needed for schema generation)
    private function getBlogPosts() {
        $sql = "SELECT slug, title, content, featured_image, category, author_name, published_at, created_at, updated_at FROM blog_posts WHERE status = 'published' ORDER BY created_at DESC";
        return $this->db->fetchAll($sql);
    }
}
