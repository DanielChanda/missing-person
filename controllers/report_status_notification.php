<?php
/**
 * for notifying users that their reports where approved via email 
 * @author chanda Danny
 */

require_once 'C:/xampp/htdocs/missing/utils/Email.php';
require_once 'C:\\xampp\\htdocs\\missing\\Models\\Report.php';
require_once 'C:/xampp/htdocs/missing/config/DatabaseConfiguration.php';
class Notification{

    private $email;
    private $report;
    private $subject;
    private $body;
	public function __construct(){
        $this->email = new Email();

        //creating the database configurations object
        $database = new DatabaseConfiguration();
        $db = $database->getConnection();

        //creating the report object
        $this->report = new Report($db);
    }
    
    /**
     * for sending a notification to a user about the status of their report
     */
    public function send($report_id,$status){
        
        // Fetch user email
        $user_email = $this->report->getUserEmailByReportId($report_id);

        // Fetch user name
        $user_name = $this->report->getUserNameByReportId($report_id);
        
        if($status == 'approved'){

            $this->subject = "Report Approved";
            $this->body = "Dear ".$user_name.", <br><br> Your report has been approved.";

        }elseif($status == 'rejected'){

            $this->subject = "Report Rejected";
            $this->body = "Dear ".$user_name.", <br><br> Your report has been rejected.";

        }

        // Send email notification    
        $this->email->sendEmail($user_email, $this->subject, $this->body);
         

	}
		
}

?>