<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../login.php");
    exit();
}

require_once '../../../../config/DatabaseConfiguration.php';
require_once '../../../../models/User.php';
require_once '../../../../models/Security.php';

$database = new DatabaseConfiguration();
$db = $database->getConnection();
$user = new User($db);

if (isset($_GET['id'])) {
    $userData = $user->getUserById($_GET['id']);
    if (!$userData) {
        echo '<h4 style="text-align:center; color:#ff0000">User not found.</h4>';
        exit();
    }
} else {
    echo '<h4 style="text-align:center; color:#ff0000">No user ID provided.</h4>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $user->id = $_GET['id'];
    $user->username = Security::sanitize($username);
    $user->email = Security::sanitize($email);
    $user->role = Security::sanitize($role);
    
    if ($user->update()) {
        header("Location: ../manage_users.php?action=userUpdated");
        exit();
    } else {
        echo '<h4 style="text-align:center; color:#ff0000">Failed to update user.</h4>';
    }
}
?>

