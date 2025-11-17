<?php
require_once '../config/config.php';
Security::requireAdmin();

$db = Database::getInstance();
$message = '';
$error = '';

// Handle IP block
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['block_ip'])) {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request';
    } else {
        $ip = trim($_POST['ip_address']);
        $reason = trim($_POST['reason']);
        $type = $_POST['block_type'];
        $duration = isset($_POST['duration']) ? (int)$_POST['duration'] : 900;
        
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            if (Security::blockIP($ip, $reason, $type, $duration)) {
                $message = 'IP address blocked successfully';
                Security::logActivity('Block IP', 'security_ip_blocks', null, "Blocked IP: $ip");
            } else {
                $error = 'Failed to block IP address';
            }
        } else {
            $error = 'Invalid IP address format';
        }
    }
}

// Handle unblock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unblock_ip'])) {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request';
    } else {
        $ip = $_POST['ip_address'];
        if (Security::unblockIP($ip)) {
            $message = 'IP address unblocked successfully';
            Security::logActivity('Unblock IP', 'security_ip_blocks', null, "Unblocked IP: $ip");
        } else {
            $error = 'Failed to unblock IP address';
        }
    }
}

// Get all blocked IPs
$blockedIPs = $db->fetchAll("SELECT * FROM security_ip_blocks ORDER BY created_at DESC");

$pageTitle = 'IP Management';
include 'views/header.php';
?>

<style>
.security-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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

.content-grid {
    display: grid;
    grid-template-columns: 400px 1fr;
    gap: 25px;
}

.form-card, .list-card {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 25px;
}

.form-card h3, .list-card h3 {
    margin-top: 0;
    color: var(--text-primary);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: var(--text-primary);
    font-weight: 600;
}

.form-input {
    width: 100%;
    padding: 10px;
    background: var(--bg-secondary);
    border: 1px solid var(--border);
    border-radius: 6px;
    color: var(--text-primary);
}

.form-select {
    width: 100%;
    padding: 10px;
    background: var(--bg-secondary);
    border: 1px solid var(--border);
    border-radius: 6px;
    color: var(--text-primary);
}

.ip-item {
    padding: 15px;
    background: var(--bg-secondary);
    border-radius: 8px;
    margin-bottom: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ip-info {
    flex: 1;
}

.ip-address {
    font-weight: 700;
    color: var(--text-primary);
    font-size: 16px;
}

.ip-reason {
    color: var(--text-secondary);
    font-size: 13px;
    margin-top: 4px;
}

.ip-meta {
    color: var(--text-secondary);
    font-size: 12px;
    margin-top: 4px;
}

.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    margin-left: 8px;
}

.badge-permanent {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
}

.badge-temporary {
    background: rgba(245, 158, 11, 0.2);
    color: #f59e0b;
}
</style>

<!-- Header -->
<div class="security-header">
    <div class="security-header-left">
        <div class="security-icon-badge">⛔</div>
        <div class="security-header-info">
            <h1>IP Management</h1>
            <div class="security-header-subtitle">Block and unblock IP addresses</div>
        </div>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?= Security::output($message) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?= Security::output($error) ?></div>
<?php endif; ?>

<div class="content-grid">
    <!-- Block IP Form -->
    <div class="form-card">
        <h3>🚫 Block IP Address</h3>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            
            <div class="form-group">
                <label>IP Address</label>
                <input type="text" name="ip_address" class="form-input" 
                       placeholder="192.168.1.1" required>
            </div>
            
            <div class="form-group">
                <label>Reason</label>
                <input type="text" name="reason" class="form-input" 
                       placeholder="Security violation" required>
            </div>
            
            <div class="form-group">
                <label>Block Type</label>
                <select name="block_type" class="form-select" id="blockType" onchange="toggleDuration()">
                    <option value="temporary">Temporary</option>
                    <option value="permanent">Permanent</option>
                </select>
            </div>
            
            <div class="form-group" id="durationGroup">
                <label>Duration (seconds)</label>
                <select name="duration" class="form-select">
                    <option value="300">5 minutes</option>
                    <option value="900" selected>15 minutes</option>
                    <option value="3600">1 hour</option>
                    <option value="86400">24 hours</option>
                    <option value="604800">7 days</option>
                </select>
            </div>
            
            <button type="submit" name="block_ip" class="btn btn-danger btn-large">
                ⛔ Block IP
            </button>
        </form>
    </div>
    
    <!-- Blocked IPs List -->
    <div class="list-card">
        <h3>📋 Blocked IP Addresses (<?= count($blockedIPs) ?>)</h3>
        
        <?php if (empty($blockedIPs)): ?>
            <div style="text-align: center; padding: 40px; color: var(--text-secondary);">
                No blocked IP addresses
            </div>
        <?php else: ?>
            <?php foreach ($blockedIPs as $block): ?>
                <div class="ip-item">
                    <div class="ip-info">
                        <div class="ip-address">
                            <?= Security::output($block['ip_address']) ?>
                            <span class="badge badge-<?= $block['block_type'] ?>">
                                <?= ucfirst($block['block_type']) ?>
                            </span>
                        </div>
                        <div class="ip-reason"><?= Security::output($block['reason']) ?></div>
                        <div class="ip-meta">
                            Blocked: <?= date('M d, Y H:i', strtotime($block['created_at'])) ?>
                            <?php if ($block['expires_at'] && $block['block_type'] === 'temporary'): ?>
                                | Expires: <?= date('M d, Y H:i', strtotime($block['expires_at'])) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <form method="POST" style="display: inline;" 
                          onsubmit="return confirm('Unblock this IP address?')">
                        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                        <input type="hidden" name="ip_address" value="<?= Security::output($block['ip_address']) ?>">
                        <button type="submit" name="unblock_ip" class="btn btn-sm btn-success">
                            ✓ Unblock
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleDuration() {
    const blockType = document.getElementById('blockType').value;
    const durationGroup = document.getElementById('durationGroup');
    durationGroup.style.display = blockType === 'temporary' ? 'block' : 'none';
}
</script>

<?php include 'views/footer.php'; ?>
