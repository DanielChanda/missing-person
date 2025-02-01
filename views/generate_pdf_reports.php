<?php
session_start();
require_once '../config/DatabaseConfiguration.php';
require_once '../Models/report.php';
require_once '../libraries/fpdf/fpdf.php';

// Initialize Database
$database = new DatabaseConfiguration();
$db = $database->getConnection();
$report = new Report($db);

// Fetch user preference
$userPreference = $_GET['report_type'] ?? 'summary'; // default to 'summary' for summary reports

// Initialize PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);

// Title based on report type
switch ($userPreference) {
    case 'monthly':
        $title = 'Monthly Summary Report';
        $startDate = date('Y-m-01'); // Start of current month
        $endDate = date('Y-m-t');    // End of current month
        generateSummaryReport($pdf, $report, $startDate, $endDate);
        break;

    case 'annual':
        $title = 'Annual Summary Report';
        $startDate = date('Y-01-01'); // Start of current year
        $endDate = date('Y-12-31');   // End of current year
        generateSummaryReport($pdf, $report, $startDate, $endDate);
        break;
    case 'success_reports':  // New case for success reports
        $title = 'Success Reports (Found & Approved)';
        generateSuccessReports($pdf, $report);
        break;
    case 'user_specific':
        $userId = $_SESSION['user_id'];
        $title = 'Your Report';
        generateUserSpecificReport($pdf, $report, $userId);
        break;

    case 'case_status':
        $title = 'Summary by Case Status';
        generateCaseStatusReport($pdf, $report);
        break;
    case 'todays_reports':  // Handle today's reports
        $title = 'Today\'s Reports';
        generateTodaysReports($pdf, $report);
        break;

    case 'all_reports':
        $title = 'All Reports';
        generateAllReports($pdf, $report);
        break;

    default:
        $title = 'Monthly Report Summary';
        $startDate = date('Y-m-01'); // Default to monthly if no type specified
        $endDate = date('Y-m-t');
        generateSummaryReport($pdf, $report, $startDate, $endDate);
}

// Output title and final PDF
$pdf->Cell(0, 10, $title, 0, 1, 'C');
$pdf->Ln(10);
$pdf->Output('report.pdf', 'D');

// Function for generating different reports
function generateSummaryReport($pdf, $reportModel, $startDate, $endDate) {
    // Fetch data based on the given date range
    $reports = $reportModel->getReportsByPeriod($startDate, $endDate);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Report Summary (' . $startDate . ' to ' . $endDate . ')', 0, 1);
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Line from (x1, y1) to (x2, y2)
    $pdf->SetFont('Arial', '', 10);

    foreach ($reports as $row) {
        $pdf->Cell(40, 10, "Report ID: " . $row['id'], 0, 1);
        $pdf->Cell(60, 10, "Reported person name: " . $row['name'], 0, 1);
        $pdf->Cell(60, 10, "Status: " . $row['status'], 0, 1);
        $pdf->Cell(60, 10, "Date Reported: " . $row['created_at'], 0, 1);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Line from (x1, y1) to (x2, y2)
        $pdf->Ln(5);
    }
}
//for generating reports based on specific user id
function generateUserSpecificReport($pdf, $report, $userId) {
    // Fetch user-specific data
    $reports = $report->getReportsByUserId($userId);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, "User-Specific Report", 0, 1);
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Line from (x1, y1) to (x2, y2)
    $pdf->SetFont('Arial', '', 10);

    foreach ($reports as $row) {
        $pdf->Cell(40, 10, "Report ID: " . $row['id'], 0, 1);
        $pdf->Cell(60, 10, "Status: " . $row['status'], 0, 1);
        $pdf->Cell(60, 10, "Reported person name: " . $row['name'], 0, 1);
        $pdf->Cell(60, 10, "Age: " . $row['age'], 0, 1);
        $pdf->Cell(60, 10, "Date: " . $row['created_at'], 0, 1);
        $pdf->Cell(60, 10, "Type of report: " . $row['type'], 0, 1);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Line from (x1, y1) to (x2, y2)
        $pdf->Ln(5);
    }
}
//generating the count of reports based on status
function generateCaseStatusReport($pdf, $report) {
    // Fetch case status data
    $caseStatuses = $report->getCaseStatuses();

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, "Case Status Summary", 0, 1);
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Line from (x1, y1) to (x2, y2)
    $pdf->SetFont('Arial', '', 10);

    foreach ($caseStatuses as $status => $count) {
        $pdf->Cell(40, 10, ucfirst($status) . ": " . $count, 0, 1);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Line from (x1, y1) to (x2, y2)
    }
}
//for generating all the reports
function generateAllReports($pdf, $report) {
    // Fetch all reports data
    $allReports = $report->getAllReports();

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, "All Reports Summary", 0, 1);
    $pdf->SetFont('Arial', '', 10);

    foreach ($allReports as $row) {
        $pdf->Cell(20, 10, "ID: " . $row['id'], 1);
        $pdf->Cell(60, 10, "Name: " . $row['name'], 1);
        $pdf->Cell(40, 10, "Status: " . $row['status'], 1);
        $pdf->Cell(40, 10, "Date Reported: " . $row['created_at'], 1);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Line from (x1, y1) to (x2, y2)
        $pdf->Ln();
    }
}
//fection for generating found and matched
function generateSuccessReports($pdf, $report) {
    // Fetch success reports
    $successReports = $report->getSuccessReports();

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, "Success Reports - Found & Approved", 0, 1);
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); 
    $pdf->SetFont('Arial', '', 10);

    foreach ($successReports as $row) {
        $pdf->Cell(40, 10, "Report ID: " . $row['id'], 0, 1);
        $pdf->Cell(60, 10, "Name: " . $row['name'], 0, 1);
        $pdf->Cell(60, 10, "Status: " . $row['status'], 0, 1);
        $pdf->Cell(60, 10, "Date Reported: " . $row['created_at'], 0, 1);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); 
        $pdf->Ln(5);
    }
}
function generateTodaysReports($pdf, $report) {
    $todaysReports = $report->getTodaysReports();

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, "Today's Reports", 0, 1);
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); 
    $pdf->SetFont('Arial', '', 10);

    if (empty($todaysReports)) {
        $pdf->Cell(0, 10, "No reports found for today.", 0, 1);
        return;
    }

    foreach ($todaysReports as $row) {
        $pdf->Cell(40, 10, "Report ID: " . $row['id'], 0, 1);
        $pdf->Cell(60, 10, "Name: " . $row['name'], 0, 1);
        $pdf->Cell(60, 10, "Status: " . $row['status'], 0, 1);
        $pdf->Cell(60, 10, "Date Reported: " . $row['created_at'], 0, 1);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(5);
    }
}

?>
