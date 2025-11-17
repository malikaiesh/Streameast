<?php

class Tag {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Add tag
    public function add($name) {
        $slug = Security::generateSlug($name);
        $sql = "INSERT INTO tags (name, slug) VALUES (?, ?)";
        $this->db->query($sql, [$name, $slug]);
        return $this->db->lastInsertId();
    }

    // Delete tag
    public function delete($id) {
        $sql = "DELETE FROM tags WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }

    // Get all tags
    public function getAll() {
        $sql = "SELECT * FROM tags ORDER BY name ASC";
        return $this->db->fetchAll($sql);
    }

    // Get tag by ID
    public function getById($id) {
        $sql = "SELECT * FROM tags WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    // Find or create tag
    public function findOrCreate($name) {
        $slug = Security::generateSlug($name);
        
        // Check if exists
        $sql = "SELECT id FROM tags WHERE slug = ?";
        $tag = $this->db->fetchOne($sql, [$slug]);
        
        if ($tag) {
            return $tag['id'];
        }
        
        // Create new
        return $this->add($name);
    }
}
