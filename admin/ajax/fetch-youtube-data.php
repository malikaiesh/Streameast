<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$videoUrl = $_POST['video_url'] ?? '';

if (empty($videoUrl)) {
    echo json_encode(['success' => false, 'message' => 'No URL provided']);
    exit;
}

$data = YouTubeHelper::fetchVideoData($videoUrl);

if ($data) {
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Could not fetch video data'
    ]);
}
