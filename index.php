<?php
// Main landing point of the project
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/manage_menu.php');
    } else {
        header('Location: student/menu.php');
    }
} else {
    // default for non logged users
    header('Location: auth/login.php');
}
exit;
?>
