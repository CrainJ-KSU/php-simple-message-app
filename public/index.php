<?php
// Front Controller - Entry Point
session_start();

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Autoloader for classes
spl_autoload_register(function ($class) {
    $directories = [
        BASE_PATH . '/src/controllers/',
        BASE_PATH . '/src/models/',
        BASE_PATH . '/config/'
    ];

    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Get route from URL
$route = isset($_GET['route']) ? $_GET['route'] : 'auth/login';
$parts = explode('/', $route);
$controller = isset($parts[0]) ? $parts[0] : 'auth';
$action = isset($parts[1]) ? $parts[1] : 'login';

// Route to appropriate controller
try {
    switch ($controller) {
        case 'auth':
            $authController = new AuthController();
            if ($action === 'login') {
                $authController->login();
            } elseif ($action === 'signup') {
                $authController->signup();
            } elseif ($action === 'logout') {
                $authController->logout();
            } elseif ($action === 'processLogin') {
                $authController->processLogin();
            } elseif ($action === 'processSignup') {
                $authController->processSignup();
            }
            break;

        case 'chat':
            $chatController = new ChatController();
            if ($action === 'index') {
                $chatController->index();
            } elseif ($action === 'sendMessage') {
                $chatController->sendMessage();
            }
            break;

        case 'group':
            $groupController = new GroupController();
            if ($action === 'create') {
                $groupController->create();
            } elseif ($action === 'join') {
                $groupController->join();
            } elseif ($action === 'processCreate') {
                $groupController->processCreate();
            } elseif ($action === 'processJoin') {
                $groupController->processJoin();
            }
            break;

        default:
            // Default to auth/login
            $authController = new AuthController();
            $authController->login();
            break;
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}