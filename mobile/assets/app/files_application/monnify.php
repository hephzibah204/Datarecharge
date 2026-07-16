<?php
// Monnify Authentication
function authenticateMonnify() {
    $url = "https://sandbox.monnify.com/api/v1/auth/login";
    $apiKey = "MK_PROD_QMSVGXY8BF";
    $secretKey = "TW8SFWHP0EWMG8GCFHTP2S0F4ZDTGE47";

    $credentials = base64_encode("$apiKey:$secretKey");

    $headers = [
        "Authorization: Basic $credentials",
        "Content-Type: application/json"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    return $data['responseBody']['accessToken'] ?? false;
}

// Initiate Bank Transfer
function initiateBankTransfer($accessToken, $amount, $bankCode, $accountNumber, $reference, $narration = "Wallet Withdrawal") {
    $url = "https://sandbox.monnify.com/api/v1/disbursements/single";
    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ];

    $data = [
        "amount" => $amount,
        "reference" => $reference,
        "narration" => $narration,
        "destinationBankCode" => $bankCode,
        "destinationAccountNumber" => $accountNumber,
        "currencyCode" => "NGN"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Fetch Bank Codes
function fetchBankCodes($accessToken) {
    $url = "https://sandbox.monnify.com/api/v1/banks";
    $headers = [
        "Authorization: Bearer $accessToken"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}
?>