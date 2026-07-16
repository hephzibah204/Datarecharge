<?php
// Include database connection
require_once 'conn.php';

// Query to fetch electricity plans
$query = "SELECT * FROM electricityid";
$result = $conn->query($query);

// Check if records exist
if ($result->num_rows > 0) {
    $plans = [];

    // Fetch and store results in an array
    while ($row = $result->fetch_assoc()) {
        $plans[] = $row;
    }

    // Return the data as JSON
    echo json_encode(["success" => true, "plans" => $plans]);
} else {
    echo json_encode(["success" => false, "message" => "No electricity plans found."]);
}

// Close connection
$conn->close();
?>
