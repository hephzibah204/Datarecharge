<?php
header('Content-Type: application/json');

// Include the database connection
require_once 'conn.php';

// Retrieve the POST data
$token = $_POST['token'] ?? '';
$quantity = $_POST['quantity'] ?? '';
$exam = $_POST['exam'] ?? '';
$amount = $_POST['amount'] ?? '';

// Validate required fields
if (empty($token) || empty($quantity) || empty($exam) || empty($amount)) {
    echo json_encode(["success" => false, "message" => "Required fields are missing"]);
    exit();
}

// Fetch user details using token
$fetch_user_query = "SELECT sId, sWallet, sFname FROM subscribers WHERE token = ?";
$stmt = $conn->prepare($fetch_user_query);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Invalid token"]);
    exit();
}

$user = $result->fetch_assoc();
$sId = $user['sId'];
$current_balance = $user['sWallet'];
$username = $user['sFname'];

// Check if balance is sufficient
if ($current_balance < $amount) {
    echo json_encode(["success" => false, "message" => "Insufficient balance"]);
    exit();
}

// Fetch API details for exam pins
$api_query = "SELECT apikey, apilink FROM api2 WHERE value = 'exam-pin'";
$api_result = $conn->query($api_query);
if ($api_result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Exam pin API configuration not found"]);
    exit();
}

$api = $api_result->fetch_assoc();
$api_key = $api['apikey'];
$api_link = $api['apilink'];

// Prepare API payload
$payload = [
    "exam" => $exam,
    "quantity" => $quantity,
    "amount" => $amount,
    "token" => $api_key
];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_link);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute cURL request
$response = curl_exec($ch);
curl_close($ch);

// Decode the API response
$response_data = json_decode($response, true);

if (isset($response_data['success']) && $response_data['success'] === true) {
    // Deduct amount from user's wallet
    $new_balance = $current_balance - $amount;
    $update_balance_query = "UPDATE subscribers SET sWallet = ? WHERE sId = ?";
    $stmt = $conn->prepare($update_balance_query);
    $stmt->bind_param("di", $new_balance, $sId);
    $stmt->execute();

    // Save successful transaction
    $transaction_query = "INSERT INTO transactions (sId, transref, servicename, servicedesc, amount, status, oldbal, newbal, date) 
                           VALUES (?, ?, 'Exam Pin', ?, ?, 0, ?, ?, NOW())";
    $trans_ref = uniqid("TRANS_");
    $description = "Purchase of $quantity $exam pin(s)";
    $stmt = $conn->prepare($transaction_query);
    $stmt->bind_param("issdidd", $sId, $trans_ref, $description, $amount, $current_balance, $new_balance);
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "message" => "Transaction successful",
        "transaction_ref" => $trans_ref,
        "new_balance" => $new_balance,
        "exam_details" => $response_data['data'] ?? []
    ]);
} else {
    // Save failed transaction
    $transaction_query = "INSERT INTO transactions (sId, transref, servicename, servicedesc, amount, status, oldbal, newbal, date) 
                           VALUES (?, ?, 'Exam Pin', ?, ?, 1, ?, ?, NOW())";
    $trans_ref = uniqid("TRANS_");
    $description = "Failed purchase of $quantity $exam pin(s)";
    $stmt = $conn->prepare($transaction_query);
    $stmt->bind_param("issdidd", $sId, $trans_ref, $description, $amount, $current_balance, $current_balance);
    $stmt->execute();

    echo json_encode([
        "success" => false,
        "message" => $response_data['message'] ?? "Transaction failed",
        "transaction_ref" => $trans_ref
    ]);
}

// Close the connection
$stmt->close();
$conn->close();
?>