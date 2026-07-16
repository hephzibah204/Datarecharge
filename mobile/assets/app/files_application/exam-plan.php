<?php
header('Content-Type: application/json');

// Include the database connection
require_once 'conn.php';

try {
    // Query to fetch all records from the `examid` table
    $sql = "SELECT eId, examid, provider, price, buying_price, providerStatus FROM examid";
    $result = $conn->query($sql);

    // Check if any records exist
    if ($result->num_rows > 0) {
        $plans = [];

        // Fetch all rows as associative arrays
        while ($row = $result->fetch_assoc()) {
            $plans[] = [
                "eId" => $row['eId'],
                "examid" => $row['examid'],
                "provider" => $row['provider'],
                "price" => $row['price'],
                "buying_price" => $row['buying_price'],
                "providerStatus" => $row['providerStatus']
            ];
        }

        // Return the plans as JSON
        echo json_encode([
            "success" => true,
            "plans" => $plans
        ]);
    } else {
        // No records found
        echo json_encode([
            "success" => false,
            "message" => "No plans found"
        ]);
    }
} catch (Exception $e) {
    // Handle errors
    echo json_encode([
        "success" => false,
        "message" => "An error occurred",
        "error" => $e->getMessage()
    ]);
}

// Close the database connection
$conn->close();
?>