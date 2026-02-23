
<?php
// Main Chat Interface
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];

// Open database
$db = new SQLite3('messages.db');

// Get selected conversation type and ID from URL
$view_type = isset($_GET['type']) ? $_GET['type'] : 'direct';
$view_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get all users for direct messaging
$users_query = "SELECT * FROM users WHERE id != $current_user_id ORDER BY username";
$users_result = $db->query($users_query);
$all_users = array();
while ($user = $users_result->fetchArray(SQLITE3_ASSOC)) {
    $all_users[] = $user;
}

// Get user's groups
$groups_query = "SELECT g.* FROM groups g 
                 INNER JOIN group_members gm ON g.id = gm.group_id 
                 WHERE gm.user_id = $current_user_id 
                 ORDER BY g.name";
$groups_result = $db->query($groups_query);
$user_groups = array();
while ($group = $groups_result->fetchArray(SQLITE3_ASSOC)) {
    $user_groups[] = $group;
}

// Get messages based on selected conversation
$messages = array();
$conversation_title = '';

if ($view_type == 'direct' && $view_id > 0) {
    // Get direct messages between current user and selected user
    $messages_query = "SELECT m.*, u.username as sender_name 
                      FROM messages m 
                      INNER JOIN users u ON m.sender_id = u.id 
                      WHERE ((m.sender_id = $current_user_id AND m.recipient_id = $view_id) 
                             OR (m.sender_id = $view_id AND m.recipient_id = $current_user_id))
                      AND m.group_id IS NULL
                      ORDER BY m.sent_at ASC";
    $messages_result = $db->query($messages_query);

    // Get recipient name
    $recipient_query = "SELECT username FROM users WHERE id = $view_id";
    $recipient_result = $db->query($recipient_query);
    $recipient = $recipient_result->fetchArray(SQLITE3_ASSOC);
    $conversation_title = 'Chat with ' . $recipient['username'];

    while ($msg = $messages_result->fetchArray(SQLITE3_ASSOC)) {
        $messages[] = $msg;
    }
} elseif ($view_type == 'group' && $view_id > 0) {
    // Get group messages
    $messages_query = "SELECT m.*, u.username as sender_name 
                      FROM messages m 
                      INNER JOIN users u ON m.sender_id = u.id 
                      WHERE m.group_id = $view_id
                      ORDER BY m.sent_at ASC";
    $messages_result = $db->query($messages_query);

    // Get group name
    $group_query = "SELECT name FROM groups WHERE id = $view_id";
    $group_result = $db->query($group_query);
    $group = $group_result->fetchArray(SQLITE3_ASSOC);
    $conversation_title = 'Group: ' . $group['name'];

    while ($msg = $messages_result->fetchArray(SQLITE3_ASSOC)) {
        $messages[] = $msg;
    }
}

$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - Messaging App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 60px; }
        .chat-sidebar {
            height: calc(100vh - 80px);
            overflow-y: auto;
            border-right: 1px solid #dee2e6;
        }
        .chat-main {
            height: calc(100vh - 80px);
            display: flex;
            flex-direction: column;
        }
        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .message-bubble {
            max-width: 70%;
            margin-bottom: 15px;
            padding: 10px 15px;
            border-radius: 15px;
        }
        .message-sent {
            background-color: #0d6efd;
            color: white;
            margin-left: auto;
            text-align: right;
        }
        .message-received {
            background-color: white;
            border: 1px solid #dee2e6;
        }
        .message-time {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 5px;
        }
        .send-form {
            padding: 15px;
            background-color: white;
            border-top: 1px solid #dee2e6;
        }
        .user-item, .group-item {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            text-decoration: none;
            display: block;
            color: #212529;
            transition: background-color 0.2s;
        }
        .user-item:hover, .group-item:hover {
            background-color: #f0f0f0;
            color: #212529;
        }
        .user-item.active, .group-item.active {
            background-color: #e7f3ff;
            color: #212529;
        }
    </style>
</head>
<body>
<!-- Top Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container-fluid">
        <span class="navbar-brand">📨 Message App</span>
        <div class="d-flex">
                <span class="navbar-text text-white me-3">
                    Welcome, <?php echo htmlspecialchars($current_username); ?>
                </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 chat-sidebar p-0">
            <div class="p-3 bg-light border-bottom">
                <h6 class="mb-0">Direct Messages</h6>
            </div>
            <?php foreach ($all_users as $user): ?>
                <a href="chat.php?type=direct&id=<?php echo $user['id']; ?>"
                   class="user-item <?php echo ($view_type == 'direct' && $view_id == $user['id']) ? 'active' : ''; ?>">
                    <strong>👤 <?php echo htmlspecialchars($user['username']); ?></strong>
                </a>
            <?php endforeach; ?>

            <div class="p-3 bg-light border-bottom border-top">
                <h6 class="mb-0">Group Chats</h6>
            </div>
            <?php foreach ($user_groups as $group): ?>
                <a href="chat.php?type=group&id=<?php echo $group['id']; ?>"
                   class="group-item <?php echo ($view_type == 'group' && $view_id == $group['id']) ? 'active' : ''; ?>">
                    <strong>👥 <?php echo htmlspecialchars($group['name']); ?></strong>
                </a>
            <?php endforeach; ?>

            <div class="p-3">
                <a href="create_group.php" class="btn btn-success btn-sm w-100 mb-2">+ Create Group</a>
                <a href="join_group.php" class="btn btn-outline-success btn-sm w-100">Join Group</a>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="col-md-9 p-0">
            <div class="chat-main">
                <?php if ($view_id > 0): ?>
                    <!-- Messages Container -->
                    <div class="messages-container">
                        <h5 class="mb-4"><?php echo htmlspecialchars($conversation_title); ?></h5>

                        <?php if (empty($messages)): ?>
                            <div class="text-center text-muted mt-5">
                                <p>No messages yet. Start the conversation!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($messages as $msg): ?>
                                <div class="message-bubble <?php echo ($msg['sender_id'] == $current_user_id) ? 'message-sent' : 'message-received'; ?>">
                                    <?php if ($msg['sender_id'] != $current_user_id): ?>
                                        <strong><?php echo htmlspecialchars($msg['sender_name']); ?></strong><br>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($msg['message']); ?>
                                    <div class="message-time">
                                        <?php echo date('M d, Y g:i A', strtotime($msg['sent_at'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Send Message Form -->
                    <div class="send-form">
                        <form method="POST" action="send_message.php">
                            <input type="hidden" name="type" value="<?php echo $view_type; ?>">
                            <input type="hidden" name="id" value="<?php echo $view_id; ?>">
                            <div class="input-group">
                                <input type="text" class="form-control" name="message" placeholder="Type your message..." required>
                                <button type="submit" class="btn btn-primary">Send</button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="text-center text-muted">
                            <h4>Welcome to Messaging App!</h4>
                            <p>Select a user or group from the sidebar to start chatting</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>