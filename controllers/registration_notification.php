<?php
require_once '../utils/Email.php';
/**
 *  for notifying registered users via email 
 *	@author chanda Danny
*/
class Notification{
	private $email;
	private $subject = "Registration Successful";
  	private $body = '';

	public function __construct(){
		$this->email = new Email();
	}

	public function send($user_email, $username){
					
		$this->body.="Dear ".$username." , <br><br> Your registration was successful. Welcome to the Missing Persons App!";
		$this->email->sendEmail($user_email, $this->subject, $this->body);
		

	}
	public function sendVerificationCode($code, $user_email){

		$this->body.="Hello your verification code is: <h2>".$code."</h2>";
		$this->email->sendEmail($user_email, "Verification code", $this->body);
		

	}
		
}
?>