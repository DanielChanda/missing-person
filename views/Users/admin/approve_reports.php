<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../../../config/DatabaseConfiguration.php';
require_once '../../../models/Report.php';
require_once '../../../models/user.php';
require_once '../../../constants.php';
require_once '../../../controllers/report_status_notification.php';
require_once '../../../models/Audit_log.php';
require_once '../../navbar.php';

// Creating the database configurations object
$database = new DatabaseConfiguration();
$db = $database->getConnection();

//creating the audit object
$auditLog = new AuditLog($db);

// Creating the report object
$report = new Report($db);
$reports = $report->getPendingReports();

// Creating the notification object
$nofier = new Notification();

$user = new User($db);
// Get the user's profile picture
$profilePic = $user->getProfilePicture(security::sanitizeInput($_SESSION['user_id']));

//get user role to use to determine the content available for them
$role = $_SESSION['role'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'])) {
    $report->id = $_POST['report_id'];
    if ($_POST['action'] === 'approve') {
        // Report approved
        $report->status = 'approved';
        $action = 'approve';
        // Notify the user who sent the report
        $nofier->send($_POST['report_id'], 'approved');
    } elseif ($_POST['action'] === 'reject') {
        // Report rejected
        $report->status = 'rejected';
        $action = 'reject';
        // Notify the user who sent the report
        $nofier->send($_POST['report_id'], 'rejected');
    }

    // Check if status update is successful
    if ($report->updateStatus()) {
        // Log the action
        $auditLog->logAction($_SESSION['user_id'], ucfirst($action) . " Report", json_encode(['report_id' => $report->id, 'status' => $report->status]));
        ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: 'Report Status',
                    text: 'Report <?php echo $report->status ?> and user notification sent.',
                    icon: 'success'
                }).then(() => {
                    window.location.href = "approve_reports.php";
                });
            });
        </script>
        <?php 
        exit();
    } else {
        ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: 'Update Error',
                    text: "Failed to update report status.",
                    icon: 'error'
                }).then(() => {
                    window.location.href = "approve_reports.php";
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
    <title>Approve Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet"> <!-- DataTables CSS -->
    <link href="/missing/assets/css/master.css" rel="stylesheet">
    <link href="../../style.css" rel="stylesheet">
</head>
<body class="userbody">

<!-- The navigation bar start -->
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
            <?php echo getNavBar($role,'','', '', '', '', 'active" aria-current="page"'); ?>
        </ul>
    </div>
</nav>
<!-- End of navigation bar -->

<!-- Main content displayed on the Bootstrap card -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Approve Reports</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($reports)): ?>
                        <p class="text-center">No pending reports.</p>
                    <?php else: ?>

                        <!-- Table starts -->
                        <table id="reportsTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Age</th>
                                    <th>Gender</th>
                                    <th>Last Seen</th>
                                    <th>Description</th>
                                    <th>Contact Info</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reports as $report): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($report['type']); ?></td>
                                        <td><?php echo htmlspecialchars($report['name']); ?></td>
                                        <td><?php echo htmlspecialchars($report['age']); ?></td>
                                        <td><?php echo htmlspecialchars($report['gender']); ?></td>
                                        <td><?php echo htmlspecialchars($report['last_seen']); ?></td>
                                        <td><?php echo htmlspecialchars($report['description']); ?></td>
                                        <td><?php echo htmlspecialchars($report['contact_info']); ?></td>
                                        <td>
                                            <form action="approve_reports.php" method="POST">
                                                <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                                                <button type="submit" name="action" value="approve" class="btn btn-success btn-sm mt-2">Approve</button>
                                                <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm mt-2">Reject</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <!-- Table ends -->
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main content ends -->
<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script> <!-- DataTables JS -->
<script>
    $(document).ready(function() {
        $('#reportsTable').DataTable(); // Initialize DataTable
    });
</script>
</body>
</html>
