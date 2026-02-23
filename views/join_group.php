<?php
$pageTitle = 'Join Group - Messaging App';
include __DIR__ . '/layouts/header.php';
?>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <span class="navbar-brand">📨 Message App (MVC)</span>
            <div class="d-flex">
                <a href="index.php?route=chat/index" class="btn btn-outline-light btn-sm me-2">Back to Chat</a>
                <a href="index.php?route=auth/logout" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4">Join a Group</h4>

                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success">
                                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                                <div class="mt-3">
                                    <a href="index.php?route=chat/index" class="btn btn-primary">Go to Chat</a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="index.php?route=group/processJoin" class="mb-4">
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

                        <?php if (empty($allGroups)): ?>
                            <p class="text-muted">No groups available yet.</p>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($allGroups as $group): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">
                                                # <?php echo htmlspecialchars($group['name']); ?>
                                                <?php if ($group['is_member']): ?>
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
                                        <?php if (!$group['is_member']): ?>
                                            <form method="POST" action="index.php?route=group/processJoin" class="d-inline">
                                                <input type="hidden" name="group_id" value="<?php echo $group['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-success">Join Group</button>
                                            </form>
                                        <?php else: ?>
                                            <a href="index.php?route=chat/index&type=group&id=<?php echo $group['id']; ?>" class="btn btn-sm btn-primary">Open Chat</a>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="text-center mt-4">
                            <a href="index.php?route=chat/index">Back to Chat</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/layouts/footer.php'; ?>