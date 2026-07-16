<?php
header('Content-Type: application/json');

// Include the database connection
require_once 'conn.php';

// Retrieve the token and new PIN from the POST request
$token = $_POST['token'] ?? null;
$new_pin = $_POST['new-pin'] ?? null;

// Validate input
if (!$token) {
    echo json_encode(["success" => false, "message" => "Token is required."]);
    exit();
}

if (!$new_pin) {
    echo json_encode(["success" => false, "message" => "New PIN is required."]);
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

    // Update the PIN in the database
    $update_query = "UPDATE subscribers SET sPin = ? WHERE sId = ?";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->bind_param("si", $new_pin, $sId);

    if ($stmt_update->execute()) {
        echo json_encode(["success" => true, "message" => "PIN updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update PIN."]);
    }

    $stmt_update->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid token."]);
}

// Close the connection
$stmt->close();
$conn->close();
?>