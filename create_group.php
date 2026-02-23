<?php
// Create Group Page
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

// Handle group creation form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $group_name = $_POST['group_name'];
    $current_user_id = $_SESSION['user_id'];

    if (empty($group_name)) {
        $error = 'Group name is required';
    } else {
        $db = new SQLite3('messages.db');

        // Insert new group
        $insert_query = "INSERT INTO groups (name, created_by) VALUES ('" .
            SQLite3::escapeString($group_name) . "', $current_user_id)";

        if ($db->exec($insert_query)) {
            // Get the newly created group ID
            $group_id = $db->lastInsertRowID();

            // Add creator as first member
            $member_query = "INSERT INTO group_members (group_id, user_id) VALUES ($group_id, $current_user_id)";
            $db->exec($member_query);

            $success = "Group created successfully! Group ID: $group_id";
        } else {
            $error = 'Failed to create group';
        }

        $db->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Group - Messaging App</title>
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
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="card-title text-center mb-4">Create New Group</h4>

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

                    <form method="POST" action="create_group.php">
                        <div class="mb-3">
                            <label for="group_name" class="form-label">Group Name</label>
                            <input type="text" class="form-control" id="group_name" name="group_name" required>
                            <div class="form-text">Choose a descriptive name for your group</div>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Create Group</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="chat.php">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>