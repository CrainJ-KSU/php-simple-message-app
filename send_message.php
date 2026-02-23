<?php
// Message Sending Handler
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_user_id = $_SESSION['user_id'];
    $message = $_POST['message'];
    $type = $_POST['type'];
    $id = intval($_POST['id']);
    
    if (!empty($message) && $id > 0) {
        $db = new SQLite3('messages.db');
        
        if ($type == 'direct') {
            // Send direct message
            $query = "INSERT INTO messages (sender_id, recipient_id, message) VALUES (" . 
                     $current_user_id . ", " . $id . ", '" . SQLite3::escapeString($message) . "')";
        } elseif ($type == 'group') {
            // Send group message
            $query = "INSERT INTO messages (sender_id, group_id, message) VALUES (" . 
                     $current_user_id . ", " . $id . ", '" . SQLite3::escapeString($message) . "')";
        }
        
        $db->exec($query);
        $db->close();
        
        // Redirect back to chat
        header("Location: chat.php?type=$type&id=$id");
        exit();
    }
}

// If something went wrong, redirect to chat home
header('Location: chat.php');
exit();
?>
