<?php
require_once 'conn.php';

function createVirtualAccount($token) {
    global $conn;

    // Retrieve user details using token
    $stmt = $conn->prepare("SELECT sEmail, sPhone, sFname, sLname FROM subscribers WHERE token = ?");
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

    // Set up the data for the API request
    $url = "https://api.billstack.co/v2/thirdparty/generateVirtualAccount/";
    $data = [
        "reference" => uniqid("ref_"),
        "email" => $email,
        "phone" => $phone,
        "firstName" => $firstName,
        "lastName" => $lastName,
        "bank" => "BANKLY" // Replace with desired bank
    ];
    $headers = [
        "Content-Type: application/json",
        "Authorization: Bearer Bill_Stack-SEC-KEY-7370d1c24f3655ab6e841adc41b31bd6" // Ensure this is correct
    ];

    // Send the API request
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);

    // Check if the account creation was successful
    if (isset($responseData['status']) && $responseData['status'] && isset($responseData['data']['account'][0])) {
        $account = $responseData['data']['account'][0];
        $accountNumber = $account['account_number'];

        // Update subscribers table with only the account number
        $updateStmt = $conn->prepare("UPDATE subscribers SET sBanklyBank = ? WHERE token = ?");
        $updateStmt->bind_param("ss", $accountNumber, $token);
        
        if ($updateStmt->execute()) {
            return ["success" => true, "message" => "Virtual account created successfully"];
        } else {
            return ["success" => false, "message" => "Failed to save account details"];
        }
    } else {
        return ["success" => false, "message" => "Account creation failed"];
    }
}

// Usage example
$token = trim($_POST['token']); // Token passed from the app
$response = createVirtualAccount($token);

// Send only success status and message back to the app
echo json_encode(["success" => $response['success'], "message" => $response['message']]);
?>