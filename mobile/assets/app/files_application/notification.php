<?php
/**
 * SMART PAY NIG - Fetch Latest Notification
 * Developer: Muhammad MK
 * Contact: 07066620622
 */

// Include the database connection file
require_once 'conn.php';

// Query to fetch the latest unread notification (status = 0)
$sql = "SELECT subject, message FROM notifications WHERE status = '0' ORDER BY dPosted DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Fetch the latest notification
    $row = $result->fetch_assoc();
    $notification = [
        'subject' => $row['subject'],
        'message' => $row['message']
    ];

    // Send the latest notification to the app
    echo json_encode([
        'success' => true,
        'notification' => $notification
    ]);
} else {
    // No unread notifications found
    echo json_encode([
        'success' => true,
        'notification' => null
    ]);
}

// Close the database connection
$conn->close();
?>