
<?php
header('Content-Type: application/json');
require_once('conn.php');

// Fetch site name from site_settings table
$sitename = "Smart Pay Nigeria"; // Default name if database fetch fails
$site_query = "SELECT sitename FROM sitesettings LIMIT 1";
$site_result = $conn->query($site_query);
if ($site_result->num_rows > 0) {
    $row = $site_result->fetch_assoc();
    $sitename = $row['sitename']; // Use the site name from database
}

// Allowed Nigerian states
$valid_states = [
    "Abuja FCT", "Abia", "Adamawa", "Akwa Ibom", "Anambra", "Bauchi", "Bayelsa", "Benue", "Borno", "Cross River", 
    "Delta", "Ebonyi", "Edo", "Ekiti", "Enugu", "Gombe", "Imo", "Jigawa", "Kaduna", "Kano", "Katsina", "Kebbi", 
    "Kogi", "Kwara", "Lagos", "Nassarawa", "Niger", "Ogun", "Ondo", "Osun", "Oyo", "Plateau", "Rivers", "Sokoto", 
    "Taraba", "Yobe", "Zamfara"
];

// Retrieve and sanitize input
$sFname = trim($_POST['sFname']);
$sLname = trim($_POST['sLname']);
$sPhone = trim($_POST['sPhone']);
$sEmail = trim($_POST['sEmail']);
$sState = trim($_POST['sState']);
$sPass = trim($_POST['sPass']);
$sPin = trim($_POST['sPin']);
$sType = trim($_POST['sType']);
$sWallet = trim($_POST['sWallet']);
$sPinStatus = trim($_POST['sPinStatus']);
$sRefWallet = trim($_POST['sRefWallet']);
$sRegStatus = trim($_POST['sRegStatus']);
$sAccountLimit = trim($_POST['sAccountLimit']);

// Phone Number Validation
if (!preg_match('/^\d{11}$/', $sPhone)) {
    echo json_encode(['success' => false, 'message' => 'Invalid phone number. It must be 11 digits.']);
    exit;
}

// Email Validation
if (!filter_var($sEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit;
}

// State Validation (Case-insensitive)
if (!in_array(ucwords(strtolower($sState)), $valid_states)) {
    echo json_encode(['success' => false, 'message' => 'Invalid state. Please select a valid Nigerian state.']);
    exit;
}

// Password Strength Validation
if (strlen($sPass) < 8 || $sPass == "1234" || strtolower($sPass) == "password" || $sPass == $sPhone) {
    echo json_encode(['success' => false, 'message' => 'Weak password. Use at least 8 characters and avoid common patterns.']);
    exit;
}

// Hash Password Securely
$hash = substr(sha1(md5($sPass)), 3, 10);

// Generate Unique API Key
$sApiKey = substr(str_shuffle("0123456789ABCDEFGHIJklmnopqrstvwxyzAbAcAdAeAfAgAhBaBbBcBdC1C23C3C4C5C6C7C8C9xix2x3"), 0, 60) . time();

// Registration Date
$sRegDate = date('Y-m-d H:i:s');

// Check if Email or Phone already exists
$sql = "SELECT * FROM subscribers WHERE sEmail = ? OR sPhone = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $sEmail, $sPhone);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email or phone number is already registered.']);
} else {
    // Insert new user
    $sql = "INSERT INTO subscribers (sApiKey, sFname, sLname, sEmail, sPhone, sPass, sState, sPin, sPinStatus, sType, sWallet, sRefWallet, sRegStatus, sRegDate, sAccountLimit) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssss", $sApiKey, $sFname, $sLname, $sEmail, $sPhone, $hash, $sState, $sPin, $sPinStatus, $sType, $sWallet, $sRefWallet, $sRegStatus, $sRegDate, $sAccountLimit);

    if ($stmt->execute()) {
        // Send welcome email
        sendEmailNotification($sEmail, $sFname, "Welcome to $sitename", 
            "Welcome to $sitename – Your Ultimate VTU Solution!

Dear $sFname,

We are excited to welcome you to $sitename! Your registration was successful, and your journey to seamless and affordable VTU services starts now.

At $sitename, we are dedicated to providing you with fast, secure, and reliable virtual top-up services, including data bundles, airtime, electricity bills, cable TV subscriptions, and more. Whether you are a regular user or a reseller, our platform is designed to meet all your needs efficiently.

Your Account Details

Name: $sFname $sLname

Phone Number: $sPhone

Email: $sEmail

Registration Date: $sRegDate

Account Type: $sType
Getting Started

1️⃣ Log in to your account here using your registered phone number and password.
2️⃣ Fund your wallet to start enjoying seamless transactions.
3️⃣ Explore our services – Buy data, airtime, pay bills, and more!
4️⃣ Refer & Earn – Invite your friends and earn commissions on their transactions.

Did You Just Log In?

If you recently logged into your account, you can ignore this message. However, if you did not authorize this login, please reset your password immediately and contact our support team.

Need Help?

Our support team is always available to assist you. You can reach us via Whatsapp:
We appreciate your trust in $sitename and look forward to serving you better. Enjoy fast, reliable, and affordable VTU services with us!

Best Regards,
💙 $sitename Team");

        echo json_encode(['success' => true, 'message' => 'User registered successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to register user', 'error' => $stmt->error]);
    }
}

$stmt->close();
$conn->close();
?>