<?php
/**
 * First-time setup installer for Stream East
 * Creates admin account from environment variables
 * 
 * Security: This file should be deleted after initial setup
 */

require_once __DIR__ . '/../config/config.php';

// Check if admin already exists
$db = Database::getInstance();
$existingAdmin = $db->fetchOne("SELECT COUNT(*) as count FROM admin");

if ($existingAdmin && $existingAdmin['count'] > 0) {
    die('Setup already completed. Admin account exists. Please delete this file for security.');
}

$success = false;
$error = '';

// Try to create admin from environment variables
$envUsername = getenv('ADMIN_USERNAME') ?: $_ENV['ADMIN_USERNAME'] ?? '';
$envPassword = getenv('ADMIN_PASSWORD') ?: $_ENV['ADMIN_PASSWORD'] ?? '';
$envEmail = getenv('ADMIN_EMAIL') ?: $_ENV['ADMIN_EMAIL'] ?? 'admin@example.com';

// Auto-setup from environment variables if available
if (!empty($envUsername) && !empty($envPassword)) {
    $hashedPassword = Security::hashPassword($envPassword);
    
    $sql = "INSERT INTO admin (username, password, email) VALUES (?, ?, ?)";
    if ($db->execute($sql, [$envUsername, $hashedPassword, $envEmail])) {
        $success = true;
        $message = "Admin account created successfully from environment variables!";
    } else {
        $error = "Failed to create admin account from environment.";
    }
}

// Manual setup via web form if environment variables not set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($success)) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $email = trim($_POST['email'] ?? 'admin@example.com');
    
    // Validation
    if (empty($username) || strlen($username) < 3) {
        $error = 'Username must be at least 3 characters';
    } elseif (empty($password) || strlen($password) < 8) {
        $error = 'Password must be at least 8 characters';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        $hashedPassword = Security::hashPassword($password);
        
        $sql = "INSERT INTO admin (username, password, email) VALUES (?, ?, ?)";
        if ($db->execute($sql, [$username, $hashedPassword, $email])) {
            $success = true;
            $message = "Admin account created successfully!";
        } else {
            $error = "Failed to create admin account.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - Stream East</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .setup-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        .setup-box {
            background: var(--card-bg, #1a1a1a);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
        }
        .setup-box h1 {
            color: var(--text-primary, #fff);
            margin-bottom: 10px;
        }
        .setup-box p {
            color: var(--text-secondary, #aaa);
            margin-bottom: 30px;
        }
        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #10b981;
            color: white;
        }
        .alert-error {
            background: #ef4444;
            color: white;
        }
        .alert-warning {
            background: #f59e0b;
            color: white;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            color: var(--text-primary, #fff);
            margin-bottom: 8px;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            background: var(--input-bg, #2a2a2a);
            border: 1px solid var(--border-color, #333);
            border-radius: 6px;
            color: var(--text-primary, #fff);
            font-size: 14px;
        }
        .form-group small {
            color: var(--text-secondary, #aaa);
            display: block;
            margin-top: 5px;
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .env-hint {
            background: var(--card-bg, #2a2a2a);
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #667eea;
            margin-bottom: 20px;
        }
        .env-hint code {
            background: var(--bg-primary, #1a1a1a);
            padding: 2px 6px;
            border-radius: 3px;
            color: #10b981;
        }
        .success-actions {
            margin-top: 20px;
        }
        .success-actions a {
            display: inline-block;
            padding: 10px 20px;
            background: #10b981;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-right: 10px;
        }
        .security-warning {
            margin-top: 30px;
            padding: 15px;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid #ef4444;
            border-radius: 6px;
            color: var(--text-primary, #fff);
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-box">
            <h1>🚀 Stream East Setup</h1>
            <p>Create your first admin account to get started</p>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    ✅ <?= htmlspecialchars($message) ?>
                </div>
                <div class="success-actions">
                    <a href="login.php">Go to Login</a>
                </div>
                <div class="security-warning">
                    <strong>⚠️ Security Notice:</strong><br>
                    Please delete <code>admin/setup.php</code> file immediately for security!
                </div>
            <?php else: ?>
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        ❌ <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (empty($envUsername) || empty($envPassword)): ?>
                    <div class="env-hint">
                        <strong>💡 Tip:</strong> You can set environment variables instead:<br>
                        <code>ADMIN_USERNAME</code>, <code>ADMIN_PASSWORD</code>, <code>ADMIN_EMAIL</code>
                    </div>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label>Username *</label>
                            <input type="text" name="username" required minlength="3" 
                                   placeholder="Choose a username" autofocus>
                            <small>At least 3 characters</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" 
                                   placeholder="admin@example.com">
                        </div>
                        
                        <div class="form-group">
                            <label>Password *</label>
                            <input type="password" name="password" required minlength="8"
                                   placeholder="Choose a strong password">
                            <small>At least 8 characters</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Confirm Password *</label>
                            <input type="password" name="confirm_password" required
                                   placeholder="Re-enter password">
                        </div>
                        
                        <button type="submit" class="btn">Create Admin Account</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
