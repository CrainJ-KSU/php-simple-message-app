<?php
// Landing Page - Redirects to login or chat
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: chat.php');
    exit();
} else {
    header('Location: login.php');
    exit();
}
?>
