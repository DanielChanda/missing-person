<?php
session_start();

require_once '../config/DatabaseConfiguration.php';
require_once '../controllers/UserController.php';
require_once '../controllers/registration_notification.php';
require_once '../constants.php';

$developer = 'Chanda Danny';

$database = new DatabaseConfiguration();
$db = $database->getConnection();

$userController = new UserController($db);
$notification = new Notification();

$userVerification = '';
$showToast = false; // Initialize the toast flag

if (isset($_POST['submitEmail'])) {
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
        // Check email existence
        if ($userController->emailExists($email)) {
            //at this point, email is registered.
            //generate random 5 digits code
            $code = rand(10000, 99999);
            //send the email to the provided email with the code
            $notification->sendVerificationCode($code, $email);
            
            // Store the new code to the database replacing the old one
            if (!($userController->setCode($code, $email))) {
                //email is not registered show error message
                echo '<div class="alert alert-warning" role="alert">
                        Code verification process could not proceed because!
                        '.$email.' is not recognized.consider registering an account!
                    </div>';
                exit();
                
            }
    
            $userVerification = '
                <div class="alert alert-primary">A five-digit verification code has been sent to your email.</div>
                <form action="forgetPassword.php" method="POST" id="verificationForm">
                    <input type="hidden" name="email" value="' . htmlspecialchars($email) . '">
                    <div id="verification-code">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Verification Code</span>
                            <input type="text" name="usercode" class="form-control" placeholder="Enter verification code" aria-label="Verification Code" aria-describedby="basic-addon1">
                            <button type="submit" name="submitCode" class="btn btn-primary">Verify</button>
                        </div>
                    </div>
                </form>';

            // Set the flag to show the toast
            $showToast = true;

        } else {
            echo '<div class="alert alert-warning text-center" role="alert">
                        Email not registered!
                    </div>';
            
        }
    }
}

// Handle POST request to verify the code
if (isset($_POST['submitCode'])) {
    $usercode = $_POST['usercode'];
    $email = $_POST['email']; // Ensure email is retrieved from form

    //do the code verification
    if ($userController->verifyCode($usercode, $email)) { 
        //code is successfully verified set the flag to true in the session
        $_SESSION['verified'] = true;
        //store the user code in the session
        $_SESSION['code'] = $usercode;
        //store the user email in the session
        $_SESSION['email'] = $email;
        //redirect to the change password page so the user can actually change the password
        header('location:changePassword.php');
        //exit current page
        exit();
    } else {
        //code verification was not successful
        echo '<div class="alert alert-warning" role="alert">
                        Could not verify because the code mismatched!
                    </div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/master.css" rel="stylesheet">
    <style>
        body {
            padding-top: 56px;
            background: url('../images/background.jpeg') no-repeat center center fixed;
            background-size: cover;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #f8f9fa;
            padding: 1rem 0;
            text-align: center;
        }
        .navbar-dark .navbar-nav .nav-link {
            color: white;
        }
        .card {
            margin: 40px auto;
            max-width: 400px;
        }
        .card-header, .card-body, .card-footer {
            padding: 1.25rem;
        }
        .form-floating {
            margin-bottom: 1.5rem;
        }
        .btn-primary {
            width: 100%;
        }
        
    </style>
    <link href="style.css" rel="stylesheet">
</head>
<body class="userbody">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="#">Missing Person App</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="../index.php">Home <?php echo getIcon('home'); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="login.php">login <?php echo getIcon('login'); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="register.php">Register <?php echo getIcon('register'); ?></a>
            </li>
        </ul>
    </div>
</nav>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title text-center">Forgot Password</h5>
                    </div>
                    <div class="card-body">
                        <form action="forgetPassword.php" method="POST">
                            <div class="form-floating">
                                <input class="form-control" type="email" id="email" name="email" placeholder="user@gmail.com" required>
                                <label for="email">Enter your email:</label>
                            </div>
                            <button class="btn btn-primary" name="submitEmail" type="submit">Send</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <?php echo $userVerification; ?>
                    </div>
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
                <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
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
