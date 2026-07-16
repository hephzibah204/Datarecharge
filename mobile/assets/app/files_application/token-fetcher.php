<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'conn.php'; // Database connection
require_once'monnify-auth.php';
// Function to get a valid access token
function getValidToken($conn) {
    $currentTime = date('Y-m-d H:i:s');

    // Check for an unexpired token
    $stmt = $conn->prepare("SELECT token, expires_at FROM api_tokens WHERE expires_at > ? ORDER BY id DESC LIMIT 1");
    if (!$stmt) {
        die("Database query error: " . $conn->error);
    }
    $stmt->bind_param("s", $currentTime);
    $stmt->execute();
    $stmt->bind_result($token, $expires_at);
    $stmt->fetch();
    $stmt->close();

    // If a valid token exists, return it
    if (!empty($token)) {
        return $token;
    }

    // Generate a new token if no valid token is found
    return generateNewToken($conn);
}

// Function to generate a new access token
function generateNewToken($conn) {
    // Monnify API credentials
    $apiKey = "MK_PROD_QMSVGXY8BF";        // Replace with your Monnify API key
    $secretKey = "TW8SFWHP0EWMG8GCFHTP2S0F4ZDTGE47";  // Replace with your Monnify secret key
    $authUrl = "https://api.monnify.com/api/v1/auth/login";

    // Initialize cURL
    $ch = curl_init($authUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Basic " . base64_encode("$apiKey:$secretKey"),
        "Content-Type: application/json"
    ]);

    // Execute cURL request
    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    // Handle cURL errors
    if ($response === false) {
        die("cURL Error: " . $curlError);
    }

    $result = json_decode($response, true);

    // Check if the request was successful
    if (isset($result['responseMessage']) && $result['responseMessage'] === "success") {
        $newToken = $result['responseBody']['accessToken'];
        $expiresIn = $result['responseBody']['expiresIn'];
        $expiresAt = date('Y-m-d H:i:s', strtotime("+$expiresIn seconds"));

        // Save the new token in the database
        $stmt = $conn->prepare("INSERT INTO api_tokens (token, expires_at) VALUES (?, ?)");
        if (!$stmt) {
            die("Database query error: " . $conn->error);
        }
        $stmt->bind_param("ss", $newToken, $expiresAt);
        $stmt->execute();
        $stmt->close();

        return $newToken; // Return the new token
    } else {
        // Log the failed response for debugging
        file_put_contents('monnify_error_log.txt', print_r($result, true));
        die("Failed to generate a new access token. Please check your API credentials.");
    }
}

// Test the token fetcher
// Removed the echo statement