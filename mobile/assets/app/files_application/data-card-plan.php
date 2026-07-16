<?php
// Include the database connection file
require_once 'conn.php';

// Define the network mapping
$networkNames = [
    1 => 'MTN',
    4 => 'AIRTEL',
    2 => 'GLO',
    3 => '9MOBILE'
];

// SQL query to retrieve only the specified columns
$sql = "SELECT dpId, planid, name, price, datanetwork, day, type FROM datapins";
$result = $conn->query($sql);

$dataplans = array();

if ($result->num_rows > 0) {
    // Fetch all data plans into an array
    while ($row = $result->fetch_assoc()) {
        // Convert the network ID to network name
        $row['datanetwork'] = isset($networkNames[$row['datanetwork']]) ? $networkNames[$row['datanetwork']] : "Unknown";
        
        $dataplans[] = $row;
    }
}

// Send the data plans as a JSON response
header('Content-Type: application/json');
echo json_encode(array("dataplans" => $dataplans));

// Close the connection
$conn->close();
?>