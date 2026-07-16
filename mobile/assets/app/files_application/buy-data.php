<?php
require_once 'conn.php';

// Logger function
function logToFile($message) {
    $logFile = "buy_data.log";
    $timestamp = date("Y-m-d H:i:s");
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Get request data from POST
$token = $_POST['token'] ?? '';
$mobile_number = $_POST['phone'] ?? '';
$network_input = $_POST['network'] ?? '';
$plan_id = $_POST['data_plan'] ?? '';
$ported_number = $_POST['ported_number'] ?? 'false';
$request_id = $_POST['request-id'] ?? '';
$service = "Data From App";

// Validate inputs
if (!$token || !$mobile_number || !$network_input || !$plan_id || !$request_id) {
    echo json_encode(["success" => false, "message" => "Missing required fields."]);
    exit;
}

// Map network ID (example)
$network_map = [
    '1' => '1',
    '2' => '4',
    '3' => '2',
    '4' => '3',
];
$network_id = $network_map[$network_input] ?? $network_input;
logToFile("Mapped Network: Original = $network_input → Final = $network_id");

// Get user
$user_stmt = $conn->prepare("SELECT sWallet, sId, sType FROM subscribers WHERE token = ?");
$user_stmt->bind_param("s", $token);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows === 0) {
    logToFile("Transaction Failed: User not found.");
    echo json_encode(["success" => false, "message" => "User not found."]);
    exit;
}

$user_data = $user_result->fetch_assoc();
$user_balance = $user_data['sWallet'];
$sId = $user_data['sId'];
$sType = $user_data['sType'];
$old_balance = $user_balance;

// Get plan from appdata
$plan_stmt = $conn->prepare("SELECT price, userprice, agentprice, vendorprice, api_id FROM appdata WHERE planid = ?");
$plan_stmt->bind_param("s", $plan_id);
$plan_stmt->execute();
$plan_result = $plan_stmt->get_result();

if ($plan_result->num_rows === 0) {
    logToFile("Transaction Failed: Plan ID $plan_id not found in appdata.");
    echo json_encode(["success" => false, "message" => "Data plan not found."]);
    exit;
}

$plan_data = $plan_result->fetch_assoc();
$api_price = $plan_data['price'];
$selling_price = ($sType == 1) ? $plan_data['userprice'] : (($sType == 2) ? $plan_data['agentprice'] : $plan_data['vendorprice']);
$profit = $selling_price - $api_price;
$plan_api_id = $plan_data['api_id'];  

logToFile("Plan: $plan_id | Price: $api_price | Selling: $selling_price | Profit: $profit");

// Check user balance
if ($user_balance < $selling_price) {
    logToFile("Insufficient balance: $user_balance < $selling_price");
    echo json_encode(["success" => false, "message" => "Insufficient balance."]);
    exit;
}

// Get API settings from api2 based on plan_api_id
$api_stmt = $conn->prepare("SELECT apikey, apilink FROM api2 WHERE id = ?");
$api_stmt->bind_param("i", $plan_api_id);
$api_stmt->execute();
$api_result = $api_stmt->get_result();

if ($api_result->num_rows === 0) {
    logToFile("API Config Missing for Plan $plan_id");
    echo json_encode(["success" => false, "message" => "API configuration not found for this plan."]);
    exit;
}

$api_data = $api_result->fetch_assoc();
$apikey = $api_data['apikey'];
$apilink = trim($api_data['apilink']);
logToFile("API Selected for Plan $plan_id: $apilink | Key: $apikey");

// ✅ Allowed APIs only
$allowed_apis = [
    "https://alrahuzdata.com.ng/api/data/",
    "https://vtunaija.com.ng/api/data/",
    "https://amzaet.com/api/data/",
    "https://smeplug.ng/api/v1/data/purchase"
];
if (!in_array($apilink, $allowed_apis)) {
    logToFile("Unauthorized API: $apilink");
    echo json_encode(["success" => false, "message" => "This API is not allowed."]);
    exit;
}

// ✅ Prepare API Payload based on provider
if ($apilink === "https://alrahuzdata.com.ng/api/data/") {
    $payload = [
        'network' => $network_id,
        'mobile_number' => $mobile_number,
        'plan' => $plan_id,
        'Ported_number' => filter_var($ported_number, FILTER_VALIDATE_BOOLEAN)
    ];
} elseif ($apilink === "https://vtunaija.com.ng/api/data/") {
    $payload = [
        'network' => $network_id,
        'mobile_number' => $mobile_number,
        'plan' => $plan_id,
        'Ported_number' => filter_var($ported_number, FILTER_VALIDATE_BOOLEAN)
    ];
} elseif ($apilink === "https://amzaet.com/api/data/") {
    $payload = [
        'network' => $network_id,
        'mobile_number' => $mobile_number,
        'plan' => $plan_id,
        'Ported_number' => filter_var($ported_number, FILTER_VALIDATE_BOOLEAN)
    ];
} elseif ($apilink === "https://smeplug.ng/api/v1/data/purchase") {
    // ✅ smeplug payload
    $payload = [
        "network_id" => (int)$network_id,
        "plan_id"    => (int)$plan_id,
        "phone"      => $mobile_number
    ];
} else {
    logToFile("Invalid API chosen: $apilink");
    echo json_encode(["success" => false, "message" => "Invalid API provider."]);
    exit;
}
logToFile("Payload: " . json_encode($payload));

// ✅ Update curl header for smeplug
$headers = ($apilink === "https://smeplug.ng/api/v1/data/purchase")
    ? ["Authorization: Bearer $apikey", "Content-Type: application/json"]
    : ["Authorization: Token $apikey", "Content-Type: application/json"];

// Save transaction as "Processing"
$description = "Data Purchase for $mobile_number";
$status = 2; // processing
$timestamp = date("Y-m-d H:i:s");

$trans_stmt = $conn->prepare("INSERT INTO transactions (sId, transref, servicename, servicedesc, amount, status, oldbal, newbal, profit, date) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$trans_stmt->bind_param("ssssdsddds", $sId, $request_id, $service, $description, $selling_price, $status, $old_balance, $old_balance, $profit, $timestamp);
$trans_stmt->execute();

// Send cURL request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apilink);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response_raw = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    logToFile("Curl Error: $error");
    $fail_status = 1;
    $stmt = $conn->prepare("UPDATE transactions SET status=? WHERE transref=?");
    $stmt->bind_param("is", $fail_status, $request_id);
    $stmt->execute();
    echo json_encode(["success" => false, "message" => "Failed to connect to API."]);
    exit;
}

$response = json_decode($response_raw, true);
logToFile("API Response: " . json_encode($response));

// ✅ Check success for all APIs
$is_success = false;

// Normal APIs
$api_status = strtolower($response['status'] ?? $response['Status'] ?? '');
if (in_array($api_status, ['successful', 'success'])) {
    $is_success = true;
}

// Smeplug format
if ($apilink === "https://smeplug.ng/api/v1/data/purchase") {
    if (($response['status'] ?? false) === true) {
        $is_success = true;
    }
}

if ($is_success) {
    $new_balance = $user_balance - $selling_price;

    // Update user balance
    $bal_stmt = $conn->prepare("UPDATE subscribers SET sWallet = ? WHERE token = ?");
    $bal_stmt->bind_param("ds", $new_balance, $token);
    $bal_stmt->execute();

    // Update transaction as success
    $success_status = 0;
    $stmt = $conn->prepare("UPDATE transactions SET status=?, newbal=? WHERE transref=?");
    $stmt->bind_param("ids", $success_status, $new_balance, $request_id);
    $stmt->execute();

    logToFile("Transaction Success: New Bal = $new_balance");

    echo json_encode([
        "success" => true,
        "message" => $response['data']['msg'] ?? $response['message'] ?? "Data purchase successful",
        "api_response" => $response
    ]);
    exit;
} else {
    logToFile("API Failed Response: " . json_encode($response));
    $fail_status = 1;
    $stmt = $conn->prepare("UPDATE transactions SET status=? WHERE transref=?");
    $stmt->bind_param("is", $fail_status, $request_id);
    $stmt->execute();

    echo json_encode([
        "success" => false,
        "message" => $response['message'] ?? "Transaction failed.",
        "api_response" => $response
    ]);
    exit;
}

$conn->close();
?>
