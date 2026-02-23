<?php
// Join Group Page
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';
$current_user_id = $_SESSION['user_id'];

// Handle join group form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $group_id = intval($_POST['group_id']);
    
    if ($group_id <= 0) {
        $error = 'Invalid group ID';
    } else {
        $db = new SQLite3('messages.db');
        
        // Check if group exists
        $check_query = "SELECT * FROM groups WHERE id = $group_id";
        $result = $db->query($check_query);
        $group = $result->fetchArray(SQLITE3_ASSOC);
        
        if (!$group) {
            $error = 'Group not found';
        } else {
            // Check if already a member
            $member_check = "SELECT * FROM group_members WHERE group_id = $group_id AND user_id = $current_user_id";
            $member_result = $db->query($member_check);
            $already_member = $member_result->fetchArray(SQLITE3_ASSOC);
            
            if ($already_member) {
                $error = 'You are already a member of this group';
            } else {
                // Join the group
                $join_query = "INSERT INTO group_members (group_id, user_id) VALUES ($group_id, $current_user_id)";
                if ($db->exec($join_query)) {
                    $success = "Successfully joined group: " . htmlspecialchars($group['name']);
                } else {
                    $error = 'Failed to join group';
                }
            }
        }
        
        $db->close();
    }
}

// Get list of all groups
$db = new SQLite3('messages.db');
$all_groups_query = "SELECT g.*, u.username as creator_name,
                     (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as member_count,
                     (SELECT COUNT(*) FROM group_members WHERE group_id = g.id AND user_id = $current_user_id) as is_member
                     FROM groups g
                     INNER JOIN users u ON g.created_by = u.id
                     ORDER BY g.created_at DESC";
$all_groups_result = $db->query($all_groups_query);
$all_groups = array();
while ($group = $all_groups_result->fetchArray(SQLITE3_ASSOC)) {
    $all_groups[] = $group;
}
$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Group - Messaging App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <span class="navbar-brand">📨 Message App</span>
            <div class="d-flex">
                <a href="chat.php" class="btn btn-outline-light btn-sm me-2">Back to Chat</a>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4">Join a Group</h4>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <?php echo $success; ?>
                                <div class="mt-3">
                                    <a href="chat.php" class="btn btn-primary">Go to Chat</a>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="join_group.php" class="mb-4">
                            <div class="row">
                                <div class="col-md-8">
                                    <input type="number" class="form-control" name="group_id" placeholder="Enter Group ID" required>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-success w-100">Join</button>
                                </div>
                            </div>
                        </form>
                        
                        <hr>
                        
                        <h5 class="mb-3">Available Groups</h5>
                        
                        <?php if (empty($all_groups)): ?>
                            <p class="text-muted">No groups available yet.</p>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($all_groups as $group): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">
                                                # <?php echo htmlspecialchars($group['name']); ?>
                                                <?php if ($group['is_member'] > 0): ?>
                                                    <span class="badge bg-success">Joined</span>
                                                <?php endif; ?>
                                            </h6>
                                            <small class="text-muted">ID: <?php echo $group['id']; ?></small>
                                        </div>
                                        <p class="mb-1">
                                            <small>
                                                Created by: <?php echo htmlspecialchars($group['creator_name']); ?> | 
                                                Members: <?php echo $group['member_count']; ?>
                                            </small>
                                        </p>
                                        <?php if ($group['is_member'] == 0): ?>
                                            <form method="POST" action="join_group.php" class="d-inline">
                                                <input type="hidden" name="group_id" value="<?php echo $group['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-success">Join Group</button>
                                            </form>
                                        <?php else: ?>
                                            <a href="chat.php?type=group&id=<?php echo $group['id']; ?>" class="btn btn-sm btn-primary">Open Chat</a>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="text-center mt-4">
                            <a href="chat.php">Back to Chat</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
