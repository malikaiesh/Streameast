<?php

class Video {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Add new video
    public function add($data) {
        $slug = Security::generateSlug($data['title']);
        $slug = $this->generateUniqueSlug($slug);

        $sql = "INSERT INTO videos (title, description, video_url, embed_code, thumbnail, duration, video_type, category_id, slug, meta_title, meta_description, meta_keywords, is_trending, featured) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['title'],
            $data['description'] ?? '',
            $data['video_url'],
            $data['embed_code'] ?? '',
            $data['thumbnail'] ?? '',
            $data['duration'] ?? '',
            $data['video_type'] ?? 'video',
            $data['category_id'] ?? null,
            $slug,
            $data['meta_title'] ?? $data['title'],
            $data['meta_description'] ?? $data['description'],
            $data['meta_keywords'] ?? '',
            $data['is_trending'] ?? 0,
            $data['featured'] ?? 0
        ];

        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }

    // Update video
    public function update($id, $data) {
        $sql = "UPDATE videos SET title = ?, description = ?, video_url = ?, embed_code = ?, thumbnail = ?, duration = ?, video_type = ?, category_id = ?, meta_title = ?, meta_description = ?, meta_keywords = ?, is_trending = ?, featured = ?, is_active = ? WHERE id = ?";
        
        $params = [
            $data['title'],
            $data['description'] ?? '',
            $data['video_url'],
            $data['embed_code'] ?? '',
            $data['thumbnail'] ?? '',
            $data['duration'] ?? '',
            $data['video_type'] ?? 'video',
            $data['category_id'] ?? null,
            $data['meta_title'] ?? $data['title'],
            $data['meta_description'] ?? $data['description'],
            $data['meta_keywords'] ?? '',
            $data['is_trending'] ?? 0,
            $data['featured'] ?? 0,
            $data['is_active'] ?? 1,
            $id
        ];

        return $this->db->query($sql, $params);
    }

    // Delete video
    public function delete($id) {
        $sql = "DELETE FROM videos WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }

    // Get video by ID
    public function getById($id) {
        $sql = "SELECT v.*, c.name as category_name FROM videos v 
                LEFT JOIN categories c ON v.category_id = c.id 
                WHERE v.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    // Get video by slug
    public function getBySlug($slug) {
        $sql = "SELECT v.*, c.name as category_name FROM videos v 
                LEFT JOIN categories c ON v.category_id = c.id 
                WHERE v.slug = ? AND v.is_active = 1";
        return $this->db->fetchOne($sql, [$slug]);
    }

    // Get all videos with pagination
    public function getAll($page = 1, $limit = VIDEOS_PER_PAGE, $filters = []) {
        $offset = ($page - 1) * $limit;
        $where = ["v.is_active = 1"];
        $params = [];

        if (!empty($filters['type'])) {
            $where[] = "v.video_type = ?";
            $params[] = $filters['type'];
        }

        if (!empty($filters['category_id'])) {
            $where[] = "v.category_id = ?";
            $params[] = $filters['category_id'];
        }

        if (!empty($filters['trending'])) {
            $where[] = "v.is_trending = 1";
        }

        if (!empty($filters['search'])) {
            $where[] = "(v.title LIKE ? OR v.description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $whereClause = implode(' AND ', $where);
        
        $sql = "SELECT v.*, c.name as category_name FROM videos v 
                LEFT JOIN categories c ON v.category_id = c.id 
                WHERE $whereClause 
                ORDER BY v.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;

        return $this->db->fetchAll($sql, $params);
    }

    // Get total count
    public function getCount($filters = []) {
        $where = ["is_active = 1"];
        $params = [];

        if (!empty($filters['type'])) {
            $where[] = "video_type = ?";
            $params[] = $filters['type'];
        }

        if (!empty($filters['category_id'])) {
            $where[] = "category_id = ?";
            $params[] = $filters['category_id'];
        }

        if (!empty($filters['trending'])) {
            $where[] = "is_trending = 1";
        }

        if (!empty($filters['search'])) {
            $where[] = "(title LIKE ? OR description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $whereClause = implode(' AND ', $where);
        $sql = "SELECT COUNT(*) as count FROM videos WHERE $whereClause";
        $result = $this->db->fetchOne($sql, $params);
        return $result['count'] ?? 0;
    }

    // Increment view count
    public function incrementViews($id) {
        $sql = "UPDATE videos SET views = views + 1 WHERE id = ?";
        $this->db->query($sql, [$id]);
        
        // Log analytics
        $sql = "INSERT INTO analytics_views (video_id, user_ip, user_agent) VALUES (?, ?, ?)";
        $this->db->query($sql, [$id, Security::getClientIP(), $_SERVER['HTTP_USER_AGENT'] ?? '']);
    }

    // Get related videos
    public function getRelated($videoId, $categoryId, $limit = 6) {
        $sql = "SELECT v.*, c.name as category_name FROM videos v 
                LEFT JOIN categories c ON v.category_id = c.id 
                WHERE v.id != ? AND v.category_id = ? AND v.is_active = 1 
                ORDER BY RANDOM() 
                LIMIT ?";
        return $this->db->fetchAll($sql, [$videoId, $categoryId, $limit]);
    }

    // Generate unique slug
    private function generateUniqueSlug($slug) {
        $originalSlug = $slug;
        $count = 1;
        
        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        
        return $slug;
    }

    private function slugExists($slug) {
        $sql = "SELECT COUNT(*) as count FROM videos WHERE slug = ?";
        $result = $this->db->fetchOne($sql, [$slug]);
        return $result['count'] > 0;
    }

    // Add tags to video
    public function addTags($videoId, $tagIds) {
        // Remove existing tags
        $sql = "DELETE FROM video_tags WHERE video_id = ?";
        $this->db->query($sql, [$videoId]);

        // Add new tags
        if (!empty($tagIds)) {
            foreach ($tagIds as $tagId) {
                $sql = "INSERT INTO video_tags (video_id, tag_id) VALUES (?, ?)";
                $this->db->query($sql, [$videoId, $tagId]);
            }
        }
    }

    // Get video tags
    public function getTags($videoId) {
        $sql = "SELECT t.* FROM tags t 
                JOIN video_tags vt ON t.id = vt.tag_id 
                WHERE vt.video_id = ?";
        return $this->db->fetchAll($sql, [$videoId]);
    }

    // Extract YouTube ID from URL
    public static function extractYouTubeId($url) {
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $url, $match);
        return $match[1] ?? null;
    }

    // Generate embed code
    public static function generateEmbedCode($url, $type = 'video') {
        $youtubeId = self::extractYouTubeId($url);
        if ($youtubeId) {
            if ($type === 'short') {
                return '<iframe width="100%" height="100%" src="https://www.youtube.com/embed/' . $youtubeId . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
            }
            return '<iframe width="100%" height="100%" src="https://www.youtube.com/embed/' . $youtubeId . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        }
        return '<iframe width="100%" height="100%" src="' . htmlspecialchars($url) . '" frameborder="0" allowfullscreen></iframe>';
    }
}
