<?php
    //Auto Load Classes
    require_once("../autoloader.php");

    //Allowed API Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header("Access-Control-Allow-Methods: POST");
    header("Allow: POST");
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Allow-Origin");

    $headers = apache_request_headers();
    $response = array();
    $controller = new ApiAccess;
    $airtimeController = new PNV;
    date_default_timezone_set('Africa/Lagos');
            


    // -------------------------------------------------------------------
    //  Check Request Method
    // -------------------------------------------------------------------

    $requestMethod = $_SERVER["REQUEST_METHOD"]; 
    if ($requestMethod !== 'POST') {
        header('HTTP/1.0 400 Bad Request');
        $response["status"] = "fail";
        $response["msg"] = "Only POST method is allowed";
        echo json_encode($response); exit(); 
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
            header('HTTP/1.0 401 Unauthorized');
            $response["status"] = "fail";
            $response["msg"] = "Authorization token not found $token";
            echo json_encode($response); exit(); 
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
        header('HTTP/1.0 401 Unauthorized');
        // tell the user no products found
        $response["status"] = "fail";
        $response["msg"] = "Your authorization token is required.";
        echo json_encode($response); exit(); 
    }

    // -------------------------------------------------------------------
    //  Get The Request Details
    // -------------------------------------------------------------------

    $input = @file_get_contents("php://input");

    //decode the json file
    $body = json_decode($input);
    
    // Support Other API Format
    $body2 = array();   
    $body = (object) array_merge( (array)$body, $body2 );

    $network= (isset($body->network)) ? $body->network : "";
    $phone= (isset($body->phone)) ? $body->phone : "";
    
    
    $ref= (isset($body->ref)) ? $body->ref : "";
    $body->userID = $result["userid"];
    
    // -------------------------------------------------------------------
    //  Check Inputs Parameters
    // -------------------------------------------------------------------

    $requiredField = "";
   
    if($phone == ""){$requiredField ="Phone Is Requiredaaaah"; }
    
    if($requiredField <> ""){
        header('HTTP/1.0 400 Parameters Required');
        $response['status']="fail";
        $response['msg'] = $requiredField;
        echo json_encode($response);
        exit();
    }

     $result = $controller->verifyNetworkId(1);
    if($result["status"]=="fail"){
        header('HTTP/1.0 400 Invalid Network Id');
        $response['status']="fail";
        $response['msg'] = "The Network id is invalid";
        echo json_encode($response);
        exit();
    }
    else{
        $networkDetails=$result; 
    }
    
    $amountopay = 500;
    $slip = strtolower($body->slip ?? 'regular');
    try {
        $model = new Model();
        $db = $model->connectDb();
        $slipKey = str_replace(['slip', ' ', '_', '-'], '', $slip);
        $stmt = $db->prepare("SELECT user_price FROM nin_price WHERE LOWER(REPLACE(REPLACE(slip_name, ' ', ''), '_', '')) LIKE :slip LIMIT 1");
        $stmt->execute([':slip' => '%' . $slipKey . '%']);
        $matchedPrice = $stmt->fetchColumn();
        if ($matchedPrice !== false && (float)$matchedPrice > 0) {
            $amountopay = (float)$matchedPrice;
        } else {
            if ($slip == "regular") { $amountopay = 500; }
            elseif ($slip == "standard") { $amountopay = 1000; }
            elseif ($slip == "premium") { $amountopay = 1500; }
        }
    } catch (\Throwable $e) {
        $amountopay = 500;
    }
    // -------------------------------------------------------------------
    //  Check Id User Balance Can Perform The Transaction
    // -------------------------------------------------------------------
    if($amountopay > $userbalance || $amountopay < 0){
            header('HTTP/1.0 400 Insufficient Balance');
            $response['status']="fail";
            $response['msg'] = "Insufficient balance fund your wallet and try again";
            echo json_encode($response);
            exit();
    }

   
    $result = $controller->checkIfTransactionExist($ref);
    if($result["status"]=="fail"){
        header('HTTP/1.0 400 Transaction Ref Already Exist');
        $response['status']="fail";
        $response['msg'] = "Transaction Ref Already Exist";
        echo json_encode($response);
        exit();
    }

 
    $profit = $amountopay * 0.1;

    // -------------------------------------------------------------------
    // Process PNV Verification
    // -------------------------------------------------------------------

    $servicename = "PNV Verification";
    $servicedesc = "Personal Name Verification for {$phone}";
    
     
    $result = $controller->checkTransactionDuplicate($servicename,$servicedesc);
    if($result["status"]=="fail"){
        header('HTTP/1.0 400 Possible Transaction Duplicate, Please Verify Transaction & Try Again After 60 Seconds');
        $response['status']="fail";
        $response['msg'] = "Possible Transaction Duplicate, Please Verify Transaction & Try Again After 60 Seconds";
        echo json_encode($response);
        exit();
    }
   
    // Debit User Before Performing The Transaction
    $oldbalance = (float) $userbalance;
    $deibt = $oldbalance - $amountopay;
    $checkDebit=$controller->debitUserBeforeTransaction($userid,$deibt);
    
    if($checkDebit <> "success"){
        header('HTTP/1.0 400 Could Not Complete Transaction');
        $response['status']="fail";
        $response['msg'] = "Could Not Complete Transaction";
        echo json_encode($response);
        exit();
    }
    
    // -------------------------------------------------------------------
    //  Record Transaction As Processing With Status 5
    // -------------------------------------------------------------------
    $transRecord=$controller->recordTransaction($userid,'PNV Verification','Personal Name Verification for '.$phone,$amountopay,$userbalance,$body->ref,"5");
        
    // -------------------------------------------------------------------
    //  Send Request To Purchase Airtime
    // -------------------------------------------------------------------
   
    $result = $airtimeController->verifyMyPNV($body,$networkDetails);
    
    // -------------------------------------------------------------------
    // Debit User Wallet & Record Transaction
    // -------------------------------------------------------------------
    if($result["status"]=="success"){
        if($refearedby <> ""){ $controller->creditReferalBonus($referal,$referalname,$refearedby,$servicename); }
        $controller->updateTransactionStatus($userid,$body->ref,$amountopay,"0");
        $controller->saveProfit($body->ref,$profit);
        $response['status']="success";
        $response['Status']="successful";
        header('HTTP/1.0 200 Transaction Successful');
        echo json_encode($response);
        exit();
    } 
    elseif($result["status"]=="fail"){
        header('HTTP/1.0 400 Transaction Failed');
        $response['status']="fail";
        $response['Status']="failed";
        $response['msg'] = $result["msg"];
        $controller->updateTransactionStatus($userid,$body->ref,$amountopay,"1");
        echo json_encode($response);
        exit();
    }
    else{
        $response['status']="processing";
        $response['Status']="processing";
        header('HTTP/1.0 200 Transaction Processing');
        echo json_encode($response);
        exit(); 
    }

?>