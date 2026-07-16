<?php
require_once 'conn.php';

function fetchBanksForApp($conn) {
    $result = $conn->query("SELECT bank_name, bank_code, bank_logo FROM bank_list");
    $banks = [];
    while ($row = $result->fetch_assoc()) {
        $banks[] = $row;
    }
    return json_encode($banks); // Send as JSON response
}

// Output the bank list
header("Content-Type: application/json");
echo fetchBanksForApp($conn);
?>