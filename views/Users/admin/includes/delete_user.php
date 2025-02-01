<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../../../../config/DatabaseConfiguration.php';
require_once '../../../../models/User.php';

$database = new DatabaseConfiguration();
$db = $database->getConnection();
$user = new User($db);

if (isset($_GET['id'])) {
    $user->id = $_GET['id'];
    if ($user->delete()) {
        header("Location: ../manage_users.php");
        exit();
    } else {
        echo '<h4 style="text-align:center; color:#ff0000">Failed to delete user.</h4>';
    }
} else {
    echo '<h4 style="text-align:center; color:#ff0000">No user ID provided.</h4>';
}
?>