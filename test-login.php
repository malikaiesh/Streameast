<?php
require_once 'config/config.php';

echo "<h2>Testing Admin Login</h2>";

$db = Database::getInstance();
$sql = "SELECT * FROM admin WHERE username = ?";
$admin = $db->fetchOne($sql, ['admin']);

if (!$admin) {
    echo "❌ Admin user not found<br>";
    exit;
}

echo "✅ Admin user found: " . htmlspecialchars($admin['username']) . "<br>";
echo "Email: " . htmlspecialchars($admin['email']) . "<br>";
echo "Password hash (first 30 chars): " . substr($admin['password'], 0, 30) . "...<br><br>";

// Test password verification
$testPassword = 'admin123';
if (Security::verifyPassword($testPassword, $admin['password'])) {
    echo "✅ <strong style='color: green;'>Password 'admin123' VERIFIED - Login will work!</strong><br>";
} else {
    echo "❌ <strong style='color: red;'>Password 'admin123' does NOT match - Login broken!</strong><br>";
}

echo "<br><a href='admin/login.php'>Go to Admin Login →</a>";
