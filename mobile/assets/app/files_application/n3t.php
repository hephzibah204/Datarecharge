<?php
// Your N3TDATA username and password
$username = 'bashirmwali';
$password = 'Bashir,083';

// Concatenate and Base64 encode the credentials
$credentials = base64_encode("$username:$password");

// Initialize cURL session
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://legitdataway.com/api/user");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// Set HTTP header with Authorization
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Basic $credentials"
]);

// Execute request and fetch response
$response = curl_exec($ch);
curl_close($ch);

// Output the response
echo $response;
?>