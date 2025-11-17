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
                <h2>Admin Panel</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php" class="nav-item">📊 Dashboard</a>
                <a href="videos.php" class="nav-item">🎥 All Videos</a>
                <a href="add-video.php" class="nav-item">➕ Add Video</a>
                <a href="categories.php" class="nav-item">📁 Categories</a>
                <a href="tags.php" class="nav-item">🏷️ Tags</a>
                <a href="analytics.php" class="nav-item">📈 Analytics</a>
                <a href="ads.php" class="nav-item">💰 Ads Management</a>
                <a href="custom-code.php" class="nav-item">💻 Custom Code</a>
                <a href="settings.php" class="nav-item">⚙️ Settings</a>
                <a href="logout.php" class="nav-item">🚪 Logout</a>
            </nav>
        </aside>
        <main class="admin-content">
            <header class="admin-header">
                <h1>Welcome, <?= Security::output($_SESSION['admin_username']) ?></h1>
                <a href="<?= SITE_URL ?>" target="_blank" class="btn btn-secondary">View Site</a>
            </header>
            <div class="admin-main">
