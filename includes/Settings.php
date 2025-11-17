<?php

class Settings {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Get setting value
    public function get($key, $default = '') {
        $sql = "SELECT setting_value FROM site_settings WHERE setting_key = ?";
        $result = $this->db->fetchOne($sql, [$key]);
        return $result ? $result['setting_value'] : $default;
    }

    // Set setting value
    public function set($key, $value) {
        // Check if exists
        $sql = "SELECT id FROM site_settings WHERE setting_key = ?";
        $exists = $this->db->fetchOne($sql, [$key]);

        if ($exists) {
            $sql = "UPDATE site_settings SET setting_value = ? WHERE setting_key = ?";
            $this->db->query($sql, [$value, $key]);
        } else {
            $sql = "INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)";
            $this->db->query($sql, [$key, $value]);
        }
    }

    // Get all settings
    public function getAll() {
        $sql = "SELECT * FROM site_settings";
        $results = $this->db->fetchAll($sql);
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    // Get custom code
    public function getCustomCode($location) {
        $sql = "SELECT code_content FROM custom_codes WHERE code_location = ? AND is_active = 1";
        $result = $this->db->fetchOne($sql, [$location]);
        return $result ? $result['code_content'] : '';
    }

    // Set custom code
    public function setCustomCode($location, $code) {
        // Check if exists
        $sql = "SELECT id FROM custom_codes WHERE code_location = ?";
        $exists = $this->db->fetchOne($sql, [$location]);

        if ($exists) {
            $sql = "UPDATE custom_codes SET code_content = ? WHERE code_location = ?";
            $this->db->query($sql, [$code, $location]);
        } else {
            $sql = "INSERT INTO custom_codes (code_location, code_content) VALUES (?, ?)";
            $this->db->query($sql, [$location, $code]);
        }
    }

    // Get active ads by position
    public function getAds($position) {
        $sql = "SELECT ad_code FROM ads WHERE ad_position = ? AND is_active = 1";
        return $this->db->fetchAll($sql, [$position]);
    }

    // Add ad
    public function addAd($name, $position, $code) {
        $sql = "INSERT INTO ads (ad_name, ad_position, ad_code) VALUES (?, ?, ?)";
        return $this->db->query($sql, [$name, $position, $code]);
    }
}
