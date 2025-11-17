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
                    <a href="home-content.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'home-content.php' ? 'active' : '' ?>">
                        <span class="nav-icon">🏠</span>
                        <span class="nav-label">Home Content</span>
                    </a>
                    <a href="faqs.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'faqs.php' ? 'active' : '' ?>">
                        <span class="nav-icon">❓</span>
                        <span class="nav-label">FAQs</span>
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
                
                <?php
                $securityPages = ['security-dashboard.php', 'security-activity.php', 'security-ip-management.php', 'security-users.php', 'security-settings.php'];
                $isSecurityActive = in_array(basename($_SERVER['PHP_SELF']), $securityPages);
                ?>
                <div class="nav-section">
                    <div class="dropdown-toggle <?= $isSecurityActive ? 'active' : '' ?>" onclick="toggleDropdown(this)">
                        <span class="nav-icon">🔒</span>
                        <span class="nav-label">Security</span>
                        <span class="dropdown-arrow">▼</span>
                    </div>
                    <div class="dropdown-content <?= $isSecurityActive ? 'show' : '' ?>">
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
                </div>
                
                <?php
                $configPages = ['account-settings.php', 'custom-code.php', 'backup.php', 'settings.php', 'seo-manager.php'];
                $isConfigActive = in_array(basename($_SERVER['PHP_SELF']), $configPages);
                ?>
                <div class="nav-section">
                    <div class="dropdown-toggle <?= $isConfigActive ? 'active' : '' ?>" onclick="toggleDropdown(this)">
                        <span class="nav-icon">⚙️</span>
                        <span class="nav-label">Configuration</span>
                        <span class="dropdown-arrow">▼</span>
                    </div>
                    <div class="dropdown-content <?= $isConfigActive ? 'show' : '' ?>">
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
                        <a href="seo-manager.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'seo-manager.php' ? 'active' : '' ?>">
                            <span class="nav-icon">🔍</span>
                            <span class="nav-label">SEO Manager</span>
                        </a>
                        <a href="settings.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>">
                            <span class="nav-icon">⚙️</span>
                            <span class="nav-label">Settings</span>
                        </a>
                    </div>
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
            
    <script>
    function toggleDropdown(element) {
        const dropdownContent = element.nextElementSibling;
        const arrow = element.querySelector('.dropdown-arrow');
        
        // Toggle the dropdown
        dropdownContent.classList.toggle('show');
        element.classList.toggle('active');
        
        // Rotate arrow
        if (dropdownContent.classList.contains('show')) {
            arrow.style.transform = 'rotate(180deg)';
        } else {
            arrow.style.transform = 'rotate(0deg)';
        }
    }
    </script>
