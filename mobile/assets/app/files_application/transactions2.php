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
    $sql_transactions = "SELECT tId, sId, transref, servicename, servicedesc, amount, 
                         CASE 
                             WHEN status = 0 THEN 'successful' 
                             WHEN status = 1 THEN 'failed' 
                             ELSE 'unknown' 
                         END AS status_text, 
                         oldbal, newbal, profit, date, 
                         COALESCE(api_response, 'NONE') AS api_response, 
                         COALESCE(api_response_log, 'NONE') AS api_response_log 
                         FROM transactions 
                         WHERE sId = ? 
                         ORDER BY date DESC";
    $stmt_transactions = $conn->prepare($sql_transactions);
    $stmt_transactions->bind_param("i", $sId);
    $stmt_transactions->execute();
    $result_transactions = $stmt_transactions->get_result();

    $transactions = [];
    while ($transaction = $result_transactions->fetch_assoc()) {
        // Replace the status value with the text
        $transaction['status'] = $transaction['status_text'];
        unset($transaction['status_text']); // Remove the extra field

        $transactions[] = $transaction;
    }

    // Return the transactions in response
    echo json_encode([
        "success" => true,
        "transactions" => $transactions
    ]);
} else {
    // Invalid token
    echo json_encode(["success" => false, "message" => "Invalid token"]);
}

// Close the connection
$stmt->close();
$conn->close();
?>