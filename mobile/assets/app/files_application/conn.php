<?php
$servername = "localhost";
$username = "irvprxtj_apk";
$password = "irvprxtj_apk";
$dbname = "irvprxtj_apk";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establish Database Connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check Connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Require PHPMailer with Correct Path
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';

// Use PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Function to Send Email Notifications
function sendEmailNotification($toEmail, $toName, $subject, $messageBody)
{
    $mail = new PHPMailer(true);

    try {
        // Enable Debugging (Optional)
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Use SMTP::DEBUG_OFF for no debug output

        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'mail.alihisandata.com.ng'; // SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'support@alihisandata.com.ng'; // SMTP username
        $mail->Password = 'irvprxtj_apk'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use 'ssl' if required
        $mail->Port = 587;

        // Email Content
        $mail->setFrom('Support@alihisandata.com.ng', 'alihisanData');
        $mail->addAddress($toEmail, $toName);
        $mail->Subject = $subject;
        $mail->Body = $messageBody;
        $mail->isHTML(true); // Set to true if sending HTML emails

        // Send Email
        if ($mail->send()) {
            return "Email Sent Successfully to $toEmail";
        } else {
            return "Failed to send email: " . $mail->ErrorInfo;
        }
    } catch (Exception $e) {
        return "Email sending failed: " . $mail->ErrorInfo;
    }
}
?>