<?php
// Include the database connection
require_once 'conn.php';

// Get token and subscription details from your app
$token = $_POST['token'];  
$amount = $_POST['amount'];  
$cable_name = $_POST['cable_name'];  
$cable_plan = $_POST['cable_plan'];  
$smart_card_number = $_POST['smart_card_number'];
$service = "Cable Subscription"; // Service type
$service_desc = "$cable_name - $cable_plan"; // Description

// Generate a unique transaction reference
$transref = "Cable_" . uniqid();

// Step 1: Get API details from the database
$api_query = "SELECT apikey, apilink FROM api2 WHERE value = 'cabletv' LIMIT 1";
$api_result = $conn->query($api_query);

if ($api_result->num_rows > 0) {
    $api_data = $api_result->fetch_assoc();
    $api_link = $api_data['apilink'];
    $api_key = $api_data['apikey'];
} else {
    echo json_encode(array("success" => false, "message" => "API details not found."));
    exit();
}

// Step 2: Check user balance
$user_query = "SELECT sId, sWallet FROM subscribers WHERE token = '$token'";
$user_result = $conn->query($user_query);

if ($user_result->num_rows > 0) {
    $user_data = $user_result->fetch_assoc();
    $sId = $user_data['sId'];
    $user_balance = $user_data['sWallet'];
    $old_balance = $user_balance; // Store old balance for logging

    // Step 3: Verify balance is sufficient
    if ($user_balance >= $amount) {
        
        // Prepare API request payload
        $payload = array(
            'cable' => 1,
            'iuc' => $smart_card_number,
            'cable_plan' => $cable_plan,
            'bypass' => false,
            'request-id' => $transref
        );

        // Initialize cURL session
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_link);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = [
            "Authorization: Token $api_key",
            'Content-Type: application/json'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Execute API request
        $response = curl_exec($ch);
        curl_close($ch);

        // Decode API response
        $response_data = json_decode($response, true);
        $api_response_log = json_encode($response_data); // Save full API response

        // Check if transaction was successful
        if (isset($response_data['status']) && $response_data['status'] === "success") {
            // Deduct balance
            $new_balance = $user_balance - $amount;
            $update_balance_query = "UPDATE subscribers SET sWallet = '$new_balance' WHERE sApiKey = '$token'";
            
            if ($conn->query($update_balance_query) === TRUE) {
                
                // Save transaction (Success)
                $status = 1; // Successful transaction
                $profit = 0; // Adjust profit if needed
                $api_response = $response_data['message'] ?? "Success";
                
                $transaction_query = "INSERT INTO transactions (sId, transref, servicename, servicedesc, amount, status, oldbal, newbal, profit, date, api_response, api_response_log) 
                                      VALUES ('$sId', '$transref', '$service', '$service_desc', '$amount', '$status', '$old_balance', '$new_balance', '$profit', CURRENT_TIMESTAMP, '$api_response', '$api_response_log')";
                $conn->query($transaction_query);

                // Return success response
                echo json_encode(array("success" => true, "message" => $api_response));
            } else {
                echo json_encode(array("success" => false, "message" => "Failed to update balance."));
            }
        } else {
            // Save transaction (Failed)
            $status = 0; // Failed transaction
            $profit = 0; 
            $api_response = $response_data['message'] ?? "Service temporarily unavailable.";

            $transaction_query = "INSERT INTO transactions (sId, transref, servicename, servicedesc, amount, status, oldbal, newbal, profit, date, api_response, api_response_log) 
                                  VALUES ('$sId', '$transref', '$service', '$service_desc', '$amount', '$status', '$old_balance', '$old_balance', '$profit', CURRENT_TIMESTAMP, '$api_response', '$api_response_log')";
            $conn->query($transaction_query);

            // Return failure message
            echo json_encode(array("success" => false, "message" => $api_response));
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