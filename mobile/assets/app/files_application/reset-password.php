<?php
// Include the database connection file
require_once 'conn.php';

// Function to hash the new password
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Check if the request is valid
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $code = trim($_POST['code']);
    $newPassword = trim($_POST['new_password']);

    // Check if the email and reset code match
    $stmt = $conn->prepare("SELECT * FROM subscribers WHERE sEmail = ? AND sVerCode = ?");
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Reset the password
        $hashedPassword = hashPassword($newPassword);

        $updateStmt = $conn->prepare("UPDATE subscribers SET sPass = ?, sVerCode = NULL WHERE sEmail = ?");
        $updateStmt->bind_param("ss", $hashedPassword, $email);

        if ($updateStmt->execute()) {
            echo json_encode(["success" => true, "message" => "Password reset successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to reset password."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid reset code or email."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>