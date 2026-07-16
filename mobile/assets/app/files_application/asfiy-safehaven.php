<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'conn.php';

if (!$conn) {
    die(json_encode(["success" => false, "message" => "Database connection failed", "error" => mysqli_connect_error()]));
}

// Define API URL and Token
define('ASPFY_API_URL', 'https://api-v1.aspfiy.com/reserve-account/');
define('ASPFY_API_TOKEN', 'Aspfiy-cb22151399f428dbce54b31ccd64cd5a'); // Replace with actual token

// Ensure POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(["success" => false, "message" => "Invalid request method. Use POST."]));
}

// Retrieve token from request
$token = $_POST['token'] ?? '';

if (empty($token)) {
    die(json_encode(["success" => false, "message" => "Token is required."]));
}

// Fetch user details
$stmt = $conn->prepare("SELECT sFname, sLname, sEmail, sPhone FROM subscribers WHERE token = ?");
$stmt->bind_param('s', $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die(json_encode(["success" => false, "message" => "Invalid token or user not found."]));
}

$user = $result->fetch_assoc();
$firstName = $user['sFname'];
$lastName = $user['sLname'];
$email = $user['sEmail'];
$phone = $user['sPhone'];

// Generate unique reference
$reference = 'REF' . time() . mt_rand(1000, 9999);
$webhookUrl = 'https://www.mamustopup.com.ng/webhook/aspfiy/index.php';

// Prepare API payload
$data = [
    'reference' => $reference,
    'firstName' => $firstName,
    'lastName' => $lastName,
    'email' => $email,
    'phone' => $phone,
    'webhookUrl' => $webhookUrl
];

// Log request payload
file_put_contents('debug.log', date('Y-m-d H:i:s') . " - Request Data: " . json_encode($data) . PHP_EOL, FILE_APPEND);

// Initialize cURL
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => ASPFY_API_URL,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . ASPFY_API_TOKEN,
        "Content-Type: application/json",
        "accept: application/json"
    ],
]);

// Execute API request
$response = curl_exec($curl);
$err = curl_error($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

// Log API response
file_put_contents('debug.log', date('Y-m-d H:i:s') . " - HTTP Code: $httpCode - Response: " . $response . PHP_EOL, FILE_APPEND);

if ($err) {
    curl_close($curl);
    die(json_encode(["success" => false, "message" => "cURL error occurred", "error" => $err]));
}

curl_close($curl);

// Decode API response
$result = json_decode($response, true);

// Check if response is valid JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    die(json_encode([
        "success" => false,
        "message" => "Invalid JSON response from ASPFIY API",
        "json_error" => json_last_error_msg(),
        "raw_response" => $response
    ]));
}

// Extract message and account number
$message = $result['message'] ?? 'No message provided';
$accountNumber = $result['data']['account']['account_number'] ?? null;

// Log response message
file_put_contents('debug.log', date('Y-m-d H:i:s') . " - API Message: " . $message . PHP_EOL, FILE_APPEND);

// Handle API errors
if ($httpCode !== 200 || !$accountNumber) {
    die(json_encode([
        "success" => false,
        "message" => $message,
        "httpCode" => $httpCode,
        "response" => $result
    ]));
}

// Save virtual account to database
$updateStmt = $conn->prepare("UPDATE subscribers SET s9PSBBank = ? WHERE token = ?");
$updateStmt->bind_param('ss', $accountNumber, $token);

if ($updateStmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Virtual account successfully created",
        "data" => ["account_number" => $accountNumber]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to save account details to the database"]);
}
?>