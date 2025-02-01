<?php
require_once '../config/DatabaseConfiguration.php';
require_once '../controllers/UserController.php';
require_once '../controllers/registration_notification.php';
require_once '../constants.php';

$database = new DatabaseConfiguration();
$db = $database->getConnection();
$notification = new Notification();
$userController = new UserController($db);

$email = '';
$userVerification = '';

// Handle GET request to display the verification form
if (isset($_GET['email'])) {
    $email = htmlspecialchars($_GET['email']); // Sanitize input
    $username = htmlspecialchars($_GET['username']); // Sanitize input
    $userVerification = '
        <div class="alert alert-primary"> A five-digit verification code has been sent to your email.</div>
        <form action="register.php" method="POST" id="verificationForm">
            <input type="hidden" name="email" value="' . htmlspecialchars($email) . '">
            <input type="hidden" name="username" value="' . htmlspecialchars($username) . '">
            <div id="verification-code">
                <div class="form-floating mb-3">
                    <input type="text" name="usercode" class="form-control" id="usercode" placeholder="Enter verification code" aria-label="Verification Code">
                    <label for="usercode">Verification Code</label>
                    <button type="submit" name="submitCode" class="btn btn-primary mt-2">Verify</button>
                </div>
            </div>
        </form>';

    // Set the flag to show the toast
    $showToast = true;
}

// Handle POST request to register the user
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = 'allowed_user';
    $image_path = null;

    // Check if email already exists
    if ($userController->emailExists($email)) {
        echo '<div class="alert alert-warning text-center" role="alert">
                        Email already registered!
                </div>';
                
        
    }else{
        //generate random 5 digits number
        $code = rand(10000, 99999);
        //send notification to the provided email
        $notification->sendVerificationCode($code, $email);

        // Handle image upload if $_FILES['image'] is set in the request
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            //require the unique ID generator file
            require_once 'uniqueIdentifierGenerator.php';
            //requre the upload files file
            require_once '../utils/UploadFiles.php';

            //create an instance of upload files
            $uploading = new UploadFiles();

            //provide the allowed extensions
            $allowed_extensions = ['jpg', 'jpeg', 'png'];
            //the directory to upload a file to
            $upload_dir = '../uploads/profilePictures/';
            //the unique name provided by unique ID generator ,user email and the current microtime
            $unique_name = generateUUID() . '_' . $_POST['email'] . '_' . microtime(true);
            //the maximum allowed file size
            $maxFileSize = 5000000;
            //where to redirect to when there is an error submitting the file
            $redirectTo = "register.php";

            $image_path = $uploading->upload($upload_dir, $allowed_extensions, $maxFileSize, $unique_name, $redirectTo);
        }

        // Register the user
        if ($userController->register($username, $email, $password, $role, $image_path, $code)) {
            header('Location: register.php?username='.$username.'&email=' . $email);
            exit();
        } else {
            echo '<h4 style="text-align:center;color:#dd0000"> Registration failed.</h4>';
        }
    }

    
}

// Handle POST request to verify the code
if (isset($_POST['submitCode'])) {
    $usercode = $_POST['usercode'];
    $username = $_POST['username'];
    $email = $_POST['email']; // Ensure email is retrieved from form

    if ($userController->verifyCode($usercode, $email)) {
        //send a notification to let the user know that they have been registered sucessfully
        $notification->send($email, $username);
        //redirect to the login page with a success message passed in the query paramiter
        header('Location: login.php?success=successful');
        exit();
    } else {
        echo '<div class="alert alert-warning text-center" role="alert">
                        Code mismatch!
                    </div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/master.css" rel="stylesheet">
    <style>
        .toast-container {
            position: fixed;
            bottom: 20px; /* Adjust distance from the bottom */
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050; /* Ensures the toast is on top of other content */
        }
        body {
            background: url('../images/missingimg.jpg') no-repeat center center fixed;
            background-size: cover;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand ms-1" href="#">Missing Person App</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse me-1" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../index.php">Home <?php echo getIcon('home');?></a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout <?php echo getIcon('logout');?></a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login <?php echo getIcon('login');?></a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Register</h3>
                    </div>
                    <div class="card-body">
                        <form action="register.php" method="POST" enctype="multipart/form-data">
                            <div class="form-floating mb-3">
                                <input type="text" name="username" id="username" class="form-control" placeholder="Username" required>
                                <label for="username">Username</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                                <label for="email">Email</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                                <label for="password">Password</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="file" name="image" id="image" class="form-control">
                                <label for="image">Profile Picture</label>
                            </div>
                            <button type="submit" name="register" class="btn btn-primary btn-block">Register</button>
                        </form>
                    </div>
                    <?php echo $userVerification; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Toast HTML -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="liveToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Verification code sent successfully!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($showToast): ?>
                var toastEl = document.getElementById('liveToast');
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            <?php endif; ?>
        });
    </script>
</body>
</html>
