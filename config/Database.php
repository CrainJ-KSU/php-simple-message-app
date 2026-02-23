<?php
// Database Connection Class

class Database {
    private static $instance = null;
    private $connection;
    private $db_file;

    private function __construct() {
        // Database file is in the root directory (one level up from public)
        $this->db_file = dirname(__DIR__) . '/messages.db';
        $this->connection = new SQLite3($this->db_file);
        if (!$this->connection) {
            throw new Exception("Unable to connect to database");
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

    public function query($sql) {
        return $this->connection->query($sql);
    }

    public function exec($sql) {
        return $this->connection->exec($sql);
    }

    public function escape($string) {
        return SQLite3::escapeString($string);
    }

    public function lastInsertId() {
        return $this->connection->lastInsertRowID();
    }

    // Prevent cloning
    private function __clone() {}

    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}