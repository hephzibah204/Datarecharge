<?php
require_once 'paystack_config.php';

function verifyAccount($accountNumber, $bankCode) {
    $url = "https://api.paystack.co/bank/resolve?account_number=$accountNumber&bank_code=$bankCode";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, getHeaders());

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Example Usage
$accountNumber = $_POST['account_number']; // From the app
$bankCode = $_POST['bank_code']; // From the app

$result = verifyAccount($accountNumber, $bankCode);
header('Content-Type: application/json');
echo json_encode($result);
?>