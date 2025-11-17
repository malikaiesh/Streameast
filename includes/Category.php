<?php

class Category {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Add category
    public function add($name, $description = '') {
        $slug = Security::generateSlug($name);
        $sql = "INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)";
        $this->db->query($sql, [$name, $slug, $description]);
        return $this->db->lastInsertId();
    }

    // Update category
    public function update($id, $name, $description = '') {
        $sql = "UPDATE categories SET name = ?, description = ? WHERE id = ?";
        return $this->db->query($sql, [$name, $description, $id]);
    }

    // Delete category
    public function delete($id) {
        $sql = "DELETE FROM categories WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }

    // Get all categories
    public function getAll() {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        return $this->db->fetchAll($sql);
    }

    // Get category by ID
    public function getById($id) {
        $sql = "SELECT * FROM categories WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    // Get category by slug
    public function getBySlug($slug) {
        $sql = "SELECT * FROM categories WHERE slug = ?";
        return $this->db->fetchOne($sql, [$slug]);
    }
}
