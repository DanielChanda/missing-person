<?php
/**
 * responsible for modeling the reports submitted by users.
 * provides all methods relating to reports.
 * @author Chanda Danny
 */
class Report {
    private $conn;
    private $table_name = "reports";

    public $id;
    public $type;
    public $name;
    public $age;
    public $gender;
    public $last_seen;
    public $description;
    public $contact_info;
    public $user_id;
    public $status;
    public $location;
    public $longitude;
    public $latitude;

    public function __construct($db) {
        $this->conn = $db;
    }
    /**
     * creates the report given the information by the user.
     * return true if the report is successfully created otherwise false
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
              SET type=:type, name=:name, age=:age, gender=:gender, last_seen=:last_seen, 
                  description=:description, contact_info=:contact_info, user_id=:user_id, 
                  image_path=:image_path, location=:location,latitude=:latitude,longitude=:longitude, status='pending'";

        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':age', $this->age);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':last_seen', $this->last_seen);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':contact_info', $this->contact_info);
        $stmt->bindParam(':user_id', $this->user_id);
    	$stmt->bindParam(':image_path', $this->image_path);
        $stmt->bindParam(':location', $this->location);
        $stmt->bindParam(':latitude', $this->latitude);
        $stmt->bindParam(':longitude', $this->longitude);
        
        if ($stmt->execute()) {
            //retrive the last inserted id
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }
	
    /**
     * retrieves all the pending reports
     */
	public function getPendingReports() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * retrieves all the reports with persons marked as missing
     */
	public function getPendingMissingReports() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE type = 'missing' AND status = 'approved'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * change the status of the report
     */
    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bindParam(':status', $this->status);
        
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    /**
     * retrieve the approved missing persons
     */
	public function getApprovedMissingPersons() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'approved' AND type = 'missing'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * count all the reports
     */
	public function getTotalReports() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * count all the peding reports
     */
    public function getPendingReportsCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * count all the approved reports
     */
    public function getApprovedReportsCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status = 'approved'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * count all the regected reports
     */
    public function getRejectedReportsCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status = 'rejected'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * gets the email of the user who submitted the report of the given report id
     * 
     */
	public function getUserEmailByReportId($report_id) {
        $query = "SELECT u.email FROM " . $this->table_name . " r
                JOIN users u ON r.user_id = u.id
                WHERE r.id = :report_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':report_id', $report_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['email'];
    }

    /**
     * gets the name of the user who submitted the report of the given report id
     * 
     */
	public function getUserNameByReportId($report_id) {
        $query = "SELECT u.username FROM " . $this->table_name . " r
                JOIN users u ON r.user_id = u.id
                WHERE r.id = :report_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':report_id', $report_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['username'];
    }

    /**
     * performs searching and filtering of reports. This is done by name,status,from_date and to_date.
     */
	public function searchReports($name, $status, $from_date, $to_date) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        
        if ($name) {
            $query .= " AND name LIKE :name";
        }
        if ($status) {
            $query .= " AND status = :status";
        }
        if ($from_date) {
            $query .= " AND created_at >= :from_date";
        }
        if ($to_date) {
            $query .= " AND created_at <= :to_date";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($name) {
            $stmt->bindValue(':name', '%' . $name . '%');
        }
        if ($status) {
            $stmt->bindValue(':status', $status);
        }
        if ($from_date) {
            $stmt->bindValue(':from_date', $from_date);
        }
        if ($to_date) {
            $stmt->bindValue(':to_date', $to_date);
        }
        
        $stmt->execute();
        
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $reports;
    }

    public function searchReportsAdvanced($name, $location, $age, $limit, $offset) {
        //select all reports containing persons approved to be missing
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'approved' AND type = 'missing'";

        //if any pattern in the name field is included ,use it to find possible matches that is if the candidate contains it
        if ($name) {
            $query .= " AND name LIKE :name";
        }

        //if any pattern in location name field is included ,use it to find possible matches that is if the candidate contains it
        if ($location) {
            $query .= " AND location LIKE :location";
        }
        //if any pattern in the age field is included ,use it to find possible matches that is if the candidate contains it
        if ($age) {
            $query .= " AND age = :age";
        }
        //limit and offset the search by the provided limit and offset
        $query .= " LIMIT :limit OFFSET :offset";
        //prepare the statement
        $stmt = $this->conn->prepare($query);
        //bind the name paramiter
        if ($name) {
            $stmt->bindValue(':name', '%' . $name . '%');
        }
        //bind the location paramiter
        if ($location) {
            $stmt->bindValue(':location', '%' . $location . '%');
        }
        //bind the age paramiter
        if ($age) {
            $stmt->bindValue(':age', $age, PDO::PARAM_INT);
        }
        //bind the limit paramiter
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        //bind the ofsset paramiter
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        //execute statemennt
        $stmt->execute();
        //fetch the results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //----------Matching functions--------------------

    public function findMatches() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE type = 'found' AND status = 'pending' AND name = ? AND age = ? AND gender = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->name);
        $stmt->bindParam(2, $this->age);
        $stmt->bindParam(3, $this->gender);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsFound() {
        $query = "UPDATE " . $this->table_name . " SET status = 'found' WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }

    //-------------auditing funcions------------------

    //function to log admin actions
    public function logAction($user_id, $action, $details) {
        $query = "INSERT INTO audit_logs (user_id, action, details) VALUES (:user_id, :action, :details)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':details', $details);
        $stmt->execute();
    }
                
    // method to approve a report and log the action
    public function approveReport($report_id, $user_id) {
        $query = "UPDATE reports SET status = 'approved' WHERE id = :report_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':report_id', $report_id);
        if ($stmt->execute()) {
            $this->logAction($user_id, 'approve_report', "Report ID: $report_id approved.");
            return true;
        }
        return false;
    }
                    
    // method to reject a report and log the action
    public function rejectReport($report_id, $user_id, $reason) {
        $query = "UPDATE reports SET status = 'rejected', rejection_reason = :reason WHERE id = :report_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':report_id', $report_id);
        $stmt->bindParam(':reason', $reason);
        if ($stmt->execute()) {
            $this->logAction($user_id, 'reject_report', "Report ID: $report_id rejected. Reason: $reason");
            return true;
        }
        return false;
    }
    


    /**
     * Get the distribution of missing persons by gender.
     * 
     * @return array An array of associative arrays with 'gender' and 'count' keys.
     */
    public function getGenderDistribution() {
        $query = "SELECT gender, COUNT(*) as count 
                FROM " . $this->table_name . " 
                GROUP BY gender";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $genderDistribution = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $genderDistribution[] = $row; // Add each row as an associative array
        }

        return $genderDistribution;
    }


    /**
     * Get the distribution of missing persons by age group.
     * 
     * @return array An associative array with age group as the key and count as the value.
     */
    public function getAgeGroupDistribution() {
        $query = "SELECT 
                    CASE 
                        WHEN age < 18 THEN 'Under 18'
                        WHEN age BETWEEN 18 AND 35 THEN '18-35'
                        WHEN age BETWEEN 36 AND 60 THEN '36-60'
                        ELSE 'Above 60'
                    END as age_group, COUNT(*) as count
                  FROM " . $this->table_name . "
                  GROUP BY age_group";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $ageGroupDistribution = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ageGroupDistribution[$row['age_group']] = $row['count'];
        }

        return $ageGroupDistribution;
    }

    /**
     * Get the distribution of missing persons by case status (e.g., Found vs. Missing).
     * 
     * @return array An associative array with status as the key and count as the value.
     */
    public function getStatusDistribution() {
        $query = "SELECT status, COUNT(*) as count 
                  FROM " . $this->table_name . " 
                  GROUP BY status";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $statusDistribution = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $statusDistribution[$row['status']] = $row['count'];
        }

        return $statusDistribution;
    }

    /**
     * Get the number of reports filed each month or year.
     * 
     * @param string $interval The interval to group by (e.g., 'month', 'year').
     * @return array An associative array with the interval as the key and count as the value.
     */
    public function getReportsByInterval($interval = 'month') {
        $dateFormat = ($interval === 'year') ? '%Y' : '%Y-%m';

        $query = "SELECT DATE_FORMAT(created_at, '$dateFormat') as period, COUNT(*) as count
                  FROM " . $this->table_name . " 
                  GROUP BY period
                  ORDER BY period";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $reportsByInterval = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reportsByInterval[$row['period']] = $row['count'];
        }

        return $reportsByInterval;
    }

    /**
     * Get the number of reports filed by each user role.
     * 
     * @return array An associative array with user role as the key and count as the value.
     */
    public function getReportsByRole() {
        $query = "SELECT u.role, COUNT(r.id) as count 
                  FROM users u
                  INNER JOIN " . $this->table_name . " r ON u.id = r.user_id
                  GROUP BY u.role";
    
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    
        $reportsByRole = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Append each role and count as an associative array
            $reportsByRole[] = $row;
        }
    
        return $reportsByRole;
    }
    

    /**
     * Get the number of reports filed by a specific user over time.
     * 
     * @param int $userId The ID of the user.
     * @return array An associative array with the period as the key and count as the value.
     */
    public function getUserReportsOverTime($userId) {
        $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as count
                  FROM " . $this->table_name . "
                  WHERE user_id = :user_id
                  GROUP BY period
                  ORDER BY period";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        $userReportsOverTime = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $userReportsOverTime[$row['period']] = $row['count'];
        }

        return $userReportsOverTime;
    }

    /**
     * Get the distribution of a user's reports by status (e.g., resolved vs. ongoing).
     * 
     * @param int $userId The ID of the user.
     * @return array An associative array with status as the key and count as the value.
     */
    public function getUserReportsByStatus($userId) {
        $query = "SELECT status, COUNT(*) as count
                  FROM " . $this->table_name . "
                  WHERE user_id = :user_id
                  GROUP BY status";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        $userReportsByStatus = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $userReportsByStatus[$row['status']] = $row['count'];
        }

        return $userReportsByStatus;
    }

    /**
     * Get the distribution of cases by status reported by a specific user.
     * 
     * @param int $userId The ID of the user.
     * @return array An associative array with status as the key and count as the value.
     */
    public function getUserCaseDistribution($userId) {
        $query = "SELECT status, COUNT(*) as count
                  FROM " . $this->table_name . "
                  WHERE user_id = :user_id
                  GROUP BY status";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        $caseDistribution = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $caseDistribution[$row['status']] = $row['count'];
        }

        return $caseDistribution;
    }
/**
 * Get the number of reports filed by a specific user over time.
 * 
 * @param int $userId The ID of the user.
 * @return array An associative array where the keys are months and the values are the number of reports filed in that month.
 */
public function getReportsByUser($userId)
{
    try {
        // SQL query to count reports filed by the user, grouped by month.
        $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS total_reports
                  FROM reports
                  WHERE user_id = :user_id
                  GROUP BY month
                  ORDER BY month ASC";

        // Prepare the SQL statement
        $stmt = $this->conn->prepare($query);

        // Bind the user ID to the statement
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        // Execute the statement
        $stmt->execute();

        // Fetch all results as an associative array
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the results
        return $results;
    } catch (PDOException $e) {
        // Log error and return an empty array if something goes wrong
        error_log("Error in getReportsByUser: " . $e->getMessage());
        return [];
    }
}

/**
 * Get the distribution of a user's reports by status.
 * 
 * @param int $userId The ID of the user whose report status distribution is to be retrieved.
 * @return array An associative array where the keys are statuses (e.g., 'Pending', 'Approved', 'Rejected') and the values are the counts for each status.
 */
public function getStatusDistributionByUser($userId)
{
    try {
        // SQL query to count reports by their status for the specified user.
        $query = "SELECT status, COUNT(*) AS total
                  FROM reports
                  WHERE user_id = :user_id
                  GROUP BY status";

        // Prepare the SQL statement
        $stmt = $this->conn->prepare($query);

        // Bind the user ID to the statement
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        // Execute the statement
        $stmt->execute();

        // Fetch all results as an associative array
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the results
        return $results;
    } catch (PDOException $e) {
        // Log error and return an empty array if something goes wrong
        error_log("Error in getStatusDistributionByUser: " . $e->getMessage());
        return [];
    }
}

/**
 * Get the total number of reports filed each month.
 * 
 * @return array An associative array where the keys are months (formatted as 'YYYY-MM') and the values are the total number of reports filed in that month.
 */
public function getReportsByMonth()
{
    try {
        // SQL query to count total reports filed each month.
        $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS total_reports
                  FROM reports
                  GROUP BY month
                  ORDER BY month ASC";

        // Prepare the SQL statement
        $stmt = $this->conn->prepare($query);

        // Execute the statement
        $stmt->execute();

        // Fetch all results as an associative array
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the results
        return $results;
    } catch (PDOException $e) {
        // Log error and return an empty array if something goes wrong
        error_log("Error in getReportsByMonth: " . $e->getMessage());
        return [];
    }
}

/**
 * Get reports submitted by a particular user
 * @param int the user id
 * @return array the array of all the reports submitted by a particular user 
 */
    function getReportsByUserId($userId){
        try{
            $query = "SELECT * FROM ".$this->table_name." r WHERE 
                        r.user_id = :userId;";

            // Prepare the SQL statement
            $stmt = $this->conn->prepare($query);

            // Bind the user ID to the statement
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            // Execute the statement
            $stmt->execute();

            // Fetch all results as an associative array
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Return the results
            return $results;
        } catch (PDOException $e) {
            // Log error and return an empty array if something goes wrong
            error_log("Error in getReportsByUserId: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieve all reports.
     * 
     * @return array An array containing all reports.
     */
    public function getAllReports() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve reports within a specified period.
     *
     * @param string $startDate Start date for the period.
     * @param string $endDate End date for the period.
     * @return array Reports within the specified period.
     */
    public function getReportsByPeriod($startDate, $endDate) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE created_at BETWEEN :startDate AND :endDate";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve reports based on case status.
     *
     * @param string $status The status of the cases (e.g., 'open', 'closed').
     * @return array An array of reports with the specified status.
     */
    public function getReportsByStatus($status) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
/**
 * Retrieve the count of reports by status.
 * 
 * @return array An associative array with status as the key and count as the value.
 */
public function getCaseStatuses() {
    $query = "SELECT status, COUNT(*) as count FROM " . $this->table_name . " GROUP BY status";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    
    $caseStatuses = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $caseStatuses[$row['status']] = $row['count'];
    }
    
    return $caseStatuses;
}


public function updateReport($id, $data) {
    session_start();
    $query = "UPDATE reports SET name = :name, age = :age, gender = :gender, 
              description = :description, location = :location WHERE id = :id AND user_id = :user_id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':age', $data['age']);
    $stmt->bindParam(':gender', $data['gender']);
    $stmt->bindParam(':description', $data['description']);
    $stmt->bindParam(':location', $data['location']);
    return $stmt->execute();
}

public function deleteReport($id) {
    session_start();
    $query = "DELETE FROM reports WHERE id = :id AND user_id = :user_id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    return $stmt->execute();
}
// fetch the successfuly matched reports
public function getSuccessReports() {
    $query = "SELECT * FROM reports WHERE status = 'matched' ORDER By Name";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// fetch todays reports
public function getTodaysReports() {
    $query = "SELECT * FROM reports WHERE DATE(created_at) = CURDATE()";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}

?>
