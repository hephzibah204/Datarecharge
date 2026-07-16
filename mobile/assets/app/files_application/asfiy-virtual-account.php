<?php
header('Content-Type: application/json');

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once 'conn.php';

// Function to log debug information
function log_debug($message) {
    file_put_contents('asfiy_debug.log', date('Y-m-d H:i:s') . " - " . $message . PHP_EOL, FILE_APPEND);
}

// Check database connection
if (!$conn) {
    log_debug("Database connection failed: " . mysqli_connect_error());
    die(json_encode(["success" => false, "message" => "Database connection failed"]));
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    log_debug("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    die(json_encode(["success" => false, "message" => "Invalid request method. Use POST."]));
}

// Retrieve token from the request
$token = $_POST['token'] ?? '';

if (empty($token)) {
    log_debug("Token is missing in the request.");
    die(json_encode(["success" => false, "message" => "Token is required."]));
}

// Fetch ASPFY API token from the database
$apiStmt = $conn->prepare("SELECT value FROM apiconfigs WHERE aId = 158");
$apiStmt->execute();
$apiResult = $apiStmt->get_result();

if ($apiResult->num_rows === 0) {
    log_debug("ASPFY API token not found in database.");
    die(json_encode(["success" => false, "message" => "API token not found in database."]));
}

$apiData = $apiResult->fetch_assoc();
$apiToken = $apiData['value'];

// Fetch user details
$stmt = $conn->prepare("SELECT sFname, sLname, sEmail, sPhone, sPaga, sAsfiyBank, sSafehavenBank FROM subscribers WHERE token = ?");
$stmt->bind_param('s', $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    log_debug("Invalid token or user not found.");
    die(json_encode(["success" => false, "message" => "Invalid token or user not found."]));
}

$user = $result->fetch_assoc();
$firstName = $user['sFname'];
$lastName = $user['sLname'];
$email = $user['sEmail'];
$phone = $user['sPhone'];
$palmpayBank = $user['sPaga'];
$pagaBank = $user['sAsfiyBank'];
$safehavenBank = $user['sSafehavenBank'];

// Determine which virtual account to create
$accountField = null;
$apiUrl = null;

if (empty($palmpayBank)) {
    $accountField = 'sPaga';
    $apiUrl = "https://api-v1.aspfiy.com/reserve-palmpay/";
} elseif (empty($pagaBank)) {
    $accountField = 'sAsfiyBank';
    $apiUrl = "https://api-v1.aspfiy.com/reserve-paga/";
} elseif (empty($safehavenBank)) {
    $accountField = 'sSafehavenBank';
    $apiUrl = "https://api-v1.aspfiy.com/reserve-account/";
} else {
    log_debug("All virtual accounts already exist for token: $token");
    die(json_encode(["success" => false, "message" => "All virtual accounts already exist."]));
}

// Generate unique reference
$reference = uniqid('ref_');
$webhookUrl = 'https://alihisandata.com.ng/webhook/aspfiy/index.php';

// Prepare API request data
$requestData = [
    'reference' => $reference,
    'firstName' => $firstName,
    'lastName' => $lastName,
    'email' => $email,
    'phone' => $phone,
    'webhookUrl' => $webhookUrl
];

log_debug("Requesting virtual account for token: $token, API URL: $apiUrl, Payload: " . json_encode($requestData));

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout after 30 seconds
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for debugging

// Execute API request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

log_debug("API Response: HTTP Code: $httpCode, Response: " . $response);

if ($curlError) {
    log_debug("cURL Error: $curlError");
    curl_close($ch);
    die(json_encode(["success" => false, "message" => "cURL error occurred", "error" => $curlError]));
}

curl_close($ch);

// Validate JSON response
$result = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    log_debug("Invalid JSON response: " . json_last_error_msg());
    die(json_encode([
        "success" => false,
        "message" => "Invalid JSON response from ASPFY API",
        "json_error" => json_last_error_msg(),
        "response" => $response
    ]));
}

// Process response and update the database
if ($httpCode === 200 && isset($result['data']['account']['account_number'])) {
    $accountNumber = $result['data']['account']['account_number'];

    // Save the virtual account details in the database
    $updateStmt = $conn->prepare("UPDATE subscribers SET $accountField = ? WHERE token = ?");
    $updateStmt->bind_param('ss', $accountNumber, $token);

    if ($updateStmt->execute()) {
        log_debug("Successfully created virtual account: $accountNumber for token: $token");
        echo json_encode([
            "success" => true,
            "message" => "Virtual account created successfully",
            "data" => ["account_number" => $accountNumber]
        ]);
    } else {
        log_debug("Database update failed for token: $token, Error: " . $conn->error);
        echo json_encode(["success" => false, "message" => "Failed to save account details to the database"]);
    }
} else {
    log_debug("Failed to retrieve account details from ASPFY API for token: $token");
    echo json_encode([
        "success" => false,
        "message" => "Failed to retrieve account details from ASPFY",
        "httpCode" => $httpCode,
        "response" => $response
    ]);
}

// Close database connection
$conn->close();
?>