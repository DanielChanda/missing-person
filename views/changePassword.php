<?php
session_start();

// Check if the user is verified; this is just an example and should be adapted to your actual verification logic
if (!isset($_SESSION['verified']) || $_SESSION['verified'] !== true || !isset($_SESSION['email']) || !isset($_SESSION['code'])) {
    // Redirect to the verification page if not verified
    header('Location: forgetPassword.php?action=failedVerification');
    exit();
}
require_once '../config/DatabaseConfiguration.php';
require_once '../controllers/UserController.php';

$database = new DatabaseConfiguration();
$db = $database->getConnection();

$userController = new UserController($db);

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($newPassword === $confirmPassword) {
        
        if($userController->updatePassword($newPassword,$_SESSION['email'],$_SESSION['code'])){
            // Unset all session variables
            $_SESSION = array();

            // Destroy the session cookie if it exists
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }

            // Destroy the session
            session_destroy();

            // Redirect to the login page with a query parameter
            header('location:login.php?action=passwordChanged');
        }
        
        
        
    } else {
        echo "<script>alert('Passwords do not match.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/master.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            width: 100%;
            max-width: 400px;
            border-radius: 0.75rem;
        }
        .form-label {
            margin-bottom: 0.5rem;
        }
        .form-control {
            border-radius: 0.375rem;
        }
        .btn-primary {
            border-radius: 0.375rem;
        }
        
    </style>
    <link href="style.css" rel="stylesheet">
    
</head>
<body class="userbody">
    <div class="card shadow">
        <div class="card-body">
            <h5 class="card-title mb-4">Change Your Password</h5>
            <form method="POST" id="changePasswordForm">
                <div class="mb-3">
                    <label for="newPassword" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="Enter new password" required>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm new password" required>
                </div>
                <button type="submit" class="btn btn-primary">Change Password</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
