<?php
require_once '../config/config.php';
Security::requireAdmin();

$tag = new Tag();
$tags = $tag->getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (Security::verifyCSRFToken($csrf_token)) {
        $name = $_POST['name'] ?? '';
        if ($name) {
            $tag->add($name);
            header('Location: tags.php');
            exit;
        }
    }
}

if (isset($_GET['delete']) && isset($_GET['csrf'])) {
    if (Security::verifyCSRFToken($_GET['csrf'])) {
        $tag->delete($_GET['delete']);
        header('Location: tags.php');
        exit;
    }
}

$csrf_token = Security::generateCSRFToken();
include 'views/header.php';
?>

<div class="page-header">
    <h2>Tags</h2>
</div>

<div class="admin-grid">
    <div class="grid-col-8">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tags as $t): ?>
                    <tr>
                        <td><?= $t['id'] ?></td>
                        <td><strong><?= Security::output($t['name']) ?></strong></td>
                        <td><?= Security::output($t['slug']) ?></td>
                        <td>
                            <a href="?delete=<?= $t['id'] ?>&csrf=<?= $csrf_token ?>" class="btn-small btn-danger" onclick="return confirm('Delete this tag?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="grid-col-4">
        <div class="card">
            <h3>Add New Tag</h3>
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <div class="form-group">
                    <label>Tag Name *</label>
                    <input type="text" name="name" required>
                </div>
                <button type="submit" name="add" class="btn btn-primary">Add Tag</button>
            </form>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>
