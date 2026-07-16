<?php
include 'conn.php';

$sql = "SELECT subject, message FROM notifications WHERE status = '0' ORDER BY dPosted DESC LIMIT 1"; // Fetch only one latest notification
$result = $conn->query($sql);

$response = ["success" => false];

if ($result && $row = $result->fetch_assoc()) {
    $response = [
        "success" => true,
        "notification" => [
            "subject" => $row['subject'],
            "message" => $row['message']
        ]
    ];
}

echo json_encode($response);
$conn->close();
?>