<?php
require_once 'paystack_config.php';

function fetchBanks() {
    $url = "https://api.paystack.co/bank";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, getHeaders());

    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);

    if (isset($responseData['status']) && $responseData['status'] === true) {
        $banks = $responseData['data'];

        // Format the response to include bank names, codes, and logos
        $formattedBanks = [];
        foreach ($banks as $bank) {
            $formattedBanks[] = [
                "name" => $bank['name'],
                "code" => $bank['code'],
                "slug" => $bank['slug'], // Bank slug (useful for logos)
                "logo" => "https://logo.clearbit.com/" . $bank['slug'] . ".com" // Example logo URL generator
            ];
        }

        return ["success" => true, "banks" => $formattedBanks];
    } else {
        return ["success" => false, "message" => "Failed to fetch banks"];
    }
}

// Example usage
$response = fetchBanks();

header('Content-Type: application/json');
echo json_encode($response);
?>