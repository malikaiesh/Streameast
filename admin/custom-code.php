<?php
require_once '../config/config.php';
Security::requireAdmin();

$settings = new Settings();
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (Security::verifyCSRFToken($csrf_token)) {
        $settings->setCustomCode('head', $_POST['code_head'] ?? '');
        $settings->setCustomCode('body_top', $_POST['code_body_top'] ?? '');
        $settings->setCustomCode('body_bottom', $_POST['code_body_bottom'] ?? '');
        $settings->setCustomCode('footer', $_POST['code_footer'] ?? '');
        
        $success = 'Custom codes saved successfully!';
    }
}

$codeHead = $settings->getCustomCode('head');
$codeBodyTop = $settings->getCustomCode('body_top');
$codeBodyBottom = $settings->getCustomCode('body_bottom');
$codeFooter = $settings->getCustomCode('footer');

$csrf_token = Security::generateCSRFToken();
include 'views/header.php';
?>

<h2>Custom Code Injection</h2>

<p>Add custom HTML, CSS, or JavaScript code to different sections of your website.</p>

<?php if ($success): ?>
    <div class="alert alert-success"><?= Security::output($success) ?></div>
<?php endif; ?>

<form method="POST" action="" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
    
    <div class="form-group">
        <label>Code in HEAD Section</label>
        <textarea name="code_head" rows="5" placeholder="<meta>, <link>, <script> tags..."><?= Security::output($codeHead) ?></textarea>
        <small>This code will be inserted in the &lt;head&gt; section of all pages.</small>
    </div>
    
    <div class="form-group">
        <label>Code in BODY (Top)</label>
        <textarea name="code_body_top" rows="5" placeholder="Google Tag Manager, etc..."><?= Security::output($codeBodyTop) ?></textarea>
        <small>This code will be inserted right after the opening &lt;body&gt; tag.</small>
    </div>
    
    <div class="form-group">
        <label>Code in BODY (Bottom)</label>
        <textarea name="code_body_bottom" rows="5" placeholder="Analytics, pixel tracking..."><?= Security::output($codeBodyBottom) ?></textarea>
        <small>This code will be inserted before the closing &lt;/body&gt; tag.</small>
    </div>
    
    <div class="form-group">
        <label>Code in FOOTER</label>
        <textarea name="code_footer" rows="5" placeholder="Copyright notice, links..."><?= Security::output($codeFooter) ?></textarea>
        <small>This code will be inserted in the footer section.</small>
    </div>
    
    <button type="submit" class="btn btn-primary">Save Custom Codes</button>
</form>

<?php include 'views/footer.php'; ?>
