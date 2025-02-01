<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/DatabaseConfiguration.php';
require_once '../models/user.php';
require_once '../models/security.php';
require_once '../models/report.php';
require_once '../constants.php';
require_once 'navbar.php';

// Fetch user details
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

$database = new DatabaseConfiguration();
$db = $database->getConnection();
$report = new Report($db);
$user = new User($db);

$totalUsers = $user->getTotalUsers();
$totalReports = $report->getTotalReports();
$pendingReports = $report->getPendingReportsCount();
$approvedReports = $report->getApprovedReportsCount();
$rejectedReports = $report->getRejectedReportsCount();
$reportsByUserId = $report->getReportsByUserId($user_id);
// Fetch data for dashboard based on role
if ($role === 'admin') {
    $genderDistribution = $report->getGenderDistribution();
    $reportsByMonth = $report->getReportsByMonth();
    $reportsByRole = $report->getReportsByRole();
}

// Get the user's profile picture
$profilePic = $user->getProfilePicture(security::sanitizeInput($_SESSION['user_id']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="../assets/css/master.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
    google.charts.load('current', {'packages':['corechart', 'geochart'],'mapsApiKey': 'AIzaSyAV3XDFGZcX1LeCtg-RWuGGw7T8-ey4_SM'});
    google.charts.setOnLoadCallback(drawCharts);

    function drawCharts() {
        <?php if ($role === 'admin'): ?>

            // Draw the report status chart
            var data = google.visualization.arrayToDataTable([
                ['Report Status', 'Count'],
                ['Pending Reports', <?php echo $pendingReports; ?>],
                ['Approved Reports', <?php echo $approvedReports; ?>],
                ['Rejected Reports', <?php echo $rejectedReports; ?>]
            ]);

            var options = {
                title: 'Report Status Distribution',
                pieHole: 0.4,
            };

            var chart = new google.visualization.PieChart(document.getElementById('reportStatusChart'));
            chart.draw(data, options);

            // Draw the user counter chart
            var data = google.visualization.arrayToDataTable([
                ['Type', 'Count'],
                ['Total Users', <?php echo $totalUsers; ?>],
                ['Total Reports', <?php echo $totalReports; ?>]
            ]);

            var options = {
                chart: {
                    title: 'User and Report Summary'
                }
            };

            var chart = new google.visualization.BarChart(document.getElementById('userCountChart'));
            chart.draw(data, options);

            // Draw Pie Chart for Gender Distribution
            var genderData = google.visualization.arrayToDataTable([
                ['Gender', 'Count'],
                <?php foreach ($genderDistribution as $row) {
                    echo "['{$row['gender']}', {$row['count']}],";
                } ?>
            ]);
            var genderOptions = { title: 'Gender Distribution' };
            var genderChart = new google.visualization.PieChart(document.getElementById('gender_chart'));
            genderChart.draw(genderData, genderOptions);

            // Draw Column Chart for Reports by Month
            var monthData = google.visualization.arrayToDataTable([
                ['Month', 'Reports'],
                <?php foreach ($reportsByMonth as $row) {
                    echo "['" . addslashes($row['month']) . "', " . intval($row['total_reports']) . "],";
                } ?>
            ]);
            var monthOptions = { title: 'Reports by Month', hAxis: { title: 'Month' }, vAxis: { title: 'Number of Reports' } };
            var monthChart = new google.visualization.ColumnChart(document.getElementById('month_chart'));
            monthChart.draw(monthData, monthOptions);

            // Draw Bar Chart for Reports by User Role
            var roleData = google.visualization.arrayToDataTable([
                ['Role', 'Reports'],
                <?php foreach ($reportsByRole as $row) {
                    echo "['{$row['role']}', {$row['count']}],";
                } ?>
            ]);
            var roleOptions = { title: 'Reports by User Role', hAxis: { title: 'Role' }, vAxis: { title: 'Number of Reports' } };
            var roleChart = new google.visualization.BarChart(document.getElementById('role_chart'));
            roleChart.draw(roleData, roleOptions);

        <?php elseif ($role === 'allowed_user'): ?>
            // Draw Column Chart for Reports by User
            var userMonthData = google.visualization.arrayToDataTable([
                ['Month', 'Reports'],
                <?php foreach ($reportsByMonth as $row) {
                    // Ensure month is a string and total_reports is a number
                    echo "['{$row['month']}', {$row['total_reports']}],";
                } ?>
            ]);
            var userMonthOptions = { title: 'Your Reports by Month', hAxis: { title: 'Month' }, vAxis: { title: 'Number of Reports' } };
            var userMonthChart = new google.visualization.ColumnChart(document.getElementById('user_month_chart'));
            userMonthChart.draw(userMonthData, userMonthOptions);


            // Draw Bar Chart for Report Outcomes
            var statusData = google.visualization.arrayToDataTable([
                ['Status', 'Count'],
                <?php foreach ($statusDistribution as $row) {
                    // Ensure status is a string and count is a number
                    echo "['{$row['status']}', {$row['total']}],";
                } ?>
            ]);
            var statusOptions = { title: 'Report Outcomes', hAxis: { title: 'Status' }, vAxis: { title: 'Number of Reports' } };
            var statusChart = new google.visualization.BarChart(document.getElementById('status_chart'));
            statusChart.draw(statusData, statusOptions);


            // Draw Pie Chart for Case Status Distribution
            var caseStatusData = google.visualization.arrayToDataTable([
                ['Status', 'Count'],
                <?php foreach ($statusDistribution as $row) {
                    // Ensure status is a string and count is a number
                    echo "['{$row['status']}', {$row['count']}],";
                } ?>
            ]);
            
            var caseStatusOptions = { title: 'Case Status Distribution' };
            var caseStatusChart = new google.visualization.PieChart(document.getElementById('case_status_chart'));
            caseStatusChart.draw(caseStatusData, caseStatusOptions);


        <?php endif; ?>
    }
</script>
<link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="../assets/css/master.css" rel="stylesheet">
<style>
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
<nav class="navbar navbar-expand-lg sticky-lg-top navbar-dark bg-dark">
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
            <!-- home is active set it-->
            <?php echo getNavBar($role, 'active" aria-current="page"','', '', '', '', ''); ?>
            <li class="nav-item">
                <a href="#reports" class="nav-link">your Reports</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container mt-5 pt-4">

    <?php if ($role === 'admin'): ?>
        <div class="row text-center text-info text-capitalize fw-bold bg-dark mb-2 rounded-3"><h1 class="display-4">Welcome, <?php echo htmlspecialchars($username); ?>!</h1></div>
        <div class="row">
            <!---start of admin dashboard--->
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="text-center">Admin Dashboard</h3>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6 offset-md-3">
                                        <div class="input-group">
                                            <label class="input-group-text" for="reportType">Select Report Type</label>
                                            <select class="form-select" id="reportType" onchange="updateReportLink()">
                                                <option value="">Choose...</option>
                                                <option value="all_reports">All Report</option>
                                                <option value="monthly">Monthly Report</option>
                                                <option value="annual">Annual Report</option>
                                                <option value="user_specific">Your Report</option>
                                                <option value="success_reports">Success Reports(found and matched)</option>
                                                <option value="todays_reports">Today's Reports</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <a id="generateReportLink" href="generate_pdf_reports.php" class="btn btn-primary" style="display: none;">Download PDF Report</a>


                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="card text-white bg-primary mb-3">
                                                <div class="card-body">
                                                    <h5 class="card-title">Total Users</h5>
                                                    <p class="card-text"><?php echo htmlspecialchars($totalUsers); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card text-white bg-success mb-3">
                                                <div class="card-body">
                                                    <h5 class="card-title">Total Reports</h5>
                                                    <p class="card-text"><?php echo htmlspecialchars($totalReports); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card text-white bg-warning mb-3">
                                                <div class="card-body">
                                                    <h5 class="card-title">Pending Reports</h5>
                                                    <p class="card-text"><?php echo htmlspecialchars($pendingReports); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card text-white bg-success mb-3">
                                                <div class="card-body">
                                                    <h5 class="card-title">Approved Reports</h5>
                                                    <p class="card-text"><?php echo htmlspecialchars($approvedReports); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card text-white bg-danger mb-3">
                                                <div class="card-body">
                                                    <h5 class="card-title">Rejected Reports</h5>
                                                    <p class="card-text"><?php echo htmlspecialchars($rejectedReports); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div id="reportStatusChart" style="width: 100%; height: 400px;"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div id="userCountChart" style="width: 100%; height: 400px;"></div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="row text-center">
                
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div id="gender_chart" style="width: 900px; height: 500px;"></div>
                                                        <div id="month_chart" style="width: 900px; height: 500px;"></div>
                                                        <!--<div id="geo_chart" style="width: 900px; height: 500px;"></div>-->
                                                        <div id="role_chart" style="width: 900px; height: 500px;"></div>
                                                    </div>
                                                </div

                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!----end of admin dashboard----->
        </div>
    <?php elseif ($role === 'allowed_user'): ?>
        <div class="alert alert-info">
            <h1 class="display-4">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
            <p class="lead"> You're now logged in to the Missing Persons Portal as verified user.</p>
            <hr class="my-4">
            <p>Access key features via the navigation menu above.</p>
        </div>
        <div class="row">
            <div class="col-md-6">
                <p><a class="btn btn-primary" href="submit_report.php" role="button">Submit Report &raquo;</a></p>
            </div>
        </div>
        
    <?php endif; ?>
    <?php  ?>


<div class="row">
            <div class="col-md-12">
                <h2 class="text-center text-info text-capitalize fw-bold bg-dark mb-2 rounded-3" id="reports">Your reports</h2>

                <table id="reportsTable" class="table table-bordered text-center table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Date Reported</th>
                            <th>Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($reportsByUserId) && !empty($reportsByUserId)): ?>
                            <?php foreach ($reportsByUserId as $report): ?>
                                <tr>
                                    <td class="align-middle"><?= htmlspecialchars($report['id']) ?></td>
                                    <td class="align-middle"><?= htmlspecialchars($report['name']) ?></td>
                                    <td class="align-middle"><?= htmlspecialchars($report['status']) ?></td>
                                    <td class="align-middle"><?= htmlspecialchars($report['created_at']) ?></td>
                                    <td class="align-middle"><img src="<?= htmlspecialchars($report['image_path']) ?>" alt="missing person's image" class="image-fluid" style="max-width: 100px; height: auto;"></td>
                                    <td>
                                        <button 
                                            class="btn btn-warning btn-sm editReportButton" 
                                            data-id="<?= $report['id'] ?>" 
                                            data-name="<?= htmlspecialchars($report['name']) ?>" 
                                            data-age="<?= htmlspecialchars($report['age']) ?>" 
                                            data-gender="<?= htmlspecialchars($report['gender']) ?>" 
                                            data-description="<?= htmlspecialchars($report['description']) ?>" 
                                            data-location="<?= htmlspecialchars($report['location']) ?>" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editReportModal">
                                            Edit
                                        </button>
                                        <form action="delete_report.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="report_id" value="<?= $report['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="row text-center text-info text-capitalize fw-bold bg-dark mb-2 rounded-3">No reports from you found!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

</div>
<!-- Modal for Editing Report -->
<div class="modal fade" id="editReportModal" tabindex="-1" aria-labelledby="editReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editReportModalLabel">Update Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editReportForm">
                    <input type="hidden" name="id" id="reportId">
                    <div class="mb-3">
                        <label for="reportName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="reportName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="reportAge" class="form-label">Age</label>
                        <input type="number" class="form-control" id="reportAge" name="age" required>
                    </div>
                    <div class="mb-3">
                        <label for="reportGender" class="form-label">Gender</label>
                        <select class="form-control" id="reportGender" name="gender" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="reportDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="reportDescription" name="description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="reportLocation" class="form-label">Location</label>
                        <input type="text" class="form-control" id="reportLocation" name="location" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#reportsTable').DataTable(); // Initialize DataTable on the table with id "reportsTable"
    });
</script>
    <script>
        function updateReportLink() {
            const reportType = document.getElementById('reportType').value;
            const reportLink = document.getElementById('generateReportLink');

            if (reportType) {
                reportLink.href = `generate_pdf_reports.php?report_type=${reportType}`;
                reportLink.style.display = 'inline-block'; // Show the button when a type is selected
            } else {
                reportLink.style.display = 'none'; // Hide the button if no type is selected
            }
        }
        document.addEventListener('DOMContentLoaded', () => {
        const editButtons = document.querySelectorAll('.editReportButton');
        const reportIdField = document.getElementById('reportId');
        const reportNameField = document.getElementById('reportName');
        const reportAgeField = document.getElementById('reportAge');
        const reportGenderField = document.getElementById('reportGender');
        const reportDescriptionField = document.getElementById('reportDescription');
        const reportLocationField = document.getElementById('reportLocation');

        // Populate modal with selected report's data
        editButtons.forEach(button => {
            button.addEventListener('click', () => {
                reportIdField.value = button.dataset.id;
                reportNameField.value = button.dataset.name;
                reportAgeField.value = button.dataset.age;
                reportGenderField.value = button.dataset.gender;
                reportDescriptionField.value = button.dataset.description;
                reportLocationField.value = button.dataset.location;
            });
        });

        // Handle form submission
            document.getElementById('editReportForm').addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent default form submission
                const formData = new FormData(this);

                fetch('edit_report.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Report updated successfully.');
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert('Failed to update report.');
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });

    </script>

</body>
</html>
