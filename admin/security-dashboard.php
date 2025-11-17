<?php
require_once '../config/config.php';
Security::requireAdmin();

// Clean expired blocks
Security::cleanExpiredBlocks();

// Get security statistics
$stats = Security::getSecurityStats();

$db = Database::getInstance();

// Get recent failed login attempts
$recentFailedLogins = $db->fetchAll(
    "SELECT * FROM security_login_attempts WHERE success = 0 ORDER BY created_at DESC LIMIT 10"
);

// Get blocked IPs
$blockedIPs = $db->fetchAll(
    "SELECT * FROM security_ip_blocks ORDER BY created_at DESC LIMIT 10"
);

// Get recent activity
$recentActivity = $db->fetchAll(
    "SELECT sal.*, a.username FROM security_activity_logs sal 
     LEFT JOIN admin a ON sal.admin_id = a.id 
     ORDER BY sal.created_at DESC LIMIT 15"
);

$pageTitle = 'Security Dashboard';
include 'views/header.php';
?>

<style>
.security-header {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: var(--card-bg);
    padding: 25px;
    border-radius: 12px;
    border-left: 4px solid;
}

.stat-card.danger {
    border-color: #ef4444;
}

.stat-card.success {
    border-color: #10b981;
}

.stat-card.warning {
    border-color: #f59e0b;
}

.stat-card.info {
    border-color: #3b82f6;
}

.stat-label {
    color: var(--text-secondary);
    font-size: 14px;
    margin-bottom: 10px;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--text-primary);
}

.content-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 25px;
}

.info-card {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 25px;
}

.info-card h3 {
    margin-top: 0;
    margin-bottom: 20px;
    color: var(--text-primary);
    font-size: 18px;
}

.log-item {
    padding: 12px;
    background: var(--bg-secondary);
    border-radius: 6px;
    margin-bottom: 10px;
    font-size: 13px;
}

.log-item-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
}

.log-username {
    font-weight: 600;
    color: var(--text-primary);
}

.log-time {
    color: var(--text-secondary);
    font-size: 12px;
}

.log-details {
    color: var(--text-secondary);
}

.ip-blocked {
    color: #ef4444;
    font-weight: 600;
}

.no-data {
    text-align: center;
    padding: 40px;
    color: var(--text-secondary);
}
</style>

<!-- Header -->
<div class="security-header">
    <div class="security-header-left">
        <div class="security-icon-badge">🔒</div>
        <div class="security-header-info">
            <h1>Security Dashboard</h1>
            <div class="security-header-subtitle">Monitor and manage website security</div>
        </div>
    </div>
</div>

<!-- Statistics Grid -->
<div class="stats-grid">
    <div class="stat-card danger">
        <div class="stat-label">Failed Logins (24h)</div>
        <div class="stat-value"><?= number_format($stats['failed_logins_24h']) ?></div>
    </div>
    
    <div class="stat-card success">
        <div class="stat-label">Successful Logins (24h)</div>
        <div class="stat-value"><?= number_format($stats['successful_logins_24h']) ?></div>
    </div>
    
    <div class="stat-card warning">
        <div class="stat-label">Blocked IPs</div>
        <div class="stat-value"><?= number_format($stats['blocked_ips']) ?></div>
    </div>
    
    <div class="stat-card info">
        <div class="stat-label">Activity Logs (24h)</div>
        <div class="stat-value"><?= number_format($stats['activity_logs_24h']) ?></div>
    </div>
</div>

<!-- Content Grid -->
<div class="content-grid">
    <!-- Recent Failed Logins -->
    <div class="info-card">
        <h3>🚫 Recent Failed Login Attempts</h3>
        <?php if (empty($recentFailedLogins)): ?>
            <div class="no-data">No failed login attempts</div>
        <?php else: ?>
            <?php foreach ($recentFailedLogins as $attempt): ?>
                <div class="log-item">
                    <div class="log-item-header">
                        <span class="log-username"><?= Security::output($attempt['username'] ?? 'Unknown') ?></span>
                        <span class="log-time"><?= date('M d, H:i', strtotime($attempt['created_at'])) ?></span>
                    </div>
                    <div class="log-details">
                        IP: <span class="ip-blocked"><?= Security::output($attempt['ip_address']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div style="margin-top: 15px;">
            <a href="security-activity.php" class="btn btn-sm btn-secondary">View All Login Attempts →</a>
        </div>
    </div>
    
    <!-- Blocked IPs -->
    <div class="info-card">
        <h3>⛔ Blocked IP Addresses</h3>
        <?php if (empty($blockedIPs)): ?>
            <div class="no-data">No blocked IPs</div>
        <?php else: ?>
            <?php foreach ($blockedIPs as $block): ?>
                <div class="log-item">
                    <div class="log-item-header">
                        <span class="ip-blocked"><?= Security::output($block['ip_address']) ?></span>
                        <span class="log-time"><?= ucfirst($block['block_type']) ?></span>
                    </div>
                    <div class="log-details">
                        <?= Security::output($block['reason']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div style="margin-top: 15px;">
            <a href="security-ip-management.php" class="btn btn-sm btn-secondary">Manage IP Blocks →</a>
        </div>
    </div>
</div>

<div style="margin-top: 25px;">
    <div class="info-card">
        <h3>📊 Recent Admin Activity</h3>
        <?php if (empty($recentActivity)): ?>
            <div class="no-data">No recent activity</div>
        <?php else: ?>
            <?php foreach ($recentActivity as $activity): ?>
                <div class="log-item">
                    <div class="log-item-header">
                        <span class="log-username"><?= Security::output($activity['username'] ?? 'Unknown') ?></span>
                        <span class="log-time"><?= date('M d, H:i', strtotime($activity['created_at'])) ?></span>
                    </div>
                    <div class="log-details">
                        <?= Security::output($activity['action']) ?>
                        <?php if ($activity['table_name']): ?>
                            - <?= Security::output($activity['table_name']) ?>
                            <?php if ($activity['record_id']): ?>
                                #<?= $activity['record_id'] ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div style="margin-top: 15px;">
            <a href="security-activity.php" class="btn btn-sm btn-secondary">View All Activity →</a>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>
