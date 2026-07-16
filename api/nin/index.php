<?php

// NIN Verification & Modification API Endpoint

require_once "../autoloader.php";

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST");
header("Allow: POST");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Allow-Origin");

$headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
$response = [];
$controller = new NIN;
$ninModel = new NINModification;

date_default_timezone_set('Africa/Lagos');

// Check Request Method
if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    respond(["status" => "fail", "msg" => "Only POST method is allowed"]);
}

// Validate Authorization
$token = '';
if (!empty($headers['Authorization'])) {
    $token = trim(str_replace("Token ", "", $headers['Authorization']));
} elseif (!empty($headers['authorization'])) {
    $token = trim(str_replace("Token ", "", $headers['authorization']));
} elseif (!empty($headers['Token'])) {
    $token = trim(str_replace("Token ", "", $headers['Token']));
} elseif (!empty($headers['token'])) {
    $token = trim(str_replace("Token ", "", $headers['token']));
}

if (empty($token)) {
    header('HTTP/1.0 401 Unauthorized');
    respond(["status" => "fail", "msg" => "Your authorization token is required."]);
}

$result = $controller->validateAccessToken($token);
if ($result["status"] == "fail") {
    header('HTTP/1.0 401 Unauthorized');
    respond(["status" => "fail", "msg" => "Authorization token not found"]);
}

// Check NIN service status
$cfgDb = $ninModel->connect();
$cfgQuery = $cfgDb->prepare("SELECT value FROM apiconfigs WHERE name = :name");
$cfgQuery->execute([':name' => 'ninStatus']);
$ninStatus = $cfgQuery->fetchColumn();
if ($ninStatus === 'Off') {
    respond(["status" => "fail", "msg" => "NIN service is currently disabled by admin"]);
}

$usertype = $result["usertype"];
$userbalance = (float) $result["balance"];
$userid = $result["userid"];

// Parse request body
$input = @file_get_contents("php://input");
$body = json_decode($input);

if (!$body) {
    respond(["status" => "fail", "msg" => "Invalid JSON body"]);
}

// Normalize fields
$body2 = [];
if (isset($body->Ported_number)) $body2["ported_number"] = $body->Ported_number;
if (isset($body->mobile_number)) $body2["phone"] = $body->mobile_number;
if (isset($body->plan)) $body2["data_plan"] = $body->plan;
if (!isset($body->ref)) $body2["ref"] = "REF_" . mt_rand(100, 999) . time();
$body = (object) array_merge((array)$body, $body2);

$network = $body->network ?? '';
$phone = str_replace(" ", "", $body->phone ?? '');
$ported_number = $body->ported_number ?? "false";
$tracking_ids = $body->tracking_ids ?? [];
$service = $body->service ?? '';

// Determine request type
$isModification = isset($body->modification_type) || (isset($body->verification_type) && $body->verification_type == 'nin_verification');
$isIPE = !empty($tracking_ids) || $service === 'ipe';
$isValidation = !$isModification && !$isIPE;

// Validate required fields
$requiredField = "";
if ($isValidation && empty($phone)) $requiredField = "Phone is required";
if ($isValidation && empty($network)) $requiredField = "Network Id required";
if ($isIPE && !is_array($tracking_ids)) $requiredField = "tracking_ids must be an array";

if (!empty($requiredField)) {
    header('HTTP/1.0 400 Parameters Required');
    respond(["status" => "fail", "msg" => $requiredField]);
}

if ($isIPE) {
    processIPERequest($tracking_ids, $result, $controller);
} elseif ($isModification) {
    processModificationRequest($body, $result, $ninModel);
} else {
    processVerificationRequest($body, $result, $controller);
}

// -------------------------------------------------------------------
// Helper functions
// -------------------------------------------------------------------

function respond($data) {
    echo json_encode($data);
    exit;
}

function processModificationRequest($body, $result, $ninModel) {
    $modificationType = isset($body->modification_type) ? $body->modification_type : $body->verification_type;
    $fee = $ninModel->getModificationFee($modificationType);

    if ($result["balance"] < $fee) {
        respond(["status" => "fail", "msg" => "Insufficient balance. Required: N$fee"]);
    }

    $request = $ninModel->createNINModificationRequest($result["userid"], $body, $fee);

    respond([
        "status" => "success",
        "ref" => $request['ref'],
        "fee" => $fee,
        "new_balance" => $request['new_balance'],
        "message" => "NIN modification request submitted successfully"
    ]);
}

function processIPERequest($trackingIds, $result, $controller) {
    $fee = 250;
    $userbalance = (float) $result["balance"];

    if ($userbalance < $fee) {
        respond(["status" => "fail", "msg" => "Insufficient balance. Required: N$fee"]);
    }

    $ipeResult = $controller->submitIPE($trackingIds);

    if (isset($ipeResult['status']) && $ipeResult['status'] === 'success') {
        $userid = (float) $result["userid"];
        $debit = $userbalance - $fee;
        $ref = 'IPE_' . time();
        $controller->debitUserBeforeTransaction($userid, $debit);
        $controller->recordTransaction($userid, 'IPE Clearance', 'IPE Clearance Processing', $ref, $fee, $userbalance, $debit, '0');
    }

    respond($ipeResult);
}

function processVerificationRequest($body, $result, $controller) {
    $fee = 1000;
    $userid = (float) $result["userid"];
    $userbalance = (float) $result["balance"];
    $ref = $body->ref ?? 'NIN_' . time();

    if ($userbalance < $fee) {
        respond(["status" => "fail", "msg" => "Insufficient balance. Required: N$fee"]);
    }

    $networkDetails = [];
    $verificationResult = $controller->verifyMyNIN($body, $networkDetails);

    if (!isset($verificationResult['status']) || $verificationResult['status'] === 'success') {
        $debit = $userbalance - $fee;
        $controller->debitUserBeforeTransaction($userid, $debit);
        $controller->recordTransaction($userid, 'NIN Verification', 'NIN Slip Verification', $ref, $fee, $userbalance, $debit, '0');
    }

    respond([
        "status" => $verificationResult['status'] ?? 'success',
        "msg" => $verificationResult['msg'] ?? 'NIN verification completed',
        "ref" => $ref,
        "data" => $verificationResult['data'] ?? $verificationResult
    ]);
}
