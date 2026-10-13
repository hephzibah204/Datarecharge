<?php
header('Content-Type: application/json');
require_once 'conn.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt');

function debug_log($message) {
    error_log(date("Y-m-d H:i:s") . " - " . $message . "\n", 3, "debug_log_nin.txt");
}

// Log script start
debug_log("NIN Slip API called");

// Check database connection (support PDO/SQLite and fallback to mysqli)
$pdo = null;
$modelPath = dirname(__DIR__, 4) . '/core/Models/Model.php';
if (file_exists($modelPath)) {
    require_once $modelPath;
    try {
        $model = new Model();
        $pdo = $model->connectDb();
    } catch (\Throwable $e) {}
}

if (!$pdo && (!$conn || !($conn instanceof mysqli))) {
    debug_log("Database connection failed: No valid PDO or mysqli connection");
    die(json_encode(["success" => false, "message" => "Database connection failed"]));
}

// Ensure request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    debug_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    die(json_encode(["success" => false, "message" => "Invalid request method. Use POST."]));
}

// Retrieve and validate input
$token = $_POST['token'] ?? '';
$nin = $_POST['nin'] ?? '';
$slip_type = $_POST['slip_type'] ?? '';

debug_log("Received input: token=$token, nin=$nin, slip_type=$slip_type");

if (empty($token) || empty($nin) || empty($slip_type)) {
    debug_log("Validation failed: missing required fields");
    die(json_encode(["success" => false, "message" => "Missing required fields: token, nin, slip_type."]));
}

// **Fetch User Details**
$user = null;
if ($pdo) {
    $stmt = $pdo->prepare("SELECT sId, sWallet FROM subscribers WHERE token = :t LIMIT 1");
    $stmt->execute([':t' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $query = "SELECT sId, sWallet FROM subscribers WHERE token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}

if (!$user) {
    debug_log("Invalid token provided");
    echo json_encode(["success" => false, "message" => "Invalid Token"]);
    exit();
}

$sId = $user['sId'];
$wallet_balance = floatval($user['sWallet']);

// **Fetch API Details**
$api_details = null;
if ($pdo) {
    $stmt = $pdo->prepare("SELECT apikey, apilink FROM api2 WHERE value = 'nin' LIMIT 1");
    $stmt->execute();
    $api_details = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $api_query = "SELECT apikey, apilink FROM api2 WHERE value = 'nin'";
    $api_result = $conn->query($api_query);
    $api_details = $api_result->fetch_assoc();
}

if (!$api_details) {
    debug_log("API details not found in database");
    echo json_encode(["success" => false, "message" => "API details not found"]);
    exit();
}

$api_key = $api_details['apikey'];
$api_url = $api_details['apilink'];

// **Fetch Slip Price**
$price_data = null;
if ($pdo) {
    $cleanSlip = str_replace(['slip', ' ', '_'], '', strtolower($slip_type));
    $stmt = $pdo->prepare("SELECT buying_price FROM nin_price WHERE LOWER(REPLACE(REPLACE(slip_name, ' ', ''), '_', '')) LIKE :s LIMIT 1");
    $stmt->execute([':s' => '%' . $cleanSlip . '%']);
    $price_data = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $price_query = "SELECT buying_price FROM nin_price WHERE slip_name = ?";
    $stmt = $conn->prepare($price_query);
    $stmt->bind_param("s", $slip_type);
    $stmt->execute();
    $result = $stmt->get_result();
    $price_data = $result->fetch_assoc();
}

if (!$price_data) {
    debug_log("Invalid slip type provided: " . $slip_type);
    echo json_encode(["success" => false, "message" => "Invalid slip type"]);
    exit();
}

$amount = floatval($price_data['buying_price']);

// **Check Wallet Balance**
if ($wallet_balance < $amount) {
    debug_log("Insufficient balance: Wallet=$wallet_balance, Required=$amount");
    echo json_encode(["success" => false, "message" => "Insufficient balance"]);
    exit();
}

// **Call NIN Verification API**
$request_body = json_encode(["nin" => $nin, "consent" => true]);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $api_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $request_body,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer $api_key"
    ],
]);

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

// **Log API Response**
debug_log("API Response: " . $response);
if ($err) {
    debug_log("cURL Error: " . $err);
    echo json_encode(["success" => false, "message" => "API request failed", "error" => $err]);
    exit();
}

$result = json_decode($response, true);

// **Check API Response**
if (!isset($result['status']) || $result['status'] !== true) {
    debug_log("NIN verification failed. Response: " . $response);
    echo json_encode(["success" => false, "message" => "NIN verification failed"]);
    exit();
}

// **Hash Sensitive Data**
$hashed_nin = hash("sha256", $nin);
$hashed_dob = hash("sha256", $result['data']['dob']);

// **Deduct from Wallet**
$new_balance = $wallet_balance - $amount;
$trans_id = uniqid("NIN_");

if ($pdo) {
    $stmt = $pdo->prepare("UPDATE subscribers SET sWallet = :b WHERE sId = :id");
    $stmt->execute([':b' => $new_balance, ':id' => $sId]);

    $stmt = $pdo->prepare("INSERT INTO transactions (sId, transref, servicename, servicedesc, amount, status, oldbal, newbal, date)
                 VALUES (:id, :ref, 'NIN Verification', :nin, :amt, 'success', :old, :new, datetime('now'))");
    $stmt->execute([
        ':id' => $sId,
        ':ref' => $trans_id,
        ':nin' => $hashed_nin,
        ':amt' => $amount,
        ':old' => $wallet_balance,
        ':new' => $new_balance
    ]);
} else {
    $update_wallet = "UPDATE subscribers SET sWallet = ? WHERE sId = ?";
    $stmt = $conn->prepare($update_wallet);
    $stmt->bind_param("ds", $new_balance, $sId);
    $stmt->execute();

    // **Save Transaction**
    $insert_query = "INSERT INTO transactions (sId, transref, servicename, servicedesc, amount, status, oldbal, newbal, date)
                     VALUES (?, ?, 'NIN Verification', ?, ?, 'success', ?, NOW())";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("sssdss", $sId, $trans_id, $hashed_nin, $amount, $wallet_balance, $new_balance);
    $stmt->execute();

    if ($stmt->error) {
        debug_log("Transaction save error: " . $stmt->error);
        echo json_encode(["success" => false, "message" => "Transaction failed", "error" => $stmt->error]);
        exit();
    }
}

// **Generate PDF**
$pdf_url = generate_pdf($trans_id, $hashed_nin, $hashed_dob, $result['data'], $slip_type);
debug_log("PDF generated: " . $pdf_url);

echo json_encode([
    "success" => true,
    "message" => "NIN verified successfully",
    "pdf_url" => $pdf_url
]);
exit();

// **Generate PDF Function**
function generate_pdf($trans_id, $hashed_nin, $hashed_dob, $data, $slip_type) {
    $html = "<h2>NIN Verification Slip</h2>";
    $html .= "<p><strong>Transaction ID:</strong> $trans_id</p>";
    $html .= "<p><strong>Hashed NIN:</strong> $hashed_nin</p>";
    $html .= "<p><strong>Hashed DOB:</strong> $hashed_dob</p>";
    $html .= "<p><strong>Full Name:</strong> {$data['firstname']} {$data['lastname']}</p>";
    $html .= "<p><strong>Gender:</strong> {$data['gender']}</p>";

    if ($slip_type == "standardslip" || $slip_type == "premiumslip") {
        $html .= "<p><strong>State:</strong> {$data['state']}</p>";
        $html .= "<p><strong>Nationality:</strong> {$data['nationality']}</p>";
    }

    if ($slip_type == "premiumslip") {
        $html .= "<p><strong>Phone Number:</strong> {$data['phone']}</p>";
        $html .= "<p><strong>Email:</strong> {$data['email']}</p>";
    }

    require_once 'tcpdf/tcpdf.php';
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->writeHTML($html);
    
    $pdf_filename = "nin_slips/$trans_id.pdf";
    $pdf->Output($pdf_filename, "F");

    return "https://yourwebsite.com/$pdf_filename";
}
?>