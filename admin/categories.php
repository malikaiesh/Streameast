<?php
require_once '../config/config.php';
Security::requireAdmin();

$category = new Category();
$categories = $category->getAll();
$success = '';
$error = '';

// Add category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (Security::verifyCSRFToken($csrf_token)) {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        if ($name) {
            $category->add($name, $description);
            $success = 'Category added successfully!';
            header('Location: categories.php');
            exit;
        }
    }
}

// Delete category
if (isset($_GET['delete']) && isset($_GET['csrf'])) {
    if (Security::verifyCSRFToken($_GET['csrf'])) {
        $category->delete($_GET['delete']);
        header('Location: categories.php');
        exit;
    }
}

$csrf_token = Security::generateCSRFToken();
include 'views/header.php';
?>

<div class="page-header">
    <h2>Categories</h2>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?= Security::output($success) ?></div>
<?php endif; ?>

<div class="admin-grid">
    <div class="grid-col-8">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= $cat['id'] ?></td>
                        <td><strong><?= Security::output($cat['name']) ?></strong></td>
                        <td><?= Security::output($cat['slug']) ?></td>
                        <td><?= Security::output($cat['description']) ?></td>
                        <td>
                            <a href="?delete=<?= $cat['id'] ?>&csrf=<?= $csrf_token ?>" class="btn-small btn-danger" onclick="return confirm('Delete this category?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="grid-col-4">
        <div class="card">
            <h3>Add New Category</h3>
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <div class="form-group">
                    <label>Category Name *</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
                <button type="submit" name="add" class="btn btn-primary">Add Category</button>
            </form>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>
