<?php

    class Cable extends ApiAccess{
        

        //Verify Cable Tv Number
		public function validateIUCNumber($body,$cableid,$provider){

			$response = array();
            $details=$this->model->getApiDetails();
            
            //Get Ap Details
            $host = self::getConfigValue($details,"cableVerificationProvider");
            $apiKey = self::getConfigValue($details,"cableVerificationApi");

            //Set Authentication Type And Parameters
            $aunType = "Basic";

            if(strpos($host, 'n3tdata.com') !== false){
                $aunType = "Basic";
                $host = $host . "?iuc=".$body->iucnumber."&cable=".$cableid;
            }
            elseif (strpos($host, 'n3tdata247') !== false){
                $aunType = "Basic";
                $host = $host . "?iuc=".$body->iucnumber."&cable=".$cableid;
            }
            elseif (strpos($host, 'nabatulu') !== false){
                $aunType = "Basic";
                $host = $host . "?iuc=".$body->iucnumber."&cable=".$cableid;
            }
            elseif (strpos($host, 'legitdataway') !== false){
                $aunType = "Basic";
                $host = $host . "?iuc=".$body->iucnumber."&cable=".$cableid;
            }
            else{
                $aunType = "Token";
                $host = $host . "?smart_card_number=".$body->iucnumber."&cablename=".$provider;
            }
             
            // ------------------------------------------
            //  Verify Cable Plan
            // ------------------------------------------
        
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $host,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                "Authorization: $aunType $apiKey"
            ),
            ));

            $exereq = curl_exec($curl);

            $err = curl_error($curl);

            if($err){
                $response["status"] = "fail";
                $response["msg"] = "Server Connection Error";
                file_put_contents("iuc_error_log2.txt",json_encode($response).$err);
                curl_close($curl);
                return $response;
            }
 
            $result=json_decode($exereq);
            curl_close($curl);
            
            
            if(isset($result->name)){
                $response["status"] = "success";
                $response["msg"] = $result->name;
                $response["others"] = $result;
            }
            else{
                $response["status"] = "fail";
                file_put_contents("iuc_error_log.txt",json_encode($result));
            }

            return $response;
		}

        //Purchase Cable Tv
        public function purchaseCableTv($body,$cableid,$provider,$cableplan){

			$response = array();
            $details=$this->model->getApiDetails();

            $host = self::getConfigValue($details,"cableProvider");
            $apiKey = self::getConfigValue($details,"cableApi");

            //Check If API Is Is Using N3TData Or Bilalsubs
            if(strpos($host, 'n3tdata247') !== false){
                $hostuserurl="https://n3tdata247.com/api/user/";
                return $this->purchaseCableWithBasicAuthentication($body,$host,$hostuserurl,$apiKey,$cableid,$provider,$cableplan);
            }
            if(strpos($host, 'n3tdata') !== false){
                $hostuserurl="https://n3tdata.com/api/user/";
                return $this->purchaseCableWithBasicAuthentication($body,$host,$hostuserurl,$apiKey,$cableid,$provider,$cableplan);
            }

            if(strpos($host, 'bilalsadasub') !== false){
                $hostuserurl="https://bilalsadasub.com/api/user/";
                return $this->purchaseCableWithBasicAuthentication($body,$host,$hostuserurl,$apiKey,$cableid,$provider,$cableplan);
            }

            if(strpos($host, 'legitdataway') !== false){
                $hostuserurl="https://legitdataway.com/api/user/";
                return $this->purchaseCableWithBasicAuthentication($body,$host,$hostuserurl,$apiKey,$cableid,$provider,$cableplan);
            }

           
            // ------------------------------------------
            //  Purchase Cable Plan
            // ------------------------------------------
        
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $host,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "cablename": "'.$cableid.'",
                "smart_card_number": "'.$body->iucnumber.'",
                "cableplan":"'.$cableplan.'"
            }',
            
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                "Authorization: Token $apiKey"
            ),
            ));

            $exereq = curl_exec($curl);
            $err = curl_error($curl);
            
            if($err){
                $response["status"] = "fail";
                $response["msg"] = "Server Connection Error";
                $response["api_response_log"]=json_encode($response)." : ".$err;
                file_put_contents("cable_error_log2.txt",json_encode($response).$err);
                curl_close($curl);
                return $response;
            }
 
            $result=json_decode($exereq);
            curl_close($curl);

             //Log API Response To Database
             $response["api_response_log"]=$exereq;

             //Get API Status
             if(isset($result->Status)){$apiStatus = strtolower($result->Status);}
             elseif(isset($result->status)){$apiStatus = strtolower($result->status);}
             else{$apiStatus = "";}
            
            if($apiStatus == 'successful' || $apiStatus == 'success'){
                $response["status"] = "success";
            }
            elseif($apiStatus == 'failed' || $apiStatus == 'fail'){
                $response["status"] = "fail";
                $response["msg"] = "Transaction Failed, Please Try Again Later";

                //If Server Returns Message, Capture It If Message Is Not About A Low Wallet Balance
                //If Server Returns Message, Capture It If Message Is Not About A Low Wallet Balance
                if(isset($result->msg)){
                    if(strpos($result->msg, 'balance') !== false || strpos($result->msg, 'insufficient') !== false){$response["msg"] ="Unable To Complete Transaction: Please Report To Admin. Error Code BB.";}
                    else{$response["msg"] = $result->msg;}
                }

                //If Server Returns Message, Capture It If Message Is Not About A Low Wallet Balance
                if(isset($result->error[0])){
                    if(strpos($result->error[0], 'balance') !== false || strpos($result->error[0], 'insufficient') !== false){$response["msg"] ="Unable To Complete Transaction: Please Report To Admin. Error Code BB.";}
                    else{$response["msg"] = $result->error[0];}
                }   
                
                //If Server Returns Message, Capture It If Message Is Not About A Low Wallet Balance
                if(isset($result->message)){
                    if(strpos($result->message, 'balance') !== false || strpos($result->message, 'insufficient') !== false){$response["msg"] ="Unable To Complete Transaction: Please Report To Admin. Error Code BB.";}
                    else{$response["msg"] = $result->message;}
                }

                //Log Error On Server
                file_put_contents("cabletv_fail_log.txt",json_encode($result));
            }
            elseif($apiStatus == 'processing' || $apiStatus == 'process'){
                $response["status"] = "processing";
                file_put_contents("cabletv_processing_log.txt",json_encode($result));
            }
            elseif($apiStatus == 'pending'){
                $response["status"] = "processing";
                file_put_contents("cabletv_processing_log.txt",json_encode($result));
            }
            else{
                $response["status"] = "fail";
                $response["msg"] = "Transaction Failed, Please Try Again Later";
                //Log Error On Server
                file_put_contents("cabletv_fail_log.txt",json_encode($result));
            }

            return $response;
		} 





        //Purchase Cable Tv
        
		public function purchaseCableWithBasicAuthentication($body,$host,$hostuserurl,$apiKey,$cableid,$provider,$cableplan){

			$response = array();
            $details=$this->model->getApiDetails();

            $host = self::getConfigValue($details,"cableProvider");
            $apiKey = self::getConfigValue($details,"cableApi");
           
            // ------------------------------------------
            //  Get User Access Token
            // ------------------------------------------
            
            $curlA = curl_init();
            curl_setopt_array($curlA, array(
                CURLOPT_URL => $hostuserurl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Basic  $apiKey",
                    'Content-Type: application/json'
                ),
            ));
        
            $exereqA = curl_exec($curlA);
            $err = curl_error($curlA);
            
            if($err){
                $response["status"] = "fail";
                $response["msg"] = "Server Connection Error";
                $response["api_response_log"]=json_encode($response)." : ".$err;
                file_put_contents("cable_error_log2.txt",json_encode($response).$err);
                curl_close($curlA);
                return $response;
            }

            $resultA=json_decode($exereqA);
            $apiKey=$resultA->AccessToken;
            curl_close($curlA);
            
            
            // ------------------------------------------
            //  Purchase Cable Plan
            // ------------------------------------------
        
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $host,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "cable": "'.$cableid.'",
                "iuc": "'.$body->iucnumber.'",
                "cable_plan":"'.$cableplan.'",
                "bypass" : false,
                "request-id" : "'.$body->ref.'"
            }',
            
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                "Authorization: Token $apiKey"
            ),
            ));

            $exereq = curl_exec($curl);
            $err = curl_error($curl);
            
            if($err){
                $response["status"] = "fail";
                $response["msg"] = "Server Connection Error";
                $response["api_response_log"]=json_encode($response)." : ".$err;
                file_put_contents("cable_error_log2.txt",json_encode($response).$err);
                curl_close($curl);
                return $response;
            }
 
            $result=json_decode($exereq);
            curl_close($curl);
            
            //Log API Response To Database
            $response["api_response_log"]=$exereq;

            //Get API Status
            if(isset($result->Status)){$apiStatus = strtolower($result->Status);}
            elseif(isset($result->status)){$apiStatus = strtolower($result->status);}
            else{$apiStatus = "";}
           
           if($apiStatus == 'successful' || $apiStatus == 'success'){
               $response["status"] = "success";
           }
           elseif($apiStatus == 'failed' || $apiStatus == 'fail'){
               $response["status"] = "fail";
               $response["msg"] = "Transaction Failed, Please Try Again Later";

               //If Server Returns Message, Capture It If Message Is Not About A Low Wallet Balance
               if(isset($result["msg"])){
                   if(strpos($result["msg"], 'balance') !== false || strpos($result["msg"], 'insufficient') !== false){$response["msg"] ="Unable To Complete Transaction: Please Report To Admin. Error Code BB.";}
                   else{$response["msg"] = $result["msg"];}
               }

               //If Server Returns Message, Capture It If Message Is Not About A Low Wallet Balance
               if(isset($result->error[0])){
                   if(strpos($result->error[0], 'balance') !== false || strpos($result->error[0], 'insufficient') !== false){$response["msg"] ="Unable To Complete Transaction: Please Report To Admin. Error Code BB.";}
                   else{$response["msg"] = $result->error[0];}
               }   
               
               //If Server Returns Message, Capture It If Message Is Not About A Low Wallet Balance
               if(isset($result["message"])){
                   if(strpos($result["message"], 'balance') !== false || strpos($result["message"], 'insufficient') !== false){$response["msg"] ="Unable To Complete Transaction: Please Report To Admin. Error Code BB.";}
                   else{$response["msg"] = $result["message"];}
               }

               //Log Error On Server
               file_put_contents("cabletv_fail_log.txt",json_encode($result));
           }
           elseif($apiStatus == 'processing' || $apiStatus == 'process'){
               $response["status"] = "processing";
               file_put_contents("cabletv_processing_log.txt",json_encode($result));
           }
           elseif($apiStatus == 'pending'){
               $response["status"] = "processing";
               file_put_contents("cabletv_processing_log.txt",json_encode($result));
           }
           else{
               $response["status"] = "fail";
               $response["msg"] = "Transaction Failed, Please Try Again Later";
               //Log Error On Server
               file_put_contents("cabletv_fail_log.txt",json_encode($result));
           }

            return $response;
		} 


    }

?>