<?php

// NIN Modification API Endpoint
// NEW dedicated API for NIN modification requests
// Uses proper model and controller for modification workflows

// Auto Load Classes
require_once "../autoloader.php";

//Allowed API Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST");
header("Allow: POST");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Allow-Origin");

$headers = apache_request_headers();
$response = array();
$controller = new NINModificationController();  // Use new modification controller
$NINController = new NINModificationModel();  // New model for modification logic

date_default_timezone_set('Africa/Lagos');

// -------------------------------------------------------------------
//  Check Request Method
// -------------------------------------------------------------------

$requestMethod = $_SERVER["REQUEST_METHOD"]; 
if ($requestMethod !== 'POST') {
    $response["status"] = "fail";
    $response["msg"] = "Only POST method is allowed";
    $this->respond($response);
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
        // tell the user no products found
        $response["status"] = "fail";
        $response["msg"] = "Authorization token not found $token";
        header('HTTP/1.0 401 Unauthorized');
        $this->respond($response);
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
    $this->respond($response);
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
if(!isset($body->ref)){$body2["ref"]="NINREF_".rand(100,999).time();}
$body = (object) array_merge( (array)$body, $body2 );

$data_plan = (isset($body->data_plan)) ? $body->data_plan : "";
$phone = (isset($body->phone)) ? $body->phone : "";
$phone=str_replace(" ","",$phone);
$network= (isset($body->network)) ? $body->network : "";
$ref= (isset($body->ref)) ? $body->ref : "";

// Check if this is a modification or verification request
if (isset($body->modification_type) || (isset($body->verification_type) && $body->verification_type == 'nin_verification')) {
    // Process modification request
    $this->processNINModificationRequest($body, $controller, $result, $response);
} else {
    // Default to NIN verification
    $this->processNINVerificationRequest($body, $controller, $result, $response);
}

function respond($response) {
    header('HTTP/1.0 200 OK');
    echo json_encode($response);
    exit;
}

function processNINModificationRequest($body, $controller, $result, &$response) {
    $modificationType = isset($body->modification_type) ? $body->modification_type : $body->verification_type;
    $fee = $controller->getModificationFee($modificationType);
    
    if ($result["balance"] < $fee) {
        $response["status"] = "fail";
        $response["msg"] = "Insufficient balance. Required: N$fee";
        $this->respond($response);
    }
    
    $request = $controller->createNINModificationRequest($result["userid"], $modificationType, $body, $fee);
    
    $newBalance = $result["balance"] - $fee;
    $controller->debitUserBeforeTransaction($result["userid"], $newBalance, "NIN Modification Fee - $modificationType", $request['ref']);
    
    $response = [
        "status" => "success",
        "ref" => $request['ref'],
        "fee" => $fee,
        "new_balance" => $newBalance,
        "message" => "NIN modification request submitted successfully"
    ];
    $this->respond($response);
}

function processNINVerificationRequest($body, $controller, $result, &$response) {
    $verificationType = $body->verification_type ?? 'nin_verification';
    $fee = $controller->getModificationFee($verificationType);
    
    if ($result["balance"] < $fee) {
        $response["status"] = "fail";
        $response["msg"] = "Insufficient balance. Required: N$fee";
        $this->respond($response);
    }
    
    $request = $controller->createNINModificationRequest($result["userid"], $verificationType, $body, $fee);
    
    $newBalance = $result["balance"] - $fee;
    $controller->debitUserBeforeTransaction($result["userid"], $newBalance, "NIN Verification Fee", $request['ref']);
    
    $response = [
        "status" => "success",
        "ref" => $request['ref'],
        "fee" => $fee,
        "new_balance" => $newBalance,
        "message" => "NIN verification request submitted successfully"
    ];
    $this->respond($response);
}

// Helper methods for API responses
function getModificationFee($type) {
    $settings = $this->getSiteSettings();
    $fees = [
        'name' => $settings->fee_name_mod ?? 5000,
        'phone' => $settings->fee_phone_mod ?? 5000,
        'address' => $settings->fee_address_mod ?? 4000,
        'email' => $settings->fee_email_mod ?? 4000,
        'dob' => $settings->fee_dob_mod ?? 28574,
        'lga' => $settings->fee_lga_mod ?? 3000,
        'gender' => $settings->fee_gender_mod ?? 8000,
        'marital_status' => $settings->fee_marital_mod ?? 6000,
        'nin_verification' => $settings->fee_nin_verification ?? 1000,
        'affidavit' => $settings->fee_affidavit ?? 5000,
        'birth_certificate' => $settings->fee_birth_certificate ?? 10000,
    ];
    return (float)($fees[$type] ?? 5000);
}

function getSiteSettings() {
    $sql = "SELECT * FROM sitesettings WHERE sId = 1";
    $query = $this->connect()->prepare($sql);
    $query->execute();
    return $query->fetch(PDO::FETCH_OBJ);
}
