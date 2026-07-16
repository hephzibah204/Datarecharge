<?php
header('Content-Type: application/json');

// Include the database connection file (which contains PHPMailer setup)
require_once 'conn.php';

// Fetch site name from site_settings table
$sitename = "Smart Pay Nigeria"; // Default name if database fetch fails
$site_query = "SELECT sitename FROM sitesettings LIMIT 1";
$site_result = $conn->query($site_query);
if ($site_result->num_rows > 0) {
    $row = $site_result->fetch_assoc();
    $sitename = $row['sitename']; // Use the site name from database
}

// Retrieve POST parameters
$sPhone = trim($_POST['sPhone']);
$sPass = trim($_POST['sPass']);

// Validate input
if (empty($sPhone) || empty($sPass)) {
    echo json_encode(['success' => false, 'message' => 'Phone number and password are required']);
    exit;
}

// Hash the password (as per your security practice)
$hash = substr(sha1(md5($sPass)), 3, 10);

// Query to check credentials
$sql = "SELECT token, sEmail, sFname FROM subscribers WHERE sPhone = ? AND sPass = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $sPhone, $hash);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch user details
    $row = $result->fetch_assoc();
    $token = $row['token'];
    $email = $row['sEmail'];
    $name = $row['sFname'];

    // If token is empty, generate a new token
    if (empty($token)) {
        $token = bin2hex(random_bytes(16)); // Generating a secure token

        // Update the token in the database
        $update_sql = "UPDATE subscribers SET token = ? WHERE sPhone = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $token, $sPhone);
        $update_stmt->execute();
        $update_stmt->close();
    }

    // Send email notification (calling function from conn.php)
    sendEmailNotification($email, $name, "Login Successful", 
        "New Login Alert – to Your $sitename Account!\n\n
        Dear $name,\n\n
        We noticed a new login to your $sitename account from a new device or location. If this was you, no further action is required. However, if you did not initiate this login, please take immediate action to secure your account.");

    // Return success response with the token
    echo json_encode(["success" => true, "token" => $token]);
} else {
    // Return failure response
    echo json_encode(["success" => false, "message" => "Invalid phone number or password"]);
}

// Close the connection
$stmt->close();
$conn->close();
?>