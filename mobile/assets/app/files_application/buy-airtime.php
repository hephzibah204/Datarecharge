<?php
// Include database connection
require_once 'conn.php';

// Log helper
function logToFile($message) {
    file_put_contents("airtime.log", date("[Y-m-d H:i:s]") . " $message\n", FILE_APPEND);
}

// Validate required fields
$required_params = ['token', 'amount', 'phone', 'network', 'Ported_number', 'airtime_type'];
foreach ($required_params as $param) {
    if (empty($_POST[$param])) {
        logToFile("ERROR: Missing parameter - $param");
        echo json_encode(["success" => false, "message" => "Missing parameter: $param"]);
        exit();
    }
}

// Assign POST variables
$token = $_POST['token'];
$amount = floatval($_POST['amount']);
$phone = $_POST['phone'];
$network = $_POST['network'];
$ported_number = $_POST['Ported_number'];
$airtime_type = $_POST['airtime_type'];
$service = "Airtime Purchase";

// ✅ Map network (if necessary)
$network_map = ['1' => '1', '2' => '4', '3' => '2', '4' => '3'];
$network_id = $network_map[$network] ?? $network;
logToFile("INFO: Network mapped → $network → $network_id");

// Minimum amount check
if ($amount < 50) {
    logToFile("ERROR: Amount too low - ₦$amount");
    echo json_encode(["success" => false, "message" => "Minimum airtime purchase amount is ₦50."]);
    exit();
}

try {
    // Step 1: Fetch subscriber info
    $stmt = $conn->prepare("SELECT sId, sWallet FROM subscribers WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) throw new Exception("Subscriber not found.");

    $sId = $user['sId'];
    $user_balance = floatval($user['sWallet']);

    if ($user_balance < $amount) throw new Exception("Insufficient wallet balance.");

    // Step 2: Get API configuration
    $stmt = $conn->prepare("SELECT apikey, apilink FROM api2 WHERE value = 'airtime' LIMIT 1");
    $stmt->execute();
    $api = $stmt->get_result()->fetch_assoc();

    if (!$api) throw new Exception("Airtime API not configured.");

    $api_key = $api['apikey'];
    $api_link = $api['apilink'];

    // Step 3: Generate transaction ref and prepare payload
    $transref = uniqid("trans_", true);
    $timestamp = date("Y-m-d H:i:s");
    $description = "Airtime Purchase for $phone";
    $status = 2; // Processing

    if (in_array($api_link, ["https://nabatulusub.com/api/topup/", "https://legitdataway.com/api/topup/", "https://n3tdata.com/api/topup/"])) {
        $payload = json_encode([
            'network' => $network_id,
            'phone' => $phone,
            'bypass' => false,
            'amount' => $amount,
            'plan_type' => $airtime_type,
            'request-id' => $transref
        ]);
    } else {
        $payload = json_encode([
            'network' => $network_id,
            'amount' => $amount,
            'mobile_number' => $phone,
            'Ported_number' => filter_var($ported_number, FILTER_VALIDATE_BOOLEAN),
            'airtime_type' => $airtime_type
        ]);
    }

    // Step 4: Log transaction as Processing
    $stmt = $conn->prepare("INSERT INTO transactions (sId, transref, servicename, servicedesc, amount, status, date, oldbal, newbal) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssdissd", $sId, $transref, $service, $description, $amount, $status, $timestamp, $user_balance, $user_balance);
    $stmt->execute();

    // Step 5: Send API request
    $ch = curl_init($api_link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Token $api_key",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    logToFile("API Request to $api_link | Payload: $payload | Response: $response");

    if ($curl_error) throw new Exception("cURL error: $curl_error");

    $res_data = json_decode($response, true);
    $api_status = strtolower(trim($res_data['status'] ?? $res_data['Status'] ?? ''));

    // ✅ Check for success status or success message
    if (in_array($api_status, ['success', 'successful', 'completed']) || (isset($res_data['message']) && stripos($res_data['message'], 'success') !== false)) {
        $new_balance = $user_balance - $amount;

        // Update wallet
        $stmt = $conn->prepare("UPDATE subscribers SET sWallet = ? WHERE sId = ?");
        $stmt->bind_param("ds", $new_balance, $sId);
        $stmt->execute();

        // Update transaction
        $status = 0; // Success
        $stmt = $conn->prepare("UPDATE transactions SET status = ?, newbal = ? WHERE transref = ?");
        $stmt->bind_param("ids", $status, $new_balance, $transref);
        $stmt->execute();

        logToFile("SUCCESS: $transref completed. New balance: $new_balance");
        echo json_encode(["success" => true, "message" => "Airtime purchase successful", "api_response" => $res_data]);
    } else {
        // Failed transaction
        $status = 1;
        $stmt = $conn->prepare("UPDATE transactions SET status = ? WHERE transref = ?");
        $stmt->bind_param("is", $status, $transref);
        $stmt->execute();

        logToFile("FAILED: API response status - $api_status | Response: $response");
        echo json_encode(["success" => false, "message" => "Airtime purchase failed", "api_response" => $res_data]);
    }
} catch (Exception $e) {
    logToFile("ERROR: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>