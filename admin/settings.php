<?php
require_once '../config/config.php';
Security::requireAdmin();

$settings = new Settings();
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (Security::verifyCSRFToken($csrf_token)) {
        $settings->set('site_name', $_POST['site_name'] ?? '');
        $settings->set('site_description', $_POST['site_description'] ?? '');
        $settings->set('site_keywords', $_POST['site_keywords'] ?? '');
        $settings->set('theme_mode', $_POST['theme_mode'] ?? 'dark');
        $settings->set('enable_downloads', isset($_POST['enable_downloads']) ? '1' : '0');
        $settings->set('videos_per_page', $_POST['videos_per_page'] ?? '12');
        $settings->set('adsense_code', $_POST['adsense_code'] ?? '');
        $settings->set('google_analytics', $_POST['google_analytics'] ?? '');
        
        $success = 'Settings saved successfully!';
    }
}

$siteSettings = $settings->getAll();
$csrf_token = Security::generateCSRFToken();
include 'views/header.php';
?>

<h2>Site Settings</h2>

<?php if ($success): ?>
    <div class="alert alert-success"><?= Security::output($success) ?></div>
<?php endif; ?>

<form method="POST" action="" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
    
    <h3>General Settings</h3>
    
    <div class="form-group">
        <label>Site Name</label>
        <input type="text" name="site_name" value="<?= Security::output($siteSettings['site_name'] ?? '') ?>">
    </div>
    
    <div class="form-group">
        <label>Site Description</label>
        <textarea name="site_description" rows="2"><?= Security::output($siteSettings['site_description'] ?? '') ?></textarea>
    </div>
    
    <div class="form-group">
        <label>Site Keywords (comma separated)</label>
        <input type="text" name="site_keywords" value="<?= Security::output($siteSettings['site_keywords'] ?? '') ?>">
    </div>
    
    <div class="form-row">
        <div class="form-group col-6">
            <label>Theme Mode</label>
            <select name="theme_mode">
                <option value="dark" <?= ($siteSettings['theme_mode'] ?? 'dark') === 'dark' ? 'selected' : '' ?>>Dark</option>
                <option value="light" <?= ($siteSettings['theme_mode'] ?? 'dark') === 'light' ? 'selected' : '' ?>>Light</option>
            </select>
        </div>
        <div class="form-group col-6">
            <label>Videos Per Page</label>
            <input type="number" name="videos_per_page" value="<?= Security::output($siteSettings['videos_per_page'] ?? '12') ?>">
        </div>
    </div>
    
    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="enable_downloads" <?= ($siteSettings['enable_downloads'] ?? '0') === '1' ? 'checked' : '' ?>> Enable Download Button
        </label>
    </div>
    
    <hr>
    <h3>Tracking & Analytics</h3>
    
    <div class="form-group">
        <label>Google AdSense Code</label>
        <textarea name="adsense_code" rows="3" placeholder="Paste your AdSense code here"><?= Security::output($siteSettings['adsense_code'] ?? '') ?></textarea>
    </div>
    
    <div class="form-group">
        <label>Google Analytics Code</label>
        <textarea name="google_analytics" rows="3" placeholder="Paste your Google Analytics code here"><?= Security::output($siteSettings['google_analytics'] ?? '') ?></textarea>
    </div>
    
    <button type="submit" class="btn btn-primary">Save Settings</button>
</form>

<?php include 'views/footer.php'; ?>
