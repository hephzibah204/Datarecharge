<?php
header('Content-Type: application/json');

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once 'conn.php';

// Check database connection
if (!$conn) {
    die(json_encode(["success" => false, "message" => "Database connection failed", "error" => mysqli_connect_error()]));
}

// Define the ASPFY API URL and token
define('ASPFY_API_URL', 'https://api-v1.aspfiy.com/reserve-palmpay/');
define('ASPFY_API_TOKEN', 'Aspfiy-e8e6ec3c17ff5ff5815889095eb92de7'); // Replace with your actual API token

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(["success" => false, "message" => "Invalid request method. Use POST."]));
}

// Retrieve token sent from the app
$token = $_POST['token'] ?? '';

if (empty($token)) {
    die(json_encode(["success" => false, "message" => "Token is required."]));
}

// Fetch user details from the database
$stmt = $conn->prepare("SELECT sFname, sLname, sEmail, sPhone FROM subscribers WHERE token = ?");
$stmt->bind_param('s', $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die(json_encode(["success" => false, "message" => "Invalid token or user not found."]));
}

// Retrieve user details
$user = $result->fetch_assoc();
$firstName = $user['sFname'];
$lastName = $user['sLname'];
$email = $user['sEmail'];
$phone = $user['sPhone'];

// Generate unique reference for the user
$reference = uniqid('ref_');
$webhookUrl = 'https://www.ibstardataspark.com.ng/webhook/aspfiy/index.php'; // Replace with your webhook handler URL

// Prepare request payload for the ASPFY API
$data = [
    'reference' => $reference,
    'firstName' => $firstName,
    'lastName' => $lastName,
    'email' => $email,
    'phone' => $phone,
    'webhookUrl' => $webhookUrl
];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, ASPFY_API_URL);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . ASPFY_API_TOKEN,
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout after 30 seconds
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for debugging

// Execute the API request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Log the response for debugging
file_put_contents('debug.log', date('Y-m-d H:i:s') . " - HTTP Code: $httpCode - Response: " . $response . PHP_EOL, FILE_APPEND);

// Check for cURL errors
if (curl_errno($ch)) {
    $curlError = curl_error($ch);
    curl_close($ch);
    die(json_encode([
        "success" => false,
        "message" => "cURL error occurred",
        "error" => $curlError
    ]));
}

curl_close($ch);

// Ensure API response is valid JSON
$result = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die(json_encode([
        "success" => false,
        "message" => "Invalid JSON response from ASPFY API",
        "json_error" => json_last_error_msg(),
        "response" => $response
    ]));
}

// Check if account details are included in the API response
if ($httpCode === 200 && isset($result['data']['account']['account_number'])) {
    $accountNumber = $result['data']['account']['account_number'];

    // Save the virtual account details to the database
    $updateStmt = $conn->prepare("UPDATE subscribers SET sPaga = ? WHERE token = ?");
    $updateStmt->bind_param('ss', $accountNumber, $token);

    if ($updateStmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Virtual account created successfully",
            "data" => ["account_number" => $accountNumber]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to save account details to the database"]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to retrieve account details from ASPFY",
        "httpCode" => $httpCode,
        "response" => $response
    ]);
}
?>