<?php
require_once 'config/config.php';

$db = Database::getInstance();

echo "<h2>Database Verification</h2>";

// Check admin user
$admin = $db->fetchOne("SELECT * FROM admin LIMIT 1");
if ($admin) {
    echo "✅ Admin user exists: " . htmlspecialchars($admin['username']) . "<br>";
    echo "Password hash: " . substr($admin['password'], 0, 20) . "...<br>";
} else {
    echo "❌ No admin user found<br>";
}

// Check categories
$categories = $db->fetchAll("SELECT COUNT(*) as count FROM categories");
echo "✅ Categories: " . $categories[0]['count'] . "<br>";

// Check settings
$settings = $db->fetchAll("SELECT COUNT(*) as count FROM site_settings");
echo "✅ Site settings: " . $settings[0]['count'] . "<br>";

// Check custom codes table
$customCodes = $db->fetchAll("SELECT * FROM custom_codes");
echo "✅ Custom codes rows: " . count($customCodes) . "<br>";

// Check ads table
$ads = $db->fetchAll("SELECT COUNT(*) as count FROM ads");
echo "✅ Ads: " . $ads[0]['count'] . "<br>";

// List all tables
$tables = $db->fetchAll("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
echo "<h3>Tables:</h3><ul>";
foreach ($tables as $table) {
    echo "<li>" . htmlspecialchars($table['name']) . "</li>";
}
echo "</ul>";

echo "<h3>Test Login</h3>";
echo "Username: admin<br>";
echo "Password: admin123<br>";
echo "Hash matches: ";
if (password_verify('admin123', $admin['password'])) {
    echo "✅ YES";
} else {
    echo "❌ NO";
}
