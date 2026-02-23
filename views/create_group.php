<?php
$pageTitle = 'Create Group - Messaging App';
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
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4">Create New Group</h4>

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

                        <form method="POST" action="index.php?route=group/processCreate">
                            <div class="mb-3">
                                <label for="group_name" class="form-label">Group Name</label>
                                <input type="text" class="form-control" id="group_name" name="group_name" required>
                                <div class="form-text">Choose a descriptive name for your group</div>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Create Group</button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="index.php?route=chat/index">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/layouts/footer.php'; ?>