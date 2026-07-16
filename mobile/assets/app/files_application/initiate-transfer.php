<?php
require_once 'conn.php'; // Include database connection
require_once 'paystack_config.php'; // Include Paystack configuration

function initiateTransfer($token, $amount, $recipientCode) {
    global $conn;

    // Validate inputs
    if (empty($token) || empty($amount) || empty($recipientCode)) {
        return ["success" => false, "message" => "All fields are required"];
    }

    // Fetch user wallet balance
    $stmt = $conn->prepare("SELECT sWallet FROM subscribers WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return ["success" => false, "message" => "User not found"];
    }

    $user = $result->fetch_assoc();
    $oldBalance = $user['sWallet'];

    // Ensure sufficient balance (including ₦10 charge)
    $totalDeduction = $amount + 10; // Transfer amount + ₦10 charge
    if ($oldBalance < $totalDeduction) {
        return ["success" => false, "message" => "Insufficient balance"];
    }

    // Deduct from wallet
    $newBalance = $oldBalance - $totalDeduction;
    $updateStmt = $conn->prepare("UPDATE subscribers SET sWallet = ? WHERE token = ?");
    $updateStmt->bind_param("ds", $newBalance, $token);
    $updateStmt->execute();

    // Initiate transfer with Paystack
    $url = "https://api.paystack.co/transfer";
    $data = [
        "source" => "balance",
        "amount" => $amount * 100, // Convert to kobo
        "recipient" => $recipientCode,
        "reason" => "Wallet withdrawal" // Hardcoded reason
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, getHeaders());

    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);

    if (isset($responseData['status']) && $responseData['status'] === true) {
        // Save transaction in the database
        $transactionRef = $responseData['data']['transfer_code'];
        $stmt = $conn->prepare("INSERT INTO transactions (sId, transref, servicename, servicedesc, amount, status, oldbal, newbal, profit, date)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $serviceName = "Bank Transfer";
        $serviceDesc = "Wallet withdrawal"; // Hardcoded reason
        $status = "Success";
        $profit = 10; // ₦10 charge as profit
        $stmt->bind_param("sssssdidd", $token, $transactionRef, $serviceName, $serviceDesc, $amount, $status, $oldBalance, $newBalance, $profit);
        $stmt->execute();

        return ["success" => true, "message" => "Transfer successful", "transactionRef" => $transactionRef];
    } else {
        // Revert wallet deduction on failure
        $revertStmt = $conn->prepare("UPDATE subscribers SET sWallet = ? WHERE token = ?");
        $revertStmt->bind_param("ds", $oldBalance, $token);
        $revertStmt->execute();

        return [
            "success" => false,
            "message" => $responseData['message'] ?? "Transfer failed"
        ];
    }
}

// Example Usage
$token = $_POST['token']; // User's token
$amount = $_POST['amount']; // Amount in Naira
$recipientCode = $_POST['recipient_code']; // Recipient code from the app

$response = initiateTransfer($token, $amount, $recipientCode);

header('Content-Type: application/json');
echo json_encode($response);
?>