<?php
require_once 'config/config.php';
require_once 'includes/Page.php';

$pageModel = new Page();
$pageData = $pageModel->getBySlug('dmca');

if (!$pageData) {
    http_response_code(404);
    echo '<!DOCTYPE html><html><head><title>404 - Page Not Found</title></head><body><h1>404 - Page Not Found</h1><p>The page you are looking for does not exist.</p></body></html>';
    exit;
}

$pageTitle = $pageData['title'] . ' - Stream East';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Security::output($pageTitle) ?></title>
    <?php if (!empty($pageData['meta_description'])): ?>
        <meta name="description" content="<?= Security::output($pageData['meta_description']) ?>">
    <?php endif; ?>
    <link rel="icon" type="image/png" href="assets/favicon.png">
    <link rel="shortcut icon" type="image/png" href="assets/favicon.png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'views/header.php'; ?>
    
    <div class="legal-page">
        <div class="legal-container">
            <h1><?= Security::output($pageData['title']) ?></h1>
            <p class="last-updated">Last Updated: <?= date('F d, Y', strtotime($pageData['updated_at'])) ?></p>
            
            <div class="legal-content">
                <?= $pageData['content'] ?>
            </div>
        </div>
    </div>
    
    <?php include 'views/footer.php'; ?>
</body>
</html>
