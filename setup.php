<?php
// Database Setup Script
// Run this file once to create the SQLite database and tables

$db_file = 'messages.db';

// Delete existing database if it exists (for fresh setup)
if (file_exists($db_file)) {
    unlink($db_file);
}

// Create new database connection
$db = new SQLite3($db_file);

if (!$db) {
    die("Unable to open database");
}

// Create users table
$db->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        email TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
");

// Create groups table
$db->exec("
    CREATE TABLE IF NOT EXISTS groups (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        created_by INTEGER NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES users(id)
    )
");

// Create group_members table (many-to-many relationship)
$db->exec("
    CREATE TABLE IF NOT EXISTS group_members (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        group_id INTEGER NOT NULL,
        user_id INTEGER NOT NULL,
        joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (group_id) REFERENCES groups(id),
        FOREIGN KEY (user_id) REFERENCES users(id),
        UNIQUE(group_id, user_id)
    )
");

// Create messages table
$db->exec("
    CREATE TABLE IF NOT EXISTS messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        sender_id INTEGER NOT NULL,
        recipient_id INTEGER,
        group_id INTEGER,
        message TEXT NOT NULL,
        sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (sender_id) REFERENCES users(id),
        FOREIGN KEY (recipient_id) REFERENCES users(id),
        FOREIGN KEY (group_id) REFERENCES groups(id)
    )
");

// Insert some test users (password is 'password123' - in production, use password_hash!)
$db->exec("INSERT INTO users (username, password, email) VALUES ('john', 'password123', 'john@example.com')");
$db->exec("INSERT INTO users (username, password, email) VALUES ('jane', 'password123', 'jane@example.com')");
$db->exec("INSERT INTO users (username, password, email) VALUES ('bob', 'password123', 'bob@example.com')");

// Create a test group
$db->exec("INSERT INTO groups (name, created_by) VALUES ('General Chat', 1)");
$db->exec("INSERT INTO group_members (group_id, user_id) VALUES (1, 1)");
$db->exec("INSERT INTO group_members (group_id, user_id) VALUES (1, 2)");

// Insert some test messages
$db->exec("INSERT INTO messages (sender_id, recipient_id, message) VALUES (1, 2, 'Hey Jane, how are you?')");
$db->exec("INSERT INTO messages (sender_id, recipient_id, message) VALUES (2, 1, 'Hi John! I am doing great, thanks!')");
$db->exec("INSERT INTO messages (sender_id, group_id, message) VALUES (1, 1, 'Welcome to the General Chat!')");

$db->close();

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Setup Complete</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <div class='container mt-5'>
        <div class='alert alert-success' role='alert'>
            <h4 class='alert-heading'>Database Setup Complete!</h4>
            <p>Your MVC messaging application database has been created successfully.</p>
            <hr>
            <p class='mb-0'>You can now <a href='public/index.php' class='alert-link'>start using the application</a>.</p>
            <p class='mt-2'><strong>Test Accounts:</strong></p>
            <ul>
                <li>Username: john / Password: password123</li>
                <li>Username: jane / Password: password123</li>
                <li>Username: bob / Password: password123</li>
            </ul>
        </div>
    </div>
</body>
</html>";
?>