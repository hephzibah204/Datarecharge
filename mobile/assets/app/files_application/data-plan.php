<?php
require_once 'conn.php';

$networkNames = [
    1 => 'MTN',
    4 => 'AIRTEL',
    2 => 'GLO',
    3 => '9MOBILE'
];

$token = isset($_POST['token']) ? $_POST['token'] : null;

if (!$token) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Token is required"]);
    exit;
}

// Fetch sType from subscribers table
$sql = "SELECT sType FROM subscribers WHERE token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($sType);

if ($stmt->num_rows == 0) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Invalid token"]);
    $stmt->close();
    exit;
}

$stmt->fetch();
$stmt->close();

// Fetch all data plans first
$sql = "SELECT 
    id AS pId, 
    planid, 
    plan_name AS name, 
    price, 
    userprice, 
    agentprice, 
    vendorprice, 
    CASE 
        WHEN network = 'MTN' THEN 1
        WHEN network = 'GLO' THEN 2
        WHEN network = '9MOBILE' THEN 3
        WHEN network = 'AIRTEL' THEN 4
        ELSE 0
    END AS datanetwork,
    duration AS day, 
    type 
FROM appdata";
$result = $conn->query($sql);

$allDataPlans = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $allDataPlans[] = $row;
    }
}

// Fetch network statuses from networkid table
$sql = "SELECT networkid, networkStatus, smeStatus, giftingStatus, corporateStatus, corporate2Status FROM networkid";
$networkResult = $conn->query($sql);

$networkStatuses = [];
if ($networkResult->num_rows > 0) {
    while ($networkRow = $networkResult->fetch_assoc()) {
        $networkStatuses[$networkRow['networkid']] = $networkRow;
    }
}

// Filter the data plans based on network status and plan type
$dataplans = [];

foreach ($allDataPlans as $row) {
    $networkId = $row['datanetwork'];

    // **Check if the network status is On**
    if (!isset($networkStatuses[$networkId]) || $networkStatuses[$networkId]["networkStatus"] !== "On") {
        continue;
    }

    // **Check if the plan type is Off** (Skip if Off)
    $planType = strtolower($row['type']); // Convert to lowercase for case-insensitivity
    if (($planType == 'sme' && isset($networkStatuses[$networkId]["smeStatus"]) && $networkStatuses[$networkId]["smeStatus"] === "Off") ||
        ($planType == 'gifting' && isset($networkStatuses[$networkId]["giftingStatus"]) && $networkStatuses[$networkId]["giftingStatus"] === "Off") ||
        ($planType == 'corporate' && isset($networkStatuses[$networkId]["corporateStatus"]) && $networkStatuses[$networkId]["corporateStatus"] === "Off") ||
        ($planType == 'corporate2' && isset($networkStatuses[$networkId]["corporate2Status"]) && $networkStatuses[$networkId]["corporate2Status"] === "Off")) {
        continue;
    }

    // Convert network ID to network name
    $row['datanetwork'] = isset($networkNames[$networkId]) ? $networkNames[$networkId] : "Unknown";

    // Assign correct price based on sType
    if ($sType == 1) {
        $row['price'] = $row['userprice'];
    } elseif ($sType == 2) {
        $row['price'] = $row['agentprice'];
    } elseif ($sType == 3) {
        $row['price'] = $row['vendorprice'];
    }

    $dataplans[] = $row;
}

// Send response
header('Content-Type: application/json');
echo json_encode(["dataplans" => $dataplans]);

$conn->close();
?>