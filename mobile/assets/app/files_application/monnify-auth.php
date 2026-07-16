<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once 'conn.php';

function authenticateMonnify($conn) {
    // Replace with your live Client ID and Client Secret
    $clientId = 'MK_PROD_QMSVGXY8BF'; 
    $clientSecret = 'TW8SFWHP0EWMG8GCFHTP2S0F4ZDTGE47';

    // Monnify live authentication endpoint
    $url = "https://api.monnify.com/api/v1/auth/login"; 

    // Initialize cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true); // Use POST method
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Basic " . base64_encode("$clientId:$clientSecret"), // Add Base64-encoded credentials
        "Content-Type: application/json",
    ]);

    // Execute cURL request
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        // Log cURL error for debugging
        file_put_contents('monnify_curl_error_log.txt', curl_error($ch));
        return false; // Return false to indicate failure
    }

    curl_close($ch);

    // Decode the response from Monnify
    $result = json_decode($response, true);

    // Check if accessToken is returned in the response
    if (isset($result['responseBody']['accessToken'])) {
        $accessToken = $result['responseBody']['accessToken'];
        $expiresIn = $result['responseBody']['expiresIn']; // Token validity in seconds

        // Calculate token expiration time
        $expiresAt = date('Y-m-d H:i:s', time() + $expiresIn);

        // Save the token in the database
        $stmt = $conn->prepare("
            INSERT INTO api_tokens (token, expires_at) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE 
                token = ?, 
                expires_at = ?
        ");
        $stmt->bind_param("ssss", $accessToken, $expiresAt, $accessToken, $expiresAt);

        if (!$stmt->execute()) {
            // Log database error for debugging
            file_put_contents('monnify_db_error_log.txt', $stmt->error);
            return false; // Return false to indicate failure
        }

        return true; // Return true to indicate success
    } else {
        // Log the Monnify API error for debugging
        file_put_contents('monnify_api_error_log.txt', json_encode($result));
        return false; // Return false to indicate failure
    }
}

// Call the function to authenticate and save the token
if (authenticateMonnify($conn)) {
    // Authentication succeeded
    // Do nothing, ensure no output is sent to the client
} else {
    // Authentication failed
    // Log error internally, do not output anything
}