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
}
