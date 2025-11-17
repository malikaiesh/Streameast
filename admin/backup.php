<?php
require_once __DIR__ . '/../config/config.php';

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance();
$message = '';
$error = '';

// Create backups directory if it doesn't exist
$backupDir = BASE_PATH . '/backups';
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// Handle backup creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_backup'])) {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request';
    } else {
        try {
            $timestamp = date('Y-m-d_H-i-s');
            $backupName = 'backup_' . $timestamp . '.zip';
            $backupPath = $backupDir . '/' . $backupName;
            
            // Create ZIP archive
            $zip = new ZipArchive();
            if ($zip->open($backupPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                
                // Add database file
                if (DB_TYPE === 'sqlite') {
                    $dbPath = BASE_PATH . '/' . DB_PATH;
                    if (file_exists($dbPath)) {
                        $zip->addFile($dbPath, 'database.db');
                    }
                } else {
                    // For MySQL, create SQL dump
                    $dumpFile = $backupDir . '/dump_temp.sql';
                    $tables = $db->fetchAll("SHOW TABLES");
                    $dump = "-- MySQL Backup\n-- Date: " . date('Y-m-d H:i:s') . "\n\n";
                    
                    foreach ($tables as $table) {
                        $tableName = array_values($table)[0];
                        $dump .= "DROP TABLE IF EXISTS `$tableName`;\n";
                        
                        $createTable = $db->fetchOne("SHOW CREATE TABLE `$tableName`");
                        $dump .= $createTable['Create Table'] . ";\n\n";
                        
                        $rows = $db->fetchAll("SELECT * FROM `$tableName`");
                        if (!empty($rows)) {
                            foreach ($rows as $row) {
                                $values = array_map(function($val) use ($db) {
                                    return is_null($val) ? 'NULL' : "'" . addslashes($val) . "'";
                                }, array_values($row));
                                $dump .= "INSERT INTO `$tableName` VALUES (" . implode(', ', $values) . ");\n";
                            }
                            $dump .= "\n";
                        }
                    }
                    
                    file_put_contents($dumpFile, $dump);
                    $zip->addFile($dumpFile, 'database.sql');
                }
                
                // Add uploads directory
                $uploadsDir = BASE_PATH . '/uploads';
                if (file_exists($uploadsDir)) {
                    $files = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($uploadsDir),
                        RecursiveIteratorIterator::LEAVES_ONLY
                    );
                    
                    foreach ($files as $file) {
                        if (!$file->isDir()) {
                            $filePath = $file->getRealPath();
                            $relativePath = 'uploads/' . substr($filePath, strlen($uploadsDir) + 1);
                            $zip->addFile($filePath, $relativePath);
                        }
                    }
                }
                
                // Add thumbnails
                $thumbDir = BASE_PATH . '/assets/thumbnails';
                if (file_exists($thumbDir)) {
                    $files = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($thumbDir),
                        RecursiveIteratorIterator::LEAVES_ONLY
                    );
                    
                    foreach ($files as $file) {
                        if (!$file->isDir()) {
                            $filePath = $file->getRealPath();
                            $relativePath = 'assets/thumbnails/' . substr($filePath, strlen($thumbDir) + 1);
                            $zip->addFile($filePath, $relativePath);
                        }
                    }
                }
                
                $zip->close();
                
                // Clean up temp MySQL dump
                if (isset($dumpFile) && file_exists($dumpFile)) {
                    unlink($dumpFile);
                }
                
                $message = 'Backup created successfully: ' . $backupName;
            } else {
                $error = 'Failed to create backup archive';
            }
        } catch (Exception $e) {
            $error = 'Backup failed: ' . $e->getMessage();
        }
    }
}

// Handle backup deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_backup'])) {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request';
    } else {
        $backupFile = basename($_POST['backup_file']);
        $backupPath = $backupDir . '/' . $backupFile;
        
        if (file_exists($backupPath) && strpos($backupFile, 'backup_') === 0) {
            if (unlink($backupPath)) {
                $message = 'Backup deleted successfully';
            } else {
                $error = 'Failed to delete backup';
            }
        } else {
            $error = 'Backup file not found';
        }
    }
}

// Handle backup download
if (isset($_GET['download'])) {
    $backupFile = basename($_GET['download']);
    $backupPath = $backupDir . '/' . $backupFile;
    
    if (file_exists($backupPath) && strpos($backupFile, 'backup_') === 0) {
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $backupFile . '"');
        header('Content-Length: ' . filesize($backupPath));
        readfile($backupPath);
        exit;
    }
}

// Get list of existing backups
$backups = [];
if (file_exists($backupDir)) {
    $files = scandir($backupDir, SCANDIR_SORT_DESCENDING);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && strpos($file, 'backup_') === 0) {
            $filePath = $backupDir . '/' . $file;
            $backups[] = [
                'name' => $file,
                'size' => filesize($filePath),
                'date' => filemtime($filePath)
            ];
        }
    }
}

// Format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

require_once 'views/header.php';
?>

<div class="page-header">
    <h2>💾 Website Backup</h2>
    <p>Create and manage full website backups including database and files</p>
</div>

<?php if ($message): ?>
    <div class="alert alert-success">
        ✅ <?= Security::output($message) ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error">
        ❌ <?= Security::output($error) ?>
    </div>
<?php endif; ?>

<div class="backup-container">
    <div class="backup-card">
        <h3>Create New Backup</h3>
        <p>This will create a complete backup of your website including:</p>
        <ul class="backup-includes">
            <li>✅ Database (<?= DB_TYPE === 'sqlite' ? 'SQLite' : 'MySQL' ?>)</li>
            <li>✅ Uploaded files (blog images, etc.)</li>
            <li>✅ Video thumbnails</li>
        </ul>
        
        <form method="POST" class="backup-form">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <button type="submit" name="create_backup" class="btn btn-primary btn-large">
                📦 Create Backup Now
            </button>
        </form>
        
        <div class="backup-info">
            <p><strong>💡 Tip:</strong> Download and store backups in a safe location regularly</p>
        </div>
    </div>
    
    <div class="backup-list-card">
        <h3>Available Backups (<?= count($backups) ?>)</h3>
        
        <?php if (empty($backups)): ?>
            <div class="empty-state">
                <span class="empty-icon">📭</span>
                <p>No backups found</p>
                <p class="empty-hint">Create your first backup using the button above</p>
            </div>
        <?php else: ?>
            <div class="backup-list">
                <?php foreach ($backups as $backup): ?>
                    <div class="backup-item">
                        <div class="backup-icon">📦</div>
                        <div class="backup-details">
                            <div class="backup-name"><?= Security::output($backup['name']) ?></div>
                            <div class="backup-meta">
                                <span class="backup-date">📅 <?= date('M d, Y - H:i', $backup['date']) ?></span>
                                <span class="backup-size">💾 <?= formatFileSize($backup['size']) ?></span>
                            </div>
                        </div>
                        <div class="backup-actions">
                            <a href="?download=<?= urlencode($backup['name']) ?>" class="btn btn-sm btn-success">
                                ⬇️ Download
                            </a>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this backup?')">
                                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                                <input type="hidden" name="backup_file" value="<?= Security::output($backup['name']) ?>">
                                <button type="submit" name="delete_backup" class="btn btn-sm btn-danger">
                                    🗑️ Delete
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.backup-container {
    max-width: 1000px;
}

.backup-card {
    background: var(--card-bg);
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.backup-card h3 {
    margin-top: 0;
    margin-bottom: 15px;
    color: var(--text-primary);
}

.backup-card p {
    color: var(--text-secondary);
    margin-bottom: 15px;
}

.backup-includes {
    list-style: none;
    padding: 0;
    margin: 20px 0;
}

.backup-includes li {
    padding: 8px 0;
    color: var(--text-primary);
}

.backup-form {
    margin: 30px 0;
}

.btn-large {
    padding: 15px 30px;
    font-size: 16px;
}

.backup-info {
    margin-top: 20px;
    padding: 15px;
    background: rgba(102, 126, 234, 0.1);
    border-left: 4px solid #667eea;
    border-radius: 6px;
}

.backup-info p {
    margin: 0;
    color: var(--text-primary);
}

.backup-list-card {
    background: var(--card-bg);
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.backup-list-card h3 {
    margin-top: 0;
    margin-bottom: 20px;
    color: var(--text-primary);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    font-size: 64px;
    display: block;
    margin-bottom: 20px;
}

.empty-state p {
    color: var(--text-secondary);
    margin: 10px 0;
}

.empty-hint {
    font-size: 14px;
}

.backup-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.backup-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    transition: all 0.2s;
}

.backup-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.backup-icon {
    font-size: 32px;
}

.backup-details {
    flex: 1;
}

.backup-name {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 5px;
}

.backup-meta {
    display: flex;
    gap: 20px;
    font-size: 14px;
    color: var(--text-secondary);
}

.backup-actions {
    display: flex;
    gap: 10px;
}

.btn-sm {
    padding: 8px 16px;
    font-size: 14px;
}

.btn-success {
    background: #10b981;
}

.btn-success:hover {
    background: #059669;
}

.btn-danger {
    background: #ef4444;
}

.btn-danger:hover {
    background: #dc2626;
}
</style>

<?php require_once 'views/footer.php'; ?>
