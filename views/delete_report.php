<?php
require_once '../config/DatabaseConfiguration.php';
require_once '../models/Report.php';

$database = new DatabaseConfiguration();
$db = $database->getConnection();
$report = new Report($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'])) {
    $id = $_POST['report_id'];
    if ($report->deleteReport($id)) {
        header("Location: home.php?status=deleted");
        exit();
    }
}
?>
