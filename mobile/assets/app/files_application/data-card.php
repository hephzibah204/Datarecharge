<?php
// Include the database connection
require_once 'conn.php';

// Get token and data card purchase details from your app
$token = $_POST['token'];  
$network = $_POST['network'];  
$plan_type = $_POST['plan_type'];  
$quantity = $_POST['quantity'];
$card_name = $_POST['card_name'];
$amount = $_POST['amount'];
$service = "Data Card Purchase"; // Specify the service type
$type = "debit"; // Specify transaction type

// Generate a random transaction ID
$transid = uniqid("trans_", true);

// Step 1: Get API details from the database
$api_query = "SELECT apikey, apilink FROM api2 WHERE value = 'data_card' LIMIT 1";
$api_result = $conn->query($api_query);
$api_data = $api_result->fetch_assoc();
$apilink = $api_data['apilink'];
$apikey = $api_data['apikey'];

// Step 2: Check user balance and retrieve user details
$user_query = "SELECT sWallet, sId FROM subscribers WHERE token = '$token'";
$user_result = $conn->query($user_query);

if ($user_result->num_rows > 0) {
    $user_data = $user_result->fetch_assoc();
    $sWallet = $user_data['sWallet'];
    $sId = $user_data['sId'];

    // Store the old balance for logging
    $old_balance = $sWallet;

    // Step 3: Verify balance is sufficient
    if ($sWallet >= $amount) {
        
        // Prepare API request payload
        $payload = array(
            'network' => $network,
            'plan_type' => $plan_type,
            'quantity' => $quantity,
            'card_name' => $card_name
        );

        $options = array(
            'http' => array(
                'header'  => "Content-Type: application/json\r\n" .
                             "Authorization: Token $apikey\r\n",
                'method'  => 'POST',
                'content' => json_encode($payload)
            )
        );
        $context  = stream_context_create($options);
        
        // Execute the API request with error handling
        $result = @file_get_contents($apilink, false, $context);
        if ($result === FALSE) {
            echo json_encode(array("success" => false, "message" => "Failed to connect to the API."));
            exit();
        }

        // Decode the API response
        $response = json_decode($result, true);

        // Check if the transaction was successful
        if (isset($response['status']) && $response['status'] === "success") {
            // Deduct balance and update user balance in database
            $new_balance = $sWallet - $amount;
            $update_balance_query = "UPDATE subscribers SET sWallet = '$new_balance' WHERE token = '$token'";
            if ($conn->query($update_balance_query) === TRUE) {
                
                // Insert transaction record with status "success"
                $status = 0; // Use "1" for successful transactions
                $timestamp = date("Y-m-d H:i:s");
                $transaction_query = "INSERT INTO transactions (sId, transref, servicename, servicedesc, amount, status, oldbal, newbal, profit, date) 
                                      VALUES ('$sId', '$transid', '$service', '$plan_type', '$amount', '$status', '$old_balance', '$new_balance', 0, '$timestamp')";
                $conn->query($transaction_query);

                // Return success response with API response details
                echo json_encode(array("success" => true, "message" => "Data card purchase successful.", "details" => $response));
            } else {
                echo json_encode(array("success" => false, "message" => "Failed to update balance."));
            }
        } else {
            // Transaction failed, log only if there's a failure other than insufficient balance or unavailability
            $status = 1; // Use "0" for failed transactions
            $timestamp = date("Y-m-d H:i:s");
            $transaction_query = "INSERT INTO transactions (sId, transref, servicename, servicedesc, amount, status, oldbal, newbal, profit, date) 
                                  VALUES ('$sId', '$transid', '$service', '$plan_type', '$amount', '$status', '$old_balance', '$old_balance', 0, '$timestamp')";
            $conn->query($transaction_query);

            // Return failure message
            echo json_encode(array("success" => false, "message" => $response['message'] ?? "Service temporarily unavailable."));
        }
    } else {
        // Return insufficient balance message without logging
        echo json_encode(array("success" => false, "message" => "Insufficient balance."));
    }
} else {
    echo json_encode(array("success" => false, "message" => "User not found."));
}

// Close connection
$conn->close();
?>