<?php
session_start(); // Start the session to manage user sessions
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to login page if the user is not logged in or not an admin
    header("Location: ../../login.php");
    exit();
}

require_once '../../../config/DatabaseConfiguration.php'; // Include database configuration
require_once '../../../models/Report.php'; // Include Report model
require_once '../../../models/user.php'; // Include user model
require_once '../../../constants.php'; // Include the constants
require_once '../../navbar.php'; // Include navbar
require_once '../../../utils/Email.php'; //email handler

$database = new DatabaseConfiguration(); // Create a new database configuration instance
$db = $database->getConnection(); // Get the database connection
$report = new Report($db); // Create a new report instance
$user = new User($db);

// Instantiate the Email class
$emailSender = new Email();

$missingReports = $report->getPendingMissingReports(); // Retrieve all pending missing reports

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['missing_report_id']) && isset($_POST['found_report_id'])) {
    $missingReportId = $_POST['missing_report_id'];
    $foundReportId = $_POST['found_report_id'];

    // If the form is submitted and contains missing and found report IDs, process the request
    $report->id = $_POST['missing_report_id']; // Set the missing report ID in the report instance
    $report->status = 'found'; // Set the status to 'found'
    
    if ($report->markAsFound()) {
        // If marking the missing report as found is successful, update the status of the found report
        $foundReport = new Report($db);
        $foundReport->id = $_POST['found_report_id'];
        $foundReport->status = 'matched';
        $foundReport->updateStatus(); // Update the status of the found report

        $foundReport->id = $_POST['missing_report_id'];
        $foundReport->status = 'matched';
        $foundReport->updateStatus(); // Update the status of the found report

        $subject = "Potential Match Notification";
        $missingMessage = "Hello,<br><br>A match has been found for the missing person you reported. Please log in to the system to review the details.";
        $foundMessage = "Hello,<br><br>Your found person report has been matched to a missing person. Please log in to the system to review the details.";

        // Notify the users
        $missingUserEmail = $report->getUserEmailByReportId($missingReportId);
        $foundUserEmail = $report->getUserEmailByReportId($foundReportId);

        // Send notifications
        $subject = "Potential Match Notification";
        $missingMessage = "Hello,<br><br>A match has been found for the missing person you reported. Please log in to the system to review the details.";
        $foundMessage = "Hello,<br><br>Your found person report has been matched to a missing person. Please log in to the system to review the details.";

        // Send notifications using the Email class
        $emailSender->sendEmail($missingUserEmail, $subject, $missingMessage);
        $emailSender->sendEmail($foundUserEmail, $subject, $foundMessage);
        header("Location: match_reports.php"); // Redirect to the same page to refresh the list
        exit();
    } else {
        // If marking the report as found fails, display an error message
        echo "Failed to mark report as found.";
    }
}

// Initialize an empty array to store matched reports
$matchedReports = [];
foreach ($missingReports as $missingReport) {
    // For each missing report, try to find potential matches in the found reports
    $report->name = $missingReport['name'];
    $report->age = $missingReport['age'];
    $report->gender = $missingReport['gender'];
    $matchedFoundReports = $report->findMatches(); // Find matches based on name, age, and gender
    if (!empty($matchedFoundReports)) {
        // If matches are found, add them to the matchedReports array
        $matchedReports[] = [
            'missing_report' => $missingReport,
            'found_reports' => $matchedFoundReports
        ];
    }
}

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
    <title>Match Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet"> <!-- DataTables CSS -->
    <link href="../../style.css" rel="stylesheet">
    <style>
    .image-fluid {
        max-width: 100%; /* Make sure the image scales within its container */
        height: auto; /* Maintain the aspect ratio */
        border: 2px solid #ddd; /* Add a subtle border */
        border-radius: 8px; /* Round the corners */
        margin: 10px 0; /* Add spacing above and below */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add a soft shadow for emphasis */
    }
    
    td img {
        max-height: 200px; /* Restrict maximum height for consistency */
        object-fit: cover; /* Ensure the image scales and crops appropriately */
        display: block; /* Avoid inline layout issues */
        margin: 0 auto; /* Center the image horizontally */
    }

    td {
        vertical-align: top; /* Align content to the top of the table cells */
    }

    .table-bordered {
        border-collapse: collapse; /* Collapse borders for a clean look */
    }

    .table-bordered td, .table-bordered th {
        border: 1px solid #ddd; /* Subtle borders for table cells */
        padding: 10px; /* Add padding for readability */
    }

    .text-center {
        color: #333; /* Ensure text is legible */
        font-weight: bold; /* Enhance visual weight */
    }
    td {
        vertical-align: middle; /* Align content vertically in the middle */
        
    }

    .btn {
        margin: auto; /* Center the button */
        display: block; /* Ensure it behaves like a block-level element */
    }
</style>

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
            <?php echo getNavBar($role,'','', '', '', '', 'active" aria-current="page"'); ?>
        </ul>
    </div>
</nav>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Match Reports</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($matchedReports)): ?>
                        <!-- Display message if no matches are found -->
                        <p class="text-center">No matches found.</p>
                    <?php else: ?>
                        <!-- Display table of matched reports -->
                        <table id="matchTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Missing Report</th>
                                    <th>Potential Matches</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($matchedReports as $match): ?>
                                    <tr>
                                        <td>
                                            <!-- Display details of the missing report -->
                                            <strong>Name:</strong> <?php echo htmlspecialchars($match['missing_report']['name']); ?><br>
                                            <strong>Age:</strong> <?php echo htmlspecialchars($match['missing_report']['age']); ?><br>
                                            <strong>Gender:</strong> <?php echo htmlspecialchars($match['missing_report']['gender']); ?><br>
                                            <strong>Last Seen:</strong> <?php echo htmlspecialchars($match['missing_report']['last_seen']); ?><br>
                                            <strong>Description:</strong> <?php echo htmlspecialchars($match['missing_report']['description']); ?><br>
                                            <strong>Contact Info:</strong> <?php echo htmlspecialchars($match['missing_report']['contact_info']); ?><br>
                                            <strong>Type:</strong> <?php echo htmlspecialchars($match['missing_report']['type']); ?><br>
                                            <img src="../../<?php echo htmlspecialchars($match['missing_report']['image_path']); ?>" alt="missing report" class="image-fluid">
                                        </td>
                                        
                                        <!-- Display details of potential matching found reports -->
                                        <?php foreach ($match['found_reports'] as $foundReport): ?>
                                            <td>   
                                                <strong>Name:</strong> <?php echo htmlspecialchars($foundReport['name']); ?><br>
                                                <strong>Age:</strong> <?php echo htmlspecialchars($foundReport['age']); ?><br>
                                                <strong>Gender:</strong> <?php echo htmlspecialchars($foundReport['gender']); ?><br>
                                                <strong>Last Seen:</strong> <?php echo htmlspecialchars($foundReport['last_seen']); ?><br>
                                                <strong>Description:</strong> <?php echo htmlspecialchars($foundReport['description']); ?><br>
                                                <strong>Contact Info:</strong> <?php echo htmlspecialchars($foundReport['contact_info']); ?><br>
                                                <strong>Type:</strong> <?php echo htmlspecialchars($foundReport['type']); ?><br>
                                                <img src="../../<?php echo htmlspecialchars($foundReport['image_path']); ?>" alt="found report" class="image-fluid">
                                                
                                            </td> 
                                            <td>   
                                                <form action="match_reports.php" method="POST">
                                                    <!-- hidden input field to assist with the missing person report id-->
                                                    <input type="hidden" name="missing_report_id" value="<?php echo $match['missing_report']['id']; ?>">
                                                    <!-- hidden input field to assist with the missing person report id-->
                                                    <input type="hidden" name="found_report_id" value="<?php echo $foundReport['id']; ?>">
                                                    <!-- button for submiting the potential matches to the server to get marked as found-->
                                                    <button type="submit" class="btn btn-primary btn-sm">Mark as Found</button>
                                                </form>
                                            </td>    
                                        <?php endforeach; ?>
                                        
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
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>   
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script> <!-- DataTables JS -->

<script>
    $(document).ready(function() {
        $('#matchTable').DataTable(); // Initialize DataTable
    });
</script>
</body>
</html>
