<?php
require_once __DIR__ . '/../libraries/phpMailer/PHPMailer.php';

require_once __DIR__ . '/../libraries/phpMailer/Exception.php';
require_once __DIR__ . '/../libraries/phpMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);

        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'dannychanda05@gmail.com'; // SMTP username
        $this->mail->Password = 'fmrd oeno lyfr qufg';          // SMTP password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;                 // or 'ssl'
        $this->mail->Port = 587;                          // or 465
        $this->mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ]
    ];

        // Recipients
        $this->mail->setFrom('dannychanda05@gmail.com', 'Missing Persons App');
    }

    public function sendEmail($to, $subject, $body) {
        try {
            
            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            $this->mail->isHTML(true);
            
            $this->mail->send();
            
            return true;
        } catch (Exception $e) {
            
            return false;
        }
    }
}
?>