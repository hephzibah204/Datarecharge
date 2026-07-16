<?php
require_once "../autoloader.php";

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST");
header("Allow: POST");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Allow-Origin");

$headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
$response = [];
$controller = new CAC;
date_default_timezone_set('Africa/Lagos');

if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    http_response_code(400);
    echo json_encode(["status" => "fail", "msg" => "Only POST method is allowed"]);
    exit();
}

$token = '';
if (!empty($headers['Authorization'])) $token = trim(str_replace("Token ", "", $headers['Authorization']));
elseif (!empty($headers['authorization'])) $token = trim(str_replace("Token ", "", $headers['authorization']));
elseif (!empty($headers['Token'])) $token = trim(str_replace("Token ", "", $headers['Token']));
elseif (!empty($headers['token'])) $token = trim(str_replace("Token ", "", $headers['token']));

if (empty($token)) {
    http_response_code(401);
    echo json_encode(["status" => "fail", "msg" => "Your authorization token is required."]);
    exit();
}

$apiAccess = new ApiAccess;
$result = $apiAccess->validateAccessToken($token);
if ($result["status"] == "fail") {
    http_response_code(401);
    echo json_encode(["status" => "fail", "msg" => "Authorization token not found"]);
    exit();
}

$input = @file_get_contents("php://input");
$body = json_decode($input);
if (!$body) {
    echo json_encode(["status" => "fail", "msg" => "Invalid JSON body"]);
    exit();
}

if (!isset($body->rcNumber) && !isset($body->phone)) {
    echo json_encode(["status" => "fail", "msg" => "RC Number is required"]);
    exit();
}

$body->ref = $body->ref ?? 'CAC_' . time();
$verificationResult = $controller->verifyMyCAC($body, []);

echo json_encode([
    "status" => $verificationResult['status'] ?? 'fail',
    "msg" => $verificationResult['msg'] ?? ($verificationResult['status'] == 'success' ? 'CAC verification completed' : 'Verification failed'),
    "ref" => $body->ref,
    "data" => $verificationResult['data'] ?? []
]);
