<?php
// Group Controller

class GroupController {
    private $groupModel;

    public function __construct() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?route=auth/login');
            exit();
        }

        $this->groupModel = new Group();
    }

    // Show create group page
    public function create() {
        require_once BASE_PATH . '/views/create_group.php';
    }

    // Process group creation
    public function processCreate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?route=group/create');
            exit();
        }

        $groupName = $_POST['group_name'];
        $currentUserId = $_SESSION['user_id'];

        if (empty($groupName)) {
            $_SESSION['error'] = 'Group name is required';
            header('Location: index.php?route=group/create');
            exit();
        }

        $groupId = $this->groupModel->create($groupName, $currentUserId);

        if ($groupId) {
            $_SESSION['success'] = "Group created successfully! Group ID: $groupId";
            header("Location: index.php?route=chat/index&type=group&id=$groupId");
            exit();
        } else {
            $_SESSION['error'] = 'Failed to create group';
            header('Location: index.php?route=group/create');
            exit();
        }
    }

    // Show join group page
    public function join() {
        $currentUserId = $_SESSION['user_id'];
        $allGroups = $this->groupModel->getAll();

        // Add membership status to each group
        foreach ($allGroups as &$group) {
            $group['is_member'] = $this->groupModel->isMember($group['id'], $currentUserId);
        }

        require_once BASE_PATH . '/views/join_group.php';
    }

    // Process join group
    public function processJoin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?route=group/join');
            exit();
        }

        $groupId = intval($_POST['group_id']);
        $currentUserId = $_SESSION['user_id'];

        if ($groupId <= 0) {
            $_SESSION['error'] = 'Invalid group ID';
            header('Location: index.php?route=group/join');
            exit();
        }

        $group = $this->groupModel->findById($groupId);

        if (!$group) {
            $_SESSION['error'] = 'Group not found';
            header('Location: index.php?route=group/join');
            exit();
        }

        if ($this->groupModel->isMember($groupId, $currentUserId)) {
            $_SESSION['error'] = 'You are already a member of this group';
            header('Location: index.php?route=group/join');
            exit();
        }

        if ($this->groupModel->addMember($groupId, $currentUserId)) {
            $_SESSION['success'] = "Successfully joined group: " . $group['name'];
            header("Location: index.php?route=chat/index&type=group&id=$groupId");
            exit();
        } else {
            $_SESSION['error'] = 'Failed to join group';
            header('Location: index.php?route=group/join');
            exit();
        }
    }
}