<?php
require_once '../config/DatabaseConfiguration.php';
require_once '../models/Report.php';

$database = new DatabaseConfiguration();
$db = $database->getConnection();
$report = new Report($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $data = [
        'name' => $_POST['name'],
        'age' => $_POST['age'],
        'gender' => $_POST['gender'],
        'description' => $_POST['description'],
        'location' => $_POST['location']
    ];

    if ($report->updateReport($id, $data)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
