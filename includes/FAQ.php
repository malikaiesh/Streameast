<?php

class FAQ {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $sql = "INSERT INTO faqs (question, answer, display_order, is_active) 
                VALUES (?, ?, ?, ?)";
        
        return $this->db->execute($sql, [
            strip_tags($data['question']),
            $data['answer'], // Allow HTML in answer
            $data['display_order'] ?? 0,
            isset($data['is_active']) ? 1 : 0
        ]);
    }
    
    public function update($id, $data) {
        $updates = [];
        $params = [];
        
        if (isset($data['question'])) {
            $updates[] = "question = ?";
            $params[] = strip_tags($data['question']);
        }
        if (isset($data['answer'])) {
            $updates[] = "answer = ?";
            $params[] = $data['answer'];
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
        
        $sql = "UPDATE faqs SET " . implode(", ", $updates) . " WHERE id = ?";
        
        return $this->db->execute($sql, $params);
    }
    
    public function delete($id) {
        return $this->db->execute("DELETE FROM faqs WHERE id = ?", [$id]);
    }
    
    public function getById($id) {
        return $this->db->fetchOne("SELECT * FROM faqs WHERE id = ?", [$id]);
    }
    
    public function getAll() {
        return $this->db->fetchAll("SELECT * FROM faqs ORDER BY display_order ASC, created_at DESC");
    }
    
    public function getActive() {
        return $this->db->fetchAll("SELECT * FROM faqs WHERE is_active = true ORDER BY display_order ASC, created_at DESC");
    }
    
    public function count() {
        return $this->db->fetchOne("SELECT COUNT(*) as count FROM faqs")['count'];
    }
}
