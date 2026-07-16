<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/application/conn.php'; // Include your database connection file

// Capture the incoming JSON payload
$input = file_get_contents('php://input');
file_put_contents('webhook.log', $input . PHP_EOL, FILE_APPEND); // Log webhook data
$webhookData = json_decode($input, true);

// Check if the event is "PAYMENT_NOTIFICATION"
if (isset($webhookData['event']) && $webhookData['event'] === "PAYMENT_NOTIFICATION") {
    $type = $webhookData['data']['type'] ?? '';
    $amount = floatval($webhookData['data']['amount'] ?? 0);
    $accountNumber = $webhookData['data']['account']['account_number'] ?? '';
    $transactionReference = $webhookData['data']['reference'] ?? '';

    // Validate transaction type and account number
    if ($type === "RESERVED_ACCOUNT_TRANSACTION" && !empty($accountNumber)) {
        // Fetch user's wallet based on the account number
        $stmt = $conn->prepare("SELECT sId, sWallet, sFname, sLname FROM subscribers WHERE sPalmpayBank = ?");
        $stmt->bind_param("s", $accountNumber);

        if (!$stmt->execute()) {
            error_log("Error in query: " . $stmt->error);
            echo json_encode(["success" => false, "message" => "Database query failed"]);
            exit;
        }

        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $currentBalance = floatval($user['sWallet']);
            $firstName = $user['sFname'];
            $lastName = $user['sLname'];
            $userName = $firstName . " " . $lastName;
            $sId = $user['sId'];

            // Prevent duplicate transactions
            $checkStmt = $conn->prepare("SELECT transref FROM transactions WHERE transref = ?");
            $checkStmt->bind_param("s", $transactionReference);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) {
                echo json_encode(["success" => false, "message" => "Duplicate transaction detected"]);
                exit;
            }

            $charge = $amount * 0.02;
            $netAmount = $amount - $charge;
            $newBalance = $currentBalance + $netAmount;

            // Update user's wallet
            $updateStmt = $conn->prepare("UPDATE subscribers SET sWallet = ? WHERE sPalmpayBank = ?");
            $updateStmt->bind_param("ds", $newBalance, $accountNumber);

            if ($updateStmt->execute()) {
                $status = 0; // Success status
                $transactionType = "Wallet Funding";
                $description = "Wallet funded via Palmpay Account";

                // Insert into transactions
                $insertStmt = $conn->prepare("INSERT INTO transactions (sId, transref, servicename, servicedesc, amount, status, oldbal, newbal, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $insertStmt->bind_param("isssddds", $sId, $transactionReference, $transactionType, $description, $netAmount, $status, $currentBalance, $newBalance);
                $insertStmt->execute();

                echo json_encode(["success" => true, "message" => "Wallet funded successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to update balance"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Account not found"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid transaction type"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid event"]);
}
?>