<?php
$pageTitle = 'Chat - Messaging App';
include __DIR__ . '/layouts/header.php';
?>

    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container-fluid">
            <span class="navbar-brand">📨 Message App (MVC)</span>
            <div class="d-flex">
            <span class="navbar-text text-white me-3">
                Welcome, <?php echo htmlspecialchars($currentUsername); ?>
            </span>
                <a href="index.php?route=auth/logout" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid chat-container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 chat-sidebar p-0">
                <div class="p-3 bg-light border-bottom">
                    <h6 class="mb-0">Direct Messages</h6>
                </div>
                <?php foreach ($allUsers as $user): ?>
                    <a href="index.php?route=chat/index&type=direct&id=<?php echo $user['id']; ?>"
                       class="user-item <?php echo ($viewType == 'direct' && $viewId == $user['id']) ? 'active' : ''; ?>">
                        <strong>👤 <?php echo htmlspecialchars($user['username']); ?></strong>
                    </a>
                <?php endforeach; ?>

                <div class="p-3 bg-light border-bottom border-top">
                    <h6 class="mb-0">Group Chats</h6>
                </div>
                <?php foreach ($userGroups as $group): ?>
                    <a href="index.php?route=chat/index&type=group&id=<?php echo $group['id']; ?>"
                       class="group-item <?php echo ($viewType == 'group' && $viewId == $group['id']) ? 'active' : ''; ?>">
                        <strong>👥 <?php echo htmlspecialchars($group['name']); ?></strong>
                    </a>
                <?php endforeach; ?>

                <div class="p-3">
                    <a href="index.php?route=group/create" class="btn btn-success btn-sm w-100 mb-2">+ Create Group</a>
                    <a href="index.php?route=group/join" class="btn btn-outline-success btn-sm w-100">Join Group</a>
                </div>
            </div>

            <!-- Main Chat Area -->
            <div class="col-md-9 p-0">
                <div class="chat-main">
                    <?php if ($viewId > 0): ?>
                        <!-- Messages Container -->
                        <div class="messages-container">
                            <h5 class="mb-4"><?php echo htmlspecialchars($conversationTitle); ?></h5>

                            <?php if (empty($messages)): ?>
                                <div class="text-center text-muted mt-5">
                                    <p>No messages yet. Start the conversation!</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($messages as $msg): ?>
                                    <div class="message-bubble <?php echo ($msg['sender_id'] == $currentUserId) ? 'message-sent' : 'message-received'; ?>">
                                        <?php if ($msg['sender_id'] != $currentUserId): ?>
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
                            <form method="POST" action="index.php?route=chat/sendMessage">
                                <input type="hidden" name="type" value="<?php echo $viewType; ?>">
                                <input type="hidden" name="id" value="<?php echo $viewId; ?>">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="message" placeholder="Type your message..." required>
                                    <button type="submit" class="btn btn-primary">Send</button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="welcome-screen">
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

<?php include __DIR__ . '/layouts/footer.php'; ?>