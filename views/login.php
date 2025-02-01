<?php
session_start();

require_once '../config/DatabaseConfiguration.php';
require_once '../controllers/UserController.php';
require_once '../constants.php';

$developer = 'Chanda Danny';

$database = new DatabaseConfiguration();
$db = $database->getConnection();

$userController = new UserController($db);

// Only include the alert script if the message query parameter is present
$showAlert = isset($_GET['action']) && $_GET['action'] == 'passwordChanged';

if (isset($_GET['success']) && $_GET['success'] == 'successful') {
    echo '<h5 style="color:green; text-align:center;">Registration successful</h5>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['action']) && $_GET['action'] == 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if ($userController->checkStatus($email)) {
            // Status is okay, proceed with login
            $user = $userController->login($email, $password);
            if ($user) {
                $_SESSION['user_id'] = $userController->getUserId();
                $_SESSION['username'] = $userController->getUserName();
                $_SESSION['role'] = $userController->getUserRole();
                header('Location: home.php');
                exit();
            } else {
                echo '<h4 style="text-align:center;color:#dd0000">Login failed. Please check your credentials.</h4>';
            }
        } else {
            // Display the status message if checkStatus failed
            echo $userController->message;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Missing Person Application</title>
    <!-- Bootstrap CSS (v5.3) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/master.css" rel="stylesheet">
    <style>
        body {
            padding-top: 56px;
            background: url('../images/missingimg.jpg') no-repeat center center fixed;
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
        .form {
            max-width: 400px;
            margin: 40px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .form-control {
            margin-bottom: 10px;
        }
        .form-control.btn-primary {
            width: 100%;
        }
    </style>
</head>
<body>
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
                <a class="nav-link" href="register.php">Register <?php echo getIcon('register'); ?></a>
            </li>
        </ul>
    </div>
</nav>
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <form class="form" action="login.php?action=login" method="POST">
                <h2 class="text-center">Login</h2>
                <div class="mb-3">
                    <div class="form-floating">
                        <input class="form-control" type="email" id="email" name="email" placeholder="user@gmail.com" required>
                        <label for="email">Email:</label>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-floating">
                        <input class="form-control" type="password" id="password" name="password" required>
                        <label for="password">Password:</label>
                    </div>
                </div>
                <button style="background-color:#0000ff;" class="form-control btn-primary mt-2" type="submit">Login</button>
                <hr/>
                <a href='forgetPassword.php'>Forget password?</a>
            </form>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if ($showAlert): ?>
            Swal.fire({
                title: 'Password Changed',
                icon: 'success'
            }).then(() => {
                // Remove 'action' query parameter from the URL
                let url = new URL(window.location.href);
                url.searchParams.delete('action');
                window.history.replaceState({}, document.title, url.toString());
            });
        <?php endif; ?>
    });
</script>
</body>
</html>
