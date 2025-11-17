<?php
require_once '../config/config.php';
Security::requireAdmin();

$db = Database::getInstance();
$message = '';
$error = '';

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_user') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $email = trim($_POST['email'] ?? '');
        $full_name = trim($_POST['full_name'] ?? '');
        $role = $_POST['role'] ?? 'viewer';
        
        if (empty($username) || empty($password)) {
            $error = 'Username and password are required';
        } else {
            $hashedPassword = Security::hashPassword($password);
            $result = $db->query(
                "INSERT INTO admin (username, password, email, full_name, role, status) VALUES (?, ?, ?, ?, ?, 'active')",
                [$username, $hashedPassword, $email, $full_name, $role]
            );
            
            if ($result) {
                $message = 'User added successfully';
                Security::logActivity('User Created', 'admin', $db->lastInsertId(), "Created user: $username");
            } else {
                $error = 'Failed to add user. Username may already exist.';
            }
        }
    }
    
    elseif ($action === 'edit_user') {
        $userId = $_POST['user_id'] ?? 0;
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $full_name = trim($_POST['full_name'] ?? '');
        $role = $_POST['role'] ?? 'viewer';
        $status = $_POST['status'] ?? 'active';
        $password = $_POST['password'] ?? '';
        
        if (empty($username)) {
            $error = 'Username is required';
        } else {
            if (!empty($password)) {
                $hashedPassword = Security::hashPassword($password);
                $result = $db->query(
                    "UPDATE admin SET username = ?, password = ?, email = ?, full_name = ?, role = ?, status = ?, updated_at = datetime('now') WHERE id = ?",
                    [$username, $hashedPassword, $email, $full_name, $role, $status, $userId]
                );
            } else {
                $result = $db->query(
                    "UPDATE admin SET username = ?, email = ?, full_name = ?, role = ?, status = ?, updated_at = datetime('now') WHERE id = ?",
                    [$username, $email, $full_name, $role, $status, $userId]
                );
            }
            
            if ($result) {
                $message = 'User updated successfully';
                Security::logActivity('User Updated', 'admin', $userId, "Updated user: $username");
            } else {
                $error = 'Failed to update user';
            }
        }
    }
    
    elseif ($action === 'delete_user') {
        $userId = $_POST['user_id'] ?? 0;
        
        // Prevent deleting yourself or the last super_admin
        if ($userId == $_SESSION['admin_id']) {
            $error = 'You cannot delete your own account';
        } else {
            $user = $db->fetchOne("SELECT username FROM admin WHERE id = ?", [$userId]);
            $result = $db->query("DELETE FROM admin WHERE id = ?", [$userId]);
            
            if ($result) {
                $message = 'User deleted successfully';
                Security::logActivity('User Deleted', 'admin', $userId, "Deleted user: " . ($user['username'] ?? 'Unknown'));
            } else {
                $error = 'Failed to delete user';
            }
        }
    }
}

// Get all users
$search = $_GET['search'] ?? '';
$filter_role = $_GET['filter_role'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

$whereClauses = [];
$params = [];

if (!empty($search)) {
    $whereClauses[] = "(username LIKE ? OR email LIKE ? OR full_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($filter_role)) {
    $whereClauses[] = "role = ?";
    $params[] = $filter_role;
}

$where = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

$totalUsers = $db->fetchOne("SELECT COUNT(*) as count FROM admin $where", $params)['count'];
$totalPages = ceil($totalUsers / $perPage);

$users = $db->fetchAll(
    "SELECT * FROM admin $where ORDER BY created_at DESC LIMIT $perPage OFFSET $offset",
    $params
);

$pageTitle = 'User Management';
include 'views/header.php';
?>

<div class="page-header">
    <div class="header-content">
        <div class="icon-badge">
            <span>👥</span>
        </div>
        <div>
            <h1>User Management</h1>
            <p>Manage admin users and their permissions</p>
        </div>
    </div>
    <button class="btn btn-primary" onclick="showAddUserModal()">
        <span>➕</span> Add User
    </button>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?= Security::output($message) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?= Security::output($error) ?></div>
<?php endif; ?>

<div class="filters-bar">
    <form method="GET" class="filters-form">
        <input type="text" name="search" placeholder="Search users..." value="<?= Security::output($search) ?>">
        <select name="filter_role">
            <option value="">All Roles</option>
            <option value="super_admin" <?= $filter_role === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
            <option value="admin" <?= $filter_role === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="editor" <?= $filter_role === 'editor' ? 'selected' : '' ?>>Editor</option>
            <option value="viewer" <?= $filter_role === 'viewer' ? 'selected' : '' ?>>Viewer</option>
        </select>
        <button type="submit" class="btn btn-secondary">Filter</button>
        <?php if ($search || $filter_role): ?>
            <a href="security-users.php" class="btn btn-secondary">Clear</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px;">
                            <div style="opacity: 0.5;">
                                <div style="font-size: 48px; margin-bottom: 16px;">👤</div>
                                <p>No users found</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td>
                                <strong><?= Security::output($user['username']) ?></strong>
                                <?php if ($user['id'] == $_SESSION['admin_id']): ?>
                                    <span class="badge badge-info" style="margin-left: 8px;">You</span>
                                <?php endif; ?>
                            </td>
                            <td><?= Security::output($user['full_name'] ?? '-') ?></td>
                            <td><?= Security::output($user['email'] ?? '-') ?></td>
                            <td>
                                <?php
                                $roleColors = [
                                    'super_admin' => 'badge-error',
                                    'admin' => 'badge-warning',
                                    'editor' => 'badge-info',
                                    'viewer' => 'badge-secondary'
                                ];
                                $roleLabels = [
                                    'super_admin' => 'Super Admin',
                                    'admin' => 'Admin',
                                    'editor' => 'Editor',
                                    'viewer' => 'Viewer'
                                ];
                                $badgeClass = $roleColors[$user['role'] ?? 'viewer'] ?? 'badge-secondary';
                                $roleLabel = $roleLabels[$user['role'] ?? 'viewer'] ?? 'Unknown';
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= $roleLabel ?></span>
                            </td>
                            <td>
                                <?php if (($user['status'] ?? 'active') === 'active'): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never' ?></td>
                            <td class="table-actions">
                                <button class="btn btn-sm btn-secondary" onclick='editUser(<?= json_encode($user) ?>)'>Edit</button>
                                <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                                    <button class="btn btn-sm btn-danger" onclick="deleteUser(<?= $user['id'] ?>, '<?= Security::output($user['username']) ?>')">Delete</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $filter_role ? '&filter_role=' . urlencode($filter_role) : '' ?>" 
                   class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New User</h2>
            <span class="close" onclick="closeModal('addUserModal')">&times;</span>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add_user">
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password *</label>
                <div class="password-input-wrapper">
                    <input type="password" name="password" id="add-password" required>
                    <button type="button" class="toggle-password" onclick="toggleModalPassword('add-password')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email">
            </div>
            <div class="form-group">
                <label>Role *</label>
                <select name="role" required>
                    <option value="viewer">Viewer (Read-only)</option>
                    <option value="editor">Editor (Can edit content)</option>
                    <option value="admin">Admin (Full access)</option>
                    <option value="super_admin">Super Admin (Full access + user management)</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addUserModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add User</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit User</h2>
            <span class="close" onclick="closeModal('editUserModal')">&times;</span>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="edit_user">
            <input type="hidden" name="user_id" id="edit-user-id">
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" id="edit-username" required>
            </div>
            <div class="form-group">
                <label>Password (leave blank to keep current)</label>
                <div class="password-input-wrapper">
                    <input type="password" name="password" id="edit-password">
                    <button type="button" class="toggle-password" onclick="toggleModalPassword('edit-password')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" id="edit-full-name">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="edit-email">
            </div>
            <div class="form-group">
                <label>Role *</label>
                <select name="role" id="edit-role" required>
                    <option value="viewer">Viewer (Read-only)</option>
                    <option value="editor">Editor (Can edit content)</option>
                    <option value="admin">Admin (Full access)</option>
                    <option value="super_admin">Super Admin (Full access + user management)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Status *</label>
                <select name="status" id="edit-status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editUserModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update User</button>
            </div>
        </form>
    </div>
</div>

<script>
function showAddUserModal() {
    document.getElementById('addUserModal').style.display = 'block';
}

function editUser(user) {
    document.getElementById('edit-user-id').value = user.id;
    document.getElementById('edit-username').value = user.username;
    document.getElementById('edit-full-name').value = user.full_name || '';
    document.getElementById('edit-email').value = user.email || '';
    document.getElementById('edit-role').value = user.role || 'viewer';
    document.getElementById('edit-status').value = user.status || 'active';
    document.getElementById('editUserModal').style.display = 'block';
}

function deleteUser(userId, username) {
    if (confirm(`Are you sure you want to delete user "${username}"?\n\nThis action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete_user">
            <input type="hidden" name="user_id" value="${userId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function toggleModalPassword(inputId) {
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

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>

<?php include 'views/footer.php'; ?>
