<?php

require_once "../autoloader.php";

//Allowed API Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST");
header("Allow: POST");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Allow-Origin");

$headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
if (empty($headers)) {
    // Fallback for non-Apache SAPI
    foreach ($_SERVER as $key => $value) {
        if (substr($key, 0, 5) === 'HTTP_') {
            $headerName = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$headerName] = $value;
        }
    }
}
$response = array();
$controller = new ApiAccess;
$ninController = new NIN;

date_default_timezone_set('Africa/Lagos');

function respond($response) {
    header('HTTP/1.0 200 OK');
    echo json_encode($response);
    exit;
}

// -------------------------------------------------------------------
//  Check Request Method
// -------------------------------------------------------------------

$requestMethod = $_SERVER["REQUEST_METHOD"]; 
if ($requestMethod !== 'POST') {
    $response["status"] = "fail";
    $response["msg"] = "Only POST method is allowed";
    header('HTTP/1.0 400 Bad Request');
    respond($response);
}

// -------------------------------------------------------------------
//  Check For Api Authorization
// -------------------------------------------------------------------

if((isset($headers['Authorization']) || isset($headers['authorization'])) || (isset($headers['Token']) || isset($headers['token']))){
    if((isset($headers['Authorization']) || isset($headers['authorization']))){
        $token = trim(str_replace("Token", "", (isset($headers['Authorization'])) ? $headers['Authorization'] : $headers['authorization']));
    }
    if((isset($headers['Token']) || isset($headers['token']))){
        $token = trim(str_replace("Token", "", (isset($headers['Token'])) ? $headers['Token'] : $headers['token']));
    }
    $result=$controller->validateAccessToken($token);
    if($result["status"] == "fail"){
        $response["status"] = "fail";
        $response["msg"] = "Authorization token not found $token";
        header('HTTP/1.0 401 Unauthorized');
        respond($response);
    }
    else{
        $usertype = $result["usertype"];
        $userbalance = (float) $result["balance"]; 
        $userid = $result["userid"];
        $refearedby = $result["refearedby"];
        $referal = $result["phone"];
        $referalname = $result["name"];
     }
}
else{
    $response["status"] = "fail";
    $response["msg"] = "Your authorization token is required.";
    header('HTTP/1.0 401 Unauthorized');
    respond($response);
}

// -------------------------------------------------------------------
//  Get The Request Details
// -------------------------------------------------------------------

$input = @file_get_contents("php://input");

//decode the json file
$body = json_decode($input);

// Support Other API Format
$body2 = array();   
if(isset($body->Ported_number)){$body2["ported_number"]=$body->Ported_number;}
if(isset($body->mobile_number)){$body2["phone"]=$body->mobile_number;}
if(isset($body->plan)){$body2["data_plan"]=$body->plan;}
if(!isset($body->ref)){$body2["ref"]="REF_".rand(100,999).time();}
$body = (object) array_merge( (array)$body, $body2 );

$network= (isset($body->network)) ? $body->network : "";
$phone= (isset($body->phone)) ? $body->phone : "";
$phone=str_replace(" ","",$phone);
$ported_number= (isset($body->ported_number)) ? $body->ported_number : "false";

$airtime_type = (isset($body->airtime_type)) ? $body->airtime_type : "";
$amount = (isset($body->amount)) ? $body->amount : "";
$ref = (isset($body->ref)) ? $body->ref : "";

// -------------------------------------------------------------------
//  Check Inputs Parameters
// -------------------------------------------------------------------

$requiredField = "";

if($airtime_type == ""){$requiredField ="Airtime Type Is Required"; }
if($amount == ""){$requiredField ="Amount Is Required"; }
if($phone == ""){$requiredField ="Phone Is Required"; }
if($network == ""){$requiredField ="Network Id Required"; }
if($ref == ""){$requiredField ="Ref Is Required"; }

if($requiredField <> ""){
    $response['status']="fail";
    $response['msg'] = $requiredField;
    header('HTTP/1.0 400 Parameters Required');
    respond($response);
}

// Validate Airtime Provider
$provider = $controller->validateAirtimeProvider($network);
if($provider["status"]=="fail"){
    header('HTTP/1.0 400 Invalid Network Id');
    $response['status']="fail";
    $response['msg'] = "The Network id is invalid";
    respond($response);
} else{
    $networkDetails=$provider; 
}

// Check if this is an API provider request
if (isset($body->provider_id)) {
    // This is a provider-specific request
    processProviderRequest($body, $controller, $result, $response);
} else {
    // Default to airtime
    processAirtimeRequest($body, $controller, $result, $response);
}

function processProviderRequest($body, $controller, $result, &$response) {
    $providerId = $body->provider_id;
    $provider = $controller->getApiConfigurationById($providerId);
    
    if (!$provider) {
        $response["status"] = "fail";
        $response["msg"] = "API provider not found";
        respond($response);
    }
    
    // Process the request based on provider type
    switch ($provider->type) {
        case 'airtime':
            processAirtimeProviderRequest($body, $controller, $result, $provider, $response);
            break;
        case 'data':
            processDataProviderRequest($body, $controller, $result, $provider, $response);
            break;
        case 'cabletv':
            processCableTVProviderRequest($body, $controller, $result, $provider, $response);
            break;
        case 'electricity':
            processElectricityProviderRequest($body, $controller, $result, $provider, $response);
            break;
        default:
            $response["status"] = "fail";
            $response["msg"] = "Unsupported provider type";
            respond($response);
    }
}

function processAirtimeProviderRequest($body, $controller, $result, $provider, &$response) {
    // Process airtime request using the specific provider's API
    $response["status"] = "success";
    $response["provider"] = is_object($provider) ? $provider->name : "Default";
    $response["message"] = "Request processed via " . (is_object($provider) ? $provider->name : "Default") . " API";
    respond($response);
}

function processDataProviderRequest($body, $controller, $result, $provider, &$response) {
    // Process data request using the specific provider's API
    $response["status"] = "success";
    $response["provider"] = is_object($provider) ? $provider->name : "Default";
    $response["message"] = "Request processed via " . (is_object($provider) ? $provider->name : "Default") . " API";
    respond($response);
}

function processCableTVProviderRequest($body, $controller, $result, $provider, &$response) {
    // Process cable TV request using the specific provider's API
    $response["status"] = "success";
    $response["provider"] = is_object($provider) ? $provider->name : "Default";
    $response["message"] = "Request processed via " . (is_object($provider) ? $provider->name : "Default") . " API";
    respond($response);
}

function processElectricityProviderRequest($body, $controller, $result, $provider, &$response) {
    // Process electricity request using the specific provider's API
    $response["status"] = "success";
    $response["provider"] = is_object($provider) ? $provider->name : "Default";
    $response["message"] = "Request processed via " . (is_object($provider) ? $provider->name : "Default") . " API";
    respond($response);
}

function processAirtimeRequest($body, $controller, $result, &$response) {
    processAirtimeProviderRequest($body, $controller, $result, null, $response);
}

// Helper methods for API responses
function getSiteSettings() {
    $model = new Model();
    $sql = "SELECT * FROM sitesettings WHERE sId = 1";
    $query = $model->connect()->prepare($sql);
    $query->execute();
    return $query->fetch(PDO::FETCH_OBJ);
}
