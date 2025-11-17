<?php
require_once '../config/config.php';
Security::requireAdmin();

$videoObj = new Video();
$category = new Category();
$tag = new Tag();

$id = $_GET['id'] ?? 0;
$video = $videoObj->getById($id);

if (!$video) {
    header('Location: videos.php');
    exit;
}

$categories = $category->getAll();
$tags = $tag->getAll();
$videoTags = $videoObj->getTags($id);
$videoTagIds = array_column($videoTags, 'id');

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (Security::verifyCSRFToken($csrf_token)) {
        $data = [
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'video_url' => $_POST['video_url'] ?? '',
            'video_type' => $_POST['video_type'] ?? 'video',
            'category_id' => $_POST['category_id'] ?? null,
            'duration' => $_POST['duration'] ?? '',
            'is_trending' => isset($_POST['is_trending']) ? 1 : 0,
            'featured' => isset($_POST['featured']) ? 1 : 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'meta_title' => $_POST['meta_title'] ?? $_POST['title'],
            'meta_description' => $_POST['meta_description'] ?? $_POST['description'],
            'meta_keywords' => $_POST['meta_keywords'] ?? '',
            'thumbnail' => $video['thumbnail'],
            'embed_code' => $video['embed_code'],
        ];

        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === 0) {
            $uploadDir = THUMBNAIL_PATH;
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $filename = time() . '_' . Security::sanitizeFilename($_FILES['thumbnail']['name']);
            $uploadPath = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $uploadPath)) {
                $data['thumbnail'] = 'assets/thumbnails/' . $filename;
            }
        }

        try {
            $videoObj->update($id, $data);
            
            if (!empty($_POST['tags'])) {
                $tagIds = [];
                foreach (explode(',', $_POST['tags']) as $tagName) {
                    $tagName = trim($tagName);
                    if ($tagName) {
                        $tagObj = new Tag();
                        $tagIds[] = $tagObj->findOrCreate($tagName);
                    }
                }
                $videoObj->addTags($id, $tagIds);
            }
            
            $success = 'Video updated successfully!';
            $video = $videoObj->getById($id);
        } catch (Exception $e) {
            $error = 'Error updating video: ' . $e->getMessage();
        }
    } else {
        $error = 'Invalid request';
    }
}

$csrf_token = Security::generateCSRFToken();
include 'views/header.php';
?>

<h2>Edit Video</h2>

<?php if ($success): ?>
    <div class="alert alert-success"><?= Security::output($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?= Security::output($error) ?></div>
<?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
    
    <div class="form-row">
        <div class="form-group col-8">
            <label>Video Title *</label>
            <input type="text" name="title" value="<?= Security::output($video['title']) ?>" required>
        </div>
        <div class="form-group col-4">
            <label>Video Type *</label>
            <select name="video_type" required>
                <option value="video" <?= $video['video_type'] === 'video' ? 'selected' : '' ?>>Regular Video</option>
                <option value="short" <?= $video['video_type'] === 'short' ? 'selected' : '' ?>>YouTube Short</option>
                <option value="movie" <?= $video['video_type'] === 'movie' ? 'selected' : '' ?>>Movie</option>
                <option value="live" <?= $video['video_type'] === 'live' ? 'selected' : '' ?>>Live Stream</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label>Video URL *</label>
        <input type="url" name="video_url" value="<?= Security::output($video['video_url']) ?>" required>
    </div>

    <div class="form-group">
        <label>Description</label>
        <textarea name="description" rows="4"><?= Security::output($video['description']) ?></textarea>
    </div>

    <div class="form-row">
        <div class="form-group col-4">
            <label>Category</label>
            <select name="category_id">
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $video['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= Security::output($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group col-4">
            <label>Duration</label>
            <input type="text" name="duration" value="<?= Security::output($video['duration']) ?>">
        </div>
        <div class="form-group col-4">
            <label>New Thumbnail</label>
            <input type="file" name="thumbnail" accept="image/*">
        </div>
    </div>

    <div class="form-group">
        <label>Tags (comma separated)</label>
        <input type="text" name="tags" value="<?= implode(', ', array_column($videoTags, 'name')) ?>">
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_trending" <?= $video['is_trending'] ? 'checked' : '' ?>> Trending
        </label>
        <label class="checkbox-label">
            <input type="checkbox" name="featured" <?= $video['featured'] ? 'checked' : '' ?>> Featured
        </label>
        <label class="checkbox-label">
            <input type="checkbox" name="is_active" <?= $video['is_active'] ? 'checked' : '' ?>> Active
        </label>
    </div>

    <hr>
    <h3>SEO Settings</h3>

    <div class="form-group">
        <label>Meta Title</label>
        <input type="text" name="meta_title" value="<?= Security::output($video['meta_title']) ?>">
    </div>

    <div class="form-group">
        <label>Meta Description</label>
        <textarea name="meta_description" rows="2"><?= Security::output($video['meta_description']) ?></textarea>
    </div>

    <div class="form-group">
        <label>Meta Keywords</label>
        <input type="text" name="meta_keywords" value="<?= Security::output($video['meta_keywords']) ?>">
    </div>

    <button type="submit" class="btn btn-primary">Update Video</button>
    <a href="videos.php" class="btn btn-secondary">Back to Videos</a>
</form>

<?php include 'views/footer.php'; ?>
