<?php
header('Content-Type: application/json');

// Include the database connection
require_once 'conn.php';

// Check if token is provided in the request (from the POST body)
if (!isset($_POST['token'])) {
    echo json_encode(["success" => false, "message" => "Token not provided"]);
    exit();
}

// Get the token from the POST request
$token = $_POST['token'];

// Query to find the user by token and retrieve the `sId`
$sql = "SELECT sId FROM subscribers WHERE token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch user data
    $user = $result->fetch_assoc();
    $sId = $user['sId'];

    // Query to retrieve transactions for the user using sId, ordered by date (newest first)
    $sql_transactions = "SELECT * FROM transactions WHERE sId = ? ORDER BY date DESC";
    $stmt_transactions = $conn->prepare($sql_transactions);
    $stmt_transactions->bind_param("i", $sId);
    $stmt_transactions->execute();
    $result_transactions = $stmt_transactions->get_result();

    // Check if any transactions exist
    if ($result_transactions->num_rows > 0) {
        $transactions = [];
        while ($transaction = $result_transactions->fetch_assoc()) {
            $transactions[] = $transaction;
        }

        // Return the transactions in response
        echo json_encode([
            "success" => true,
            "transactions" => $transactions
        ]);
    } else {
        // No transactions found
        echo json_encode([
            "success" => false,
            "message" => "No transactions found"
        ]);
    }
} else {
    // Invalid token
    echo json_encode(["success" => false, "message" => "Invalid token"]);
}

// Close the connection
$stmt->close();
$conn->close();
?>