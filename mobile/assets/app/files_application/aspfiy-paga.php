<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'conn.php';

// Function to log messages (optional for debugging)
function logMessage($message) {
    file_put_contents('debug.log', date('Y-m-d H:i:s') . " - " . $message . PHP_EOL, FILE_APPEND);
}

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request method. Use POST."]);
    exit;
}

// Retrieve token from POST request
$token = trim($_POST['token'] ?? '');

if (empty($token)) {
    echo json_encode(["success" => false, "message" => "Token is required"]);
    exit;
}

// Fetch user details
$stmt = $conn->prepare("SELECT sFname, sLname, sEmail, sPhone FROM subscribers WHERE token = ?");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database error", "error" => $conn->error]);
    exit;
}

$stmt->bind_param('s', $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Invalid token or user not found"]);
    exit;
}

$user = $result->fetch_assoc();
$firstName = $user['sFname'];
$lastName = $user['sLname'];
$email = $user['sEmail'];
$phone = $user['sPhone'];

// Generate a unique reference for the API request
$reference = 'REF' . time() . mt_rand(1000, 9999);
$webhookUrl = 'https://www.mamustopup.com.ng/webhook/aspfiy/index.php';

// API Payload
$data = [
    'reference' => $reference,
    'firstName' => $firstName,
    'lastName' => $lastName,
    'email' => $email,
    'phone' => $phone,
    'webhookUrl' => $webhookUrl
];

// Initialize cURL request
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://api-v1.aspfiy.com/reserve-paga/',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer Aspfiy-cb22151399f428dbce54b31ccd64cd5a",
        "Content-Type: application/json",
        "accept: application/json"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

// Handle cURL errors
if ($err) {
    echo json_encode(["success" => false, "message" => "cURL error occurred", "error" => $err]);
    exit;
}

// Decode API response
$result = json_decode($response, true);

// Check if JSON response is valid
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON response from ASPFIY API",
        "json_error" => json_last_error_msg(),
        "raw_response" => $response
    ]);
    exit;
}

// Extract account number
$accountNumber = $result['data']['account']['account_number'] ?? null;

// Handle API response
if ($httpCode !== 200 || !$accountNumber) {
    echo json_encode(["success" => false, "message" => $result['message'] ?? "Account creation failed"]);
    exit;
}

// Update the database with the new virtual account number
$updateStmt = $conn->prepare("UPDATE subscribers SET sAsfiyBank = ? WHERE token = ?");
if (!$updateStmt) {
    echo json_encode(["success" => false, "message" => "Database error", "error" => $conn->error]);
    exit;
}

$updateStmt->bind_param('ss', $accountNumber, $token);
if ($updateStmt->execute()) {
    echo json_encode(["success" => true, "message" => "Virtual account created successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to save account details"]);
}

// Close database connections
$stmt->close();
$updateStmt->close();
$conn->close();
?>