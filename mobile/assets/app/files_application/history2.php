<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once 'conn.php';

// Check database connection
if (!$conn) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . mysqli_connect_error()]));
}

// Check if token is provided in POST request
if (!isset($_POST['token'])) {
    die(json_encode(["success" => false, "message" => "Token is required"]));
}

$token = mysqli_real_escape_string($conn, $_POST['token']);

// Validate token in `subscribers` table
$sql_check = "SELECT sId FROM subscribers WHERE token = '$token' LIMIT 1";
$result_check = $conn->query($sql_check);

if (!$result_check || $result_check->num_rows == 0) {
    die(json_encode(["success" => false, "message" => "Invalid token"]));
}

$user = $result_check->fetch_assoc();
$sId = $user['sId'];

// Fetch latest 20 wallet transactions for this user
$sql = "SELECT tId, sId, transref, servicename, servicedesc, amount, status, oldbal, newbal, profit, date 
        FROM transactions 
        WHERE servicename LIKE 'wallet%' 
        AND sId = '$sId' 
        ORDER BY date DESC 
        LIMIT 20";

$result = $conn->query($sql);

$deposits = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $deposits[] = $row; // Directly push the associative array
    }
}

// Always return a valid JSON response
echo json_encode(["success" => true, "deposits" => $deposits]);

// Close database connection
$conn->close();
?>