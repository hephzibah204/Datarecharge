<?php
header('Content-Type: application/json');

// Include the database connection
require_once 'conn.php';

// Retrieve the token and new password from the POST request
$token = $_POST['token'] ?? null;
$new_password = $_POST['new-password'] ?? null;

// Validate input
if (!$token) {
    echo json_encode(["success" => false, "message" => "Token is required."]);
    exit();
}

if (!$new_password) {
    echo json_encode(["success" => false, "message" => "New password is required."]);
    exit();
}

// Check if the user exists using the token
$sql = "SELECT sId FROM subscribers WHERE token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User found
    $user = $result->fetch_assoc();
    $sId = $user['sId'];

    // Hash the new password using the specified method
    $hashed_password = substr(sha1(md5($new_password)), 3, 10);

    // Update the password in the database
    $update_query = "UPDATE subscribers SET sPass = ? WHERE sId = ?";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->bind_param("si", $hashed_password, $sId);

    if ($stmt_update->execute()) {
        echo json_encode(["success" => true, "message" => "Password updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update password."]);
    }

    $stmt_update->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid token."]);
}

// Close the connection
$stmt->close();
$conn->close();
?>