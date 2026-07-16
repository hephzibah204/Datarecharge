<?php
// Include database connection
require_once 'conn.php';

// Get data from the request
$token = $_POST['token'];
$disco = $_POST['disco']; // Distribution company ID
$meter_type = $_POST['meter_type']; // 'prepaid' or 'postpaid'
$meter_number = $_POST['meter_number'];
$amount = $_POST['amount'];
$request_id = "Bill_" . uniqid(); // Unique transaction ID

// Validate required fields
if (empty($token) || empty($disco) || empty($meter_type) || empty($meter_number) || empty($amount)) {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

// Check if the token is valid
$user_query = $conn->prepare("SELECT sId, sWallet FROM subscribers WHERE token = ?");
$user_query->bind_param("s", $token);
$user_query->execute();
$user_result = $user_query->get_result();

if ($user_result->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "Invalid token."]);
    exit;
}

$user = $user_result->fetch_assoc();
$sId = $user['sId'];
$sWallet = $user['sWallet'];

// Check if the user has sufficient balance
if ($sWallet < $amount) {
    echo json_encode(["success" => false, "message" => "Insufficient balance."]);
    exit;
}

// Fetch the API details from the database
$api_query = $conn->prepare("SELECT apikey, apilink FROM api2 WHERE value = 'electricity'");
$api_query->execute();
$api_result = $api_query->get_result();

if ($api_result->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "API details not found."]);
    exit;
}

$api = $api_result->fetch_assoc();
$api_url = $api['apilink'];
$api_key = $api['apikey'];

// Prepare the payload for the API request
$payload = [
    'disco' => $disco,
    'meter_type' => $meter_type,
    'meter_number' => $meter_number,
    'amount' => $amount,
    'bypass' => false,
    'request-id' => $request_id,
];

// Make the API request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$headers = [
    "Authorization: Token $api_key",
    "Content-Type: application/json",
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
curl_close($ch);

// Decode the API response
$response_data = json_decode($response, true);

// Handle API response
if ($response_data['status'] === "success") {
    // Transaction successful, update user's wallet
    $new_balance = $sWallet - $amount;
    $update_wallet = $conn->prepare("UPDATE subscribers SET sWallet = ? WHERE sId = ?");
    $update_wallet->bind_param("di", $new_balance, $sId);
    $update_wallet->execute();

    // Save the transaction
    $save_transaction = $conn->prepare("
        INSERT INTO transactions (sId, transref, servicename, servicedesc, amount, status, oldbal, newbal, profit, date) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $service_name = "Electricity Bill Payment";
    $service_desc = "Payment for meter number $meter_number";
    $status = 0; // Success
    $profit = 0; // Modify as necessary
    $save_transaction->bind_param("ssssiiiii", $sId, $request_id, $service_name, $service_desc, $amount, $status, $sWallet, $new_balance, $profit);
    $save_transaction->execute();

    echo json_encode(["success" => true, "message" => "Transaction successful.", "data" => $response_data]);
} else {
    // Transaction failed, save as failed transaction
    $save_transaction = $conn->prepare("
        INSERT INTO transactions (sId, transref, servicename, servicedesc, amount, status, oldbal, newbal, profit, date) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $service_name = "Electricity Bill Payment";
    $service_desc = "Payment for meter number $meter_number";
    $status = 1; // Failed
    $new_balance = $sWallet; // No deduction on failure
    $profit = 0; // Modify as necessary
    $save_transaction->bind_param("ssssiiiii", $sId, $request_id, $service_name, $service_desc, $amount, $status, $sWallet, $new_balance, $profit);
    $save_transaction->execute();

    echo json_encode(["success" => false, "message" => "Transaction failed.", "data" => $response_data]);
}
?>