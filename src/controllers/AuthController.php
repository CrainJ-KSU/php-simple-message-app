<?php
// Authentication Controller

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // Show login page
    public function login() {
        // If already logged in, redirect to chat
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?route=chat/index');
            exit();
        }

        require_once BASE_PATH . '/views/login.php';
    }

    // Show signup page
    public function signup() {
        // If already logged in, redirect to chat
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?route=chat/index');
            exit();
        }

        require_once BASE_PATH . '/views/signup.php';
    }

    // Process login
    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?route=auth/login');
            exit();
        }

        $username = $_POST['username'];
        $password = $_POST['password'];

        if ($this->userModel->authenticate($username, $password)) {
            $_SESSION['user_id'] = $this->userModel->getId();
            $_SESSION['username'] = $this->userModel->getUsername();
            header('Location: index.php?route=chat/index');
            exit();
        } else {
            $_SESSION['error'] = 'Invalid username or password';
            header('Location: index.php?route=auth/login');
            exit();
        }
    }

    // Process signup
    public function processSignup() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?route=auth/signup');
            exit();
        }

        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = isset($_POST['email']) ? $_POST['email'] : '';

        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Username and password are required';
            header('Location: index.php?route=auth/signup');
            exit();
        }

        if ($this->userModel->create($username, $password, $email)) {
            $_SESSION['success'] = 'Account created successfully! You can now login.';
            header('Location: index.php?route=auth/login');
            exit();
        } else {
            $_SESSION['error'] = 'Username already exists';
            header('Location: index.php?route=auth/signup');
            exit();
        }
    }

    // Logout
    public function logout() {
        session_destroy();
        header('Location: index.php?route=auth/login');
        exit();
    }
}