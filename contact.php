<?php
require_once 'config/config.php';
require_once 'includes/Page.php';

$pageModel = new Page();
$pageData = $pageModel->getBySlug('contact');
$message = '';
$error = '';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $messageText = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($messageText)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        $message = 'Thank you for contacting us! We will respond to your message as soon as possible.';
        $name = $email = $subject = $messageText = '';
    }
}

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
            
            <?php if ($message): ?>
                <div class="alert alert-success">
                    ✅ <?= Security::output($message) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    ❌ <?= Security::output($error) ?>
                </div>
            <?php endif; ?>
            
            <div class="legal-content">
                <?= $pageData['content'] ?>
            </div>
        </div>
    </div>
    
    <?php include 'views/footer.php'; ?>
</body>
</html>
