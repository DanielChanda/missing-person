<?php
/**
 * the user class is the responsible for modelling the various users of the systym,
 * it is responsible for performing CRUD operations of the user and all user functions in this system.
 * @author Danny Chanda
 */
require_once 'security.php';
class User {
    //conn is responsible for referencing the database connection object
    private $conn;
    //table_name holds the user's database table name
    private $table_name = "users";

    //id,username, email, password, and role are the field of the user's table
    public $id;//unique identifier
    public $username;//user's name
    public $email;//user's email
    public $password;
    public $role;
    public $image;
    public $code;
    public $status = 'not verified';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * register fuction is responsible for handling user registration into the system.
     * @return boolean True if the user was succesfully registered and stored in the database otherwise false.
     */
    public function register() {
        try{
            //check if user did not set their profile picture
            if($this->image == ''){
                //user did'nt set picture,set it to default image;
                $this->image = '../uploads/profilePictures/default.jpg';
            }
            $query = "INSERT INTO " . $this->table_name . " SET username=:username, email=:email, password=:password, role=:role, image=:image, code=:code, status=:status";

            $stmt = $this->conn->prepare($query);
            $this->username = Security::sanitizeInput($this->username);
            $this->email = Security::sanitizeInput($this->email);
            $this->password = Security::hashPassword($this->password);
            $this->role = Security::sanitizeInput($this->role);
            $this->image = Security::sanitizeInput($this->image);
            $this->code = Security::sanitizeInput($this->code);
            $this->status = Security::sanitizeInput($this->status);

            
            $stmt->bindParam(":username", $this->username);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":password", $this->password);
            $stmt->bindParam(":role", $this->role);
            $stmt->bindParam(":image", $this->image);
            $stmt->bindParam(":code", $this->code);
            $stmt->bindParam(":status", $this->status);
            
            
            if ($stmt->execute()) {
                
                return true;
            }
            
            return false; 
        }catch(Exception $e){
            
        }
        
    }
    /**
     * retrive the user's profile picture
     * @param int user's id
     */
    public function getProfilePicture($id){
        $query = "SELECT image FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id = $id;
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * responsible for login the user into the system.
     * it verifies wheather the user is authorised by making use of their credentials.
     * @return boolean true if the user was verified.
     */
    public function login() {
        $query = "SELECT id, username, password, status, role FROM " . $this->table_name . " WHERE email = :email";

        $stmt = $this->conn->prepare($query);
        $this->email = Security::sanitizeInput($this->email);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        //check verify password
        if ($row && Security::verifyPassword($this->password, $row['password'])) {
            //verify status
            if($row['status']=='verified'){
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->role = $row['role'];
                return true;
            }
            return false;
        }
        return false;
    }
    /**
     * sets the code to a newly generated code
     * @param string $email The email to check.
     * @param string the generated code to be used for user verification.
     * @return boolean True if the email exist and code replaced successfully.
     */
    public function setCode($code, $email){
        $query = 'UPDATE '.$this->table_name.' SET code=:code WHERE email=:email;';
        $smtp = $this->conn->prepare($query);
        $smtp->bindParam(':code', $code);
        $smtp->bindParam(':email', $email);
        return $smtp->execute();
    }

    /**
     * retrieves the user's id,username and email using the ID
     * @param int the users ID
     * @return associative_array an array of user details
     */
    public function getUserById($id) {
        $query = "SELECT id, username, email, role FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    /**
     * updates the user data.the password field can be left untouched if no changes are to be made.
     * @return boolean true if the update was succesful otherwise false
     */
    public function updateUser() {
        $query = "UPDATE " . $this->table_name . " SET username = :username, email = :email, password = :password, role = :role WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->username = Security::sanitizeInput($this->username);
        $this->email = Security::sanitizeInput($this->email);
        //check if the password field is not empty
        if (!empty($this->password)) {
            //not empty ,so we update it
            $this->password = Security::hashPassword($this->password);
        } else {
            //empty dont change it
            $query = "UPDATE " . $this->table_name . " SET username = :username, email = :email, role = :role WHERE id = :id";
            $stmt = $this->conn->prepare($query);
        }
        $this->role = Security::sanitizeInput($this->role);
        $this->id = Security::sanitizeInput($this->id);

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * deletes the user by making use of the ID
     * @param int user's ID
     * @return boolean True if user was deleted
     */
    public function deleteUser($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * responsible for fetching all users by their id,username,email and role
     * @return associative_array all user's data
     */
	public function getAllUsers() {
        $query = "SELECT id, username, email, role, image,status FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * updates the user data.This includes username,email, and role.
     * @return boolean true if the update was succesful otherwise false
     */
	public function update() {
        $query = "UPDATE " . $this->table_name . " SET username = :username, email = :email, role = :role WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    /**
     * deletes the user by making use of the ID
     * @return boolean True if user was deleted
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }
	
    /**
     * returns the curent total number of registered users.
     * @return int number of users
     */
    public function getTotalUsers() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
	/**
     * gets all the user details
     * @return associative_array the key value pairs of user details
     */
	public function getUserDetailsById() {
        $query = "SELECT username, email, image FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
		
    /**
     * for updating the user profile.
     * @return boolean true if the update was succesful
     */
    public function update_p() {
        $query = "UPDATE " . $this->table_name . " SET username = :username, email = :email";
        if (!empty($this->password)) {
            $query .= ", password = :password";
        }
        $query .= " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        if (!empty($this->password)) {
            $stmt->bindParam(':password', $this->password);
        }
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    /**
     * Checks if an email already exists in the database.
     *
     * @param string $email The email to check.
     * @return boolean True if the email exists, otherwise false.
     */
    public function emailExists($email) {
        // Sanitize input
        $email = Security::sanitizeInput($email);

        // Prepare the query
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email LIMIT 1";

        // Prepare the statement
        $stmt = $this->conn->prepare($query);

        // Bind the email parameter
        $stmt->bindParam(':email', $email);

        // Execute the statement
        $stmt->execute();

        // Check if any record was found
        if ($stmt->rowCount() > 0) {
            return true; // Email exists
        }

        return false; // Email does not exist
    }

    /**
     * Checks if an email and given code match.
     *
     * @param string $email The email to check.
     * @param string $code The code to check.
     * @return boolean True if the email and code match, otherwise false.
     */
    function verifyCode($code, $email) {
        // Sanitize input
        $email = Security::sanitizeInput($email);

        // Prepare the query
        $query = "SELECT code FROM " . $this->table_name . " WHERE email = :email AND code = :code LIMIT 1";

        try {
            
            // Prepare the statement
            $stmt = $this->conn->prepare($query);

            // Bind the email and code parameters
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':code', $code);

            // Execute the statement
            $stmt->execute();
            
            
            // Check if any record was found
            if ($stmt->rowCount() > 0) {
                
                // Prepare the update query
                $updateQuery = "UPDATE " . $this->table_name . " SET status = :status WHERE email = :email AND code = :code";
                
                // Prepare the statement
                $stmt = $this->conn->prepare($updateQuery);

                // Bind the parameters
                $status = 'verified'; // The status for the person should be verified in order to perform this functionality
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':code', $code);

                // Execute the statement
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                return true; // Email and code match
            }

            return false; // Email and code do not match
        } catch (PDOException $e) {
            // Handle errors (e.g., log them or rethrow)
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * updates the user password
     * @param string new password
     * @param string verified email
     * @param int verified code
     * @return boolean true if the password was successfully changed
     */
    public function updatePassword($newPassword, $email, $code) {
        // Prepare the SQL query with the correct placeholders
        $query = "UPDATE " . $this->table_name . " 
                  SET password=:newPassword 
                  WHERE email=:email 
                    AND code=:code 
                    AND status=:status";
    
        // Prepare the statement
        $stmt = $this->conn->prepare($query);
    
        // Sanitize and hash the inputs
        $newPassword = Security::hashPassword(Security::sanitizeInput($newPassword));
        $email = Security::sanitizeInput($email);
        $code = Security::sanitizeInput($code);
    
        // Bind the parameters to the query
        $status = 'verified'; // We assume the status is currently verified
        $stmt->bindParam(':newPassword', $newPassword);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':status', $status);
    
        // Execute the statement and return the result
        if ($stmt->execute()) {
            return true;
        }
    
        return false;
    }

    public function suspendUser($id){
        //sql query to suspend the user
        $query = "UPDATE ".$this->table_name." SET status='suspended' WHERE id=:id;";

        //prepare the statement
        $stmt = $this->conn->prepare($query);
        $id = Security::sanitizeInput($id);
        //bind the parameter
        $stmt->bindParam(':id',$id);

        //execute the statement
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function reactivateUser($id){
        //sql query to suspend the user
        $query = "UPDATE ".$this->table_name." SET status='verified' WHERE id=:id;";

        //prepare the statement
        $stmt=$this->conn->prepare($query);
        $id = Security::sanitizeInput($id);
        //bind the parameter
        $stmt->bindParam(':id',$id);

        //execute the statement
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    function checkStatus($email){

        $query = "SELECT status FROM ".$this->table_name." WHERE email=:email;";

        $stmt = $this->conn->prepare($query);

        $email = Security::sanitizeInput($email);
        $stmt->bindParam(':email',$email);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $row['status']; // Return the user's status
        } else {
            return null; // Email not found
        }
        

    }

    public function getUserEmailByUserId($id){
        $query = "SELECT email FROM ".$this->table_name." WHERE id=:id;";
        //prepare statement 
        $stmt = $this->conn->prepare($query);

        //bind paramiter
        $stmt->bindParam(':id',$id);

        //execute statement
        $row = $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $row['email']; // Return the user's status
        } else {
            return null; // Email not found
        }


    }


}
?>