<?php
// Sign Up Page
session_start();

$error = '';
$success = '';

// If already logged in, redirect to chat
if (isset($_SESSION['user_id'])) {
    header('Location: chat.php');
    exit();
}

// Handle signup form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    
    // Basic validation
    if (empty($username) || empty($password)) {
        $error = 'Username and password are required';
    } else {
        // Open database
        $db = new SQLite3('messages.db');
        
        // Check if username already exists
        $check_query = "SELECT * FROM users WHERE username = '" . SQLite3::escapeString($username) . "'";
        $result = $db->query($check_query);
        $existing_user = $result->fetchArray(SQLITE3_ASSOC);
        
        if ($existing_user) {
            $error = 'Username already exists';
        } else {
            // Insert new user (NOTE: In production, use password_hash!)
            $insert_query = "INSERT INTO users (username, password, email) VALUES ('" . 
                SQLite3::escapeString($username) . "', '" . 
                SQLite3::escapeString($password) . "', '" . 
                SQLite3::escapeString($email) . "')";
            
            if ($db->exec($insert_query)) {
                $success = 'Account created successfully! You can now login.';
            } else {
                $error = 'Failed to create account';
            }
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
    <title>Sign Up - Messaging App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">📨 Message App</h3>
                        <h5 class="text-center mb-4">Create Account</h5>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="signup.php">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email (optional)</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Sign Up</button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p>Already have an account? <a href="login.php">Login here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
