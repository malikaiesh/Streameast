<?php

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            if (DB_TYPE === 'sqlite') {
                // SQLite connection
                $this->connection = new PDO('sqlite:' . DB_PATH);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->initializeSQLite();
            } else {
                // MySQL connection
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
                if (defined('DB_SOCKET')) {
                    $dsn = "mysql:unix_socket=" . DB_SOCKET . ";dbname=" . DB_NAME;
                }
                $this->connection = new PDO($dsn, DB_USER, DB_PASS);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        } catch(PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    private function initializeSQLite() {
        // Create tables for SQLite if they don't exist
        $sql = file_get_contents(BASE_PATH . '/database_sqlite.sql');
        if ($sql) {
            $this->connection->exec($sql);
        }
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            error_log("Query error: " . $e->getMessage());
            return false;
        }
    }

    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
    }

    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
}
