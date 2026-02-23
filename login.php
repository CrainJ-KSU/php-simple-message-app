<?php
// Login Page
session_start();

$error = '';

// If already logged in, redirect to chat
if (isset($_SESSION['user_id'])) {
    header('Location: chat.php');
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Open database
    $db = new SQLite3('messages.db');
    
    // Query user (NOTE: In production, use prepared statements!)
    $query = "SELECT * FROM users WHERE username = '" . SQLite3::escapeString($username) . "' AND password = '" . SQLite3::escapeString($password) . "'";
    $result = $db->query($query);
    $user = $result->fetchArray(SQLITE3_ASSOC);
    
    if ($user) {
        // Login successful
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: chat.php');
        exit();
    } else {
        $error = 'Invalid username or password';
    }
    
    $db->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Messaging App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">📨 Message App</h3>
                        <h5 class="text-center mb-4">Login</h5>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="login.php">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
