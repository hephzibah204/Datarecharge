<?php
require_once 'paystack_config.php';

function createRecipient($name, $accountNumber, $bankCode) {
    $url = "https://api.paystack.co/transferrecipient";

    $data = [
        "type" => "nuban",
        "name" => $name,
        "account_number" => $accountNumber,
        "bank_code" => $bankCode,
        "currency" => "NGN"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, getHeaders());

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Example Usage
$name = $_POST['name']; // From the app
$accountNumber = $_POST['account_number']; // From the app
$bankCode = $_POST['bank_code']; // From the app

$result = createRecipient($name, $accountNumber, $bankCode);
header('Content-Type: application/json');
echo json_encode($result);
?>