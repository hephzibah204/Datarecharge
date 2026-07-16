<?php
// Include the database connection file
require_once 'conn.php';

// Prepare the SQL query to fetch all cable TV plans
$query = "SELECT cpid, name, price, cableprovider, planid FROM cableplans";
$result = $conn->query($query);

$response = [];

// Check if the query returned any rows
if ($result->num_rows > 0) {
    // Fetch each row as an associative array and add it to the response
    while ($row = $result->fetch_assoc()) {
        $response[] = [
            
            'cpid' => $row['cpid'],
            'name' => $row['name'],
            'price' => $row['price'],
            'cableprovider' => $row['cableprovider'],
            'planid' => $row['planid']
        ];
    }
    // Send a success response with data
    echo json_encode([
        'success' => true,
        'datas' => $response // Using 'datas' as key to match app parsing
    ]);
} else {
    // Send a response indicating no plans were found
    echo json_encode([
        'success' => false,
        'message' => 'No cables TV plans found'
    ]);
}

// Close the database connection
$conn->close();
?>