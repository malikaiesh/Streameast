<?php

class HomeContent {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $sql = "INSERT INTO home_content_sections (title, content, display_order, is_active) 
                VALUES (?, ?, ?, ?)";
        
        return $this->db->execute($sql, [
            strip_tags($data['title']),
            $data['content'], // Allow HTML content
            $data['display_order'] ?? 0,
            isset($data['is_active']) ? 1 : 0
        ]);
    }
    
    public function update($id, $data) {
        $updates = [];
        $params = [];
        
        if (isset($data['title'])) {
            $updates[] = "title = ?";
            $params[] = strip_tags($data['title']);
        }
        if (isset($data['content'])) {
            $updates[] = "content = ?";
            $params[] = $data['content'];
        }
        if (isset($data['display_order'])) {
            $updates[] = "display_order = ?";
            $params[] = (int)$data['display_order'];
        }
        if (isset($data['is_active'])) {
            $updates[] = "is_active = ?";
            $params[] = $data['is_active'] ? 1 : 0;
        }
        
        $updates[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $id;
        
        $sql = "UPDATE home_content_sections SET " . implode(", ", $updates) . " WHERE id = ?";
        
        return $this->db->execute($sql, $params);
    }
    
    public function delete($id) {
        return $this->db->execute("DELETE FROM home_content_sections WHERE id = ?", [$id]);
    }
    
    public function getById($id) {
        return $this->db->fetchOne("SELECT * FROM home_content_sections WHERE id = ?", [$id]);
    }
    
    public function getAll() {
        return $this->db->fetchAll("SELECT * FROM home_content_sections ORDER BY display_order ASC, created_at DESC");
    }
    
    public function getActive() {
        return $this->db->fetchAll("SELECT * FROM home_content_sections WHERE is_active = true ORDER BY display_order ASC, created_at DESC");
    }
    
    public function count() {
        return $this->db->fetchOne("SELECT COUNT(*) as count FROM home_content_sections")['count'];
    }
}
