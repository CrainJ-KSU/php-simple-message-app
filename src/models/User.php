<?php
// User Model

class User {
    private $db;
    private $id;
    private $username;
    private $email;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    // Find user by credentials
    public function authenticate($username, $password) {
        $username = $this->db->escape($username);
        $password = $this->db->escape($password);

        $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = $this->db->query($query);
        $user = $result->fetchArray(SQLITE3_ASSOC);

        if ($user) {
            $this->id = $user['id'];
            $this->username = $user['username'];
            $this->email = $user['email'];
            return true;
        }

        return false;
    }

    // Create new user
    public function create($username, $password, $email = '') {
        $username = $this->db->escape($username);
        $password = $this->db->escape($password);
        $email = $this->db->escape($email);

        // Check if username exists
        if ($this->findByUsername($username)) {
            return false;
        }

        $query = "INSERT INTO users (username, password, email) VALUES ('$username', '$password', '$email')";

        if ($this->db->exec($query)) {
            $this->id = $this->db->lastInsertId();
            $this->username = $username;
            $this->email = $email;
            return true;
        }

        return false;
    }

    // Find user by username
    public function findByUsername($username) {
        $username = $this->db->escape($username);
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = $this->db->query($query);
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    // Find user by ID
    public function findById($id) {
        $id = intval($id);
        $query = "SELECT * FROM users WHERE id = $id";
        $result = $this->db->query($query);
        $user = $result->fetchArray(SQLITE3_ASSOC);

        if ($user) {
            $this->id = $user['id'];
            $this->username = $user['username'];
            $this->email = $user['email'];
            return $user;
        }

        return null;
    }

    // Get all users except current
    public function getAllExcept($userId) {
        $userId = intval($userId);
        $query = "SELECT * FROM users WHERE id != $userId ORDER BY username";
        $result = $this->db->query($query);

        $users = array();
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $users[] = $row;
        }

        return $users;
    }
}