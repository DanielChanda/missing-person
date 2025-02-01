<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch the required files
require_once '../../../config/DatabaseConfiguration.php';
require_once '../../../models/User.php';
require_once '../../../constants.php';
require_once '../../navbar.php';

$database = new DatabaseConfiguration();
$db = $database->getConnection();
$userInstance = new User($db);
$users = $userInstance->getAllUsers();

//get user role to use to determine the content available for them
$role = $_SESSION['role'];

// Get the user's profile picture
$profilePic = $userInstance->getProfilePicture(security::sanitizeInput($_SESSION['user_id']));
// Only include the alert script if the message query parameter is present
$showAlert = isset($_GET['action']) && ($_GET['action'] === 'userAdded' || $_GET['action'] === 'userUpdated');
//also include an alert message only if the message query parameter is present
$alertMessage = '';
if($showAlert){
    if($_GET['action'] === 'userAdded'){
        $alertMessage = 'New User Added';
    }elseif($_GET['action'] === 'userUpdated'){
        $alertMessage = 'User Info Updated';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet"> <!-- DataTables CSS -->
    <link href="../../../assets/css/master.css" rel="stylesheet">
    <link href="../../style.css" rel="stylesheet">
    <style>
        /* CSS to control the size of user images */
        .user-image {
            max-width: 100px; /* Set the maximum width */
            max-height: 100px; /* Set the maximum height */
            width: auto; /* Maintain aspect ratio */
            height: auto; /* Maintain aspect ratio */
        }
    </style>
</head>
<body class="userbody">
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <a class="navbar-brand ms-1" href="#"><img class="img-fluid img-thumbnail rounded" alt="profile picture" src="../../<?php echo $profilePic['image'];?>" width="80" height="74"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <!-- implementing an offcanvas nav bar for small screen-->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
      <div class="offcanvas-header">
        <a href = "#" class="offcanvas-title" id="offcanvasNavbarLabel"><img class="img-fluid img-thumbnail rounded" alt="profile picture" src="../../<?php echo $profilePic['image'];?>" width="80" height="74"></a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body bg-dark">
        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
            <?php echo getNavBar($role,'','', 'active" aria-current="page"', '', '', ''); ?>
        </ul>
    </div>
</nav>
<div class="container mt-5">
    <h2 class="text-center text-info text-capitalize fw-bold bg-dark mb-2 rounded-3">Manage Users</h2>

    <!-- the button will toggle the modal what allows the admin to add the new user -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Add New User</button>
    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Add New User </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Modal body has the card where there form is placed-->
                    <div class="card w-100 mb-4 shadow-sm">
                        <img src="../../../uploads/profilePictures/default.jpg" class="card-img" alt="Missing Person">
                        <div class="card-body">
                            <div class="container">
                                <form action="../../../controllers/UserController.php?action=add_user" method="POST">
                                        
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">

                                                 <div class="form-floating mb-3"> 
                                                    <input type="text" name="username" id="username" class="form-control" placeholder="username" required>
                                                    <label for="username" class="form-label">Username</label>
                                                </div>  
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <div class="form-floating mb-3">
                                                    
                                                    <input type="email" name="email" id="email" class="form-control" placeholder="user@gmail.com" required>
                                                    <label for="email" class="form-label">Email</label>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <div class="form-floating mb-3">
                                                
                                                    <input type="password" name="password" id="password" class="form-control" placeholder="password" required>
                                                    <label for="password" class="form-label">Password</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <div class="form-floating mb-3">
                                                    
                                                    <select name="role" id="role" class="form-select" placeholder="allowed user" required>
                                                        <option value="">Select Role</option>
                                                        <option value="admin">Admin</option>
                                                        <option value="allowed_user">Allowed User</option>
                                                    </select>
                                                    <label for="role" class="form-label">Role</label>
                                                </div>    
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 offset-lg-3">
                                            <button type="submit" class="btn btn-primary w-100">Add User</button>
                                        </div>
                                    </div>
                                    
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- end card -->

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End of the modal -->  
     
    <!-- table start -->
    <div class="table-responsive">
        <table id="usersTable" class="table table-bordered table-hover text-center">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td><img class="img-fluid user-image" src="../../<?php echo htmlspecialchars($user['image']); ?>" alt="user image"></td>
                <td>
                    
                    <!-- the button will toggle the modal what allows the admin to edit user -->
                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#staticBackdrop<?php echo $user['id']; ?>">Edit</button>
                    <!-- start of edit user modal -->
                    <div class="modal fade" id="staticBackdrop<?php echo $user['id']; ?>" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">Edit User </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Modal body has the card where there form is placed-->
                                    <div class="card w-100 mb-4 shadow-sm">
                                        <img src="../../../uploads/profilePictures/default.jpg" class="card-img" alt="Missing Person">
                                        <div class="card-body">
                                            <form action="includes/edit_user.php?id=<?php echo htmlspecialchars($user['id']); ?>" method="POST">
                                                <div class="container">  

                                                    <!-- row for the user name and emal -->      
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                <div class="form-floating mb-3">
                                                                    
                                                                    <input type="text" name="username" id="username<?php echo $user['id']?>" class="form-control" placeholder="daniel chanda" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                                                    <label for="username<?php echo $user['id']?>">Username</label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                <div class="form-floating mb-3">
                                                                    
                                                                    <input type="email" name="email" id="email<?php echo $user['id']?>" class="form-control" placeholder="user@gmail.com" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                                                    <label for="email<?php echo $user['id']?>">Email</label>
                                                                </div>  
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- row for role and submit button -->
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                <div class="form-floating mb-3">
                                                                
                                                                    <select name="role" id="role<?php echo $user['id']?>" class="form-control" placeholder="allowed user" required>
                                                                        <option value="">Select Role</option>
                                                                        <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                                                        <option value="allowed_user" <?php if ($user['role'] == 'allowed_user') echo 'selected'; ?>>Allowed User</option>
                                                                    </select>
                                                                    <label for="role<?php echo $user['id']?>">Role</label>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class=" col-lg-6 d-flex align-items-center">
                                                            <button type="submit" class="btn btn-primary btn-block w-100">Update User</button>
                                                        </div>
                                                    </div>

                                                </div>
                                            </form>                        
                                        </div>
                                    </div>
                                    <!-- end card -->

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- end of edit user modal -->

                    <!-- start of delete user -->
                    <button class="btn btn-danger btn-sm my-3" 
                    onclick="return Swal.fire({
                        title: 'Are you sure you wish to remove <?php echo $user['username']?>?'+
                        '\nNote: Please be aware that removing a user will also permanently delete their associated data, including any filed reports.',
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: 'Don\'t Delete',
                        denyButtonText: 'Delete'
                        }).then((result) => {
                        if (result.isDenied) {
                            let role = '<?php echo $user['role']?>';
                            if (role === 'admin') {
                                Swal.fire('Cannot delete admin', '', 'warning');
                                return;
                            }
                            window.location.href = 'includes/delete_user.php?id=<?php echo $user['id']?>';
                            Swal.fire('Deleted', '', 'info');
                        } else if (result.isConfirmed) {
                            Swal.fire('Not deleted', '', 'info');
                        }
                    });">Delete</button>
                    <!-- end of delete user-->

                    <!-- start of suspend/activate use -->

                    <button class="btn btn-secondary btn-sm my-3 "
                    onclick="return Swal.fire({
                        title: 'Are you sure you want to ' + this.textContent + ' <?php echo $user['username']?>?',
                        showCancelButton: true,
                        confirmButtonText: 'Confirm'
                        }).then((result) => {
                        if (result.isConfirmed) {
                            //get the role of the user
                            let role = '<?php echo $user['role']?>';
                            //check the role of the user if it's an admin
                            if (role === 'admin') {
                                Swal.fire('Cannot suspend admin', '', 'warning');
                                return;
                            }
                            
                            if(this.textContent == 'Suspend'){
                                //lets suspend this user
                                
                                //create an ajax object for sending request to manage user access to suspend user
                                let http = new  XMLHttpRequest();
                                //open the connection
                                http.open('POST','includes/manage_user_access.php');
                                //set the request header
                                http.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                                //get the response
                                http.onload = ()=>{
                                    //if responce is okay
                                    if(http.status == 200){
                                        //notify user
                                        Swal.fire('suspended! \n<?php echo $user['username']?> won\'t be able to log in.', '', 'info');
                                        //get response text and update the button
                                        this.innerText = http.responseText;
                                        
                                    }else{
                                        //an urror must have occured,notify user and show error code
                                        Swal.fire('An error occured.errorCode='+ http.status, '', 'error');
                                    }
                                
                                };
                                //send the request with action = suspend indicating that this user is suspended
                                http.send('userId=<?php echo $user['id']?>&action=suspend');
                            
                            }else if(this.textContent == 'Reactivate'){
                                //lets reactivate this user
                                
                                //create an ajax object for sending request to manage user access to reactivate user
                                let http = new  XMLHttpRequest();
                                //open the connection
                                http.open('POST','includes/manage_user_access.php');
                                //set the request header
                                http.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                                //get the response
                                http.onload = ()=>{
                                    //if responce is okay
                                    if(http.status == 200){
                                        //notify user
                                        Swal.fire('Activated!\n<?php echo $user['username']?> is now allowed to login.', '', 'info');
                                        //get response text and update the button
                                        this.innerText = http.responseText;
                                    }else{
                                        //an urror must have occured,notify user and show error code
                                        Swal.fire('An error occured.errorCode='+ http.status, '', 'error');
                                    }
                                
                                };
                                //send the request with action = reactivate indicating that this user is activated
                                http.send('userId=<?php echo $user['id']?>&action=reactivate');
                            }
                                
                        }
                    });"><?php if($user['status'] == 'suspended'){
                        echo 'Reactivate';
                    }else{echo 'Suspend';}
                    ?></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- table end -->
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if ($showAlert): ?>
            Swal.fire({
                title: '<?php echo $alertMessage ?>',
                icon: 'info'
            }).then(() => {
                // Remove 'message' query parameter from the URL
                let url = new URL(window.location.href);
                url.searchParams.delete('action');
                window.history.replaceState({}, document.title, url.toString());
            });
        <?php endif; ?>
    });
</script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script> <!-- DataTables JS -->

<script>
    $(document).ready(function() {
        $('#usersTable').DataTable(); // Initialize DataTable
    });
</script>
</body>
</html>
