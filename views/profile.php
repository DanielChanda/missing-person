<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/DatabaseConfiguration.php';
require_once '../models/User.php';
require_once '../models/Security.php';
require_once '../constants.php';
require_once 'navbar.php';

//get the current user role to use to determine the user
$role = $_SESSION['role'];

$database = new DatabaseConfiguration();
$db = $database->getConnection();
$user = new User($db);
$user->id = $_SESSION['user_id'];
$userDetails = $user->getUserDetailsById();

// Get the user's profile picture
$profilePic = $user->getProfilePicture(security::sanitizeInput($_SESSION['user_id']));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = Security::sanitize($_POST['name']);
    $email = Security::sanitize($_POST['email']);
    $password = Security::sanitize($_POST['password']);
    
    if ($name && $email && ($password || $password === '')) {
        $user->username = $name;
        $user->email = $email;
        if ($password !== '') {
            $user->password = password_hash($password, PASSWORD_BCRYPT);
        }
        
        if ($user->update_p()) {
            $_SESSION['username'] = $name;
            $message = "";
            ?>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        title: 'profile updated',
                        text: 'Profile updated successfully.',
                        icon: 'success'
                    }).then(() => {
                        window.location.href = "profile.php";
                    });
                });
            </script>
            <?php 
        } else {
        				  
            $message = $user->password;
        }
    } else {
        
        ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: 'validation error',
                    text: 'Invalid input.',
                    icon: 'error'
                }).then(() => {
                    window.location.href = "profile.php";
                });
            });
        </script>
        <?php 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body class="userbody">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand ms-1" href="#"><img class="img-fluid img-thumbnail rounded" alt="profile picture" src="<?php echo $profilePic['image'];?>" width="80" height="74"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <!-- implementing an offcanvas nav bar for small screen-->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
        <div class="offcanvas-header">
            <a href = "#" class="offcanvas-title" id="offcanvasNavbarLabel"><img class="img-fluid img-thumbnail rounded" alt="profile picture" src="<?php echo $profilePic['image'];?>" width="80" height="74"></a>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body bg-dark">
            <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                <!-- profile is active here -->
                <?php echo getNavBar($role,'','active" aria-current="page"', '', '', '', ''); ?>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Profile</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($message)): ?>
                            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                        <?php endif; ?>
                        <form action="profile.php" method="POST">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($userDetails['username']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($userDetails['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password (leave blank to keep current password)</label>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>