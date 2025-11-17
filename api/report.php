<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $videoId = $data['video_id'] ?? 0;
    $reason = $data['reason'] ?? '';
    
    if ($videoId && $reason) {
        $db = Database::getInstance();
        $sql = "INSERT INTO reports (video_id, reason, user_ip) VALUES (?, ?, ?)";
        $db->query($sql, [$videoId, $reason, Security::getClientIP()]);
        
        echo json_encode(['success' => true, 'message' => 'Report submitted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
