<?php

class Security {
    
    // Generate CSRF token
    public static function generateCSRFToken() {
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    // Verify CSRF token
    public static function verifyCSRFToken($token) {
        if (empty($_SESSION[CSRF_TOKEN_NAME]) || $token !== $_SESSION[CSRF_TOKEN_NAME]) {
            return false;
        }
        return true;
    }

    // XSS Protection - Clean input
    public static function clean($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::clean($value);
            }
        } else {
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
        return $data;
    }

    // XSS Protection - Clean output
    public static function output($data) {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    // Hash password
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    // Verify password
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    // Check if user is admin
    public static function isAdmin() {
        return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
    }

    // Require admin login
    public static function requireAdmin() {
        if (!self::isAdmin()) {
            header('Location: ' . SITE_URL . '/admin/login.php');
            exit;
        }
    }

    // Generate slug from string
    public static function generateSlug($string) {
        $string = strtolower(trim($string));
        $string = preg_replace('/[^a-z0-9-]/', '-', $string);
        $string = preg_replace('/-+/', '-', $string);
        return trim($string, '-');
    }

    // Sanitize filename
    public static function sanitizeFilename($filename) {
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        return $filename;
    }

    // Get client IP
    public static function getClientIP() {
        $ip = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
    }

    // Get user agent
    public static function getUserAgent() {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    }

    // ========== ADVANCED SECURITY FEATURES ==========

    // Check if IP is blocked
    public static function isIPBlocked($ip = null) {
        $ip = $ip ?? self::getClientIP();
        $db = Database::getInstance();
        
        // Check for permanent blocks
        $permanent = $db->fetchOne(
            "SELECT * FROM security_ip_blocks WHERE ip_address = ? AND block_type = 'permanent'",
            [$ip]
        );
        if ($permanent) {
            return true;
        }
        
        // Check for temporary blocks that haven't expired
        $temporary = $db->fetchOne(
            "SELECT * FROM security_ip_blocks WHERE ip_address = ? AND block_type = 'temporary' AND (expires_at IS NULL OR expires_at > datetime('now'))",
            [$ip]
        );
        return $temporary !== false;
    }

    // Block IP address
    public static function blockIP($ip, $reason = 'Security violation', $type = 'temporary', $duration = 900) {
        $db = Database::getInstance();
        $expiresAt = $type === 'permanent' ? null : date('Y-m-d H:i:s', time() + $duration);
        
        // Try to insert, if duplicate then update
        $existing = $db->fetchOne("SELECT id FROM security_ip_blocks WHERE ip_address = ?", [$ip]);
        if ($existing) {
            return $db->query(
                "UPDATE security_ip_blocks SET reason = ?, block_type = ?, expires_at = ? WHERE ip_address = ?",
                [$reason, $type, $expiresAt, $ip]
            );
        } else {
            return $db->query(
                "INSERT INTO security_ip_blocks (ip_address, reason, block_type, expires_at) VALUES (?, ?, ?, ?)",
                [$ip, $reason, $type, $expiresAt]
            );
        }
    }

    // Unblock IP address
    public static function unblockIP($ip) {
        $db = Database::getInstance();
        return $db->query("DELETE FROM security_ip_blocks WHERE ip_address = ?", [$ip]);
    }

    // Log login attempt
    public static function logLoginAttempt($username, $success = false) {
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO security_login_attempts (username, ip_address, user_agent, success) VALUES (?, ?, ?, ?)",
            [$username, self::getClientIP(), self::getUserAgent(), $success ? 1 : 0]
        );
    }

    // Check if user is rate limited (brute force protection)
    public static function isRateLimited($username = null) {
        $db = Database::getInstance();
        $ip = self::getClientIP();
        
        // Get security settings
        $maxAttempts = self::getSecuritySetting('max_login_attempts', 5);
        $lockoutTime = self::getSecuritySetting('login_lockout_time', 900);
        
        // Count failed attempts in the last lockout period
        $cutoff = date('Y-m-d H:i:s', time() - $lockoutTime);
        
        if ($username) {
            $attempts = $db->fetchOne(
                "SELECT COUNT(*) as count FROM security_login_attempts WHERE (username = ? OR ip_address = ?) AND success = 0 AND created_at > ?",
                [$username, $ip, $cutoff]
            );
        } else {
            $attempts = $db->fetchOne(
                "SELECT COUNT(*) as count FROM security_login_attempts WHERE ip_address = ? AND success = 0 AND created_at > ?",
                [$ip, $cutoff]
            );
        }
        
        if ($attempts && $attempts['count'] >= $maxAttempts) {
            // Auto-block IP if too many attempts
            self::blockIP($ip, 'Too many failed login attempts', 'temporary', $lockoutTime);
            return true;
        }
        
        return false;
    }

    // Log admin activity
    public static function logActivity($action, $tableName = null, $recordId = null, $details = null) {
        if (!self::isAdmin()) {
            return;
        }
        
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO security_activity_logs (admin_id, action, table_name, record_id, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $_SESSION['admin_id'],
                $action,
                $tableName,
                $recordId,
                $details,
                self::getClientIP(),
                self::getUserAgent()
            ]
        );
    }

    // Get security setting
    public static function getSecuritySetting($key, $default = null) {
        $db = Database::getInstance();
        $setting = $db->fetchOne("SELECT setting_value FROM security_settings WHERE setting_key = ?", [$key]);
        return $setting ? $setting['setting_value'] : $default;
    }

    // Update security setting
    public static function updateSecuritySetting($key, $value) {
        $db = Database::getInstance();
        $existing = $db->fetchOne("SELECT id FROM security_settings WHERE setting_key = ?", [$key]);
        
        if ($existing) {
            return $db->query(
                "UPDATE security_settings SET setting_value = ?, updated_at = datetime('now') WHERE setting_key = ?",
                [$value, $key]
            );
        } else {
            return $db->query(
                "INSERT INTO security_settings (setting_key, setting_value) VALUES (?, ?)",
                [$key, $value]
            );
        }
    }

    // Apply security headers
    public static function applySecurityHeaders() {
        if (!self::getSecuritySetting('security_headers_enabled', 1)) {
            return;
        }
        
        // Prevent clickjacking
        header("X-Frame-Options: SAMEORIGIN");
        
        // Prevent MIME sniffing
        header("X-Content-Type-Options: nosniff");
        
        // Enable XSS protection
        header("X-XSS-Protection: 1; mode=block");
        
        // Referrer policy
        header("Referrer-Policy: strict-origin-when-cross-origin");
        
        // Content Security Policy (basic)
        header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.youtube.com https://www.youtube-nocookie.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; img-src 'self' data: https: http:; frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com;");
        
        // Permissions Policy
        header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
    }

    // Rate limiting check
    public static function checkRateLimit($identifier, $limit = null, $window = null) {
        if (!self::getSecuritySetting('enable_rate_limiting', 1)) {
            return true;
        }
        
        $limit = $limit ?? self::getSecuritySetting('rate_limit_requests', 100);
        $window = $window ?? self::getSecuritySetting('rate_limit_window', 60);
        
        // Use session to track requests
        $key = 'rate_limit_' . $identifier;
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 1,
                'reset_time' => time() + $window
            ];
            return true;
        }
        
        // Reset if window expired
        if (time() > $_SESSION[$key]['reset_time']) {
            $_SESSION[$key] = [
                'count' => 1,
                'reset_time' => time() + $window
            ];
            return true;
        }
        
        // Increment counter
        $_SESSION[$key]['count']++;
        
        // Check if limit exceeded
        return $_SESSION[$key]['count'] <= $limit;
    }

    // Clean expired IP blocks
    public static function cleanExpiredBlocks() {
        $db = Database::getInstance();
        return $db->query(
            "DELETE FROM security_ip_blocks WHERE block_type = 'temporary' AND expires_at < datetime('now')"
        );
    }

    // Get security statistics
    public static function getSecurityStats() {
        $db = Database::getInstance();
        
        return [
            'total_login_attempts' => $db->fetchOne("SELECT COUNT(*) as count FROM security_login_attempts")['count'] ?? 0,
            'failed_logins_24h' => $db->fetchOne("SELECT COUNT(*) as count FROM security_login_attempts WHERE success = 0 AND created_at > datetime('now', '-24 hours')")['count'] ?? 0,
            'successful_logins_24h' => $db->fetchOne("SELECT COUNT(*) as count FROM security_login_attempts WHERE success = 1 AND created_at > datetime('now', '-24 hours')")['count'] ?? 0,
            'blocked_ips' => $db->fetchOne("SELECT COUNT(*) as count FROM security_ip_blocks")['count'] ?? 0,
            'active_sessions' => $db->fetchOne("SELECT COUNT(*) as count FROM security_sessions WHERE last_activity > datetime('now', '-30 minutes')")['count'] ?? 0,
            'activity_logs_24h' => $db->fetchOne("SELECT COUNT(*) as count FROM security_activity_logs WHERE created_at > datetime('now', '-24 hours')")['count'] ?? 0,
        ];
    }
}
