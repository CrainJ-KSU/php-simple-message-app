<?php
// Message Model

class Message {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Create new message
    public function create($senderId, $recipientId, $groupId, $message) {
        $senderId = intval($senderId);
        $recipientId = $recipientId ? intval($recipientId) : 'NULL';
        $groupId = $groupId ? intval($groupId) : 'NULL';
        $message = $this->db->escape($message);

        $query = "INSERT INTO messages (sender_id, recipient_id, group_id, message) 
                  VALUES ($senderId, $recipientId, $groupId, '$message')";

        return $this->db->exec($query);
    }

    // Get direct messages between two users
    public function getDirectMessages($userId1, $userId2) {
        $userId1 = intval($userId1);
        $userId2 = intval($userId2);

        $query = "SELECT m.*, u.username as sender_name 
                  FROM messages m 
                  INNER JOIN users u ON m.sender_id = u.id 
                  WHERE ((m.sender_id = $userId1 AND m.recipient_id = $userId2) 
                         OR (m.sender_id = $userId2 AND m.recipient_id = $userId1))
                  AND m.group_id IS NULL
                  ORDER BY m.sent_at ASC";

        $result = $this->db->query($query);

        $messages = array();
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $messages[] = $row;
        }

        return $messages;
    }

    // Get group messages
    public function getGroupMessages($groupId) {
        $groupId = intval($groupId);

        $query = "SELECT m.*, u.username as sender_name 
                  FROM messages m 
                  INNER JOIN users u ON m.sender_id = u.id 
                  WHERE m.group_id = $groupId
                  ORDER BY m.sent_at ASC";

        $result = $this->db->query($query);

        $messages = array();
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $messages[] = $row;
        }

        return $messages;
    }
}