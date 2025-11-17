<?php
require_once '../config/config.php';
Security::requireAdmin();

$db = Database::getInstance();

// Handle filtering
$filterType = $_GET['filter'] ?? 'all';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 50;
$offset = ($page - 1) * $perPage;

// Build query based on filter
if ($filterType === 'logins') {
    $logs = $db->fetchAll(
        "SELECT * FROM security_login_attempts ORDER BY created_at DESC LIMIT ? OFFSET ?",
        [$perPage, $offset]
    );
    $total = $db->fetchOne("SELECT COUNT(*) as count FROM security_login_attempts")['count'];
} else {
    $logs = $db->fetchAll(
        "SELECT sal.*, a.username FROM security_activity_logs sal 
         LEFT JOIN admin a ON sal.admin_id = a.id 
         ORDER BY sal.created_at DESC LIMIT ? OFFSET ?",
        [$perPage, $offset]
    );
    $total = $db->fetchOne("SELECT COUNT(*) as count FROM security_activity_logs")['count'];
}

$totalPages = ceil($total / $perPage);

$pageTitle = 'Activity Logs';
include 'views/header.php';
?>

<style>
.security-header {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
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

.filter-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 25px;
}

.filter-tab {
    padding: 10px 20px;
    background: var(--bg-secondary);
    border: none;
    border-radius: 8px;
    color: var(--text-primary);
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}

.filter-tab.active {
    background: #667eea;
    color: white;
}

.filter-tab:hover {
    background: #667eea;
    color: white;
}

.logs-container {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 25px;
}

.log-table {
    width: 100%;
    border-collapse: collapse;
}

.log-table th {
    text-align: left;
    padding: 12px;
    background: var(--bg-secondary);
    color: var(--text-secondary);
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.log-table td {
    padding: 15px 12px;
    border-bottom: 1px solid var(--border);
}

.log-table tbody tr:hover {
    background: var(--bg-hover);
}

.badge-success {
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.badge-danger {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid var(--border);
}

.page-link {
    padding: 8px 16px;
    background: var(--bg-secondary);
    color: var(--text-primary);
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.2s;
}

.page-link:hover,
.page-link.active {
    background: #667eea;
    color: white;
}
</style>

<!-- Header -->
<div class="security-header">
    <div class="security-header-left">
        <div class="security-icon-badge">📊</div>
        <div class="security-header-info">
            <h1>Activity Logs</h1>
            <div class="security-header-subtitle">Monitor all security events and admin actions</div>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="filter-tabs">
    <a href="?filter=all" class="filter-tab <?= $filterType === 'all' ? 'active' : '' ?>">
        📝 All Activity
    </a>
    <a href="?filter=logins" class="filter-tab <?= $filterType === 'logins' ? 'active' : '' ?>">
        🔐 Login Attempts
    </a>
</div>

<!-- Logs Table -->
<div class="logs-container">
    <table class="log-table">
        <thead>
            <tr>
                <?php if ($filterType === 'logins'): ?>
                    <th>Username</th>
                    <th>IP Address</th>
                    <th>Status</th>
                    <th>Time</th>
                <?php else: ?>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>IP Address</th>
                    <th>Time</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="<?= $filterType === 'logins' ? 4 : 5 ?>" style="text-align: center; padding: 40px;">
                        No logs found
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <?php if ($filterType === 'logins'): ?>
                            <td><strong><?= Security::output($log['username'] ?? 'Unknown') ?></strong></td>
                            <td><?= Security::output($log['ip_address']) ?></td>
                            <td>
                                <?php if ($log['success']): ?>
                                    <span class="badge-success">✓ Success</span>
                                <?php else: ?>
                                    <span class="badge-danger">✗ Failed</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('M d, Y H:i:s', strtotime($log['created_at'])) ?></td>
                        <?php else: ?>
                            <td><strong><?= Security::output($log['username'] ?? 'Unknown') ?></strong></td>
                            <td><?= Security::output($log['action']) ?></td>
                            <td>
                                <?php if ($log['table_name']): ?>
                                    <?= Security::output($log['table_name']) ?>
                                    <?php if ($log['record_id']): ?>
                                        #<?= $log['record_id'] ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?= $log['details'] ? ' - ' . Security::output($log['details']) : '' ?>
                            </td>
                            <td><?= Security::output($log['ip_address']) ?></td>
                            <td><?= date('M d, Y H:i:s', strtotime($log['created_at'])) ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?filter=<?= $filterType ?>&page=<?= $page - 1 ?>" class="page-link">← Previous</a>
            <?php endif; ?>

            <span class="page-link active">Page <?= $page ?> of <?= $totalPages ?></span>

            <?php if ($page < $totalPages): ?>
                <a href="?filter=<?= $filterType ?>&page=<?= $page + 1 ?>" class="page-link">Next →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'views/footer.php'; ?>
