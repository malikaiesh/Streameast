<?php

class Page {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($search = '', $limit = 20, $offset = 0) {
        $params = [];
        $sql = "SELECT * FROM pages WHERE 1=1";
        
        if (!empty($search)) {
            $sql .= " AND (title LIKE ? OR slug LIKE ? OR content LIKE ?)";
            $searchTerm = "%{$search}%";
            $params = [$searchTerm, $searchTerm, $searchTerm];
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }

    public function count($search = '') {
        $params = [];
        $sql = "SELECT COUNT(*) as total FROM pages WHERE 1=1";
        
        if (!empty($search)) {
            $sql .= " AND (title LIKE ? OR slug LIKE ? OR content LIKE ?)";
            $searchTerm = "%{$search}%";
            $params = [$searchTerm, $searchTerm, $searchTerm];
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result ? $result['total'] : 0;
    }

    public function getById($id) {
        $sql = "SELECT * FROM pages WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    public function getBySlug($slug) {
        $sql = "SELECT * FROM pages WHERE slug = ? AND status = 'published'";
        return $this->db->fetchOne($sql, [$slug]);
    }

    public function create($data) {
        $sql = "INSERT INTO pages (title, slug, content, meta_description, status) VALUES (?, ?, ?, ?, ?)";
        return $this->db->query($sql, [
            $data['title'],
            $data['slug'],
            $data['content'],
            $data['meta_description'] ?? '',
            $data['status'] ?? 'published'
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE pages SET title = ?, slug = ?, content = ?, meta_description = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        return $this->db->query($sql, [
            $data['title'],
            $data['slug'],
            $data['content'],
            $data['meta_description'] ?? '',
            $data['status'] ?? 'published',
            $id
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM pages WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }

    public function slugExists($slug, $excludeId = null) {
        $sql = "SELECT id FROM pages WHERE slug = ?";
        $params = [$slug];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result !== false;
    }

    public function generateSlug($title, $excludeId = null) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}
