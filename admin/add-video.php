<?php
require_once '../config/config.php';
Security::requireAdmin();

$video = new Video();
$category = new Category();
$tag = new Tag();

$categories = $category->getAll();
$tags = $tag->getAll();
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
            'meta_title' => $_POST['meta_title'] ?? $_POST['title'],
            'meta_description' => $_POST['meta_description'] ?? $_POST['description'],
            'meta_keywords' => $_POST['meta_keywords'] ?? '',
        ];

        // Generate embed code
        $data['embed_code'] = Video::generateEmbedCode($data['video_url'], $data['video_type']);

        // Handle thumbnail upload
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
        } else {
            // Auto-fetch YouTube thumbnail
            $youtubeId = Video::extractYouTubeId($data['video_url']);
            if ($youtubeId) {
                $data['thumbnail'] = 'https://img.youtube.com/vi/' . $youtubeId . '/maxresdefault.jpg';
            }
        }

        try {
            $videoId = $video->add($data);
            
            // Add tags
            if (!empty($_POST['tags'])) {
                $tagIds = [];
                foreach (explode(',', $_POST['tags']) as $tagName) {
                    $tagName = trim($tagName);
                    if ($tagName) {
                        $tagObj = new Tag();
                        $tagIds[] = $tagObj->findOrCreate($tagName);
                    }
                }
                $video->addTags($videoId, $tagIds);
            }
            
            $success = 'Video added successfully!';
        } catch (Exception $e) {
            $error = 'Error adding video: ' . $e->getMessage();
        }
    } else {
        $error = 'Invalid request';
    }
}

$csrf_token = Security::generateCSRFToken();
include 'views/header.php';
?>

<h2>Add New Video</h2>

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
            <input type="text" name="title" required>
        </div>
        <div class="form-group col-4">
            <label>Video Type *</label>
            <select name="video_type" required>
                <option value="video">Regular Video</option>
                <option value="short">YouTube Short</option>
                <option value="movie">Movie</option>
                <option value="live">Live Stream</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label>Video URL (YouTube/Embed URL) *</label>
        <input type="url" name="video_url" required placeholder="https://www.youtube.com/watch?v=...">
    </div>

    <div class="form-group">
        <label>Description</label>
        <textarea name="description" rows="4"></textarea>
    </div>

    <div class="form-row">
        <div class="form-group col-4">
            <label>Category</label>
            <select name="category_id">
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= Security::output($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group col-4">
            <label>Duration (e.g., 10:30)</label>
            <input type="text" name="duration" placeholder="10:30">
        </div>
        <div class="form-group col-4">
            <label>Thumbnail Image</label>
            <input type="file" name="thumbnail" accept="image/*">
            <small>Leave empty to auto-fetch from YouTube</small>
        </div>
    </div>

    <div class="form-group">
        <label>Tags (comma separated)</label>
        <input type="text" name="tags" placeholder="gaming, funny, tutorial">
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_trending"> Mark as Trending
        </label>
        <label class="checkbox-label">
            <input type="checkbox" name="featured"> Featured Video
        </label>
    </div>

    <hr>
    <h3>SEO Settings</h3>

    <div class="form-group">
        <label>Meta Title</label>
        <input type="text" name="meta_title" placeholder="Leave empty to use video title">
    </div>

    <div class="form-group">
        <label>Meta Description</label>
        <textarea name="meta_description" rows="2" placeholder="Leave empty to use video description"></textarea>
    </div>

    <div class="form-group">
        <label>Meta Keywords</label>
        <input type="text" name="meta_keywords" placeholder="keyword1, keyword2, keyword3">
    </div>

    <button type="submit" class="btn btn-primary">Add Video</button>
    <a href="videos.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include 'views/footer.php'; ?>
