<?php
/**
 * A very important model class which contains atmost the static functions responsible,
 * for handling the Security of the entire application.
 * Different security threats have been identified and included mechanisms to prevent them.
 * @author Danny Chanda
 */
class Security {
    /**
     * does input sanitization by not allowing any code to be treated as code.
     * This will prentent html tags from getting executed by the browser.
     * @param string the input from the user.
     * @return string a string with special characters striped.
     */
    public static function sanitizeInput($input) {

        return htmlspecialchars(strip_tags($input));

    }

    /**
     * responsible for password enscription
     * @param string a string of password characters to hash
     * @return string an enscripted password.
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * provide the verification of the password if they match.
     * @param string password entered
     * @param string the hashed password
     * @return boolean true if the password is verified.
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * 
     */
    public static function generateToken() {
        return bin2hex(random_bytes(32));
    }

    public static function verifyToken($token, $sessionToken) {
        return hash_equals($token, $sessionToken);
    }

    /**
     * starts the session if not started.
     */
    public static function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
	public static function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }

    public static function validateCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    public static function sanitize($data) {
        return htmlspecialchars(strip_tags($data));
    }

    /**
     * checks if the provided email is valid.
     * @param string a provided email
     * @return boolean true if the email is valid
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * checks if the provided integer is a valid integer.
     * @param int a provided integer
     * @return boolean true if the integer is valid
     */
    public static function validateInt($int) {
        return filter_var($int, FILTER_VALIDATE_INT);
    }

    /**
     * checks if the provided string is a valid string.
     * @param string a provided string of text
     * @return boolean true if the string is valid
     */
    public static function validateString($string) {
        return preg_match("/^[a-zA-Z0-9\s]+$/", $string);
    }

    /**
     * checks if the provided string contains only letters, numbers, spaces, commas, periods, and dashes.
     * @param string a provided string of text
     * @return boolean true if the string is valid
     */
    public static function validateDescription($description) {
        // Regular expression to allow letters, numbers, spaces, commas, periods, and dashes
        return preg_match("/^[a-zA-Z0-9\s,.!?\-']*$/", $description);
    }
    

    /**
     * checks if the provided value is one of the expected value from the select box.
     * @param string the selected value.
     * @param array a list of allowed values.
     * @return boolean true if the selected value is allowed.
     */
    public static function validateSelect($value, $allowedValues) {
        return in_array($value, $allowedValues);
    }

    /**
     * makes sure that the input date is valid.
     * @param string an input date
     * @return boolean true if the date is valid.
     */
    public static function validateDate($input_date) {
        
        $date = DateTime::createFromFormat('Y-m-d', $input_date);

        if ($date !== false) {
            // Valid date
            return true;
        } else {
            // Invalid date
            return false;
        }
    }
    
}
?>