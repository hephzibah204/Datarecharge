<?php
header('Content-Type: application/json');
ob_clean(); // Ensure no extra output
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

require_once 'conn.php';

if (!$conn) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

$headers = apache_request_headers();
if (!isset($headers['Authorization'])) {
    echo json_encode(["success" => false, "message" => "Authorization header not provided"]);
    exit();
}

$auth_header = $headers['Authorization'];
if (strpos($auth_header, 'Bearer ') !== 0) {
    echo json_encode(["success" => false, "message" => "Invalid authorization format"]);
    exit();
}
$token = substr($auth_header, 7);

if (empty($token)) {
    echo json_encode(["success" => false, "message" => "Token is empty"]);
    exit();
}

// Fetch user details from subscribers table
$user_sql = "SELECT 
    sWallet, sPhone, sLname, sFname, 
    COALESCE(NULLIF(s9PSBBank, ''), NULL) AS s9PSBBank, 
    COALESCE(NULLIF(sAsfiyBank, ''), NULL) AS sAsfiyBank, 
    COALESCE(NULLIF(sSafehavenBank, ''), NULL) AS sSafehavenBank, 
    COALESCE(NULLIF(sPaga, ''), NULL) AS sPaga, 
    COALESCE(sEmail, '') AS sEmail, 
    sType, sKycStatus, sState, sBankName, sPin 
FROM subscribers 
WHERE token = ?";

$user_stmt = $conn->prepare($user_sql);
if (!$user_stmt) {
    echo json_encode(["success" => false, "message" => "Failed to prepare user query"]);
    exit();
}

$user_stmt->bind_param("s", $token);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows > 0) {
    $user = $user_result->fetch_assoc();
    
    // Fetch asfiyCharges from apiconfigs table where aId = 161
    $config_sql = "SELECT value FROM apiconfigs WHERE aId = 161";
    $config_stmt = $conn->prepare($config_sql);
    if (!$config_stmt) {
        echo json_encode(["success" => false, "message" => "Failed to prepare config query"]);
        exit();
    }
    $config_stmt->execute();
    $config_result = $config_stmt->get_result();
    
    $asfiyCharges = null;
    if ($config_result->num_rows > 0) {
        $config_row = $config_result->fetch_assoc();
        $asfiyCharges = $config_row['value'];
    }
    
    echo json_encode([
        "success" => true,
        "user" => [
            "balance" => $user['sWallet'],
            "phone" => $user['sPhone'],
            "lastname" => $user['sLname'],
            "firstname" => $user['sFname'],
            "paga" => $user['sAsfiyBank'],
            "email" => $user['sEmail'],
            "bankname" => $user['sBankName'] ?? null,
            "pin" => $user['sPin'] ?? null,
            "state" => $user['sState'] ?? null,
            "kyc" => $user['sKycStatus'],
            "type" => $user['sType'],
            "safehaven" => $user['sSafehavenBank'],
            "9psb" => $user['s9PSBBank'],
            "palmpay" => $user['sPaga'],
            "asfiyCharges" => $asfiyCharges
        ]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "No user found"]);
}

$user_stmt->close();
$config_stmt->close();
$conn->close();
?>