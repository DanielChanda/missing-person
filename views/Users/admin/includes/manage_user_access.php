<?php
session_start();

if (!(isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin')) {
    header("Location: ../../../login.php");
    exit();
}
require_once '../../../..//Models/user.php';
require_once '../../../../config/DatabaseConfiguration.php';
//create an instance of the database
$database = new DatabaseConfiguration();
$db = $database->getConnection();
//create an instance of the user
$user = new User($db);
//get user id from the session
$userId = $_POST['userId'];
//check if the id of the user to suspend was given and request method is POST

if(isset($userId) && $_SERVER['REQUEST_METHOD'] === 'POST'){
    //it was given,suspend change the status of the user with the given id to suspended
    //check the action to perform
    if($_POST['action'] == 'suspend'){
        if($user->suspendUser($userId)){
            //the user has been suspended ,send the response that they can be reactivate
            echo 'Reactivate';
        }
    }elseif($_POST['action'] == 'reactivate'){
        if($user->reactivateUser($userId)){
            //the user has been reactivated ,send the response that they can be suspended
            echo 'Suspend';
        }
    }

}
?>