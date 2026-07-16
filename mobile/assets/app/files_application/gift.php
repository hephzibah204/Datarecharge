<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Enable MySQLi error reporting

include 'conn.php'; // Include database connection

header("Content-Type: application/json");

// Ensure the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit;
}

// Retrieve and validate input data from $_POST
$token = isset($_POST['token']) ? trim($_POST['token']) : null;
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
$email = isset($_POST['email']) ? trim($_POST['email']) : null;
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : null;

// Check for missing fields
if (!$token || !$phone || !$email || !$amount || $amount <= 0) {
    echo json_encode(["success" => false, "message" => "Missing or invalid input data"]);
    exit;
}

// Authenticate sender using token
$senderQuery = $conn->prepare("SELECT sId, sWallet FROM subscribers WHERE token = ?");
$senderQuery->bind_param("s", $token);
$senderQuery->execute();
$senderResult = $senderQuery->get_result();

if ($senderResult->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Invalid token or user not found"]);
    exit;
}

$sender = $senderResult->fetch_assoc();
$senderId = $sender['sId'];
$senderBalance = floatval($sender['sWallet']);

// Find recipient using phone and email
$recipientQuery = $conn->prepare("SELECT sId, sWallet FROM subscribers WHERE sPhone = ? AND sEmail = ?");
$recipientQuery->bind_param("ss", $phone, $email);
$recipientQuery->execute();
$recipientResult = $recipientQuery->get_result();

if ($recipientResult->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Recipient not found"]);
    exit;
}

$recipient = $recipientResult->fetch_assoc();
$recipientId = $recipient['sId'];
$recipientBalance = floatval($recipient['sWallet']);

// Check if sender has enough balance
if ($senderBalance < $amount) {
    echo json_encode(["success" => false, "message" => "Insufficient balance"]);
    exit;
}

// Begin transaction
$conn->begin_transaction();

try {
    // Deduct amount from sender
    $deductQuery = $conn->prepare("UPDATE subscribers SET sWallet = sWallet - ? WHERE sId = ?");
    $deductQuery->bind_param("di", $amount, $senderId);
    $deductQuery->execute();

    // Credit amount to recipient
    $creditQuery = $conn->prepare("UPDATE subscribers SET sWallet = sWallet + ? WHERE sId = ?");
    $creditQuery->bind_param("di", $amount, $recipientId);
    $creditQuery->execute();

    // Insert transaction record
    $transref = "TRX" . time() . rand(1000, 9999);
    $description = "Transfer to $phone / $email";
    $newSenderBalance = $senderBalance - $amount;

    $insertTransaction = $conn->prepare("INSERT INTO transactions (sId, transref, servicename, servicedesc, amount, status, oldbal, newbal, date) 
                                         VALUES (?, ?, 'Transfer', ?, ?, 'completed', ?, ?, NOW())");
    $insertTransaction->bind_param("issddd", $senderId, $transref, $description, $amount, $senderBalance, $newSenderBalance);
    $insertTransaction->execute();

    // Commit transaction
    $conn->commit();

    echo json_encode([
        "success" => true,
        "message" => "Transfer successful",
        "transaction_id" => $transref,
        "sender_balance" => $newSenderBalance
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "Transaction failed: " . $e->getMessage()]);
}

$conn->close();
?>