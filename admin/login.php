<?php
require_once '../config/config.php';

// If already logged in, redirect to dashboard
if (Security::isAdmin()) {
    header('Location: index.php');
    exit;
}

$error = '';

// Check if IP is blocked
if (Security::isIPBlocked()) {
    $error = 'Your IP address has been blocked due to too many failed login attempts. Please try again later.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!Security::verifyCSRFToken($csrf_token)) {
        $error = 'Invalid request';
    } elseif (Security::isRateLimited($username)) {
        $error = 'Too many login attempts. Your IP has been temporarily blocked. Please try again later.';
    } else {
        $db = Database::getInstance();
        $sql = "SELECT * FROM admin WHERE username = ?";
        $admin = $db->fetchOne($sql, [$username]);

        if ($admin && Security::verifyPassword($password, $admin['password'])) {
            // Successful login
            Security::logLoginAttempt($username, true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            
            // Log successful login activity
            Security::logActivity('Admin Login', null, null, 'Logged in successfully');
            
            header('Location: index.php');
            exit;
        } else {
            // Failed login
            Security::logLoginAttempt($username, false);
            $error = 'Invalid username or password';
        }
    }
}

$csrf_token = Security::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - YouTube Clone</title>
    <link rel="icon" type="image/png" href="assets/favicon.png">
    <link rel="shortcut icon" type="image/png" href="assets/favicon.png">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <h1>Admin Login</h1>
            <?php if ($error): ?>
                <div class="alert alert-error"><?= Security::output($error) ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
