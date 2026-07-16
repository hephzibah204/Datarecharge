<?php
require_once 'conn.php';

/* ================= Helpers ================= */

function logToFile($m) {
    file_put_contents('buy_data.log', '['.date('Y-m-d H:i:s')."] $m\n", FILE_APPEND);
}

function jsonOut($arr, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($arr);
    exit;
}

/** Build https://host/api/user from any purchase URL like https://host/api/data */
function buildUserAuthUrl(string $purchaseUrl): string {
    $p = parse_url($purchaseUrl);
    if (!$p || empty($p['scheme']) || empty($p['host'])) return 'https://legitdataway.com/api/user';
    return $p['scheme'].'://'.$p['host'].'/api/user';
}

/** Two-step token flow: Basic (base64 user:pass) -> AccessToken */
function twoStep_get_token(string $userUrl, string $basicBase64): string {
    // Ensure base64 padding
    $pad = strlen($basicBase64) % 4;
    if ($pad) $basicBase64 .= str_repeat('=', 4 - $pad);

    $ch = curl_init($userUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            "Authorization: Basic {$basicBase64}",
            "Accept: application/json",
        ],
    ]);
    $res = curl_exec($ch);
    if ($res === false) {
        $err = curl_error($ch);
        curl_close($ch);
        logToFile("Two-step token cURL error: $err");
        throw new RuntimeException("Token request failed: $err");
    }
    curl_close($ch);
    logToFile("Two-step token response: $res");
    $data = json_decode($res, true);
    if (!is_array($data) || ($data['status'] ?? '') !== 'success' || empty($data['AccessToken'])) {
        throw new RuntimeException("Token request not successful. Response: $res");
    }
    return $data['AccessToken'];
}

/** Two-step purchase: Authorization: Token <AccessToken> */
function twoStep_purchase(string $purchaseUrl, string $accessToken, array $payload): array {
    $ch = curl_init($purchaseUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            "Authorization: Token {$accessToken}",
            "Content-Type: application/json",
            "Accept: application/json",
        ],
        CURLOPT_POSTFIELDS     => json_encode($payload),
    ]);
    $res = curl_exec($ch);
    if ($res === false) {
        $err = curl_error($ch);
        curl_close($ch);
        logToFile("Two-step purchase cURL error: $err");
        throw new RuntimeException("Purchase request failed: $err");
    }
    curl_close($ch);
    logToFile("Two-step purchase response: $res");
    return json_decode($res, true) ?: ['raw' => $res];
}

/* ================= Input ================= */

$token          = $_POST['token']         ?? '';
$mobile_number  = $_POST['phone']         ?? '';
$network_input  = $_POST['network']       ?? '';
$plan_id        = $_POST['data_plan']     ?? '';
$ported_number  = $_POST['ported_number'] ?? '';
$type           = $_POST['type']          ?? '';  // e.g. GIFTING/SME/VTU/etc
$description_in = $_POST['description']   ?? "Data Purchase for $mobile_number";
$request_id     = $_POST['request-id']    ?? '';
$service        = "Data From App";
$description    = "$description_in for $mobile_number";

if (!$token || !$mobile_number || !$network_input || !$plan_id || !$request_id) {
    jsonOut(["success" => false, "message" => "Missing required parameters."], 422);
}

/* ================= Network mapping ================= */

$network_map = [ '1' => '1', '2' => '2', '3' => '3', '4' => '4' ];
$network_id = $network_map[$network_input] ?? $network_input;

$network_names = [
    '1' => 'MTN',
    '3' => 'GLO',
    '4' => '9MOBILE',
    '2' => 'AIRTEL'
];
$network_name  = $network_names[$network_id] ?? 'UNKNOWN';

/* ================= Load API config ================= */

$apilink_name = "{$network_name}{$type}Provider";
$apikey_name  = "{$network_name}{$type}Api";

$stmt = $conn->prepare("SELECT value FROM apiconfigs WHERE name = ?");
$stmt->bind_param("s", $apilink_name);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    logToFile("API Link not found for $apilink_name");
    jsonOut(["success" => false, "message" => "API Link not found."], 422);
}
$apilink = rtrim($res->fetch_assoc()['value'] ?? '', '/');
$stmt->close();

$stmt = $conn->prepare("SELECT value FROM apiconfigs WHERE name = ?");
$stmt->bind_param("s", $apikey_name);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    logToFile("API Key not found for $apikey_name");
    jsonOut(["success" => false, "message" => "API Key not found."], 422);
}
$apikey = trim($res->fetch_assoc()['value'] ?? '');
$stmt->close();

logToFile("API Configs: apilink={$apilink} | apikey(len)=".strlen($apikey));

/* ================= Load user ================= */

$stmt = $conn->prepare("SELECT sWallet, sId, sType FROM subscribers WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$user_res = $stmt->get_result();
if ($user_res->num_rows === 0) {
    logToFile("User not found for token: $token");
    jsonOut(["success" => false, "message" => "User not found."], 404);
}
$user_row     = $user_res->fetch_assoc();
$user_balance = (float)$user_row['sWallet'];
$sId          = $user_row['sId'];
$sType        = $user_row['sType'];
$stmt->close();

$selling_key = ($sType === '1') ? 'userprice' : (($sType === '2') ? 'agentprice' : 'vendorprice');

logToFile("User sId={$sId} | balance={$user_balance} | price_key={$selling_key}");

/* ================= Load plan ================= */

$stmt = $conn->prepare("SELECT price, userprice, agentprice, vendorprice FROM dataplans WHERE planid = ?");
$stmt->bind_param("i", $plan_id);
$stmt->execute();
$plan_res = $stmt->get_result();
if ($plan_res->num_rows === 0) {
    logToFile("Plan not found id=$plan_id");
    jsonOut(["success" => false, "message" => "Data plan not found."], 404);
}
$plan = $plan_res->fetch_assoc();
$stmt->close();

$api_price     = (float)$plan['price'];
$selling_price = (float)$plan[$selling_key];
$profit        = $selling_price - $api_price;

logToFile("Plan: api_price={$api_price} | selling={$selling_price} | profit={$profit}");

/* ================= Wallet check ================= */

if ($user_balance < $selling_price) {
    logToFile("Insufficient balance: {$user_balance} < {$selling_price}");
    jsonOut(["success" => false, "message" => "Insufficient balance."], 422);
}

/* ================= Provider detection + payloads ================= */

$isSmeplug   = (stripos($apilink, 'smeplug.ng') !== false);
$host        = parse_url($apilink, PHP_URL_HOST) ?: '';
$isTwoStep   = (
    stripos($host, 'legitdataway.com') !== false ||
    stripos($host, 'n3tdata.com') !== false ||
    stripos($host, 'bilal') !== false // broad match for your "bilalsadasub" host
);

// Default provider payload
$payload_default = [
    'network'        => (int)$network_id,
    'mobile_number'  => $mobile_number,
    'plan'           => (int)$plan_id,
    'Ported_number'  => filter_var($ported_number, FILTER_VALIDATE_BOOLEAN),
];

// SMEplug payload
$payload_smeplug = [
    'network_id' => (int)$network_id,
    'plan_id'    => (int)$plan_id,
    'phone'      => $mobile_number,
];

// Two-step (Legit-style) payload
$payload_twostep = [
    'network'    => (int)$network_id,
    'phone'      => $mobile_number,
    'data_plan'  => (int)$plan_id,
    'bypass'     => false,
    'request-id' => $request_id,
];

/* ================= Call provider ================= */

try {
    if ($isTwoStep) {
        $userUrl = buildUserAuthUrl($apilink);
        $accessToken = twoStep_get_token($userUrl, $apikey);
        $resp = twoStep_purchase($apilink, $accessToken, $payload_twostep);
    } else if ($isSmeplug) {
        $headers = [
            "Authorization: Bearer {$apikey}",
            "Content-Type: application/json",
            "Accept: application/json",
        ];
        $ch = curl_init($apilink);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => json_encode($payload_smeplug),
        ]);
        $raw = curl_exec($ch);
        if ($raw === false) {
            $err = curl_error($ch);
            curl_close($ch);
            logToFile("SMEplug cURL error: $err");
            throw new RuntimeException("API connection failed: $err");
        }
        curl_close($ch);
        logToFile("API Response: $raw");
        $resp = json_decode($raw, true) ?: ['raw' => $raw];
    } else {
        // Default: simple Token auth
        $headers = [
            "Authorization: Token {$apikey}",
            "Content-Type: application/json",
            "Accept: application/json",
        ];
        $ch = curl_init($apilink);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => json_encode($payload_default),
        ]);
        $raw = curl_exec($ch);
        if ($raw === false) {
            $err = curl_error($ch);
            curl_close($ch);
            logToFile("Default provider cURL error: $err");
            throw new RuntimeException("API connection failed: $err");
        }
        curl_close($ch);
        logToFile("API Response: $raw");
        $resp = json_decode($raw, true) ?: ['raw' => $raw];
    }
} catch (Throwable $e) {
    logToFile("Request error: ".$e->getMessage());
    jsonOut(["success" => false, "message" => $e->getMessage()], 500);
}

/* ================= Normalize status ================= */

$isSuccess = false;
$isPending = false;
$reasonMsg = null;

if ($isSmeplug) {
    // Your log example:
    // {"status":true,"data":{"current_status":"success", ...}}
    $statusBool  = $resp['status'] ?? null;          // true/false
    $currentStat = strtolower($resp['data']['current_status'] ?? '');
    if ($statusBool === true && $currentStat === 'success') $isSuccess = true;
    if ($statusBool === true && in_array($currentStat, ['processing','process','pending'])) $isPending = true;
    $reasonMsg = $resp['data']['msg'] ?? ($resp['message'] ?? null);
} elseif ($isTwoStep) {
    // Legit / n3tdata / bilal… often: status = "success" or "process"
    $st = strtolower($resp['status'] ?? '');
    if (in_array($st, ['success','successful'])) $isSuccess = true;
    if (in_array($st, ['process','processing','pending'])) $isPending = true;
    $reasonMsg = $resp['message'] ?? null;
} else {
    // Generic: status / Status may hold success
    $st1 = strtolower($resp['status'] ?? '');
    $st2 = strtolower($resp['Status'] ?? '');
    if (in_array($st1, ['success','true','successful'], true) ||
        in_array($st2, ['success','true','successful'], true)) {
        $isSuccess = true;
    }
    if (in_array($st1, ['process','processing','pending'], true) ||
        in_array($st2, ['process','processing','pending'], true)) {
        $isPending = true;
    }
    $reasonMsg = $resp['message'] ?? ($resp['error'] ?? null);
}

/* ================= Record + debit =================
   Policy:
   - Success  => debit immediately, status=0 (or whatever you use for success).
   - Pending  => NO debit; insert transaction with status=2 (pending).
   - Failure  => no debit; return message.
   Change the statuses to fit your table semantics.
*/

if ($isSuccess) {
    $new_balance = $user_balance - $selling_price;
    $timestamp   = date("Y-m-d H:i:s");

    // Update wallet
    $stmt = $conn->prepare("UPDATE subscribers SET sWallet = ? WHERE token = ?");
    $stmt->bind_param("ds", $new_balance, $token);
    $stmt->execute();
    $stmt->close();

    // Insert transaction (status=0 = success)
    $stmt = $conn->prepare("
        INSERT INTO transactions
        (sId, transref, servicename, servicedesc, amount, status, oldbal, newbal, profit, date)
        VALUES (?, ?, ?, ?, ?, 0, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "isssdddss",
        $sId,
        $request_id,
        $service,
        $description,
        $selling_price,
        $user_balance,
        $new_balance,
        $profit,
        $timestamp
    );
    $stmt->execute();
    $stmt->close();

    logToFile("Transaction OK: {$request_id} | newbal={$new_balance}");

    jsonOut([
        "success" => true,
        "message" => $reasonMsg ?: "Data purchase successful",
        "provider_response" => $resp
    ]);
}

if ($isPending) {
    // Insert PENDING record (status=2)
    $timestamp = date("Y-m-d H:i:s");
    $stmt = $conn->prepare("
        INSERT INTO transactions
        (sId, transref, servicename, servicedesc, amount, status, oldbal, newbal, profit, date)
        VALUES (?, ?, ?, ?, ?, 2, ?, ?, ?, ?)
    ");
    // No debit yet; newbal remains same as old
    $stmt->bind_param(
        "isssdddss",
        $sId,
        $request_id,
        $service,
        $description,
        $selling_price,
        $user_balance,
        $user_balance,
        0.0, // profit unknown until final success
        $timestamp
    );
    $stmt->execute();
    $stmt->close();

    logToFile("Transaction PENDING: {$request_id}");
    jsonOut([
        "success" => false,
        "pending" => true,
        "message" => $reasonMsg ?: "Transaction is processing. Please wait.",
        "provider_response" => $resp
    ], 202);
}

/* Failure */
$msg = $reasonMsg ?: "Transaction failed";
logToFile("Transaction failed: {$request_id} | msg={$msg}");
jsonOut(["success" => false, "message" => $msg, "provider_response" => $resp], 400);