<?php
header('Content-Type: application/json');

// Include the database connection
require_once 'conn.php';

// Retrieve the token from POST
if (!isset($_POST['token'])) {
    echo json_encode(["success" => false, "message" => "Token not provided"]);
    exit();
}

$token = $_POST['token'];

// Check if the token exists and retrieve the `sId` from the subscribers table
$sql = "SELECT sId FROM subscribers WHERE token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch user data
    $user = $result->fetch_assoc();
    $sId = $user['sId'];

    // Retrieve transaction history for the user using `sId`, ordered by the most recent date
    $sql_transactions = "
        SELECT 
            tId, transref, status, oldbal, newbal, servicename, date, servicedesc, amount 
        FROM 
            transactions 
        WHERE 
            sId = ? 
        ORDER BY 
            date DESC";
    $stmt_transactions = $conn->prepare($sql_transactions);
    $stmt_transactions->bind_param("i", $sId);
    $stmt_transactions->execute();
    $history_result = $stmt_transactions->get_result();

    if ($history_result->num_rows > 0) {
        $history = [];
        while ($row = $history_result->fetch_assoc()) {
            $history[] = $row;
        }

        echo json_encode([
            "success" => true,
            "history" => $history
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "No transaction history found"]);
    }

    $stmt_transactions->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid token"]);
}

$stmt->close();
$conn->close();
?>