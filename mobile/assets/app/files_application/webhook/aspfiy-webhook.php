<?php
require_once 'conn.php'; // Include database connection

// Capture the incoming payload
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log the raw webhook payload for debugging purposes (optional)
file_put_contents('webhook_log.txt', $input . PHP_EOL, FILE_APPEND);

// Validate payload and process it
if (isset($data['event']) && $data['event'] === 'PAYMENT_NOTIFICATION') {
    // Extract relevant details
    $transactionType = $data['type'] ?? '';
    $reference = $data['reference'] ?? '';
    $amount = $data['amount'] ?? 0;
    $accountNumber = $data['account']['account_number'] ?? '';

    // Ensure the payload has the required data
    if ($transactionType === 'RESERVED_ACCOUNT_TRANSACTION' && !empty($accountNumber) && $amount > 0) {
        // Check if the transaction has already been processed
        $checkStmt = $conn->prepare("SELECT * FROM transactions WHERE transref = ?");
        $checkStmt->bind_param("s", $reference);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows === 0) {
            // Fetch the user associated with the account number
            $userStmt = $conn->prepare("SELECT sId, sWallet FROM subscribers WHERE sAspfiyPalmpayBank = ?");
            $userStmt->bind_param("s", $accountNumber);
            $userStmt->execute();
            $userResult = $userStmt->get_result();

            if ($userResult->num_rows > 0) {
                $user = $userResult->fetch_assoc();
                $sId = $user['sId'];
                $currentWallet = $user['sWallet'];
                $newWallet = $currentWallet + $amount;

                // Update user's wallet balance
                $updateStmt = $conn->prepare("UPDATE subscribers SET sWallet = ? WHERE sId = ?");
                $updateStmt->bind_param("ds", $newWallet, $sId);
                $updateStmt->execute();

                // Log the transaction
                $insertStmt = $conn->prepare("INSERT INTO transactions (sId, transref, servicename, servicedesc, amount, status, oldbal, newbal, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                $serviceName = "Wallet Funding";
                $serviceDesc = "Funded via Aspfiy Virtual Account";
                $txnStatus = 0; // Success
                $insertStmt->bind_param("ssssddds", $sId, $reference, $serviceName, $serviceDesc, $amount, $txnStatus, $currentWallet, $newWallet);
                $insertStmt->execute();

                // Respond to Aspfiy with success
                echo json_encode(["success" => true, "message" => "Wallet credited successfully"]);
            } else {
                // User not found
                echo json_encode(["success" => false, "message" => "User with this account number not found"]);
            }
        } else {
            // Duplicate transaction
            echo json_encode(["success" => false, "message" => "Transaction already processed"]);
        }
    } else {
        // Invalid or incomplete payload
        echo json_encode(["success" => false, "message" => "Invalid transaction details"]);
    }
} else {
    // Invalid event type
    echo json_encode(["success" => false, "message" => "Invalid event type"]);
}
?>