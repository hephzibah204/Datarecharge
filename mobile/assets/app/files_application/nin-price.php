<?php
include 'conn.php'; // Include your database connection file

// Query to fetch all NIN plans
$sql = "SELECT slip_name, buying_price, user_price FROM nin_price";
$result = mysqli_query($conn, $sql);

$response = array();

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $response[] = $row;
    }
    echo json_encode(["success" => true, "message" => "Plans retrieved successfully", "data" => $response]);
} else {
    echo json_encode(["success" => false, "message" => "No plans found"]);
}

// Close connection
mysqli_close($conn);
?>