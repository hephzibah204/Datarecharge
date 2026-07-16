<?php
// Include the database connection file (which contains PHPMailer setup)
require_once 'conn.php';

// Function to generate a 6-digit random OTP
function generateResetCode() {
    return rand(100000, 999999);
}

// Check if the request is valid
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    // Check if the email exists in the subscribers table
    $stmt = $conn->prepare("SELECT sFname FROM subscribers WHERE sEmail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['sFname']; // Fetch user's name

        // Generate the reset code
        $resetCode = generateResetCode();

        // Update the reset code in the subscribers table
        $updateStmt = $conn->prepare("UPDATE subscribers SET sVerCode = ? WHERE sEmail = ?");
        $updateStmt->bind_param("ss", $resetCode, $email);

        if ($updateStmt->execute()) {
            // Email subject and message
            $subject = "Password Reset Code - Smart Pay Nigeria";
            $message = "
                <p>Dear $name,</p>
                <p>You requested a password reset.</p>
                <p>Your password reset code is: <b>$resetCode</b></p>
                <p>If you did not request this, please ignore this email.</p>
                <p>Best Regards,<br>Smart Pay Nigeria Team</p>
            ";

            // Call the email function from conn.php
            if (sendEmailNotification($email, $name, $subject, $message)) {
                echo json_encode(["success" => true, "message" => "Reset code sent to your email."]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to send email. Please try again."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Failed to update reset code."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Email not found."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>