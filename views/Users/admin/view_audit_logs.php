<?php
session_start();
//only allow the user to access this page if he/she is an admin and have the user id registeres in the session super global
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
//require the necessary files
//these files include the navbar,dbconfig,audit logs,user and the constants
require_once '../../../config/DatabaseConfiguration.php';
require_once '../../../models/Audit_log.php';
require_once '../../../models/user.php';
require_once '../../../constants.php';
require_once '../../navbar.php';

//create the database and audit log instances
$database = new DatabaseConfiguration();
$db = $database->getConnection();
$auditLog = new AuditLog($db);
//get all the available logs
$logs = $auditLog->getLogs();

//create the user instance
$user = new User($db);
// Get the user's profile picture
$profilePic = $user->getProfilePicture(security::sanitizeInput($_SESSION['user_id']));

//get user role to use to determine the content available for them
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet"> <!-- DataTables CSS -->
    <link href="/missing/assets/css/master.css" rel="stylesheet">
    <link href="../../style.css" rel="stylesheet">
</head>
<body class="userbody">
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
            <?php echo getNavBar($role,'','', '', '', 'active" aria-current="page"', ''); ?>
        </ul>
    </div>
</nav>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Audit Logs</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($logs)): ?>
                        <p class="text-center">No logs found.</p>
                    <?php else: ?>
                        <table id="logsTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User ID</th>
                                    <th>Action</th>
                                    <th>Timestamp</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- fetch the audit logs from the database -->
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($log['id']); ?></td>
                                        <td><?php echo htmlspecialchars($log['user_id']); ?></td>
                                        <td><?php echo htmlspecialchars($log['action']); ?></td>
                                        <td><?php echo htmlspecialchars($log['timestamp']); ?></td>
                                        <td><?php echo htmlspecialchars($log['details']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery (required for DataTables) --> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script> <!-- DataTables JS -->

<script>
    $(document).ready(function() {
        $('#logsTable').DataTable(); // Initialize DataTable
    });
</script>
</body>
</html>