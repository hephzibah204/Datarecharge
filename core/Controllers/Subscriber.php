<?php

class Subscriber extends Controller {
    
    public $userId;
    public $firstname;
    public $lastname;
    public $loginAccount;
    
    public $pageCount = 1; 
    public $nextPage = 2; 
    public $previousePage = 0; 
    public $thePostCat = ""; 
    public $limit = 0;
    public $noFetch = 1;
    public $siteurl;

    protected $model;
 
    // Default Constructor
    public function __construct() {
        
        global $siteurl;
        $this->siteurl = $siteurl;
        
        // Check if user is logged in via cookie and session
        if (isset($_COOKIE['loginId']) && isset($_SESSION['loginId'])) {
            if (base64_decode($_COOKIE['loginId']) == $_SESSION['loginId']) {
                
                // Set User Data
                $this->userId = (float) base64_decode($_COOKIE["loginId"]);
                $this->loginAccount = (float) base64_decode($_COOKIE["loginAccount"]);
                
                // Handle Pagination
                if (isset($_GET["category"])) {
                    $this->thePostCat = $_GET["category"]; 
                }
                if (isset($_GET["page"])) {
                    $this->pageCount = (float) $_GET["page"];
                    $this->nextPage = $this->pageCount + 1; 
                    $this->previousePage = $this->pageCount - 1; 
                }
                
                // Setting Fetch Limit
                $this->limit = ($this->pageCount - 1) * $this->noFetch;
                
                // Initialize Model Class
                $this->model = new SubscriberModel;
                $this->setSeeDetails();

            } else { 
                $this->logoutUser(); 
            }
        } else { 
            // Login Exception for Contact Message Form and Settings
            if (isset($_GET["save-message"]) || isset($_GET["settings"])) {
                $this->model = new SubscriberModel;
            } else {
                $this->logoutUser();
            }
        }
}

		//----------------------------------------------------------------------------------------------------------------
		// Account & Profile Management
		//----------------------------------------------------------------------------------------------------------------

		//Logout Users From System
		public function logoutUser(){
			session_start();
			session_destroy();
			setcookie("loginId", "", time() - 3600, "/");
			setcookie("loginVisit", "", time() - 3600, "/");
			setcookie("loginAccount", "", time() - 3600, "/");
			setcookie("loginState", "", time() - 3600, "/");
			header("Location: ../");
			exit();
		}
 
		//Record Traffic
		public function recordTraffic(){
			$data= $this->model->recordTraffic();
			return $data;
		}

		//Record Last Activity
		public function recordLastActivity(){
			$data= $this->model->recordLastActivity($this->userId);
			if($data == 1){
				$this->logoutUser();
			}
			return $data;
		}


		//Get Profile Info
		public function getProfileInfo(){
			$this->setDetails();
			$data= $this->model->getProfileInfo($this->userId);
			return $data;
		}

		//Update Account Password
		public function updateProfileKey(){
			extract($_POST);
			$check=$this->model->updateProfileKey($this->userId,$oldpass,$newpass);
			return $check;
		}

		//Disable User Pin
		public function disableUserPin(){
			extract($_POST);
			$check=$this->model->disableUserPin($this->userId,$oldpin,$pinstatus);
			if($check == 0){return $this->createPopMessage("Success!!","Pin Disabled Successfully","green");}
			elseif($check == 1){return $this->createPopMessage("Error!!","Invalid Pin Provided.","red");}
			else{return $this->createPopMessage("Error!!","Unable To Disable Pin, Please Try Again Later.","red");}
			return $check;
		}

		//Update Account Pin
		public function updateTransactionPin(){
			extract($_POST);
			$check=$this->model->updateTransactionPin($this->userId,$oldpin,$newpin);
			return $check;
		}

		//----------------------------------------------------------------------------------------------------------------
		// Buy Airtime
		//----------------------------------------------------------------------------------------------------------------
		
		//Get Network
		public function getNetworks(){
			$check=$this->model->getNetworks();
			return $check;
		}

		//Get Airtime Discount
		public function getAirtimeDiscount(){
			$check=$this->model->getAirtimeDiscount();
			return $check;
		}
		
		//Verify NIN
        public function verifyNIN(){
			extract($_POST);
			$this->setDetails();
			$host = $this->siteurl."/api/nin/";
			$transkey=strip_tags($transkey);
			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
	        $load = json_encode(
	            array(
	                "phone" => $phone,
	                "ref" => $transref,
	                "network" => $network,
	                "slip" => $network,
						"consent" => true ));
						//die($load);
			if(is_object($check)){
				
		
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
					CURLOPT_POSTFIELDS => $load,
					
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
						"Token: Token $check->sApiKey"
					),
				));

				$exereq = curl_exec($curl);
				$result=json_decode($exereq);
				curl_close($curl);
				//die($exereq);
				if($result->status == "success" || $result->status == "processing"){
					header("Location: transaction-details?ref=$transref");
					return 0;
				}
				else{
					return $this->createPopMessage("Error!!",$result->msg,"red");
				}
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
		}
		
		//Verify NIN
        public function verifyPNV(){
		extract($_POST);
			$this->setDetails();
			$host = $this->siteurl."/api/pnv/";
			$transkey=strip_tags($transkey);
			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
	        $load = json_encode(
	            array(
	                "phone" => $phone,
	                "ref" => $transref,
	                "network" => $network,
	                "slip" => $network,
						"consent" => true ));
						//die($load);
			if(is_object($check)){
				
		
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
					CURLOPT_POSTFIELDS => $load,
					
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
						"Token: Token $check->sApiKey"
					),
				));

				$exereq = curl_exec($curl);
				$result=json_decode($exereq);
				curl_close($curl);
				//die($exereq);
				if($result->status == "success" || $result->status == "processing"){
					header("Location: transaction-details?ref=$transref");
					return 0;
				}
				else{
					return $this->createPopMessage("Error!!",$result->msg,"red");
				}
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
		}
		
		//Submit NIN Modification Request
        public function submitNINModification(){
            extract($_POST);
            $this->setDetails();
            $host = $this->siteurl."/api/nin/";
            $transkey = strip_tags($transkey);
            $check = $this->model->verifyTransactionPin($this->userId, $transkey);
            $load = json_encode([
                "phone" => $this->loginAccount->sPhone ?? '',
                "ref" => $transref,
                "network" => "nin_modification",
                "modification_type" => $modification_type_code ?? '',
                "new_value" => $new_value ?? '',
                "reason" => $reason ?? '',
                "consent" => true
            ]);

            if (is_object($check)) {
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $host,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $load,
                    CURLOPT_HTTPHEADER => [
                        "Content-Type: application/json",
                        "Token: Token $check->sApiKey"
                    ],
                ]);

                $exereq = curl_exec($curl);
                $result = json_decode($exereq);
                curl_close($curl);

                if ($result->status == "success") {
                    return $this->createPopMessage("Success!!", "NIN modification request submitted. Ref: {$result->ref}", "green");
                } else {
                    return $this->createPopMessage("Error!!", $result->msg ?? "Request failed", "red");
                }
            } else {
                return $this->createPopMessage("Error!!", "Incorrect Pin, Please Try Again.", "red");
            }
        }

		//Verify CAC
        public function verifyCAC(){
            extract($_POST);
            $this->setDetails();
            $host = $this->siteurl."/api/cac/";
            $transkey = strip_tags($transkey);
            $check = $this->model->verifyTransactionPin($this->userId, $transkey);
            $load = json_encode([
                "phone" => $phone ?? $this->loginAccount->sPhone ?? '',
                "ref" => $transref,
                "network" => $network,
                "rcNumber" => $rcNumber ?? '',
                "consent" => true
            ]);
            if (is_object($check)) {
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $host,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $load,
                    CURLOPT_HTTPHEADER => [
                        "Content-Type: application/json",
                        "Token: Token $check->sApiKey"
                    ],
                ]);
                $exereq = curl_exec($curl);
                $result = json_decode($exereq);
                curl_close($curl);
                if ($result->status == "success" || $result->status == "processing") {
                    header("Location: transaction-details?ref=$transref");
                    return 0;
                } else {
                    return $this->createPopMessage("Error!!", $result->msg ?? "Verification failed", "red");
                }
            } else {
                return $this->createPopMessage("Error!!", "Incorrect Pin, Please Try Again.", "red");
            }
        }

        //Verify Voter Card (VIN)
        public function verifyVIN(){
            extract($_POST);
            $this->setDetails();
            $host = $this->siteurl."/api/vin/";
            $transkey = strip_tags($transkey);
            $check = $this->model->verifyTransactionPin($this->userId, $transkey);
            $load = json_encode([
                "phone" => $phone ?? $this->loginAccount->sPhone ?? '',
                "ref" => $transref,
                "network" => $network,
                "vin" => $vin ?? '',
                "consent" => true
            ]);
            if (is_object($check)) {
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $host,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $load,
                    CURLOPT_HTTPHEADER => [
                        "Content-Type: application/json",
                        "Token: Token $check->sApiKey"
                    ],
                ]);
                $exereq = curl_exec($curl);
                $result = json_decode($exereq);
                curl_close($curl);
                if ($result->status == "success" || $result->status == "processing") {
                    header("Location: transaction-details?ref=$transref");
                    return 0;
                } else {
                    return $this->createPopMessage("Error!!", $result->msg ?? "Verification failed", "red");
                }
            } else {
                return $this->createPopMessage("Error!!", "Incorrect Pin, Please Try Again.", "red");
            }
        }

        //Verify Driver's License
        public function verifyDriversLicense(){
            extract($_POST);
            $this->setDetails();
            $host = $this->siteurl."/api/drivers-license/";
            $transkey = strip_tags($transkey);
            $check = $this->model->verifyTransactionPin($this->userId, $transkey);
            $load = json_encode([
                "phone" => $phone ?? $this->loginAccount->sPhone ?? '',
                "ref" => $transref,
                "network" => $network,
                "licenseNo" => $licenseNo ?? '',
                "consent" => true
            ]);
            if (is_object($check)) {
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $host,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $load,
                    CURLOPT_HTTPHEADER => [
                        "Content-Type: application/json",
                        "Token: Token $check->sApiKey"
                    ],
                ]);
                $exereq = curl_exec($curl);
                $result = json_decode($exereq);
                curl_close($curl);
                if ($result->status == "success" || $result->status == "processing") {
                    header("Location: transaction-details?ref=$transref");
                    return 0;
                } else {
                    return $this->createPopMessage("Error!!", $result->msg ?? "Verification failed", "red");
                }
            } else {
                return $this->createPopMessage("Error!!", "Incorrect Pin, Please Try Again.", "red");
            }
        }

        //Verify Passport
        public function verifyPassport(){
            extract($_POST);
            $this->setDetails();
            $host = $this->siteurl."/api/passport/";
            $transkey = strip_tags($transkey);
            $check = $this->model->verifyTransactionPin($this->userId, $transkey);
            $load = json_encode([
                "phone" => $phone ?? $this->loginAccount->sPhone ?? '',
                "ref" => $transref,
                "network" => $network,
                "passportNo" => $passportNo ?? '',
                "consent" => true
            ]);
            if (is_object($check)) {
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $host,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $load,
                    CURLOPT_HTTPHEADER => [
                        "Content-Type: application/json",
                        "Token: Token $check->sApiKey"
                    ],
                ]);
                $exereq = curl_exec($curl);
                $result = json_decode($exereq);
                curl_close($curl);
                if ($result->status == "success" || $result->status == "processing") {
                    header("Location: transaction-details?ref=$transref");
                    return 0;
                } else {
                    return $this->createPopMessage("Error!!", $result->msg ?? "Verification failed", "red");
                }
            } else {
                return $this->createPopMessage("Error!!", "Incorrect Pin, Please Try Again.", "red");
            }
        }

		//Verify NIN
        public function verifyBVN(){
		extract($_POST);
			$this->setDetails();
			$host = $this->siteurl."/api/bvn/";
			$transkey=strip_tags($transkey);
			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
	        $load = json_encode(
	            array(
	                "phone" => $phone,
	                "ref" => $transref,
	                "network" => $network,
	                "slip" => $network,
						"consent" => true ));
						//die($load);
			if(is_object($check)){
				
		
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
					CURLOPT_POSTFIELDS => $load,
					
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
						"Token: Token $check->sApiKey"
					),
				));

				$exereq = curl_exec($curl);
				$result=json_decode($exereq);
				curl_close($curl);
				//die($exereq);
				if($result->status == "success" || $result->status == "processing"){
					header("Location: transaction-details?ref=$transref");
					return 0;
				}
				else{
					return $this->createPopMessage("Error!!",$result->msg,"red");
				}
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
		}

		// Purchase Airtime
public function purchaseAirtime(){
    extract($_POST);
    $host = $this->siteurl."/api/airtime/";
    $transkey = strip_tags($transkey);
    $checkPin = $this->model->verifyTransactionPin($this->userId, $transkey);
    $ported_number = "false";
    if(isset($_POST["ported_number"]) && $_POST["ported_number"] == "on"){
        $ported_number = "true";
    }

    if(is_object($checkPin)){
        $checkLimit = $this->model->checkDailySpendingLimit($this->userId, $amount);
        $checkAirtimeLimit = $this->model->checkDailyAirtimeLimit($amount);
 
        if($checkLimit && $checkAirtimeLimit){
            // Purchase Airtime
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
                CURLOPT_POSTFIELDS => json_encode(array(
                    "network" => $network,
                    "amount" => $amount,
                    "phone" => $phone,
                    "ported_number" => $ported_number,
                    "ref" => $transref,
                    "airtime_type" => $networktype
                )),
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Token: Token $checkPin->sApiKey"
                ),
            ));

            $exereq = curl_exec($curl);
            $result = json_decode($exereq);
            curl_close($curl);

            if ($result->status == "success") {
                header("Location: transaction-details?ref=$transref");
                return 0;
            } elseif ($result->status == "processing") {
                header("Location: transaction-details?ref=$transref");
                return 0;
            } else {
                return $this->createPopMessage("Error!!", $result->msg, "red");
            }
        } elseif (!$checkLimit) {
            return $this->createPopMessage("Error!!", "Daily Spending Limit Exceeded", "red");
        } elseif (!$checkAirtimeLimit) {
            return $this->createPopMessage("Error!!", "Daily Airtime Limit Exceeded", "red");
        }
    } else {
        return $this->createPopMessage("Error!!", "Incorrect Pin, Please Try Again.", "red");
    }
}

		//----------------------------------------------------------------------------------------------------------------
		// Buy Recharge Card
		//----------------------------------------------------------------------------------------------------------------

		//Get Recharge Pin Discount
		public function getRechargePinDiscount(){
			$check=$this->model->getRechargePinDiscount();
			return $check;
		}
		
		//Get Recharge Pin Token
		public function getRechargePinTokens(){
			if(!isset($_GET["ref"])){header("Location: ./"); exit(); }
			$check=$this->model->getRechargePinTokens($this->userId,$_GET["ref"]);
			
			return $check;
		}

		public function getNumberOfAvailablePinsFromApi(){

			$response = array();
			$details=$this->model->getApiConfiguration();
			
			//Get Api Key Details
			$host = self::getConfigValue($details,"rechargePinProvider");
			
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => $host."available/",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			));
	
			$exereq = curl_exec($curl);
			$result=json_decode($exereq);
			curl_close($curl);
			
			if(isset($result->msg)){return $result->msg;} else{return null;}
	
		}
	
		//Purchase Recharge Card Pin
		public function purchaseRechargePin(){
			extract($_POST);
			$host = $this->siteurl."/api/rechargepin/";
			$transkey=strip_tags($transkey);
			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
			$ported_number = "false";
			$transref = "AIRTIMEPIN".rand(100,999)."_".$transref;
			if(isset($_POST["ported_number"])){
			    if($_POST["ported_number"] == "on"){$ported_number = "true";}
			}
			
			if(is_object($check)){
				
				//Purchase Airtime
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
						"network": "'.$network.'",
						"plan": "'.$amount.'",
						"quantity": "'.$quantity.'",
						"businessname": "'.$businessname.'",
						"ref" : "'.$transref.'"
					}',
					
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
						"Token: Token $check->sApiKey"
					),
				));

				$exereq = curl_exec($curl);
				$result=json_decode($exereq);
				curl_close($curl);
				
				if($result->status == "success" || $result->status == "processing"){
					header("Location: transaction-details?ref=$transref");
					return 0;
				}
				else{
					return $this->createPopMessage("Error!!",$result->msg,"red");
				}
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
		}
		
		
		//----------------------------------------------------------------------------------------------------------------
		// Alpha Topup Plan
		//----------------------------------------------------------------------------------------------------------------
		
		//Get Alpha Topup
		public function getAlphaTopupPlans(){
			$data=$this->model->getAlphaTopupPlans();
			return $data;
		}

		// ------------------------------------------
		//Purchase Alpha Topup
		// ------------------------------------------
		
		
		//Purchase Alpha Topup API
		public function purchaseAlphaTopup(){
			extract($_POST);
			$host = $this->siteurl."/api/alphatopup/";
			$transkey=strip_tags($transkey);
			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
			$ported_number = "false";
			
			if(is_object($check)){
				
				//Purchase Airtime
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
						"amount": "'.$alphaplan.'",
						"phone": "'.$phone.'",
						"ref" : "'.$transref.'"
					}',
					
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
						"Token: Token $check->sApiKey"
					),
				));

				$exereq = curl_exec($curl);
				$result=json_decode($exereq);
				curl_close($curl);
				
				if($result->status == "success" || $result->status == "processing"){
					header("Location: transaction-details?ref=$transref");
					return 0;
				}
				else{
					return $this->createPopMessage("Error!!",$result->msg,"red");
				}
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
		}

		//----------------------------------------------------------------------------------------------------------------
		// Buy Data Plan
		//----------------------------------------------------------------------------------------------------------------
		

		//Get Data Pins
		public function getDataPins(){
			$this->setDetails();
			$check=$this->model->getDataPins();
			return $check;
		}

		//Get Data Pins
		public function getDataPinTokens(){
			if(!isset($_GET["ref"])){header("Location: ./"); exit(); }
			$check=$this->model->getDataPinTokens($this->userId,$_GET["ref"]);
			return $check;
		}

		//Get Data Plans
		public function getDataPlans(){
			$check=$this->model->getDataPlans();
			return $check;
		}

		// Purchase Data
 public function purchaseData(){
    extract($_POST);
    $host = $this->siteurl."/api/data/";
    $checkPin = $this->model->verifyTransactionPin($this->userId, $transkey);
    $checkLimit = $this->model->checkDailySpendingLimit($this->userId, $amounttopay);
    $ported_number = "false";
    
    if(isset($_POST["ported_number"])){
        if($_POST["ported_number"] == "on") {
            $ported_number = "true";
        }
    }
    
    if(is_object($checkPin)){
        if($checkLimit) {
            // Purchase Data
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
                    "network": "'.$network.'",
                    "phone": "'.$phone.'",
                    "ported_number":"'.$ported_number.'",
                    "ref" : "'.$transref.'",
                    "data_plan": "'.$dataplan.'"
                }',
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Token: Token $checkPin->sApiKey"
                ),
            ));

            $exereq = curl_exec($curl);
            $result=json_decode($exereq);
            curl_close($curl);

    if ($result->status == "success") {
    header("Location: transaction-details?ref=$transref");
    return 0; } elseif ($result->status == "processing") {
    header("Location: transaction-details?ref=$transref");
    return 0;
 }
            else{
                return $this->createPopMessage("Error!!",$result->msg,"red");
            }
        }
        else{
            return $this->createPopMessage("Error!!", "Daily Spending Limit Exceeded", "red");
        }
    }
    else{
        return $this->createPopMessage("Error!!", "Incorrect Pin, Please Try Again.", "red");
    }
}

		//Purchase Data
		public function purchaseDataPin(){
			extract($_POST);
			$host = $this->siteurl."/api/datapin/";
			$transkey=strip_tags($transkey);
			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
			$ported_number = "false";
			
			if(isset($_POST["ported_number"])){
			    if($_POST["ported_number"] == "on"){$ported_number = "true";}
			}
			
			if(is_object($check)){
				
				//Purchase Data
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
						"network": "'.$network.'",
						"quantity": "'.$quantity.'",
						"businessname":"'.$businessname.'",
						"data_plan": "'.$datapinplan.'",
						"ref" : "'.$transref.'"
					}',
					
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
						"Token: Token $check->sApiKey"
					),
				));

				$exereq = curl_exec($curl);
				$result=json_decode($exereq);
				curl_close($curl);

				if($result->status == "success" || $result->status == "processing"){
					header("Location: transaction-details?ref=$transref");
					return 0;
				}
				else{
					return $this->createPopMessage("Error!!",$result->msg,"red");
				}
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
		}

		//----------------------------------------------------------------------------------------------------------------
		// Upgrade To Agent
		//----------------------------------------------------------------------------------------------------------------
		
		//Upgrade To Agent
		public function upgradeToAgent(){
			extract($_POST);
			$transref = $this->generateTransactionRef();
			$check=$this->model->upgradeToAgent($this->userId,$kpin,$transref);
			if($check == 0){return $this->createPopMessage("Success!!","Account Upgraded. You are now an Agent.","green");}
			elseif($check == 1){return $this->createPopMessage("Error!!","Invalid Transaction Pin.","red");}
			elseif($check == 2){return $this->createPopMessage("Error!!","You Are Already An Agent","red");}
			elseif($check == 3){return $this->createPopMessage("Error!!","Insufficent Balance","red");}
			else{return $this->createPopMessage("Error!!","Unexpected Error: Could Not Upgrade Account","red");}
		}

		//Upgrade To Vendor
		public function upgradeToVendor(){
			extract($_POST);
			$transref = $this->generateTransactionRef();
			$check=$this->model->upgradeToVendor($this->userId,$kpin,$transref);
			if($check == 0){return $this->createPopMessage("Success!!","Account Upgraded. You are now a Vendor.","green");}
			elseif($check == 1){return $this->createPopMessage("Error!!","Invalid Transaction Pin.","red");}
			elseif($check == 2){return $this->createPopMessage("Error!!","You Are Already An Agent","red");}
			elseif($check == 3){return $this->createPopMessage("Error!!","Insufficient Balance","red");}
			else{return $this->createPopMessage("Error!!","Unexpected Error: Could Not Upgrade Account","red");}
		}

		//----------------------------------------------------------------------------------------------------------------
		// Site Settings
		//----------------------------------------------------------------------------------------------------------------
		
		public function getSiteSettings(){
			$data=$this->model->getSiteSettings();
			return $data;
		}

		public function getApiConfiguration(){
			$data=$this->model->getApiConfiguration();
			return $data;
		}


		//----------------------------------------------------------------------------------------------------------------
		// Exam Pins
		//----------------------------------------------------------------------------------------------------------------
		
		//Get All Exam Provider
		public function getExamProvider(){
			$data=$this->model->getExamProvider();
			return $data;
		}

		//Get User Exam Pin Transactions
		public function getExamTransactions(){
			$data=$this->model->getExamPinTransactions($this->userId);
			return $data;
		}
		
		//Purchase Exam Pin Token
		public function purchaseExamPinToken(){
			extract($_POST);
			$host = $this->siteurl."/api/exam/";
			$transkey=strip_tags($transkey);
			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
			
			if(is_object($check)){
				
				//Purchase Data
				$curl = curl_init();
				$transref = $this->generateTransactionRef();
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
						"provider": "'.$provider.'",
						"quantity": "'.$quantity.'",
						"ref" : "'.$transref.'"
					}',
					
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
						"Token: Token $check->sApiKey"
					),
				));

				$exereq = curl_exec($curl);
				$result=json_decode($exereq);
				curl_close($curl);
				
				if($result->status == "success"){
					header("Location: transaction-details?ref=$transref");
					return 0;
				}
				else{
					return $this->createPopMessage("Error!!","Server Error: ".$result->msg,"red");
				}
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
		}
		//----------------------------------------------------------------------------------------------------------------
		// Electricity
		//----------------------------------------------------------------------------------------------------------------
		 
		//Get All Electricity Provider
		public function getElectricityProvider(){
			$data=$this->model->getElectricityProvider();
			return $data;
		}

		//Validate Meter Number
		public function validateMeterNumber(){
			extract($_POST);
			$host = $this->siteurl."/api/electricity/verify/";
			$data = $this->getProfileInfo();
			
			
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
					"provider": "'.$provider.'",
					"meternumber": "'.$meternumber.'",
					"metertype": "'.$metertype.'"
				}',
					
				CURLOPT_HTTPHEADER => array(
					"Content-Type: application/json",
					"Token: Token $data->sApiKey"
				),
			));

			$exereq = curl_exec($curl);
			$result=json_decode($exereq);
			curl_close($curl);
				
			if($result->status == "success"){
				return $result->msg;
			}
			else{
				return $result->msg;
			}
			
		}
		
		//Purchase Electricity Token
		public function purchaseElectricityToken(){
			extract($_POST);
			$host = $this->siteurl."/api/electricity/";
			$transkey=strip_tags($transkey);
			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
			
			if(is_object($check)){
				
				//Purchase Data
				$curl = curl_init();
				$transref = $this->generateTransactionRef();
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
						"provider": "'.$provider.'",
						"phone": "'.$phone.'",
						"metertype": "'.$metertype.'",
						"meternumber": "'.$meternumber.'",
						"ref" : "'.$transref.'",
						"amount": "'.$amount.'"
					}',
					
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
						"Token: Token $check->sApiKey"
					),
				));

				$exereq = curl_exec($curl);
				$result=json_decode($exereq);
				curl_close($curl);
				if($result->status == "success"){
					header("Location: transaction-details?ref=$transref");
					return 0;
				}
				else{
					return $this->createPopMessage("Error!!","Server Error: ".$result->msg,"red");
				}
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
		}

		//----------------------------------------------------------------------------------------------------------------
		// Cable TV
		//----------------------------------------------------------------------------------------------------------------
		
		//Get All Cable Provider
		public function getCableProvider(){
			$data=$this->model->getCableProvider();
			return $data;
		}
		
		//Get Cable Plan
		public function getCablePlans(){
			$data=$this->model->getCablePlans();
			return $data;
		}

		//Purchase Cable Tv
		public function purchaseCableTv(){
			extract($_POST);
			$host = $this->siteurl."/api/cabletv/";
			$transkey=strip_tags($transkey);
			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
			
			if(is_object($check)){
				
				//Purchase Data
				$curl = curl_init();
				$transref = $this->generateTransactionRef();
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
						"provider": "'.$provider.'",
						"phone": "'.$phone.'",
						"subtype": "'.$subtype.'",
						"iucnumber": "'.$iucnumber.'",
						"ref" : "'.$transref.'",
						"plan": "'.$cableplan.'"
					}',
					
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
						"Token: Token $check->sApiKey"
					),
				));

				$exereq = curl_exec($curl);
				$result=json_decode($exereq);
				curl_close($curl);
				
				if($result->status == "success"){
					header("Location: transaction-details?ref=$transref");
					return 0;
				}
				else{
					return $this->createPopMessage("Error!!","Server Error, Please Contact Admin","red");
				}
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
		}

		//Validate ICU Number
		public function validateIUCNumber(){
			extract($_POST);
			$host = $this->siteurl."/api/cabletv/verify/";
			$data = $this->getProfileInfo();
			
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
					"provider": "'.$provider.'",
					"iucnumber": "'.$iucnumber.'"
				}',
					
				CURLOPT_HTTPHEADER => array(
					"Content-Type: application/json",
					"Token: Token $data->sApiKey"
				),
			));

			$exereq = curl_exec($curl);
			$result=json_decode($exereq);
			curl_close($curl);
			
			if($result->status == "success"){
				return $result->msg;
			}
			else{
				return $result->msg;
			}
			
		}


		//----------------------------------------------------------------------------------------------------------------
		// Transaction Management
		//----------------------------------------------------------------------------------------------------------------
		
		//Get Network
		public function getAllTransaction($limit){
			$check=$this->model->getAllTransaction($this->userId,$limit);
			return $check;
		}
		
		//Generate Transaction Reference
		public function generateTransactionRef(){
			$tranId=rand(1000,9999).time();
			return $tranId;
		}

		
		//Get Transaction Details
		public function getTransactionDetails(){
			if(!isset($_GET["ref"])){header("Location: ./"); exit(); }
			$data=$this->model->getTransactionDetails($_GET["ref"]);
			return $data;
		}

		

		//Initialize Paystack Payment
		public function fundWithPaystack(){
			extract($_POST);
			$email=strip_tags($email);
			$amount=strip_tags($amount);
			$data=$this->model->initializePayStack($this->siteurl,$email,$amount);
			if($data["status"] == 'success'){$link = $data["msg"]; header("Location: $link"); exit();}
			return $this->createPopMessage("Opps!!",$data["msg"],"red"); 
		}
		//Get All Transasactions
    function TotalTransactions(){
          $data = $this->model->TotalTransactions($this->userId);
          return $data; }
                    
    //Get Total Fund 
		function getTotalFund(){
        $data = $this->model->getTotalFund($this->userId);
        return $data; }

   //Get Total Spent In A Week
    function dailySpent(){
        $data = $this->model->getSpent('daily', $this->userId);       
        return $data; } 
        
         function weeklySpent(){
        $data = $this->model->getSpent('weekly', $this->userId);       
        return $data; } 
        
   //Get Total Spent In A Month
     function monthlySpent(){
          $data = $this->model->getSpent('monthly', $this->userId);
          return $data; }

		//----------------------------------------------------------------------------------------------------------------
		// Wallet Transfer Management
		//----------------------------------------------------------------------------------------------------------------
		
		//Perform Funds Transfer
		public function performFundsTransfer(){
			extract($_POST);
			$amount = (float) $amount;
			$amounttopay = 0;
			$transref1 = $this->generateTransactionRef();
			$transref2 = $transref1 .":".$this->generateTransactionRef();

			if($amount < 0){
			    return $this->createPopMessage("Error!!","Not Enough Balance. Please Try Again.","red"); 
			}
			
			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
			if(is_object($check)){
				
				if($transfertype == "referral-wallet"): 
					$amounttopay = $amount;
					$data = $this->model->performReferralTransfer($this->userId,$amount,$amounttopay,$transref1,$transref2);
					if($data == 0){ header("Location: transaction-details?ref=$transref1"); return 0;}
					elseif($data == 3){	return $this->createPopMessage("Error!!","Not Enough Balance. Please Try Again.","red"); }
					elseif($data == 4){	return $this->createPopMessage("Error!!","Could Not Update Wallet, Please Try Again","red"); }
					else{return $this->createPopMessage("Error!!","Unexpected Error, Please Tray Again Later","red"); }
				endif;
				
				return $this->createPopMessage("Error!!","Could Not Perform Operation, Please Try Again Later.","red");
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
		}
		

		//----------------------------------------------------------------------------------------------------------------
		// PAGA ACCOUNCT MANAGEMENT By Sunusi Kiru 07026417709
		//----------------------------------------------------------------------------------------------------------------
		//Generate aspfiy Account
		
		public function generateAsfiyAccount(){
		    $this->model->generateAsfiyAccount($this->userId);
		}
		public function generateAsfiyPalmpay(){
		    $this->model->generateAsfiyPalmpay($this->userId);
		}

		//----------------------------------------------------------------------------------------------------------------
		// GTBANK ACCOUNCT MANAGEMENT
		//----------------------------------------------------------------------------------------------------------------
		

		//Generate GTbank Account
		
		public function generateGtbankAccount(){
		    $sta=$this->model->generateGtbankAccount($this->userId);
			if($sta <> 0){return $this->createPopMessage("Error!!","Unable To Generate Account, Please Try Again Later.","red");}
		}


		//----------------------------------------------------------------------------------------------------------------
		// Airtime To Cash
		//----------------------------------------------------------------------------------------------------------------
		
		//Submit Airtime To Cash Request
		public function submitAirtimeToCashRequest(){
			extract($_POST);
			
			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
			
			if(is_object($check)){
			    $data=$this->model->submitAirtimeToCashRequest($this->userId,$check->sWallet,$airtimetocashnetwork,$airtimetocashphone,$airtimetocashamount,$transref);
			    if($data == 0){ header("Location: transaction-details?ref=$transref"); return 0;}
			else{return $this->createPopMessage("Error!!","Unexpected Error, Please Try Again Later","red"); }
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
			
			
		}
		
		//----------------------------------------------------------------------------------------------------------------
		// Contact Message
		//----------------------------------------------------------------------------------------------------------------
		
		//Post Form Contact Message
		public function postContact(){
			extract($_POST);

			$name=strip_tags($name); $email=strip_tags($email);
			$subject=strip_tags($subject); $message=strip_tags($message);

			$email = $this->cleanParameter($email, "STRING");
			$name = $this->cleanParameter($name, "STRING");
			$subject = $this->cleanParameter($subject, "STRING");
			$message = $this->cleanParameter($message, "STRING");

			$check=$this->model->postContact($name,$email,$subject,$message);
			return $check;
		}

		//----------------------------------------------------------------------------------------------------------------
		// Notification
		//----------------------------------------------------------------------------------------------------------------
		
		//Get All Notification
		public function getAllNotification(){
			$data=$this->model->getAllNotification($this->loginAccount);
			return $data;
		}

		//Get Home Notification
		public function displayHomeNotification(){
			$data=$this->model->getHomeNotification();
			$subject = addslashes(str_replace("\r","",$data->subject));
			$subject = str_replace("\n","",$subject);
			$message = addslashes(str_replace("\r","",$data->message));
			$message = str_replace("\n","",$message);
			return "swal('{$subject}','{$message}','info');";
			return json_encode(array("subject" => $subject, "message" => $message));
		}

		//Get Home Notification
		public function displayHomeNotification2(){
			$data=$this->model->getHomeNotification();
			$subject = addslashes(str_replace("\r","",$data->subject));
			$subject = str_replace("\n","",$subject);
			$message = addslashes(str_replace("\r","",$data->message));
			$message = str_replace("\n","",$message);
			return json_encode(array("subject" => $subject, "message" => $message));
		}

		//----------------------------------------------------------------------------------------------------------------
		// Email Verification
		//----------------------------------------------------------------------------------------------------------------
		//Verify Email
		public function verifyUserMail(){
			extract($_POST);
			$verifyRecoveryCode = new AccountAccess;
            $status = $verifyRecoveryCode->verifyRecoveryCode();

			if($status == 0){
				$this->model->updateEmailVerificationStatus($this->userId);
				header("Location: homepage?msg=Email Verification Successful");
				exit();
			}
			elseif($status == 1){return $this->createPopMessage("Error!!","Wrong Verification Code","red");}
			else{return $this->createPopMessage("Error!!","Email Verification Failed","red");}
	
		}


		//Format Description
		public function formatDescription($data){
			$data=str_replace("\n\r", "<br/>", $data);
			return $data;
		}

		//Format Status
		public function formatStatus($status){
			if($status == 0){return "<b class='text-success'>Transaction Successful</b>";}
			elseif($status == 2){return "<b class='text-primary'>Transaction Processing</b>";}
			elseif($status == 5){return "<b class='text-primary'>Transaction Processing</b>";}
			else{return "<b class='text-danger'>Failed Transaction</b>";}
		}

		//Create Message Pop
		public function createPopMessage($heading,$message,$color){
			//Color is green or red for success and error respectively
			$msg='
			<div id="gen-message-box" class="menu menu-box-bottom bg-'.$color.'-dark rounded-m" data-menu-height="335" data-menu-effect="menu-over">
					<h1 class="text-center mt-4"><i class="fa fa-3x fa-times-circle scale-box color-white shadow-xl rounded-circle"></i></h1>
					<h1 class="text-center mt-3 text-uppercase color-white font-700">'.$heading.'</h1>
					<p class="boxed-text-l color-white opacity-70">
							'.$message.'
					</p>
					<a href="#" class="close-menu btn btn-m btn-center-l button-s shadow-l rounded-s text-uppercase font-600 bg-white color-black">Close</a>
			</div>
			';
        
			return $msg;
		}

		public function createPopMessage2($heading,$message){
			$msg='
			<div id="gen-message-box" class="menu menu-box-modal rounded-m" data-menu-height="400" style="display: block; width: 97%; height: 400px;">
				<h1 class="text-center mt-4"><i class="fa fa-3x fa-info-circle scale-box color-blue-dark shadow-xl rounded-circle"></i></h1>
				<h3 class="text-center mt-3 font-700">'.$heading.'</h3>
				<p class="boxed-text-xl text-dark">
					'.$message.'
				</p>
				<div class="row mb-0 me-3 ms-3">
					<div class="col-6">
						<a href="#" class="btn close-menu btn-full btn-m color-red-dark border-red-dark font-600 rounded-s">Okay</a>
					</div>
					<div class="col-6">
						<a href="notifications" class="btn btn-full btn-m color-green-dark border-green-dark font-600 rounded-s">View More</a>
					</div>
				</div>
			</div>
			';
        
			return $msg;
		}

		public function setSeeDetails(){
			if(!file_exists('../../core/helpers/vendor/manifest.php')){
				$resp="PGgxIHN0eWxlPSdjb2xvcjpyZWQ7Jz5JbGxlZ2FsIFVzZSBPZiBTb2Z0d2FyZSBEZXRlY3RlZC4gPC9oMT4KICAgICAgICAgICAgPGgyPgogICAgICAgICAgICAgICAgWW91ciBJbmZvcm1hdGlvbiBIYXZlIEJlZW4gU3VibWl0dGVkIFRvIE91ciBTZXJ2ZXIuIAogICAgICAgICAgICAgICAgPGJyLz4KICAgICAgICAgICAgICAgIFlvdSBIYXZlIDQ4IEhvdXJzIFRvIFBheSBBIEZpbmUgT2YgTjUwLDAwMCBGb3IgVXNpbmcgT3VyIFNvZnR3YXJlIFdpdGhvdXQgQSBMaWNlbnNlLiAKICAgICAgICAgICAgICAgIDxici8+CiAgICAgICAgICAgICAgICBGYWlsdXJlIFRvIERvIFNvLCBMZWdhbCBNZWFzdXJlcyBXb3VsZCBCZSBUYWtlbiBPbiBZb3UuIAogICAgICAgICAgICA8L2gyPgogICAgICAgICAgICA8aDMgc3R5bGU9J2NvbG9yOnJlZDsnPgogICAgICAgICAgICA8YSBocmVmPSdodHRwczovL3RvcHVwbWF0ZS5jb20vY29udGFjdC5waHAnPgogICAgICAgICAgICBodHRwczovL3RvcHVwbWF0ZS5jb20vY29udGFjdC5waHA8L2E+IEZvciBNb3JlIERldGFpbHMuCiAgICAgICAgICAgIDwvaDM+"; echo base64_decode($resp); exit();
			}
			if(!file_exists('../../core/site.php')){
				$resp="PGgxIHN0eWxlPSdjb2xvcjpyZWQ7Jz5JbGxlZ2FsIFVzZSBPZiBTb2Z0d2FyZSBEZXRlY3RlZC4gPC9oMT4KICAgICAgICAgICAgPGgyPgogICAgICAgICAgICAgICAgWW91ciBJbmZvcm1hdGlvbiBIYXZlIEJlZW4gU3VibWl0dGVkIFRvIE91ciBTZXJ2ZXIuIAogICAgICAgICAgICAgICAgPGJyLz4KICAgICAgICAgICAgICAgIFlvdSBIYXZlIDQ4IEhvdXJzIFRvIFBheSBBIEZpbmUgT2YgTjUwLDAwMCBGb3IgVXNpbmcgT3VyIFNvZnR3YXJlIFdpdGhvdXQgQSBMaWNlbnNlLiAKICAgICAgICAgICAgICAgIDxici8+CiAgICAgICAgICAgICAgICBGYWlsdXJlIFRvIERvIFNvLCBMZWdhbCBNZWFzdXJlcyBXb3VsZCBCZSBUYWtlbiBPbiBZb3UuIAogICAgICAgICAgICA8L2gyPgogICAgICAgICAgICA8aDMgc3R5bGU9J2NvbG9yOnJlZDsnPgogICAgICAgICAgICA8YSBocmVmPSdodHRwczovL3RvcHVwbWF0ZS5jb20vY29udGFjdC5waHAnPgogICAgICAgICAgICBodHRwczovL3RvcHVwbWF0ZS5jb20vY29udGFjdC5waHA8L2E+IEZvciBNb3JlIERldGFpbHMuCiAgICAgICAgICAgIDwvaDM+"; echo base64_decode($resp); exit();
			}
		}
		
		//SMILE DATA 
		//Get Smile Data Plans
		public function getSmileDataPlans(){
			$check=$this->model->getSmileDataPlans();
			return $check;
		}
		
		//Purchase Smile Data
		public function purchaseSmileData(){
			extract($_POST);
			$host = $this->siteurl."/api/smile-data/";

			$check=$this->model->verifyTransactionPin($this->userId,$transkey);
			
			if(is_object($check)){
				
				//Purchase Data
				$curl = curl_init();
				$transref = $this->generateTransactionRef();
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
					

						"PhoneNumber": "'.$phone.'",
						"actype": "'.$actype.'",
						"ref" : "'.$transref.'",
						"BundleTypeCode": "'.$dataplan.'"
					}',
					
					CURLOPT_HTTPHEADER => array(
						"Content-Type: application/json",
						"Authorization: Token $check->sApiKey"
					),
				));

				$exereq = curl_exec($curl);
				$result=json_decode($exereq);
				curl_close($curl);
				
				if($result->status == "success" || $result->status == "processing"){
					header("Location: transaction-details?ref=$transref");
					return 0;
				}
				else{
					return $this->createPopMessage("Error!!",$result->msg,"red");
				}
			}
			else{
				return $this->createPopMessage("Error!!","Incorrect Pin, Please Try Again.","red");
			}
		}


		//----------------------------------------------------------------------------------------------------------------
		
		//----------------------------------------------------------------------------------------------------------------
		// Payvessel ACCOUNCT MANAGEMENT
		//----------------------------------------------------------------------------------------------------------------
		
		//Generate Payvessel Account
		
		public function generatePayvesselAccount(){
		     extract($_POST);
		     $data = $this->model->generatePayvesselAccount($this->userId,$bvn);
		     
		    if ($data == 0) {
          return $this->createPopMessage("Success!!","Details Verified Successfully","green"); }
          else { return $this->createPopMessage("Failed!", "Unable To Verify Your Details. Please Try Again later", "green"); }
		}
		
		public function updatePayvesselAccount(){
		     extract($_POST);
		     $data = $this->model->updatePayvesselAccount($this->userId,$bvn);
		     
           if (strpos($data, "invalid bvn") !== false) {
           return $this->createPopMessage("Failed!", "Unable To Verify Your Details. Please Try Again later", "red");
         } else { return $this->createPopMessage("Success!!", "Details Verified Successfully.", "green"); }
           }
		
        public function generatePayvesselDynamic(){
           extract($_POST);
             if(isset($id)){
             return $this->model->generatePayvesselDynamic($id); } else { return NULL; }
       }



		//Verify User KYC Details
		public function verifyUserKYC(){
			extract($_POST);

			$checker = new Account;
			$siteSettings = $checker->getSiteConfiguration();

			if(empty($accountref) && $shouldupdate == "yes"){
				$refChecker= $checker->checkMonnifyAccountRef($email);
				if($refChecker["status"] == "success"){$accountref = $refChecker["ref"]; }
				else{return $this->createPopMessage("Error!!","Monnify Reference Not Uploaded Yet, Please Contact Admin","red");}
			}

			$kycData = [
				"userId" => $this->userId,
				"selectedKycOption" => $setkycoption,
				"dob" => date("d-M-Y",strtotime($dob)),
				"vernumber" => $vernumber,
				"kycoptionselect" => (isset($kycoptionselect)) ? $kycoptionselect : "",
				"accountReference" => $accountref,
				"fullname" => $firstname ." ".$lastname,
				"firstname" => $firstname,
				"lastname" => $lastname,
				"phone" => $phone,
				"kycNinCharges" => (int) $siteSettings->kycNinCharges,
				"kycBvnCharges" => (int) $siteSettings->kycBvnCharges,
				"shouldupdate" => $shouldupdate
			];

			$kycData = (object) $kycData;

			

			if($kycData->selectedKycOption == 'nin' || $kycData->kycoptionselect == 'nin'){
				$response = $checker->verifyAndUpdateNinOnMonnify($kycData);
				if($response["status"] == "success"){
					$msg = $this->createPopMessage("Success!!","Details Verified Successfully","green");
					$msg.=" <script>setTimeout(function(){location.replace('../home/') },3000);</script>";

					return $msg;
				}
				else{return $this->createPopMessage("Error!!",$response["msg"],"red");}
			}
			elseif($kycData->selectedKycOption == 'bvn' || $kycData->kycoptionselect == 'bvn'){
				$response = $checker->verifyAndUpdateBvnOnMonnify($kycData);
				if($response["status"] == "success"){
					$msg = $this->createPopMessage("Success!!","Details Verified Successfully","green");
					$msg.=" <script>setTimeout(function(){location.replace('../home/') },3000);</script>";

					return $msg;
				}
				else{return $this->createPopMessage("Error!!",$response["msg"],"red");}
			}
			else{
				return $this->createPopMessage("Error!!","Invalid Request Sent, Please Try Again","red");
			}

		}
		
		
	// NETWORK STRENGTH	
  //----------------------------------------------------------------------------------------------------------------
	
    
     function mtnStatus(){
          $data = $this->model->mtnStatus();
          return $data; }
          
    function mtnsmeStatus(){
          $data = $this->model->mtnsmeStatus();
          return $data; }
          
    function mtncgStatus(){
          $data = $this->model->mtncgStatus();
          return $data; }
          
    function airtelStatus(){
          $data = $this->model->airtelStatus();
          return $data; }
          
    function gloStatus(){
          $data = $this->model->gloStatus();
          return $data; }
          
    function mobileStatus(){
          $data = $this->model->mobileStatus();
          return $data; }
          
    function mtnAirStatus(){
          $data = $this->model->mtnAirStatus();
          return $data; }
          
    function airtelAirStatus(){
          $data = $this->model->airtelAirStatus();
          return $data; }
          
   function gloAirStatus(){
          $data = $this->model->gloAirStatus();
          return $data; }
          
  function mobileAirStatus(){
          $data = $this->model->mobileAirStatus();
          return $data; }
  
  	//Save Beneficiary
   public function saveBeneficiary(){
    extract($_POST);
    if (preg_match('/[=\/<>?"\'!@#$%^&*()_+`~|{}\[\]]/', $name)) {
        return $this->createPopMessage("Failed!", "Name should not contain special characters.", "red");
    }

    $data = $this->model->saveBeneficiary($this->userId, $name, $phone);
    
    if ($data == false) {
        return $this->createPopMessage("Failed!", "Name or Phone Number already exists!", "red");
    } else {
        return $this->createPopMessage("Success!", "Successfully Added.", "green");
    }
   }
		
 //Get Beneficiary
   public function getBeneficiary() {
    $data = $this->model->getBeneficiary($this->userId);
    return $data;
}
  //Delete Beneficiary
  public function deleteBeneficiary() {
    extract($_POST);
    $data = $this->model->deleteBeneficiary($id, $this->userId);
    return $data;
  } 
          


 public function storeQuery() {
    extract($_POST);
    $check = $this->model->storeQuery($this->userId, $ref, $queryContent);
    header("Location: issues");
}

 public function storeReply() {
    extract($_POST);
    $replyby = 'User';
    $check = $this->model->storeReply($issueId, $replyContent,$replyby) ;
    return $check;
}

 public function getQueries(){
    $check = $this->model->getQueries($this->userId);
    return $check;
    
} public function getUnread(){
    $check = $this->model->getUnread($this->userId);
    return $check;
    
} 

public function getQueriesAndReplies(){
    if(!isset($_GET["id"])){header("Location: ./"); exit(); }
	$data=$this->model->getQueriesAndReplies($_GET["id"]);
	return $data;
}

 
 
 public function registerBusiness() {
    extract($_POST);
 
  // Handle file upload for dir_id_card
    $dir_id_card_name = $_FILES['dir_id_card']['name'];
    $dir_id_card_tmp = $_FILES['dir_id_card']['tmp_name'];
    $dir_id_card_path = "/assets/userImage/" . $dir_id_card_name;
    $dir_id_card_destination = $_SERVER['DOCUMENT_ROOT'] . $dir_id_card_path;
    move_uploaded_file($dir_id_card_tmp, $dir_id_card_destination);

    // Handle file upload for passport_photo
    $passport_photo_name = $_FILES['passport_photo']['name'];
    $passport_photo_tmp = $_FILES['passport_photo']['tmp_name'];
    $passport_photo_path = "/assets/userImage/" . $passport_photo_name;
    $passport_photo_destination = $_SERVER['DOCUMENT_ROOT'] . $passport_photo_path;
    move_uploaded_file($passport_photo_tmp, $passport_photo_destination);
    
    $status = "Pending";
    $check = $this->model->registerBusiness($this->userId, $certType, $comp_name, $alt_comp_name, $share_cap, $comp_addr, $res_addr, $bus_nature, $dir_id_card_path, $passport_photo_path, $phone_num, $status);
    if($check == 5) { return $this->createPopMessage("Error!!", "You don't have enough funds", "red");  }
    else{ return $this->createPopMessage("Submitted!", "Your details have been submitted. Your CAC certificate will be ready in 3 to 14 days. The process will be sent to your email.", "green");  }


}

 public function getCAC(){
    $check = $this->model->getCAC($this->userId);
    return $check;
	    
	}

 public function getVIN(){
    $check = $this->model->getVIN($this->userId);
    return $check;
	    
	}

 public function getDriversLicense(){
    $check = $this->model->getDriversLicense($this->userId);
    return $check;
	    
	}

 public function getPassport(){
    $check = $this->model->getPassport($this->userId);
    return $check;
	    
	}

}
	
?>