<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Stream East</title>
    <link rel="icon" type="image/png" href="assets/favicon.png">
    <link rel="shortcut icon" type="image/png" href="assets/favicon.png">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <span class="logo-icon">🎬</span>
                    <h2>Stream East</h2>
                </div>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                    <span class="nav-icon">📊</span>
                    <span class="nav-label">Dashboard</span>
                </a>
                
                <div class="nav-section">
                    <div class="section-title">Content Management</div>
                    <a href="videos.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'videos.php' ? 'active' : '' ?>">
                        <span class="nav-icon">🎥</span>
                        <span class="nav-label">All Videos</span>
                    </a>
                    <a href="blogs.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'blogs.php' ? 'active' : '' ?>">
                        <span class="nav-icon">📝</span>
                        <span class="nav-label">Blog Posts</span>
                    </a>
                    <a href="pages.php" class="nav-item <?= in_array(basename($_SERVER['PHP_SELF']), ['pages.php', 'edit-page.php']) ? 'active' : '' ?>">
                        <span class="nav-icon">📄</span>
                        <span class="nav-label">Pages</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="section-title">Organization</div>
                    <a href="categories.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>">
                        <span class="nav-icon">📁</span>
                        <span class="nav-label">Categories</span>
                    </a>
                    <a href="tags.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'tags.php' ? 'active' : '' ?>">
                        <span class="nav-icon">🏷️</span>
                        <span class="nav-label">Tags</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="section-title">Analytics & Monetization</div>
                    <a href="analytics.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : '' ?>">
                        <span class="nav-icon">📈</span>
                        <span class="nav-label">Analytics</span>
                    </a>
                    <a href="ads.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'ads.php' ? 'active' : '' ?>">
                        <span class="nav-icon">💰</span>
                        <span class="nav-label">Ads Management</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="section-title">Security</div>
                    <a href="security-dashboard.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'security-dashboard.php' ? 'active' : '' ?>">
                        <span class="nav-icon">🔒</span>
                        <span class="nav-label">Security Dashboard</span>
                    </a>
                    <a href="security-activity.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'security-activity.php' ? 'active' : '' ?>">
                        <span class="nav-icon">📊</span>
                        <span class="nav-label">Activity Logs</span>
                    </a>
                    <a href="security-ip-management.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'security-ip-management.php' ? 'active' : '' ?>">
                        <span class="nav-icon">⛔</span>
                        <span class="nav-label">IP Management</span>
                    </a>
                    <a href="security-users.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'security-users.php' ? 'active' : '' ?>">
                        <span class="nav-icon">👥</span>
                        <span class="nav-label">User Management</span>
                    </a>
                    <a href="security-settings.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'security-settings.php' ? 'active' : '' ?>">
                        <span class="nav-icon">⚙️</span>
                        <span class="nav-label">Security Settings</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="section-title">Configuration</div>
                    <a href="account-settings.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'account-settings.php' ? 'active' : '' ?>">
                        <span class="nav-icon">👤</span>
                        <span class="nav-label">Account Settings</span>
                    </a>
                    <a href="custom-code.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'custom-code.php' ? 'active' : '' ?>">
                        <span class="nav-icon">💻</span>
                        <span class="nav-label">Custom Code</span>
                    </a>
                    <a href="backup.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'backup.php' ? 'active' : '' ?>">
                        <span class="nav-icon">💾</span>
                        <span class="nav-label">Backup</span>
                    </a>
                    <a href="settings.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>">
                        <span class="nav-icon">⚙️</span>
                        <span class="nav-label">Settings</span>
                    </a>
                </div>
                
                <a href="logout.php" class="nav-item nav-logout">
                    <span class="nav-icon">🚪</span>
                    <span class="nav-label">Logout</span>
                </a>
            </nav>
        </aside>
        <main class="admin-content">
            <header class="admin-header">
                <h1>Welcome, <?= Security::output($_SESSION['admin_username']) ?></h1>
                <a href="<?= SITE_URL ?>" target="_blank" class="btn btn-secondary">View Site</a>
            </header>
            <div class="admin-main">
