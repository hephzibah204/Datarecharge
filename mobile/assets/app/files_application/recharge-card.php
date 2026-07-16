<?php
// Include database connection
require_once 'conn.php';

// Get token and recharge card details from your app
$token = $_POST['token'];
$network = $_POST['network']; // Network ID (1 for MTN, etc.)
$network_amount = $_POST['network_amount']; // Recharge card network API ID
$quantity = $_POST['quantity'];
$card_name = $_POST['card_name']; 
$amount = $_POST['amount'];
$service = "Recharge Card Purchase";

// Generate a random transaction reference
$tRef = uniqid("trans_", true);

// Step 1: Get API details for the recharge card
$api_query = "SELECT apikey, apilink FROM api2 WHERE value = 'recharge_card' LIMIT 1";
$api_result = $conn->query($api_query);
$api_data = $api_result->fetch_assoc();
$api_url = $api_data['apilink'];
$api_key = $api_data['apikey'];

// Step 2: Check user balance and retrieve sId
$user_query = "SELECT sId, sWallet FROM subscribers WHERE token = '$token'";
$user_result = $conn->query($user_query);

if ($user_result->num_rows > 0) {
    $user_data = $user_result->fetch_assoc();
    $sId = $user_data['sId'];
    $user_balance = $user_data['sWallet'];

    // Store the old balance for logging
    $old_balance = $user_balance;

    // Step 3: Verify balance is sufficient
    if ($user_balance >= $amount) {
        
        // Prepare API request payload
        $payload = array(
            'network' => $network,
            'network_amount' => $network_amount,
            'quantity' => $quantity,
            'name_on_card' => $card_name
        );

        $options = array(
            'http' => array(
                'header'  => "Content-Type: application/json\r\n" .
                             "Authorization: Token $api_key\r\n",
                'method'  => 'POST',
                'content' => json_encode($payload)
            )
        );
        $context  = stream_context_create($options);
        
        // Execute the API request
        $result = @file_get_contents($api_url, false, $context);
        if ($result === FALSE) {
            echo json_encode(array("success" => false, "message" => "Failed to connect to the API."));
            exit();
        }

        // Decode the API response
        $response = json_decode($result, true);

        // Check if the transaction was successful
        if (isset($response['status']) && $response['status'] === "success") {
            // Deduct balance and update user balance in the database
            $new_balance = $user_balance - $amount;
            $update_balance_query = "UPDATE subscribers SET sWallet = '$new_balance' WHERE sId = '$sId'";
            if ($conn->query($update_balance_query) === TRUE) {
                
                // Insert transaction record in rechargetokens table
                $serial = $response['serial'] ?? ""; // If serial number exists in API response
                $tokens = $response['tokens'] ?? ""; // If tokens exist in API response
                $timestamp = date("Y-m-d H:i:s");

                $transaction_query = "INSERT INTO rechargetokens (tId, sId, tRef, business, network, datasize, quantity, serial, tokens, date) 
                                      VALUES (NULL, '$sId', '$tRef', '$service', '$network', '$network_amount', '$quantity', '$serial', '$tokens', '$timestamp')";
                $conn->query($transaction_query);

                // Return success response
                echo json_encode(array(
                    "success" => true,
                    "message" => "Recharge card purchase successful.",
                    "serial" => $serial,
                    "tokens" => $tokens
                ));
            } else {
                echo json_encode(array("success" => false, "message" => "Failed to update balance."));
            }
        } else {
            // Transaction failed, log only if there's a failure
            $timestamp = date("Y-m-d H:i:s");
            $transaction_query = "INSERT INTO rechargetokens (tId, sId, tRef, business, network, datasize, quantity, serial, tokens, date) 
                                  VALUES (NULL, '$sId', '$tRef', '$service', '$network', '$network_amount', '$quantity', '', '', '$timestamp')";
            $conn->query($transaction_query);

            // Return failure message
            echo json_encode(array("success" => false, "message" => $response['message'] ?? "Service temporarily unavailable."));
        }
    } else {
        echo json_encode(array("success" => false, "message" => "Insufficient balance."));
    }
} else {
    echo json_encode(array("success" => false, "message" => "User not found."));
}

// Close connection
$conn->close();
?>