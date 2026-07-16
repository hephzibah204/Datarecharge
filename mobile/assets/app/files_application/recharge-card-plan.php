<?php
require_once 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Query to fetch all rows from airtimepinprice
    $query = "SELECT aId, aNetwork, aUserDiscount, aAgentDiscount, aVendorDiscount FROM airtimepinprice";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $data = [];
        
        // Map network IDs to names
        $networkNames = [
            1 => "MTN",
            2 => "Glo",
            3 => "9Mobile",
            4 => "Airtel"
        ];

        // Loop through the results and map network names
        while ($row = $result->fetch_assoc()) {
            $row['aNetwork'] = $networkNames[$row['aNetwork']] ?? "Unknown";
            $data[] = $row;
        }

        // Send data as JSON
        echo json_encode([
            "status" => "success",
            "data" => $data
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No data found in the table."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method!"
    ]);
}
?>