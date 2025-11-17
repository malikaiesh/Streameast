<?php

class Blog {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    private function generateSlug($title, $excludeId = null) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    private function slugExists($slug, $excludeId = null) {
        if ($excludeId) {
            $result = $this->db->fetchOne(
                "SELECT id FROM blog_posts WHERE slug = ? AND id != ?",
                [$slug, $excludeId]
            );
        } else {
            $result = $this->db->fetchOne(
                "SELECT id FROM blog_posts WHERE slug = ?",
                [$slug]
            );
        }
        return $result !== false;
    }
    
    private $allowedTags = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 
        'a', 'ul', 'ol', 'li', 'blockquote', 
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'img', 'code', 'pre'
    ];
    
    private $allowedAttributes = [
        'a' => ['href', 'title'],
        'img' => ['src', 'alt', 'title']
    ];
    
    private $dangerousProtocols = [
        'javascript:', 'data:', 'vbscript:', 'file:', 'about:'
    ];
    
    private function sanitizeHTML($html) {
        if (empty($html)) {
            return '';
        }
        
        $dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        
        $dom->loadHTML(
            mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        
        libxml_clear_errors();
        
        $body = $dom->getElementsByTagName('body')->item(0);
        $root = $body ?: $dom->documentElement;
        
        if ($root) {
            $this->cleanNode($root);
        }
        
        $output = $dom->saveHTML();
        $output = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $output);
        
        return trim($output);
    }
    
    private function cleanNode($node) {
        for ($child = $node->firstChild; $child; $child = $next) {
            $next = $child->nextSibling;
            
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $tag = strtolower($child->nodeName);
                
                if (!in_array($tag, $this->allowedTags, true)) {
                    $this->cleanNode($child);
                    
                    while ($child->firstChild) {
                        $promoted = $child->firstChild;
                        $child->removeChild($promoted);
                        $node->insertBefore($promoted, $child);
                    }
                    $node->removeChild($child);
                    continue;
                }
                
                $this->cleanAttributes($child);
                $this->cleanNode($child);
                
            } elseif ($child->nodeType !== XML_TEXT_NODE) {
                $node->removeChild($child);
            }
        }
    }
    
    private function cleanAttributes($node) {
        $tagName = strtolower($node->nodeName);
        $allowedAttrs = $this->allowedAttributes[$tagName] ?? [];
        
        $attributesToRemove = [];
        
        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attr) {
                $attrName = strtolower($attr->name);
                $attrValue = $attr->value;
                
                if (!in_array($attrName, $allowedAttrs)) {
                    $attributesToRemove[] = $attrName;
                    continue;
                }
                
                if (in_array($attrName, ['href', 'src'])) {
                    if (!$this->isUrlSafe($attrValue)) {
                        $attributesToRemove[] = $attrName;
                    }
                }
                
                if (strpos($attrName, 'on') === 0) {
                    $attributesToRemove[] = $attrName;
                }
            }
        }
        
        foreach ($attributesToRemove as $attrName) {
            $node->removeAttribute($attrName);
        }
    }
    
    private function isUrlSafe($url) {
        $url = trim(strtolower($url));
        
        foreach ($this->dangerousProtocols as $protocol) {
            if (strpos($url, $protocol) === 0) {
                return false;
            }
        }
        
        $decoded = html_entity_decode($url, ENT_QUOTES, 'UTF-8');
        foreach ($this->dangerousProtocols as $protocol) {
            if (strpos($decoded, $protocol) === 0) {
                return false;
            }
        }
        
        return true;
    }
    
    public function create($data) {
        $slug = $this->generateSlug($data['title']);
        
        $publishedAt = null;
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $publishedAt = date('Y-m-d H:i:s');
        }
        
        $sql = "INSERT INTO blog_posts (title, slug, content, excerpt, featured_image, author_name, category, status, meta_title, meta_description, published_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        return $this->db->execute($sql, [
            strip_tags($data['title']),
            $slug,
            $this->sanitizeHTML($data['content']),
            strip_tags($data['excerpt'] ?? ''),
            $data['featured_image'] ?? null,
            strip_tags($data['author_name'] ?? 'Admin'),
            strip_tags($data['category'] ?? 'General'),
            $data['status'] ?? 'draft',
            strip_tags($data['meta_title'] ?? $data['title']),
            strip_tags($data['meta_description'] ?? $data['excerpt'] ?? ''),
            $publishedAt
        ]);
    }
    
    public function update($id, $data) {
        $slug = isset($data['title']) ? $this->generateSlug($data['title'], $id) : null;
        
        $publishedAt = null;
        if (isset($data['status']) && $data['status'] === 'published') {
            $existing = $this->getById($id);
            if (!$existing['published_at']) {
                $publishedAt = date('Y-m-d H:i:s');
            }
        }
        
        $updates = [];
        $params = [];
        
        if (isset($data['title'])) {
            $updates[] = "title = ?";
            $params[] = strip_tags($data['title']);
            $updates[] = "slug = ?";
            $params[] = $slug;
        }
        if (isset($data['content'])) {
            $updates[] = "content = ?";
            $params[] = $this->sanitizeHTML($data['content']);
        }
        if (isset($data['excerpt'])) {
            $updates[] = "excerpt = ?";
            $params[] = strip_tags($data['excerpt']);
        }
        if (isset($data['featured_image'])) {
            $updates[] = "featured_image = ?";
            $params[] = $data['featured_image'];
        }
        if (isset($data['author_name'])) {
            $updates[] = "author_name = ?";
            $params[] = strip_tags($data['author_name']);
        }
        if (isset($data['category'])) {
            $updates[] = "category = ?";
            $params[] = strip_tags($data['category']);
        }
        if (isset($data['status'])) {
            $updates[] = "status = ?";
            $params[] = $data['status'];
        }
        if (isset($data['meta_title'])) {
            $updates[] = "meta_title = ?";
            $params[] = strip_tags($data['meta_title']);
        }
        if (isset($data['meta_description'])) {
            $updates[] = "meta_description = ?";
            $params[] = strip_tags($data['meta_description']);
        }
        if ($publishedAt) {
            $updates[] = "published_at = ?";
            $params[] = $publishedAt;
        }
        
        $updates[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $id;
        
        $sql = "UPDATE blog_posts SET " . implode(", ", $updates) . " WHERE id = ?";
        
        return $this->db->execute($sql, $params);
    }
    
    public function delete($id) {
        $post = $this->getById($id);
        if ($post && $post['featured_image']) {
            $imagePath = __DIR__ . '/../' . $post['featured_image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        return $this->db->execute("DELETE FROM blog_posts WHERE id = ?", [$id]);
    }
    
    public function getById($id) {
        return $this->db->fetchOne("SELECT * FROM blog_posts WHERE id = ?", [$id]);
    }
    
    public function getBySlug($slug) {
        return $this->db->fetchOne("SELECT * FROM blog_posts WHERE slug = ?", [$slug]);
    }
    
    public function getAll($page = 1, $perPage = 10, $status = null) {
        $offset = ($page - 1) * $perPage;
        
        if ($status) {
            $sql = "SELECT * FROM blog_posts WHERE status = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
            return $this->db->fetchAll($sql, [$status, $perPage, $offset]);
        } else {
            $sql = "SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT ? OFFSET ?";
            return $this->db->fetchAll($sql, [$perPage, $offset]);
        }
    }
    
    public function getPublished($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM blog_posts WHERE status = 'published' ORDER BY published_at DESC LIMIT ? OFFSET ?";
        return $this->db->fetchAll($sql, [$perPage, $offset]);
    }
    
    public function count($status = null) {
        if ($status) {
            return $this->db->fetchOne("SELECT COUNT(*) as count FROM blog_posts WHERE status = ?", [$status])['count'];
        } else {
            return $this->db->fetchOne("SELECT COUNT(*) as count FROM blog_posts")['count'];
        }
    }
    
    public function uploadFeaturedImage($file) {
        $uploadDir = __DIR__ . '/../uploads/blog/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.'];
        }
        
        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'error' => 'File size too large. Maximum 5MB allowed.'];
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('blog_') . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'path' => 'uploads/blog/' . $filename];
        } else {
            return ['success' => false, 'error' => 'Failed to upload file.'];
        }
    }
}
