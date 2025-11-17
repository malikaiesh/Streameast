<?php
require_once '../config/config.php';
Security::requireAdmin();

$db = Database::getInstance();

$sql = "CREATE TABLE IF NOT EXISTS blog_posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content TEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(500),
    author_name VARCHAR(100),
    status VARCHAR(20) DEFAULT 'draft',
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    published_at DATETIME
)";

try {
    $db->getConnection()->exec($sql);
    echo "✅ Blog posts table created successfully!";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
