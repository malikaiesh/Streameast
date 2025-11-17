<?php
require_once '../config/config.php';

echo "Starting migration to add role-based access control...\n\n";

$db = Database::getInstance()->getConnection();

try {
    // Check if columns already exist
    $tableInfo = $db->query("PRAGMA table_info(admin)")->fetchAll(PDO::FETCH_ASSOC);
    $existingColumns = array_column($tableInfo, 'name');
    
    // Add role column if it doesn't exist
    if (!in_array('role', $existingColumns)) {
        echo "Adding 'role' column to admin table...\n";
        $db->exec("ALTER TABLE admin ADD COLUMN role TEXT DEFAULT 'admin'");
        echo "✓ 'role' column added\n";
    } else {
        echo "✓ 'role' column already exists\n";
    }
    
    // Add full_name column if it doesn't exist
    if (!in_array('full_name', $existingColumns)) {
        echo "Adding 'full_name' column to admin table...\n";
        $db->exec("ALTER TABLE admin ADD COLUMN full_name TEXT");
        echo "✓ 'full_name' column added\n";
    } else {
        echo "✓ 'full_name' column already exists\n";
    }
    
    // Add status column if it doesn't exist
    if (!in_array('status', $existingColumns)) {
        echo "Adding 'status' column to admin table...\n";
        $db->exec("ALTER TABLE admin ADD COLUMN status TEXT DEFAULT 'active'");
        echo "✓ 'status' column added\n";
    } else {
        echo "✓ 'status' column already exists\n";
    }
    
    // Add last_login column if it doesn't exist
    if (!in_array('last_login', $existingColumns)) {
        echo "Adding 'last_login' column to admin table...\n";
        $db->exec("ALTER TABLE admin ADD COLUMN last_login DATETIME");
        echo "✓ 'last_login' column added\n";
    } else {
        echo "✓ 'last_login' column already exists\n";
    }
    
    // Add updated_at column if it doesn't exist
    if (!in_array('updated_at', $existingColumns)) {
        echo "Adding 'updated_at' column to admin table...\n";
        $db->exec("ALTER TABLE admin ADD COLUMN updated_at DATETIME");
        // Set current timestamp for existing rows
        $db->exec("UPDATE admin SET updated_at = datetime('now') WHERE updated_at IS NULL");
        echo "✓ 'updated_at' column added\n";
    } else {
        echo "✓ 'updated_at' column already exists\n";
    }
    
    // Update existing admin to super_admin role
    echo "\nUpdating existing admin users to super_admin role...\n";
    $db->exec("UPDATE admin SET role = 'super_admin', status = 'active' WHERE role IS NULL OR role = 'admin'");
    echo "✓ Admin users updated\n";
    
    echo "\n✅ Migration completed successfully!\n";
    echo "\nRole types available:\n";
    echo "  - super_admin: Full access to everything including user management\n";
    echo "  - admin: Full access except user management\n";
    echo "  - editor: Can create and edit content\n";
    echo "  - viewer: Read-only access\n";
    
} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
}
