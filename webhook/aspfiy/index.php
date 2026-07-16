<?php
    
    //KUDA API WEBHOOK NOTIFICATION
    
    //Auto Load Classes
    require_once("../autoloader.php");
    require_once("../../core/helpers/vendor/autoload.php");
    header('Content-Type: application/json');
    date_default_timezone_set('Africa/Lagos');
    
    $headers = getallheaders();
    $response = array();
    $controller = new ApiAccess;
    //ini_set("display_errors",1); 
    //error_reporting(E_ALL);
    $input = @file_get_contents("php://input");
    $res = json_decode($input);
    file_put_contents("aspfiy_complete.txt",$input);
    file_put_contents("aspfiy_haed.txt",json_encode($headers));
    if (isset($headers["x-wiaxy-signature"])){
        $key = $headers["x-wiaxy-signature"];
    } elseif(isset($headers["X-Wiaxy-Signature"])) {
        $key = $headers["X-Wiaxy-Signature"];
    }else{
        $key = "";
    } 
   
    $amount = (isset($res->data->amount)) ? $res->data->amount : "";
    $email = (isset($res->data->customer->email)) ? $res->data->customer->email : "";
    $transactionReference = (isset($res->data->transaction_ref)) ? $res->data->transaction_ref : "";
    
    if($key == ""):
        echo "UnAutorized"; http_response_code(401); exit();
    endif;
    
    //Email, Amount
    if($amount == "" || $email == ""):
        echo "UnAutorized"; http_response_code(401); exit();
    endif;
     
    //Verify The Provided Username & Password
    
    $check= $controller->verifyAspfiyNotification($key,$email);
    
    if($check->status == "success"):
            
            $userid = $check->userid;
            $userbalance = $check->balance;
            $email = $check->useremail;
            $charges = (float) $check->charges;
            $chargestype = $check->chargestype;
            
            
            
            if($chargestype == "flat"): 
                $amounttosave = $amount - $charges;
                $chargesText ="N".$charges;
            else: 
                $amounttosave = $amount - ($amount * ($charges/100)); 
                $chargesText = $charges."%";
            endif;
            
            $servicename = "Wallet Topup";
            $servicedesc = "Wallet funding of N{$amount} via Paga bank transfer with a service charges of $chargesText";
            $servicedesc.=". You wallet have been credited with N{$amounttosave}";
            $transactionReference = "PAGA_".$transactionReference;
            $result = $controller->recordAspfiyTransaction($userid,$servicename,$servicedesc,$amounttosave,$userbalance,$transactionReference,"0");
            $message = $servicedesc . ". Your transaction reference is $transactionReference";
            
            //Send Email Notification
            
            $controller->sendEmailNotification($servicename,$message,$email);
            

            echo "Success";
            http_response_code(200);
            exit();

    else:
        echo "UnAutorized"; http_response_code(401); exit();
    endif;
    
?>