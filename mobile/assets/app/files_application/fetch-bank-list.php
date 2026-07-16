<?php
require_once 'conn.php'; // Database connection
require_once 'token-fetcher.php'; // The getValidToken() function

function fetchBanksWithLogos($conn) {
    $accessToken = getValidToken($conn); // Retrieve a valid token
    $url = "https://api.monnify.com/api/v1/banks";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['responseBody'])) {
        foreach ($result['responseBody'] as $bank) {
            // Save banks with logos into your database
            $stmt = $conn->prepare("INSERT INTO bank_list (bank_name, bank_code, bank_logo) VALUES (?, ?, ?) 
                                    ON DUPLICATE KEY UPDATE bank_name = VALUES(bank_name), bank_logo = VALUES(bank_logo)");
            $bankLogo = $bank['logo'] ?? ''; // Some banks may not have a logo
            $stmt->bind_param("sss", $bank['name'], $bank['code'], $bankLogo);
            $stmt->execute();
        }
        return "Banks with logos fetched and stored successfully!";
    } else {
        return "Failed to fetch banks: " . json_encode($result);
    }
}

// Call the function to fetch and store banks
echo fetchBanksWithLogos($conn);
?>