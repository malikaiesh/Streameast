<?php
// Configuration file
if (!session_id()) {
    @session_start();
}

// Database configuration - Using SQLite for simplicity (can easily switch to MySQL)
define('DB_TYPE', 'sqlite');
define('DB_PATH', __DIR__ . '/../database.db');

// MySQL Configuration (uncomment to use MySQL instead)
// define('DB_TYPE', 'mysql');
// define('DB_HOST', 'localhost');
// define('DB_NAME', 'youtube_clone');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_SOCKET', '/tmp/mysql.sock'); // For Replit

// Site configuration - Auto-detect HTTPS (works with Replit proxy)
if (php_sapi_name() === 'cli') {
    // Running from command line
    $protocol = 'https://';
    $host = 'localhost';
} else {
    // Running as web server
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        $protocol = $_SERVER['HTTP_X_FORWARDED_PROTO'] . '://';
    } elseif (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $protocol = 'https://';
    } elseif (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
        $protocol = 'https://';
    } else {
        $protocol = 'https://';
    }
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
}
define('SITE_URL', $protocol . $host);
define('BASE_PATH', dirname(__DIR__));
define('UPLOAD_PATH', BASE_PATH . '/uploads/');
define('THUMBNAIL_PATH', BASE_PATH . '/assets/thumbnails/');

// Security
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_LIFETIME', 3600); // 1 hour

// Pagination
define('VIDEOS_PER_PAGE', 12);

// Timezone
date_default_timezone_set('UTC');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Auto-include classes
spl_autoload_register(function ($class_name) {
    $file = BASE_PATH . '/includes/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Apply security headers
if (class_exists('Security')) {
    Security::applySecurityHeaders();
}
