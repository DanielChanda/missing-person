<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/DatabaseConfiguration.php';
require_once '../models/Report.php';
require_once '../models/user.php';
require_once '../constants.php';
require_once 'navbar.php';

$database = new DatabaseConfiguration();
$db = $database->getConnection();
$report = new Report($db);

// Fetch filtered reports
$name = isset($_GET['name']) ? $_GET['name'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';
$reports = $report->searchReports($name, $status, $from_date, $to_date);

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
    <title>View Missing Persons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet">

    <link href="../assets/css/master.css" rel="stylesheet">
    <style>
        .d-none {
            display: none !important;
        }

        .userbody {
                
                background: url('../images/missingimg2.jpg') no-repeat center center fixed;
                background-size: cover;
            }
        /* Customize pagination buttons */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            background-color: #343a40;  /* Dark background for pagination buttons */
            color: white !important;    /* White text for pagination buttons */
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
        }

        /* Change active pagination button style */
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #007bff;  /* Blue background for active page */
            color: white !important;    /* White text for active page */
        }
        /* Style the DataTable search input box */
        .dataTables_filter input {
            background-color: #040120;   /* Light background for the input */
            border: 2px solid #000000;   /* Border with primary color */
            border-radius: 20px;         /* Rounded corners */
            padding: 8px;                /* Padding inside the input */
            color: white;                 /* Text color inside the search input */

        }

        /* Change placeholder text color */
        .dataTables_filter input::placeholder {
            color: #888;                 /* Placeholder text color */
            opacity: 0.7;
        }

        /* Style the search label */
        .dataTables_filter label {
            font-weight: bold;
            color: green;              /* Dark color for the label */
            background-color: #040120;
            border-radius: 20px;  
        }
        /* Change the color of the "Show X entries" dropdown */
        .dataTables_wrapper .dataTables_length select {
            background-color: #007bff;
            color: #ffffff;
            border: 1px solid #007bff;
        }
        /* Change the color of the "Showing X to Y of Z entries" text */
        .dataTables_wrapper .dataTables_info {
            color: #007bff; /* Text color */
            background-color: #000000;
        }
        /* Change the color of the "Show X entries" label */
        .dataTables_wrapper .dataTables_length label {
            color: #007bff; /* Text color */
            font-weight: bold; /* Make the label bold (optional) */
        }

    </style>
</head>
    
<body class="userbody">
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand ms-1" href="#"><img class="img-fluid  rounded" alt="profile picture" src="<?php echo $profilePic['image'];?>" width="80" height="74"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <!-- implementing an offcanvas nav bar for small screen-->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
      <div class="offcanvas-header">
        <a href = "#" class="offcanvas-title" id="offcanvasNavbarLabel"><img class="img-fluid rounded" alt="profile picture" src="<?php echo $profilePic['image'];?>" width="80" height="74"></a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body bg-dark">
        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
            <?php echo getNavBar($role,'','', '', '', '', 'active" aria-current="page"'); ?>
        </ul>
    </div>
</nav>
<div class="container mt-5">
    <h2 class="text-center text-info text-capitalize fw-bold bg-dark mb-2 rounded-3">View Missing Persons</h2>
    <form method="GET" action="view_missing_persons.php" class="d-flex flex-column flex-md-row mb-3">
        <input type="text" name="name" class="form-control mb-2 mb-md-0 me-md-2" data-bs-toggle="tooltip" 
               data-bs-placement="top" 
               data-bs-title="Enter the name to search for." placeholder="Name" value="<?php echo htmlspecialchars($name); ?>">
        <select name="status" class="form-select mb-2 mb-md-0 me-md-2" data-bs-toggle="tooltip" 
               data-bs-placement="top" 
               data-bs-title="Select the person's status to search for.">
            <option value="">All Statuses</option>
            <option value="pending" <?php if ($status == 'pending') echo 'selected'; ?>>Pending</option>
            <option value="approved" <?php if ($status == 'approved') echo 'selected'; ?>>Approved</option>
            <option value="rejected" <?php if ($status == 'rejected') echo 'selected'; ?>>Rejected</option>
        </select>
        <input type="date" name="from_date" class="form-control mb-2 mb-md-0 me-md-2" data-bs-toggle="tooltip" 
               data-bs-placement="top" 
               data-bs-title="The date to start searching from" value="<?php echo htmlspecialchars($from_date); ?>">
            
        <input type="date" name="to_date" class="form-control mb-2 mb-md-0 me-md-2" data-bs-toggle="tooltip" 
               data-bs-placement="top" 
               data-bs-title="The date to stop searching from" value="<?php echo htmlspecialchars($to_date); ?>">
        <button type="submit" class="btn btn-primary mb-2 mb-md-0">Filter</button>
    </form>
    <button id="viewReportsBtn" class="btn btn-info mb-4">View Reports</button>
    <table id="reportsTable" class="table table-bordered text-center table-striped d-none">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Date Reported</th>
                <th>Image</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reports as $report) : ?>
                <tr>
                    <td class="align-middle"><?php echo htmlspecialchars($report['id']); ?></td>
                    <td class="align-middle"><?php echo htmlspecialchars($report['name']); ?></td>
                    <td class="align-middle"><?php echo htmlspecialchars($report['status']); ?></td>
                    <td class="align-middle"><?php echo htmlspecialchars($report['created_at']); ?></td>
                    <td class="align-middle">
                        <img src="<?php echo htmlspecialchars($report['image_path']); ?>" alt="missing person's image" class="image-fluid" style="max-width: 100px; height: auto;">
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
</div>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Initialize all tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    document.getElementById("viewReportsBtn").addEventListener("click", function() {
    document.getElementById("reportsTable").classList.remove("d-none");
    this.style.display = "none";  // Hide the button after clicking
});

</script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="../assets/js/dataTable.js"></script>

</body>
</html>
