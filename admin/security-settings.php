<?php
require_once '../config/config.php';
Security::requireAdmin();

$db = Database::getInstance();
$message = '';
$error = '';

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request';
    } else {
        // Update all settings
        Security::updateSecuritySetting('max_login_attempts', $_POST['max_login_attempts']);
        Security::updateSecuritySetting('login_lockout_time', $_POST['login_lockout_time']);
        Security::updateSecuritySetting('session_timeout', $_POST['session_timeout']);
        Security::updateSecuritySetting('enable_rate_limiting', isset($_POST['enable_rate_limiting']) ? '1' : '0');
        Security::updateSecuritySetting('rate_limit_requests', $_POST['rate_limit_requests']);
        Security::updateSecuritySetting('rate_limit_window', $_POST['rate_limit_window']);
        Security::updateSecuritySetting('security_headers_enabled', isset($_POST['security_headers_enabled']) ? '1' : '0');
        
        $message = 'Security settings updated successfully!';
        Security::logActivity('Update Security Settings', 'security_settings', null, 'Updated security configuration');
    }
}

// Get current settings
$settings = [
    'max_login_attempts' => Security::getSecuritySetting('max_login_attempts', 5),
    'login_lockout_time' => Security::getSecuritySetting('login_lockout_time', 900),
    'session_timeout' => Security::getSecuritySetting('session_timeout', 1800),
    'enable_rate_limiting' => Security::getSecuritySetting('enable_rate_limiting', 1),
    'rate_limit_requests' => Security::getSecuritySetting('rate_limit_requests', 100),
    'rate_limit_window' => Security::getSecuritySetting('rate_limit_window', 60),
    'security_headers_enabled' => Security::getSecuritySetting('security_headers_enabled', 1),
];

$pageTitle = 'Security Settings';
include 'views/header.php';
?>

<style>
.security-header {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    padding: 30px 40px;
    border-radius: 12px;
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.security-header-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

.security-icon-badge {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
}

.security-header-info h1 {
    margin: 0;
    color: white;
    font-size: 32px;
    font-weight: 700;
}

.security-header-subtitle {
    color: rgba(255,255,255,0.9);
    font-size: 14px;
    margin-top: 5px;
}

.settings-container {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 30px;
    max-width: 800px;
}

.settings-section {
    margin-bottom: 40px;
}

.settings-section h3 {
    color: var(--text-primary);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--border);
}

.setting-item {
    margin-bottom: 25px;
}

.setting-item label {
    display: block;
    color: var(--text-primary);
    font-weight: 600;
    margin-bottom: 8px;
}

.setting-item .description {
    color: var(--text-secondary);
    font-size: 13px;
    margin-bottom: 10px;
}

.setting-input {
    width: 100%;
    max-width: 300px;
    padding: 10px;
    background: var(--bg-secondary);
    border: 1px solid var(--border);
    border-radius: 6px;
    color: var(--text-primary);
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 28px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 28px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #10b981;
}

input:checked + .slider:before {
    transform: translateX(22px);
}
</style>

<!-- Header -->
<div class="security-header">
    <div class="security-header-left">
        <div class="security-icon-badge">⚙️</div>
        <div class="security-header-info">
            <h1>Security Settings</h1>
            <div class="security-header-subtitle">Configure security features and policies</div>
        </div>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?= Security::output($message) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?= Security::output($error) ?></div>
<?php endif; ?>

<div class="settings-container">
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        
        <!-- Login Protection -->
        <div class="settings-section">
            <h3>🔐 Login Protection</h3>
            
            <div class="setting-item">
                <label>Max Login Attempts</label>
                <div class="description">
                    Number of failed login attempts before IP is temporarily blocked
                </div>
                <input type="number" name="max_login_attempts" class="setting-input" 
                       value="<?= $settings['max_login_attempts'] ?>" min="3" max="20">
            </div>
            
            <div class="setting-item">
                <label>Login Lockout Time (seconds)</label>
                <div class="description">
                    How long an IP remains blocked after exceeding login attempts
                </div>
                <select name="login_lockout_time" class="setting-input">
                    <option value="300" <?= $settings['login_lockout_time'] == 300 ? 'selected' : '' ?>>5 minutes</option>
                    <option value="900" <?= $settings['login_lockout_time'] == 900 ? 'selected' : '' ?>>15 minutes</option>
                    <option value="1800" <?= $settings['login_lockout_time'] == 1800 ? 'selected' : '' ?>>30 minutes</option>
                    <option value="3600" <?= $settings['login_lockout_time'] == 3600 ? 'selected' : '' ?>>1 hour</option>
                    <option value="86400" <?= $settings['login_lockout_time'] == 86400 ? 'selected' : '' ?>>24 hours</option>
                </select>
            </div>
        </div>
        
        <!-- Session Management -->
        <div class="settings-section">
            <h3>⏱️ Session Management</h3>
            
            <div class="setting-item">
                <label>Session Timeout (seconds)</label>
                <div class="description">
                    Auto-logout inactive admin sessions after this duration
                </div>
                <select name="session_timeout" class="setting-input">
                    <option value="900" <?= $settings['session_timeout'] == 900 ? 'selected' : '' ?>>15 minutes</option>
                    <option value="1800" <?= $settings['session_timeout'] == 1800 ? 'selected' : '' ?>>30 minutes</option>
                    <option value="3600" <?= $settings['session_timeout'] == 3600 ? 'selected' : '' ?>>1 hour</option>
                    <option value="7200" <?= $settings['session_timeout'] == 7200 ? 'selected' : '' ?>>2 hours</option>
                </select>
            </div>
        </div>
        
        <!-- Rate Limiting -->
        <div class="settings-section">
            <h3>🚦 Rate Limiting</h3>
            
            <div class="setting-item">
                <label>
                    <label class="toggle-switch">
                        <input type="checkbox" name="enable_rate_limiting" 
                               <?= $settings['enable_rate_limiting'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                    Enable Rate Limiting
                </label>
                <div class="description">
                    Limit number of requests from a single IP to prevent abuse
                </div>
            </div>
            
            <div class="setting-item">
                <label>Max Requests per Window</label>
                <div class="description">
                    Maximum number of requests allowed per time window
                </div>
                <input type="number" name="rate_limit_requests" class="setting-input" 
                       value="<?= $settings['rate_limit_requests'] ?>" min="10" max="1000">
            </div>
            
            <div class="setting-item">
                <label>Time Window (seconds)</label>
                <div class="description">
                    Time period for rate limit counter
                </div>
                <select name="rate_limit_window" class="setting-input">
                    <option value="60" <?= $settings['rate_limit_window'] == 60 ? 'selected' : '' ?>>1 minute</option>
                    <option value="300" <?= $settings['rate_limit_window'] == 300 ? 'selected' : '' ?>>5 minutes</option>
                    <option value="600" <?= $settings['rate_limit_window'] == 600 ? 'selected' : '' ?>>10 minutes</option>
                </select>
            </div>
        </div>
        
        <!-- Security Headers -->
        <div class="settings-section">
            <h3>🛡️ Security Headers</h3>
            
            <div class="setting-item">
                <label>
                    <label class="toggle-switch">
                        <input type="checkbox" name="security_headers_enabled" 
                               <?= $settings['security_headers_enabled'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                    Enable Security Headers
                </label>
                <div class="description">
                    Add HTTP security headers (X-Frame-Options, CSP, X-XSS-Protection, etc.)
                </div>
            </div>
        </div>
        
        <button type="submit" name="save_settings" class="btn btn-primary btn-large">
            💾 Save Settings
        </button>
    </form>
</div>

<?php include 'views/footer.php'; ?>
