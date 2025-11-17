<?php
require_once '../config/config.php';
Security::requireAdmin();

$db = Database::getInstance();
$message = '';
$error = '';

// Get current user data
$currentUser = $db->fetchOne("SELECT * FROM admin WHERE id = ?", [$_SESSION['admin_id']]);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $full_name = trim($_POST['full_name'] ?? '');
        
        if (empty($username)) {
            $error = 'Username is required';
        } else {
            $result = $db->query(
                "UPDATE admin SET username = ?, email = ?, full_name = ?, updated_at = datetime('now') WHERE id = ?",
                [$username, $email, $full_name, $_SESSION['admin_id']]
            );
            
            if ($result) {
                $_SESSION['admin_username'] = $username;
                $message = 'Profile updated successfully';
                Security::logActivity('Profile Updated', 'admin', $_SESSION['admin_id'], 'Updated profile information');
                $currentUser = $db->fetchOne("SELECT * FROM admin WHERE id = ?", [$_SESSION['admin_id']]);
            } else {
                $error = 'Failed to update profile. Username may already exist.';
            }
        }
    }
    
    elseif ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = 'All password fields are required';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'New password and confirm password do not match';
        } elseif (strlen($newPassword) < 6) {
            $error = 'New password must be at least 6 characters';
        } elseif (!Security::verifyPassword($currentPassword, $currentUser['password'])) {
            $error = 'Current password is incorrect';
        } else {
            $hashedPassword = Security::hashPassword($newPassword);
            $result = $db->query(
                "UPDATE admin SET password = ?, updated_at = datetime('now') WHERE id = ?",
                [$hashedPassword, $_SESSION['admin_id']]
            );
            
            if ($result) {
                $message = 'Password changed successfully';
                Security::logActivity('Password Changed', 'admin', $_SESSION['admin_id'], 'Changed account password');
            } else {
                $error = 'Failed to change password';
            }
        }
    }
}

$pageTitle = 'Account Settings';
include 'views/header.php';
?>

<div class="page-header">
    <div class="header-content">
        <div class="icon-badge">
            <span>👤</span>
        </div>
        <div>
            <h1>Account Settings</h1>
            <p>Manage your account information and security</p>
        </div>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?= Security::output($message) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?= Security::output($error) ?></div>
<?php endif; ?>

<div class="settings-grid">
    <!-- Profile Information -->
    <div class="card">
        <div class="card-header">
            <h2>Profile Information</h2>
            <p>Update your account details</p>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="update_profile">
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" value="<?= Security::output($currentUser['username']) ?>" required>
            </div>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?= Security::output($currentUser['full_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= Security::output($currentUser['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Role</label>
                <input type="text" value="<?= ucfirst(str_replace('_', ' ', $currentUser['role'] ?? 'admin')) ?>" disabled>
                <small>Your role determines your access level in the admin panel</small>
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>

    <!-- Change Password -->
    <div class="card">
        <div class="card-header">
            <h2>Change Password</h2>
            <p>Update your password to keep your account secure</p>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="change_password">
            <div class="form-group">
                <label>Current Password *</label>
                <div class="password-input-wrapper">
                    <input type="password" name="current_password" id="current-password" required>
                    <button type="button" class="toggle-password" onclick="togglePasswordField('current-password')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label>New Password *</label>
                <div class="password-input-wrapper">
                    <input type="password" name="new_password" id="new-password" required minlength="6">
                    <button type="button" class="toggle-password" onclick="togglePasswordField('new-password')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                <small>Password must be at least 6 characters long</small>
            </div>
            <div class="form-group">
                <label>Confirm New Password *</label>
                <div class="password-input-wrapper">
                    <input type="password" name="confirm_password" id="confirm-password" required minlength="6">
                    <button type="button" class="toggle-password" onclick="togglePasswordField('confirm-password')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Change Password</button>
        </form>
    </div>

    <!-- Account Information -->
    <div class="card">
        <div class="card-header">
            <h2>Account Information</h2>
            <p>View your account details</p>
        </div>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Account ID</span>
                <span class="info-value">#<?= $currentUser['id'] ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Member Since</span>
                <span class="info-value"><?= date('M d, Y', strtotime($currentUser['created_at'])) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Last Login</span>
                <span class="info-value">
                    <?= $currentUser['last_login'] ? date('M d, Y H:i', strtotime($currentUser['last_login'])) : 'Never' ?>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Account Status</span>
                <span class="info-value">
                    <span class="badge badge-success">
                        <?= ucfirst($currentUser['status'] ?? 'active') ?>
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>

<style>
.settings-grid {
    display: grid;
    gap: 24px;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
}

.card-header {
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e5e7eb;
}

.card-header h2 {
    margin: 0 0 8px 0;
    font-size: 18px;
    color: #1f2937;
}

.card-header p {
    margin: 0;
    font-size: 14px;
    color: #6b7280;
}

.info-grid {
    display: grid;
    gap: 16px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: #f9fafb;
    border-radius: 6px;
}

.info-label {
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
}

.info-value {
    font-size: 14px;
    color: #1f2937;
    font-weight: 600;
}
</style>

<script>
function togglePasswordField(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling;
    const svg = button.querySelector('svg');
    
    if (input.type === 'password') {
        input.type = 'text';
        svg.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
    } else {
        input.type = 'password';
        svg.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
    }
}
</script>

<?php include 'views/footer.php'; ?>
