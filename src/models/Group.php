<?php
// Group Model

class Group {
    private $db;
    private $id;
    private $name;
    private $createdBy;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getCreatedBy() {
        return $this->createdBy;
    }

    // Create new group
    public function create($name, $createdBy) {
        $name = $this->db->escape($name);
        $createdBy = intval($createdBy);

        $query = "INSERT INTO groups (name, created_by) VALUES ('$name', $createdBy)";

        if ($this->db->exec($query)) {
            $groupId = $this->db->lastInsertId();

            // Add creator as first member
            $memberQuery = "INSERT INTO group_members (group_id, user_id) VALUES ($groupId, $createdBy)";
            $this->db->exec($memberQuery);

            $this->id = $groupId;
            $this->name = $name;
            $this->createdBy = $createdBy;

            return $groupId;
        }

        return false;
    }

    // Get user's groups
    public function getUserGroups($userId) {
        $userId = intval($userId);
        $query = "SELECT g.* FROM groups g 
                  INNER JOIN group_members gm ON g.id = gm.group_id 
                  WHERE gm.user_id = $userId 
                  ORDER BY g.name";
        $result = $this->db->query($query);

        $groups = array();
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $groups[] = $row;
        }

        return $groups;
    }

    // Get all groups
    public function getAll() {
        $query = "SELECT g.*, u.username as creator_name,
                  (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as member_count
                  FROM groups g
                  INNER JOIN users u ON g.created_by = u.id
                  ORDER BY g.created_at DESC";
        $result = $this->db->query($query);

        $groups = array();
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $groups[] = $row;
        }

        return $groups;
    }

    // Check if user is member
    public function isMember($groupId, $userId) {
        $groupId = intval($groupId);
        $userId = intval($userId);

        $query = "SELECT * FROM group_members WHERE group_id = $groupId AND user_id = $userId";
        $result = $this->db->query($query);
        return $result->fetchArray(SQLITE3_ASSOC) !== false;
    }

    // Add member to group
    public function addMember($groupId, $userId) {
        $groupId = intval($groupId);
        $userId = intval($userId);

        // Check if already a member
        if ($this->isMember($groupId, $userId)) {
            return false;
        }

        $query = "INSERT INTO group_members (group_id, user_id) VALUES ($groupId, $userId)";
        return $this->db->exec($query);
    }

    // Find group by ID
    public function findById($id) {
        $id = intval($id);
        $query = "SELECT * FROM groups WHERE id = $id";
        $result = $this->db->query($query);
        return $result->fetchArray(SQLITE3_ASSOC);
    }
}