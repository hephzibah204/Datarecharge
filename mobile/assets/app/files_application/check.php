<?php
// Include your database connection file
require_once 'conn.php';

// Fetch the API key and API link from the database
$stmt = $conn->prepare("SELECT apikey, apilink FROM api2 WHERE value = 'user'");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "API key and link not found!"
    ]);
    exit;
}

$row = $result->fetch_assoc();
$apiKey = $row['apikey'];
$apiLink = $row['apilink'];

// API endpoint for wallet balance
$url = $apiLink . "/user";

// Initialize CURL to make the API call
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $apiKey",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
curl_close($ch);

// Check if the response is valid JSON
if ($response !== false) {
    $data = json_decode($response, true);

    if (isset($data['status']) && $data['status'] === "success") {
        // Send wallet balance to the app
        echo json_encode([
            "status" => "success",
            "wallet_balance" => $data['wallet_balance']
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => $data['message'] ?? "Failed to retrieve wallet balance!"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Unable to connect to the API!"
    ]);
}
?>