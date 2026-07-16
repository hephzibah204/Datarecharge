<?php
require_once 'conn.php';

function createVirtualAccount($token) {
    global $conn;

    // Enable error reporting for debugging
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Retrieve user details using token
    $stmt = $conn->prepare("SELECT sEmail, sPhone, sFname, sLname, sPalmpayBank, s9PSBBank, sSafehavenBank FROM subscribers WHERE token = ?");
    if (!$stmt) {
        return ["success" => false, "message" => "Database error: " . $conn->error];
    }
    
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return ["success" => false, "message" => "User not found or incorrect token"];
    }

    $user = $result->fetch_assoc();
    $email = $user['sEmail'];
    $phone = $user['sPhone'];
    $firstName = $user['sFname'];
    $lastName = $user['sLname'];

    // Check for the first missing virtual account in the given order
    if (empty($user['sPalmpayBank'])) {
        $column = "sPalmpayBank";
        $bank = "PALMPAY";
    } elseif (empty($user['s9PSBBank'])) {
        $column = "s9PSBBank";
        $bank = "9PSB";
    } elseif (empty($user['sSafehavenBank'])) {
        $column = "sSafehavenBank";
        $bank = "SAFEHAVEN";
    } else {
        return ["success" => false, "message" => "You already have a virtual account"];
    }

    // Retrieve the secret key from the database
    $keyStmt = $conn->prepare("SELECT value FROM apiconfigs WHERE aId = 173");
    if (!$keyStmt) {
        return ["success" => false, "message" => "Database error: " . $conn->error];
    }
    
    $keyStmt->execute();
    $keyResult = $keyStmt->get_result();

    if ($keyResult->num_rows === 0) {
        return ["success" => false, "message" => "API secret key not found"];
    }

    $apiConfig = $keyResult->fetch_assoc();
    $secretKey = $apiConfig['value'];

    // Check if the secret key is empty
    if (empty($secretKey)) {
        return ["success" => false, "message" => "API secret key is missing"];
    }

    // Set up the data for the API request
    $url = "https://api.billstack.co/v2/thirdparty/generateVirtualAccount/";
    $data = [
        "reference" => uniqid("ref_"),
        "email" => $email,
        "phone" => $phone,
        "firstName" => $firstName,
        "lastName" => $lastName,
        "bank" => $bank
    ];
    $headers = [
        "Content-Type: application/json",
        "Authorization: Bearer $secretKey"
    ];

    // Send the API request
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    
    // Check for cURL errors
    if (curl_errno($ch)) {
        return ["success" => false, "message" => "cURL error: " . curl_error($ch)];
    }

    curl_close($ch);

    $responseData = json_decode($response, true);

    // Log the API response for debugging
    file_put_contents("debug_log.txt", date("Y-m-d H:i:s") . " - API Response: " . $response . "\n", FILE_APPEND);

    // Check if the account creation was successful
    if (isset($responseData['status']) && $responseData['status'] && isset($responseData['data']['account'][0])) {
        $account = $responseData['data']['account'][0];
        $accountNumber = $account['account_number'];

        // Update subscribers table with the new account number
        $updateStmt = $conn->prepare("UPDATE subscribers SET $column = ? WHERE token = ?");
        if (!$updateStmt) {
            return ["success" => false, "message" => "Database error: " . $conn->error];
        }

        $updateStmt->bind_param("ss", $accountNumber, $token);
        
        if ($updateStmt->execute()) {
            return ["success" => true, "message" => "$bank virtual account created successfully"];
        } else {
            return ["success" => false, "message" => "Failed to save account details"];
        }
    } else {
        return ["success" => false, "message" => "Account creation failed: " . json_encode($responseData)];
    }
}

// Usage example
$token = trim($_POST['token']); // Token passed from the app
$response = createVirtualAccount($token);

// Send only success status and message back to the app
echo json_encode(["success" => $response['success'], "message" => $response['message']]);
?>