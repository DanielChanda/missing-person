<?php
require_once '../models/Report.php';
require_once '../models/Security.php';

class ReportController {
    private $db;
    private $report;

    public function __construct($db) {
        $this->db = $db;
        $this->report = new Report($db);
    }

    public function createReport($name, $age, $gender, $last_seen, $contact_info, $image) {
        $this->report->name = $name;
        $this->report->age = $age;
        $this->report->gender = $gender;
        $this->report->last_seen = $last_seen;
        $this->report->contact_info = $contact_info;
        $this->report->image = $image;
        
        return $this->report->create();
    }

    public function getAllReports() {
        return $this->report->readAll();
    }
}
?>