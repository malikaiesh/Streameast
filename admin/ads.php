<?php
require_once '../config/config.php';
Security::requireAdmin();

$settings = new Settings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (Security::verifyCSRFToken($csrf_token)) {
        $name = $_POST['ad_name'] ?? '';
        $position = $_POST['ad_position'] ?? '';
        $code = $_POST['ad_code'] ?? '';
        
        if ($name && $position && $code) {
            $settings->addAd($name, $position, $code);
            $success = 'Ad added successfully!';
        }
    }
}

$csrf_token = Security::generateCSRFToken();
include 'views/header.php';
?>

<h2>Ads Management</h2>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<div class="admin-form">
    <h3>Add New Ad</h3>
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        
        <div class="form-group">
            <label>Ad Name</label>
            <input type="text" name="ad_name" required>
        </div>
        
        <div class="form-group">
            <label>Ad Position</label>
            <select name="ad_position" required>
                <option value="">Select Position</option>
                <option value="header">Header</option>
                <option value="sidebar">Sidebar</option>
                <option value="footer">Footer</option>
                <option value="video_overlay">Video Overlay</option>
                <option value="between_videos">Between Videos</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Ad Code (HTML/JavaScript)</label>
            <textarea name="ad_code" rows="6" required placeholder="Paste your ad code here..."></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Add Ad</button>
    </form>
</div>

<div class="dashboard-section" style="margin-top: 32px;">
    <h3>ads.txt Generator</h3>
    <p>Create an ads.txt file for Google AdSense and other ad networks.</p>
    <textarea rows="5" style="width: 100%; padding: 12px; font-family: monospace;"># Example ads.txt content
google.com, pub-0000000000000000, DIRECT, f08c47fec0942fa0</textarea>
    <p><small>Save this content to your website root as ads.txt</small></p>
</div>

<?php include 'views/footer.php'; ?>
