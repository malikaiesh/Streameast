<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php
    $settings = new Settings();
    $siteName = $settings->get('site_name', 'YouTube Clone');
    
    // SEO Meta Tags
    if (isset($video)) {
        $metaTitle = $video['meta_title'] ?: $video['title'];
        $metaDescription = $video['meta_description'] ?: $video['description'];
        $metaKeywords = $video['meta_keywords'];
        $metaImage = $video['thumbnail'];
    } else {
        $metaTitle = ($pageTitle ?? 'Home') . ' - ' . $siteName;
        $metaDescription = $settings->get('site_description', 'Watch and share videos');
        $metaKeywords = $settings->get('site_keywords', '');
        $metaImage = '';
    }
    ?>
    
    <title><?= Security::output($metaTitle) ?></title>
    <meta name="description" content="<?= Security::output($metaDescription) ?>">
    <?php if ($metaKeywords): ?>
    <meta name="keywords" content="<?= Security::output($metaKeywords) ?>">
    <?php endif; ?>
    
    <!-- OpenGraph Tags -->
    <meta property="og:title" content="<?= Security::output($metaTitle) ?>">
    <meta property="og:description" content="<?= Security::output($metaDescription) ?>">
    <meta property="og:type" content="<?= isset($video) ? 'video.other' : 'website' ?>">
    <meta property="og:url" content="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
    <?php if ($metaImage): ?>
    <meta property="og:image" content="<?= htmlspecialchars($metaImage) ?>">
    <?php endif; ?>
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= Security::output($metaTitle) ?>">
    <meta name="twitter:description" content="<?= Security::output($metaDescription) ?>">
    <?php if ($metaImage): ?>
    <meta name="twitter:image" content="<?= htmlspecialchars($metaImage) ?>">
    <?php endif; ?>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/assets/favicon.png">
    <link rel="shortcut icon" type="image/png" href="/assets/favicon.png">
    
    <!-- Schema.org VideoObject -->
    <?php if (isset($video)): ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "VideoObject",
        "name": "<?= addslashes($video['title']) ?>",
        "description": "<?= addslashes($video['description']) ?>",
        "thumbnailUrl": "<?= addslashes($video['thumbnail']) ?>",
        "uploadDate": "<?= $video['created_at'] ?>",
        "duration": "PT<?= $video['duration'] ?? '0M' ?>",
        "interactionCount": "<?= $video['views'] ?>"
    }
    </script>
    <?php endif; ?>
    
    <!-- Custom Head Code -->
    <?php echo $settings->getCustomCode('head'); ?>
    
    <!-- AdSense Code -->
    <?php
    $adsenseCode = $settings->get('adsense_code');
    if ($adsenseCode) {
        echo $adsenseCode;
    }
    ?>
    
    <!-- Google Analytics -->
    <?php
    $analyticsCode = $settings->get('google_analytics');
    if ($analyticsCode) {
        echo $analyticsCode;
    }
    ?>
    
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="/assets/js/main.js" defer></script>
</head>
<body class="theme-<?= $settings->get('theme_mode', 'dark') ?>">
    <!-- Custom Body Top Code -->
    <?php echo $settings->getCustomCode('body_top'); ?>
    
    <header class="main-header">
        <div class="header-left">
            <button class="menu-btn" onclick="toggleSidebar()">☰</button>
            <a href="<?= SITE_URL ?>" class="logo">
                <h1><?= Security::output($siteName) ?></h1>
            </a>
        </div>
        
        <div class="header-search">
            <form action="<?= SITE_URL ?>/index.php" method="GET" class="search-form">
                <input type="search" name="s" placeholder="Search videos..." value="<?= Security::output($_GET['s'] ?? '') ?>">
                <button type="submit">🔍</button>
            </form>
        </div>
    </header>
    
    <div class="layout">
        <aside class="sidebar" id="sidebar">
            <nav class="sidebar-nav">
                <a href="<?= SITE_URL ?>" class="nav-item">🏠 Home</a>
                <a href="<?= SITE_URL ?>/trending.php" class="nav-item">🔥 Trending</a>
                <a href="<?= SITE_URL ?>/shorts.php" class="nav-item">📱 Shorts</a>
                <a href="<?= SITE_URL ?>/movies.php" class="nav-item">🎬 Movies</a>
                <a href="<?= SITE_URL ?>/live.php" class="nav-item">📺 Live Sports</a>
                <hr>
                <h4>Categories</h4>
                <?php
                $categories = (new Category())->getAll();
                foreach ($categories as $cat):
                ?>
                    <a href="<?= SITE_URL ?>/?category=<?= $cat['slug'] ?>" class="nav-item"><?= Security::output($cat['name']) ?></a>
                <?php endforeach; ?>
            </nav>
        </aside>
        
        <main class="main-content">
