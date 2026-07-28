<?php
    // PAYMENT POINT API WEBHOOK NOTIFICATION
    
    //Auto Load Classes
    require_once("../autoloader.php");
    require_once("../../core/helpers/vendor/autoload.php");
    header('Content-Type: application/json');
    date_default_timezone_set('Africa/Lagos');
    
    $headers = getallheaders();
    $response = array();
    $controller = new ApiAccess;
    
    $input = @file_get_contents("php://input");
    $res = json_decode($input);
    
    $email = $res->customer->email ?? $res->email ?? '';
    $amount = $res->amount ?? 0;
    $transactionReference = $res->reference ?? $res->transactionReference ?? '';
    
    $check = $controller->verifyPaymentpointRef($email, $headers, $input);
    
    if ($check && $check->status == "success"):
            $userid = $check->userid;
            $userbalance = $check->balance;
            $email = $check->useremail;
            $charges = (float) $check->charges;
            $chargestype = $check->chargestype;
            $amount = (float) $amount;
            
            if ($chargestype == "flat"): 
                $amounttosave = $amount - $charges;
                $chargesText = "N" . $charges;
            else: 
                $amounttosave = $amount - ($amount * ($charges / 100)); 
                $chargesText = $charges . "%";
            endif;
            
            $servicename = "Wallet Topup";
            $servicedesc = "Wallet funding of N{$amount} via Payment Point transfer with a service charges of $chargesText";
            $servicedesc .= ". Your wallet has been credited with N{$amounttosave}";
            $transactionReference = "PAYMENTPOINT_" . $transactionReference;
            
            $result = $controller->recordPayvesselTransaction($userid, $servicename, $servicedesc, $amounttosave, $userbalance, $transactionReference, "0");
            $message = $servicedesc . ". Your transaction reference is $transactionReference";
            
            $controller->sendEmailNotification($servicename, $message, $email);
            
            echo json_encode(["status" => "success", "message" => "Processed"]);
            http_response_code(200);
            exit();
    else:
        echo json_encode(["status" => "error", "message" => "Unauthorized"]);
        http_response_code(401);
        exit();
    endif;
?>
