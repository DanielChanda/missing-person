<?php
require_once '../models/User.php';
require_once '../models/Security.php';
require_once '../config/DatabaseConfiguration.php';
require_once '../controllers/registration_notification.php';

//add user actions
if(isset($_POST['submitCode'])){

}elseif
   ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    if($_GET['action'] == 'add_user'){
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        $image_path = '../uploads/profilePictures/default.jpg';

        $database = new DatabaseConfiguration();
        $db = $database->getConnection();

        $controller = new UserController($db);

        $notification = new Notification();

        // Check if email already exists
        if ($controller->emailExists($email)) {
            echo '<div class="alert alert-warning text-center" role="alert">
                            Email already registered!
                    </div>';
                    
            
        }else{
            //generate random 5 digits number
            $code = rand(10000, 99999);
            //send notification to the provided email
            $notification->sendVerificationCode($code.' ,Use it as a log In password But you can change anytime.', $email);
            //make the password become the generated code
            $password = $code;
            if ($controller->register($username, $email, $password, $role, $image_path, $code)) {

                if ($controller->verifyCode($code, $email)) {
                    //send a notification to let the user know that they have been registered sucessfully
                    $notification->send($email, $username);
                    //redirect to the manage user page
                    header("Location: ../views/users/admin/manage_users.php?action=userAdded");
                    exit();
                }
                
            } else {
                echo "Failed to add user.";
            }
        }
            
    }
    
}

class UserController {
    private $db;
    private $user;

    public $message;

    public function __construct($db) {
        $this->db = $db;
        $this->user = new User($db);
    }
    public function getUserId(){
    	return $this->user->id;
    }

	public function getUserRole(){
    	return $this->user->role;
    }

	public function getUserName(){
    	return $this->user->username;
    }
    
    public function register($username, $email, $password, $role, $image_path, $code) {
        
        $this->user->username = $username;
        $this->user->email = $email;
        $this->user->password = $password;
        $this->user->role = $role;
        $this->user->image = $image_path;
        $this->user->code = $code; 
        
        
        return $this->user->register();
    }

    public function login($email, $password) {
        $this->user->email = $email;
        $this->user->password = $password;
        
        return $this->user->login();
    }

    function checkStatus($email) {
        // Set the user's email
        $this->user->email = $email;
        
    
        // Get the user status once
        $status = $this->user->checkStatus($email);
    
        // Check the user status and set the message accordingly
        if ($status === 'suspended') {
            $this->message = '<div class="alert alert-info"><div class="text-center fw-bold border-bottom border-2 border-dark">This account has been temporarily blocked.</div> Please contact our <a href="mailto:dannychanda05@gmail.com">support team</a> to determine the reason. Thank you for your understanding.</div>';
            return false;
        } elseif ($status === 'not verified') {
            $this->message = '<div class="alert alert-info"><div class="text-center fw-bold border-bottom border-2 border-dark">Account Not Verified</div>
                Please verify your account. For a new code, click "Forgot Password" and enter your email.
                Thank you.</div>';
            return false;
        }
    
        // If the status is neither 'suspended' nor 'not verified', return true
        return true;
    }
    

    public function getUserById($id) {
        return $this->user->getUserById($id);
    }
        /**
     * Checks if an email already exists in the database.
     *
     * @param string $email The email to check.
     * @return boolean True if the email exists, otherwise false.
     */
    public function emailExists($email) {
        return $this->user->emailExists($email);
    }

    /**
     * Checks if an email and given code match.
     *
     * @param string $email The email to check.
     * @param string $code The code to check.
     * @return boolean True if the email and code match, otherwise false.
     */
    function verifyCode($code, $email){
        return $this->user->verifyCode($code, $email);
    }

    /**
     * sets the code to a newly generated code
     * 
     * @param string the generated code to be used for user verification.
     * @param string $email The email to check.
     * @return boolean True if the email exist and code replaced successfully.
     */
    public function setCode($code, $email){
        return $this->user->setCode($code, $email);
    }

    /**
     * updates the user password
     * @param string new password
     * @param string verified email
     * @param int verified code
     * @return boolean true if the password was successfully changed
     */
    public function updatePassword($newPassword,$email,$code){
        return $this->user->updatePassword($newPassword,$email,$code);
    }
}
?>