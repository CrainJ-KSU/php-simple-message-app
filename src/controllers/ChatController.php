<?php
// Chat Controller

class ChatController {
    private $userModel;
    private $messageModel;
    private $groupModel;

    public function __construct() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?route=auth/login');
            exit();
        }

        $this->userModel = new User();
        $this->messageModel = new Message();
        $this->groupModel = new Group();
    }

    // Show chat page
    public function index() {
        $currentUserId = $_SESSION['user_id'];
        $currentUsername = $_SESSION['username'];

        // Get conversation details from URL
        $viewType = isset($_GET['type']) ? $_GET['type'] : 'direct';
        $viewId = isset($_GET['id']) ? intval($_GET['id']) : 0;

        // Get all users except current
        $allUsers = $this->userModel->getAllExcept($currentUserId);

        // Get user's groups
        $userGroups = $this->groupModel->getUserGroups($currentUserId);

        // Get messages based on conversation
        $messages = array();
        $conversationTitle = '';

        if ($viewType == 'direct' && $viewId > 0) {
            $messages = $this->messageModel->getDirectMessages($currentUserId, $viewId);
            $recipient = $this->userModel->findById($viewId);
            $conversationTitle = 'Chat with ' . $recipient['username'];
        } elseif ($viewType == 'group' && $viewId > 0) {
            $messages = $this->messageModel->getGroupMessages($viewId);
            $group = $this->groupModel->findById($viewId);
            $conversationTitle = 'Group: ' . $group['name'];
        }

        require_once BASE_PATH . '/views/chat.php';
    }

    // Send message
    public function sendMessage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?route=chat/index');
            exit();
        }

        $currentUserId = $_SESSION['user_id'];
        $message = $_POST['message'];
        $type = $_POST['type'];
        $id = intval($_POST['id']);

        if (empty($message) || $id <= 0) {
            header('Location: index.php?route=chat/index');
            exit();
        }

        if ($type == 'direct') {
            $this->messageModel->create($currentUserId, $id, null, $message);
        } elseif ($type == 'group') {
            $this->messageModel->create($currentUserId, null, $id, $message);
        }

        header("Location: index.php?route=chat/index&type=$type&id=$id");
        exit();
    }
}