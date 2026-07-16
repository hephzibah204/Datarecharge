<?php
function getBanks($country = "NG") {
    $url = "https://api.flutterwave.com/v3/banks/$country";
    $secretKey = "FLWSECK_TEST-8e52c3ecae27356a1e4292dcee8b2186-X"; // Replace with your secret key

    $headers = [
        "Authorization: Bearer $secretKey",
        "Content-Type: application/json"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Call the function
$banks = getBanks();
header('Content-Type: application/json');
echo json_encode($banks);
?>