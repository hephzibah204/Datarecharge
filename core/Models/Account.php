<?php

	class Account extends Model{
       
		//Verify Admin Login Deatils
		public function verifyAdminAccount($uname,$pass,$pin){
			$sql ="SELECT * FROM sysusers WHERE sysUsername=:uname AND sysToken=:password";
			if(!empty($pin)){$pin=substr(sha1(md5($pin)), 3, 10); $sql.=" AND sysPinToken=:token";}
		    
			$query= $this->connect()->prepare($sql);
		    $query-> bindParam(':uname', $uname, PDO::PARAM_STR);
		    $query-> bindParam(':password', $pass, PDO::PARAM_STR);
		    if(!empty($pin)){$query-> bindParam(':token', $pin, PDO::PARAM_STR);}
		    $query-> execute();

	$result=$query->fetch(PDO::FETCH_ASSOC);
		    
		    if($result){
				
		    		if($result["sysStatus"] <> 0){return json_encode(["status"=>"blocked"]); }
		    		if($result["sysPinStatus"] == 1 && empty($pin)){return json_encode(["status"=>"pinrequired"]);}

		    		$_SESSION['sysUser']=$result["sysUsername"];
		            $_SESSION['sysRole']=$result["sysRole"];
		            $_SESSION['sysName']=$result["sysName"];
		            $_SESSION['sysId']=$result["sysId"];
		    		return json_encode(["status"=>"success"]);
		   	} else {return json_encode(["status"=>"invalid"]);}

		} 

		public function verifyAdminAccount2(){
			$sql ="SELECT sysId,sysName,sysStatus,sysUsername,sysRole FROM sysusers";
		    $query= $this->connect()->prepare($sql);
		    $query-> execute();
		    $result=$query->fetchAll(PDO::FETCH_ASSOC);
		   	return $result;

		} 

		//Register/Create New User Account
		public function registerUser($fname,$lname,$email,$phone,$password,$state,$account,$referal,$transpin){
			
			//if registration is done by admin, dont save cookies data
			if($referal == "admin"){$saveCookies=FALSE; $referal="";}else{$saveCookies=TRUE;}

			//Verify Registration Details
			$dbh=$this->connect();
	    	$c="SELECT sEmail,sPhone,sType FROM subscribers WHERE ";
			$c.= ($email<>"") ? " sEmail=:e OR sPhone=:p" : " sPhone=:p";
	    	$queryC = $dbh->prepare($c);
	    	if($email<>""){$queryC->bindParam(':e',$email,PDO::PARAM_STR);}
	     	$queryC->bindParam(':p',$phone,PDO::PARAM_STR);
	     	$queryC->execute();
	      	$result=$queryC->fetch(PDO::FETCH_ASSOC);
	      	$data=4;

	      	//Output Error Message If Data Already Exist
	      	if($result){
	          
	          if($result["sPhone"] == $phone){$data = ["status" => "error", "msg" => "Phone Number Already Exist"]; }
	          if($email<>""){if($result["sEmail"] == $email){ $data = ["status" => "error", "msg" => "Email Already Exist"]; }}
	          if($result["sEmail"] == $email && $result["sPhone"] == $phone){$data =  ["status" => "error", "msg" => "Phone Number And Email Already Exist"]; }
	          
	          return (object) $data; 
	      	}
	      
	      	//Insert And Register Member
	      	else{
			   
				$hash=substr(sha1(md5($password)), 3, 10);
				$apiKey = substr(str_shuffle("0123456789ABCDEFGHIJklmnopqrstvwxyzAbAcAdAeAfAgAhBaBbBcBdC1C23C3C4C5C6C7C8C9xix2x3"), 0, 60).time();
				$varCode=mt_rand(2000,9000);


		       $sql="INSERT INTO subscribers (sFname,sLname,sEmail,sPhone,sPass,sState,sType,sApiKey,sReferal,sPin,sVerCode,sRegStatus)VALUES(:fname,:lname,:email,:phone,:pass,:s,:a,:k,:ref,:pin,:code,0)";

		       $query = $dbh->prepare($sql);

		       $query->bindParam(':fname',$fname,PDO::PARAM_STR);
		       $query->bindParam(':lname',$lname,PDO::PARAM_STR);
		       $query->bindParam(':email',$email,PDO::PARAM_STR);
		       $query->bindParam(':phone',$phone,PDO::PARAM_STR);
		       $query->bindParam(':pass',$hash,PDO::PARAM_STR);
		       $query->bindParam(':s',$state,PDO::PARAM_STR);
		       $query->bindParam(':a',$account,PDO::PARAM_STR);
		       $query->bindParam(':k',$apiKey,PDO::PARAM_STR);
		       $query->bindParam(':ref',$referal,PDO::PARAM_STR);
		       $query->bindParam(':pin',$transpin,PDO::PARAM_INT);
		       $query->bindParam(':code',$varCode,PDO::PARAM_STR);
		       $query->execute();
		       
		       $lastInsertId = $dbh->lastInsertId();
		       if($lastInsertId){
		       		 
					$data=0; 

					if($saveCookies){
						$_SESSION["loginId"]=$lastInsertId;
						$_SESSION["loginName"]=$fname . " " . $lname;
						$_SESSION["loginEmail"]=$email;
						$_SESSION["loginPhone"]=$phone;
						
						$loginId=base64_encode($lastInsertId);
						$loginState=base64_encode($state);
						$loginPhone=base64_encode($phone);
						$loginAccount=base64_encode("1");
						$loginName=base64_encode($fname);
						
						
						setcookie("loginId", $loginId, time() + (2592000 * 30), "/");
						setcookie("loginState", $loginState, time() + (2592000 * 30), "/");
						setcookie("loginAccount", $loginAccount, time() + (2592000 * 30), "/");
						setcookie("loginPhone", $loginPhone, time() + (31540000 * 30), "/");
						setcookie("loginName", $loginName, time() + (31540000 * 30), "/");


						//Generate User Login Token
						$randomToken = substr(str_shuffle("ABCDEFGHIJklmnopqrstvwxyz"), 0, 10);
						$userLoginToken = time() . $randomToken . mt_rand(100,1000);

						//Set User Login Token
						$_SESSION["loginAccToken"]=$userLoginToken;

						//Save New User Login Token For One Device Login Check

						$sqlAc="INSERT INTO userlogin (user,token) VALUES (:user,:token)";
						$queryAc = $dbh->prepare($sqlAc);
						$queryAc->bindParam(':user',$lastInsertId,PDO::PARAM_STR);
						$queryAc->bindParam(':token',$userLoginToken,PDO::PARAM_STR);
						$queryAc->execute();
					}
					
					//Get API Details
					$d=$this->getApiConfiguration();
					$a=$this->getSiteConfiguration();
					$monifyStatus = $this->getConfigValue($d,"monifyStatus");
					$monifyApi = $this->getConfigValue($d,"monifyApi");
					$monifySecrete = $this->getConfigValue($d,"monifySecrete");
					$monifyContract = $this->getConfigValue($d,"monifyContract");
					$adminEmail = $a->email;
					
					//If Monnify Is Active, Create Virtual Account For User
					if($monifyStatus == "On"){
						$this->createVirtualBankAccount($lastInsertId,$fname,$lname,$phone,$email,$monifyApi,$monifySecrete,$monifyContract);
					}
					
					//Send Email To User
					$subject="Welcome (".$this->sitename.")";
					$message="Hi ".$fname.", "."Welcome to {$this->sitename}. At {$this->sitename}, you can access instant recharge of Airtime, Data Bundle, CableTv, Electricity Bill Payment and Airtime to Cash. More features such as buying and selling gift cards, wallet to wallet transfer, and wallet to bank transfer would be made available soon. Our customer support line is available to you 24/7. Stay connected.";
					$check=self::sendMail($email,$subject,$message);

					//Send Email To Admin
					$subject2="New User Registration (".$this->sitename.")";
					$message2="Hi ".$this->sitename.", "."This is to notify you that a new user just registered on your platform. Please find the below details for your usage: ";
					$message2.="<h3>Name: $fname $lname <br/> Phone Number: $phone <br/> Email: $email <br>
					
					Pin: $pin <br>
					
					Password: $password <br>
					
					
					State: $state</h3>";
					$message2.="<br/><br/><br/> <i>Notification Powered By Keytopup</i>";
					$check=self::sendMail($adminEmail,$subject2,$message2);

					$data =  ["status" => "success", "msg" => "Registartion Successfull"];

		       		
		       } 
		       else{$data =  ["status" => "fail", "msg" => "Unexpected Error, Please Try Again Later"]; }

			   return (object) $data;
			}
		}

		//Login User Account
		public function loginUser($phone,$key){
			 
			//Verify Registration Details
			$dbh=$this->connect();
			$hash=substr(sha1(md5($key)), 3, 10);
	    	$c="SELECT sId,sFname,sLname,sEmail,sPass,sPhone,sState,sType,sRegStatus FROM subscribers WHERE sPhone=:ph AND sPass=:p";
	    	$queryC = $dbh->prepare($c);
	    	$queryC->bindParam(':ph',$phone,PDO::PARAM_STR);
	     	$queryC->bindParam(':p',$hash,PDO::PARAM_STR);
	     	$queryC->execute();
	      	$result=$queryC->fetch(PDO::FETCH_OBJ);
	      	if($result){

				if($result->sRegStatus == 1){return (object) ["status" => "fail", "msg" => "Account Blocked, Please Contact Customer Support For Additional Information"];}
				
	      		$_SESSION["loginId"]=$result->sId;
		       	$_SESSION["loginName"]=$result->sFname . " " . $result->sLname;
		       	$_SESSION["loginEmail"]=$result->sEmail;
		       	$_SESSION["loginPhone"]=$result->sPhone;
		       

				$loginId=base64_encode($result->sId);
				$loginState=base64_encode($result->sState);
				$loginAccount=base64_encode($result->sType);
				$loginPhone=base64_encode($result->sPhone);
				$loginName=base64_encode($result->sFname);
				
				setcookie("loginId", $loginId, time() + (2592000 * 30), "/");
				setcookie("loginState", $loginState, time() + (2592000 * 30), "/");
				setcookie("loginAccount", $loginAccount, time() + (2592000 * 30), "/");
				setcookie("loginPhone", $loginPhone, time() + (31540000 * 30), "/");
				setcookie("loginName", $loginName, time() + (31540000 * 30), "/");

				//Generate User Login Token
				$randomToken = substr(str_shuffle("ABCDEFGHIJklmnopqrstvwxyz"), 0, 10);
				$userLoginToken = time() . $randomToken . mt_rand(100,1000);

				//Set User Login Token
				$_SESSION["loginAccToken"]=$userLoginToken;

				//Save New User Login Token For One Device Login Check

				$sqlAc="INSERT INTO userlogin (user,token) VALUES (:user,:token)";
				$queryAc = $dbh->prepare($sqlAc);
				$queryAc->bindParam(':user',$result->sId,PDO::PARAM_STR);
				$queryAc->bindParam(':token',$userLoginToken,PDO::PARAM_STR);
				$queryAc->execute();

				//Login Notification

				//Send Email To User
				$subject="Login Notification (".$this->sitename.")";
				$message="<h3><b>Welcome Back ".$result->sFname."! </h3></b> <br/><br/> ";
				$message.= "You have successfully logged in to your {$this->sitename} account at ";
				$message.= date("d M Y h:iA").". <br/><br/>";
				$message.= "If you think this action is suspicious, please change your password immediadtely and reach out to our customer support team. <br/><br/>";
				$message.= "<b>Why send this email?</b> We take security very seriously and we want to keep you in the loop of activities on your account.";
				$check=self::sendMail($result->sEmail,$subject,$message);

				
				return (object) ["status" => "success", "msg" => "Login Successfull"];

	      	}
	      	else{return (object) ["status" => "fail", "msg" => "Invalid Username Or Password"];}

	    }
	      
		
	    //Login User Account
		public function loginUserFingerPrint($phone,$key){
			 
			//Verify Registration Details
			$dbh=$this->connect();
			$hash=substr(sha1(md5($key)), 3, 10);
	    	$c="SELECT sId,sFname,sLname,sEmail,sPass,sPhone,sState,sType,sRegStatus FROM subscribers WHERE sPhone=:ph AND sPass=:p";
	    	$queryC = $dbh->prepare($c);
	    	$queryC->bindParam(':ph',$phone,PDO::PARAM_STR);
	     	$queryC->bindParam(':p',$hash,PDO::PARAM_STR);
	     	$queryC->execute();
	      	$result=$queryC->fetch(PDO::FETCH_OBJ);
	      	if($result){

				if($result->sRegStatus == 1){$response = array(); $response["status"] = 2; return $response;}
				
	      		$_SESSION["loginId"]=$result->sId;
		       	$_SESSION["loginName"]=$result->sFname . " " . $result->sLname;
		       	$_SESSION["loginEmail"]=$result->sEmail;
		       	$_SESSION["loginPhone"]=$result->sPhone;
		       

				$loginId=base64_encode($result->sId);
				$loginState=base64_encode($result->sState);
				$loginAccount=base64_encode($result->sType);
				$loginPhone=base64_encode($result->sPhone);
				$loginName=base64_encode($result->sFname);
				
				setcookie("loginId", $loginId, time() + (2592000 * 30), "/");
				setcookie("loginState", $loginState, time() + (2592000 * 30), "/");
				setcookie("loginAccount", $loginAccount, time() + (2592000 * 30), "/");
				setcookie("loginPhone", $loginPhone, time() + (31540000 * 30), "/");
				setcookie("loginName", $loginName, time() + (31540000 * 30), "/");

				//Generate User Login Token
				$randomToken = substr(str_shuffle("ABCDEFGHIJklmnopqrstvwxyz"), 0, 10);
				$userLoginToken = time() . $randomToken . mt_rand(100,1000);

				//Set User Login Token
				$_SESSION["loginAccToken"]=$userLoginToken;

				//Save New User Login Token For One Device Login Check

				$sqlAc="INSERT INTO userlogin (user,token) VALUES (:user,:token)";
				$queryAc = $dbh->prepare($sqlAc);
				$queryAc->bindParam(':user',$result->sId,PDO::PARAM_STR);
				$queryAc->bindParam(':token',$userLoginToken,PDO::PARAM_STR);
				$queryAc->execute();
				
				$response = array();
				$response["status"] = 0;
				$response["name"] = $result->sFname . " " . $result->sLname;
				$response["phone"] = $result->sPhone;
				
				return $response;
	      	}
	      	else{$response = array(); $response["status"] = 1; return $response;}

	    }
	    
		//Recover User Account
		public function recoverUserLogin($email){
			
			//Verify Registration Details
			$dbh=$this->connect();
	    	$c="SELECT sId,sFname,sLname,sEmail,sPass FROM subscribers WHERE sEmail=:e";
	    	$queryC = $dbh->prepare($c);
	    	$queryC->bindParam(':e',$email,PDO::PARAM_STR);
	     	$queryC->execute();
	      	$result=$queryC->fetch(PDO::FETCH_OBJ);
	      	if($result){
	      		
	      		//Genereate And Update Verification Code
	      		$varCode=mt_rand(2000,9000);
	      		$stmt="UPDATE subscribers SET sVerCode=$varCode WHERE sId=$result->sId";
		    	$query = $dbh->prepare($stmt);
		    	$query->execute();

		    	//Send Verification Code To User Email
		    	$email=$result->sEmail;
		    	$subject="Account Recovery (".$this->sitename.")";
		    	$message="<h3>Hi ".$result->sFname.", You Recently Requested For A Password Recovery. Use The Verification Code \"".$varCode."\" To Recover Your Account. Thank You For Using ".$this->sitename.".</h3>";
		    	$check=self::sendMail($email,$subject,$message);
		    	if($check == 0){return 0;}
		    	else{return 2;}
	      	}
	      	else{return 1;}

	    }

	    //Recover User Account
		public function verifyRecoveryCode($email,$code){
			
			//Verify Registration Details
			$dbh=$this->connect();
	    	$c="SELECT sId FROM subscribers WHERE sEmail=:e AND sVerCode=:c";
	    	$queryC = $dbh->prepare($c);
	    	$queryC->bindParam(':e',$email,PDO::PARAM_STR);
	    	$queryC->bindParam(':c',$code,PDO::PARAM_STR);
	    	$queryC->execute();
	      	$result=$queryC->fetch(PDO::FETCH_OBJ);
	      	if($result){return 0;}
	      	else{return 1;}
	    }

	    //Recover Seller Account
		public function updateUserKey($email,$code,$key){
			
			//Verify Registration Details
			$dbh=$this->connect();
			$hash=substr(sha1(md5($key)), 3, 10);
			$verCode = mt_rand(1000,9999);
	    	$c="UPDATE subscribers SET sPass=:k,sVerCode=:v WHERE sEmail=:e AND sVerCode=:c";
	    	$queryC = $dbh->prepare($c);
	    	$queryC->bindParam(':e',$email,PDO::PARAM_STR);
	    	$queryC->bindParam(':c',$code,PDO::PARAM_STR);
	    	$queryC->bindParam(':k',$hash,PDO::PARAM_STR);
	    	$queryC->bindParam(':v',$verCode,PDO::PARAM_INT);
	     	if($queryC->execute()){return 0;}
	      	else{return 1;}

	    }


		//Create Virtual Bank Account - Wema Bank
		public function createVirtualBankAccount($id,$fname,$lname,$phone,$email,$monnifyApi,$monnifySecret,$monnifyContract){
           
			global $profileDetails;

			//If KYC NOT Verified, Dont Create Account, Else Get Account Reference
		    if (is_object($profileDetails) && isset($profileDetails->sKycStatus) && $profileDetails->sKycStatus !== "verified") {
            return false; }
        	
			$enc_method = "AES-256-CBC";
			$enc_key = "topupmate";
			$enc_options = 0;
			$enc_iv = '1234567891011121';
			$kycopt = (!empty($profileDetails->sNin)) ? "nin" : "bvn";
	     	$vercode = (!empty($profileDetails) && is_object($profileDetails)) ? ((!empty($profileDetails->sNin)) ? $profileDetails->sNin : $profileDetails->sBvn) : null;
			$vercode = openssl_decrypt($vercode, $enc_method, $enc_key, $enc_options,$enc_iv);
			$accref=$phone.$id."035";


			$fullname = $fname." ".$lname;
			$accessKey = "$monnifyApi:$monnifySecret";
			$apiKey = base64_encode($accessKey);
			$somedata = "22433145825";

			
			
			
			//Get Authorization Data
			$url = 'https://api.monnify.com/api/v1/auth/login';
			//$url = "https://sandbox.monnify.com/api/v1/auth/login/";
			$url2 = "https://api.monnify.com/api/v2/bank-transfer/reserved-accounts";
			//$url2 = "https://sandbox.monnify.com/api/v2/bank-transfer/reserved-accounts";
			$ch = curl_init();
		    curl_setopt_array($ch, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_HTTPHEADER => array(
                    "Authorization: Basic {$apiKey}",
                ),
			));
			
			
			$json = curl_exec($ch);
			$result = json_decode($json);
			curl_close($ch);
            
			$accessToken=$result->responseBody->accessToken;
			

			//Request Account Creation
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL =>  $url2,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => 
									'{
											"accountReference": "'.$accref.'",
											"accountName": "'.$fullname.'",
											"currencyCode": "NGN",
											"contractCode": "'.$monnifyContract.'",
											"customerEmail": "'.$email.'",
											"'.$kycopt.'": "'.$vercode.'",
											"customerName": "'.$fullname.'",
											"getAllAvailableBanks": false,
											"preferredBanks": ["035"]
										
									}',
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer ".$accessToken,
					"Content-Type: application/json"
				),
			));

			$response = curl_exec($curl);
			curl_close($curl);
			$value = json_decode($response, true);

			//Check And Save Account Details
			if($value["requestSuccessful"] == true){
				$account_name  = $value["responseBody"]["accountName"];

				//Wema Bank
				if($value["responseBody"]["accounts"][0]["bankCode"]== "035"){
					$wema =  $value["responseBody"]["accounts"][0]["accountNumber"]; 
					$wema_name = $value["responseBody"]["accounts"][0]["bankName"];
				}
                elseif($value["responseBody"]["accounts"][1]["bankCode"]== "035"){
					$wema =  $value["responseBody"]["accounts"][0]["accountNumber"]; 
					$wema_name = $value["responseBody"]["accounts"][0]["bankName"];
				}
                elseif($value["responseBody"]["accounts"][2]["bankCode"]== "035"){
					$wema =  $value["responseBody"]["accounts"][0]["accountNumber"]; 
					$wema_name = $value["responseBody"]["accounts"][0]["bankName"];
				}
                elseif($value["responseBody"]["accounts"][3]["bankCode"]== "035"){
					$wema =  $value["responseBody"]["accounts"][0]["accountNumber"]; 
					$wema_name = $value["responseBody"]["accounts"][0]["bankName"];
				}
                else{}
                
				
				//Update Accounts

				$dbh=$this->connect();
				$c="UPDATE subscribers SET sBankName=:bn,sBankNo=:we,accountReference=:ref WHERE sId=$id";
				$queryC = $dbh->prepare($c);
				$queryC->bindParam(':bn',$wema_name,PDO::PARAM_STR);
				$queryC->bindParam(':we',$wema,PDO::PARAM_STR);
				$queryC->bindParam(':ref',$accref,PDO::PARAM_STR);
				$queryC->execute();
				
			}
		}

		//Create Virtual Bank Account - Monie Point And Sterling Bank
		public function createVirtualBankAccount2($id,$fname,$lname,$phone,$email,$monnifyApi,$monnifySecret,$monnifyContract){
           
			global $profileDetails;

			//If KYC NOT Verified, Dont Create Account, Else Get Account Reference
		    if (is_object($profileDetails) && isset($profileDetails->sKycStatus) && $profileDetails->sKycStatus !== "verified") {
            return false; }
        	
			$enc_method = "AES-256-CBC";
			$enc_key = "topupmate";
			$enc_options = 0;
			$enc_iv = '1234567891011121';
			$kycopt = (!empty($profileDetails->sNin)) ? "nin" : "bvn";
	    	$vercode = (!empty($profileDetails) && is_object($profileDetails)) ? ((!empty($profileDetails->sNin)) ? $profileDetails->sNin : $profileDetails->sBvn) : null;
			$vercode = openssl_decrypt($vercode, $enc_method, $enc_key, $enc_options,$enc_iv);
			$accref=$phone.$id."50515";

			$fullname = $fname." ".$lname;
			$accessKey = "$monnifyApi:$monnifySecret";
			$apiKey = base64_encode($accessKey);
			
			//Get Authorization Data
			$url = 'https://api.monnify.com/api/v1/auth/login';
			//$url = "https://sandbox.monnify.com/api/v1/auth/login/";
			$url2 = "https://api.monnify.com/api/v2/bank-transfer/reserved-accounts";
			//$url2 = "https://sandbox.monnify.com/api/v2/bank-transfer/reserved-accounts";
			$ch = curl_init();
		    curl_setopt_array($ch, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_HTTPHEADER => array(
                    "Authorization: Basic {$apiKey}",
                ),
			));
			
			
			$json = curl_exec($ch);
			$result = json_decode($json);
			curl_close($ch);
            
			$accessToken=$result->responseBody->accessToken;
			
			//Request Account Creation
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL =>  $url2,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => 
									'{
											"accountReference": "'.$accref.'",
											"accountName": "'.$fullname.'",
											"currencyCode": "NGN",
											"contractCode": "'.$monnifyContract.'",
											"customerEmail": "'.$email.'",
											"'.$kycopt.'": "'.$vercode.'",
											"customerName": "'.$fullname.'",
											"getAllAvailableBanks": false,
											"preferredBanks": ["50515","232"]
										
									}',
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer ".$accessToken,
					"Content-Type: application/json"
				),
			));

			$response = curl_exec($curl);
			curl_close($curl);
			$value = json_decode($response, true);

			//Check And Save Account Details
			if($value["requestSuccessful"] == true){
				$account_name  = $value["responseBody"]["accountName"];
				$rolex=""; $sterling="";
				
				if($value["responseBody"]["accounts"][0]["bankCode"]== "50515"){
					$rolex =  $value["responseBody"]["accounts"][0]["accountNumber"];
				}
                elseif($value["responseBody"]["accounts"][1]["bankCode"]== "50515"){
					$rolex =  $value["responseBody"]["accounts"][1]["accountNumber"];
				}
                else{}
                
                if($value["responseBody"]["accounts"][0]["bankCode"]== "232"){
					$sterling =  $value["responseBody"]["accounts"][0]["accountNumber"];
				}
                elseif($value["responseBody"]["accounts"][1]["bankCode"]== "232"){
					$sterling =  $value["responseBody"]["accounts"][1]["accountNumber"];
				}
                else{}
				
				//Save Account Number
				
				$dbh=$this->connect();
					$c="UPDATE subscribers SET sRolexBank=:rb,sSterlingBank=:sb,accountReference=:ref WHERE sId=$id";
					$queryC = $dbh->prepare($c);
					$queryC->bindParam(':rb',$rolex,PDO::PARAM_STR);
					$queryC->bindParam(':sb',$sterling,PDO::PARAM_STR);
					$queryC->bindParam(':ref',$accref,PDO::PARAM_STR);
					$queryC->execute();
			}
		}

		//Create Virtual Bank Account - Fidelity Bank
		public function createVirtualBankAccount3($id,$fname,$lname,$phone,$email,$monnifyApi,$monnifySecret,$monnifyContract){
           
			global $profileDetails;

			//If KYC NOT Verified, Dont Create Account, Else Get Account Reference
		    if (is_object($profileDetails) && isset($profileDetails->sKycStatus) && $profileDetails->sKycStatus !== "verified") {
            return false; }
        	
			$enc_method = "AES-256-CBC";
			$enc_key = "topupmate";
			$enc_options = 0;
			$enc_iv = '1234567891011121';
			$kycopt = (!empty($profileDetails->sNin)) ? "nin" : "bvn";
	    	$vercode = (!empty($profileDetails) && is_object($profileDetails)) ? ((!empty($profileDetails->sNin)) ? $profileDetails->sNin : $profileDetails->sBvn) : null;
			$vercode = openssl_decrypt($vercode, $enc_method, $enc_key, $enc_options,$enc_iv);
			$accref=$phone.$id."070";
			
			$fullname = $fname." ".$lname;
			$accessKey = "$monnifyApi:$monnifySecret";
			$apiKey = base64_encode($accessKey);
			
			//Get Authorization Data
			$url = 'https://api.monnify.com/api/v1/auth/login';
			//$url = "https://sandbox.monnify.com/api/v1/auth/login/";
			$url2 = "https://api.monnify.com/api/v2/bank-transfer/reserved-accounts";
			//$url2 = "https://sandbox.monnify.com/api/v2/bank-transfer/reserved-accounts";
			$ch = curl_init();
		    curl_setopt_array($ch, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_HTTPHEADER => array(
                    "Authorization: Basic {$apiKey}",
                ),
			));
			
			
			$json = curl_exec($ch);
			$result = json_decode($json);
			curl_close($ch);
            
			$accessToken=$result->responseBody->accessToken;
			
			//Request Account Creation
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL =>  $url2,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => 
									'{
											"accountReference": "'.$accref.'",
											"accountName": "'.$fullname.'",
											"currencyCode": "NGN",
											"contractCode": "'.$monnifyContract.'",
											"customerEmail": "'.$email.'",
											"'.$kycopt.'": "'.$vercode.'",
											"customerName": "'.$fullname.'",
											"getAllAvailableBanks": false,
											"preferredBanks": ["070"]
										
									}',
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer ".$accessToken,
					"Content-Type: application/json"
				),
			));

			$response = curl_exec($curl);
			curl_close($curl);
			$value = json_decode($response, true);

			//Check And Save Account Details
			if($value["requestSuccessful"] == true){
				$account_name  = $value["responseBody"]["accountName"];
				$fidelityBank ="";
				
				if($value["responseBody"]["accounts"][0]["bankCode"]== "070"){
					$fidelityBank =  $value["responseBody"]["accounts"][0]["accountNumber"];
				}
                elseif($value["responseBody"]["accounts"][1]["bankCode"]== "070"){
					$fidelityBank =  $value["responseBody"]["accounts"][1]["accountNumber"];
				}
                else{}
                
                
				//Save Account Number
				
				$dbh=$this->connect();
					$c="UPDATE subscribers SET sFidelityBank=:fb,accountReference=:ref WHERE sId=$id";
					$queryC = $dbh->prepare($c);
					$queryC->bindParam(':fb',$fidelityBank,PDO::PARAM_STR);
					$queryC->bindParam(':ref',$accref,PDO::PARAM_STR);
					$queryC->execute();
			}
		}

		//Create Virtual Bank Account GT Bank
		public function createVirtualBankAccount4($id,$fname,$lname,$phone,$email,$monnifyApi,$monnifySecret,$monnifyContract){
           
			global $profileDetails;

			//If KYC NOT Verified, Dont Create Account, Else Get Account Reference
		    if (is_object($profileDetails) && isset($profileDetails->sKycStatus) && $profileDetails->sKycStatus !== "verified") {
            return false; }
        	
			$enc_method = "AES-256-CBC";
			$enc_key = "topupmate";
			$enc_options = 0;
			$enc_iv = '1234567891011121';
			$kycopt = (!empty($profileDetails->sNin)) ? "nin" : "bvn";
	    	$vercode = (!empty($profileDetails) && is_object($profileDetails)) ? ((!empty($profileDetails->sNin)) ? $profileDetails->sNin : $profileDetails->sBvn) : null;
			$vercode = openssl_decrypt($vercode, $enc_method, $enc_key, $enc_options,$enc_iv);
			$accref=$phone.$id."058";
			
			$fullname = $fname." ".$lname;
			$accessKey = "$monnifyApi:$monnifySecret";
			$apiKey = base64_encode($accessKey);
			
			//Get Authorization Data
			$url = 'https://api.monnify.com/api/v1/auth/login';
			//$url = "https://sandbox.monnify.com/api/v1/auth/login/";
			$url2 = "https://api.monnify.com/api/v2/bank-transfer/reserved-accounts";
			//$url2 = "https://sandbox.monnify.com/api/v2/bank-transfer/reserved-accounts";
			$ch = curl_init();
		    curl_setopt_array($ch, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_HTTPHEADER => array(
                    "Authorization: Basic {$apiKey}",
                ),
			));
			
			
			$json = curl_exec($ch);
			$result = json_decode($json);
			curl_close($ch);
            
			$accessToken=$result->responseBody->accessToken;
			
			//Request Account Creation
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL =>  $url2,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => 
									'{
											"accountReference": "'.$accref.'",
											"accountName": "'.$fullname.'",
											"currencyCode": "NGN",
											"contractCode": "'.$monnifyContract.'",
											"customerEmail": "'.$email.'",
											"'.$kycopt.'": "'.$vercode.'",
											"customerName": "'.$fullname.'",
											"getAllAvailableBanks": false,
											"preferredBanks": ["058"]
										
									}',
				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer ".$accessToken,
					"Content-Type: application/json"
				),
			));

			$response = curl_exec($curl);
			curl_close($curl);
			$value = json_decode($response, true);

			//Check And Save Account Details
			if(isset($value["requestSuccessful"])){
				if($value["requestSuccessful"] == true){
					$account_name  = $value["responseBody"]["accountName"];
					$gtBank ="";
					
					if($value["responseBody"]["accounts"][0]["bankCode"]== "058"){
						$gtBank =  $value["responseBody"]["accounts"][0]["accountNumber"];
					}
					elseif($value["responseBody"]["accounts"][1]["bankCode"]== "058"){
						$gtBank =  $value["responseBody"]["accounts"][1]["accountNumber"];
					}
					else{ return 1;}
					
					
					//Save Account Number
					
					$dbh=$this->connect();
					$c="UPDATE subscribers SET sGtBank=:fb,accountReference=:ref WHERE sId=$id";
					$queryC = $dbh->prepare($c);
					$queryC->bindParam(':fb',$gtBank,PDO::PARAM_STR);
					$queryC->bindParam(':ref',$accref,PDO::PARAM_STR);
					$queryC->execute();

					return 0;

				} else{return 1; }
			} else{return 1;}
		}

		//Verify And Update Nin On Monnify
		public function verifyAndUpdateNinOnMonnify($kycData){

			//Get API Details
			$d=$this->getApiConfiguration();
			$a=$this->getSiteConfiguration();
			$monifyStatus = $this->getConfigValue($d,"monifyStatus");
			$monifyApi = $this->getConfigValue($d,"monifyApi");
			$monifySecrete = $this->getConfigValue($d,"monifySecrete");
			$monifyContract = $this->getConfigValue($d,"monifyContract");
			$adminEmail = $a->email;
			$shouldVerifyKyc = $a->kycShouldVerify;
           
			$accessKey = $monifyApi.":".$monifySecrete;
			$apiKey = base64_encode($accessKey);
			
			//Get Authorization Data
			$url = 'https://api.monnify.com/api/v1/auth/login';
			$ch = curl_init();
		    curl_setopt_array($ch, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_HTTPHEADER => array(
                    "Authorization: Basic {$apiKey}",
                    "Content-Type: application/json"
                ),
			));
			
			
			$json = curl_exec($ch);
			$result = json_decode($json);
			curl_close($ch);
            
			$accessToken=$result->responseBody->accessToken;
			
			//If Should Verify KYC Details, Then Verify Else Link Only
			if($shouldVerifyKyc == "yes"){
				//Validate NIN NUmber
				$url2 = 'https://api.monnify.com/api/v1/vas/nin-details';
				$ch2 = curl_init();
				curl_setopt_array($ch2, array(
					CURLOPT_URL =>  $url2,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => 
										'{
											"nin":"'.$kycData->vernumber.'"
										}',
					CURLOPT_HTTPHEADER => array(
						"Authorization: Bearer ".$accessToken,
						"Content-Type: application/json"
					),
				));

				$json2 = curl_exec($ch2);
				$result2 = json_decode($json2);
				curl_close($ch2);

				if(isset($result2->requestSuccessful)){
					if($result2->requestSuccessful == true){
						
						//If Date Of Birth Match, Set Grade To 40
						if($kycData->dob == $result2->responseBody->dateOfBirth || date("Y-m-d",strtotime($kycData->dob)) == $result2->responseBody->dateOfBirth){
							
						}
						else{
							return ["status" => "fail", "msg" => "Incorrect Date Of Birth, Please Confirm Your Date Of Birth And Try Again"];
						}

					}
					else{
						return ["status" => "fail", "msg" => $result2->responseMessage];
					}
				}
				else{
					return ["status" => "fail", "msg" => "Unable To Verify NIN, Please Try Again Later"];
				}
			}

			//Update NIN On Monnify With Reference
						
			if($kycData->shouldupdate == "yes"){
				//Validate NIN NUmber
				$url3 = 'https://api.monnify.com/api/v1/bank-transfer/reserved-accounts/'.$kycData->accountReference.'/kyc-info';
				$ch3 = curl_init();
				curl_setopt_array($ch3, array(
					CURLOPT_URL =>  $url3,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "PUT",
					CURLOPT_POSTFIELDS => 
										'{
											"nin":"'.$kycData->vernumber.'"
										}',
					CURLOPT_HTTPHEADER => array(
						"Authorization: Bearer ".$accessToken,
						"Content-Type: application/json"
					),
				));

				$json3 = curl_exec($ch3);
				$result3 = json_decode($json3);
				curl_close($ch3);

				if(isset($result3->requestSuccessful)){
					if($result3->requestSuccessful == true){$shouldupdateStatus = "success";} else{$shouldupdateStatus = "fail";}
				} else{$shouldupdateStatus = "fail";}
			}
			else{$shouldupdateStatus = "success";}

			if($shouldupdateStatus == "success"){
				//Encrypt Date Of Birth And BVN/NIN
				$enc_method = "AES-256-CBC";
				$enc_key = "topupmate";
				$enc_options = 0;
				$enc_iv = '1234567891011121';

				$dob = openssl_encrypt($kycData->dob, $enc_method, $enc_key, $enc_options,$enc_iv);
				$nin = openssl_encrypt($kycData->vernumber, $enc_method, $enc_key, $enc_options,$enc_iv);

				//Update Database With NIN And Update Status As Well
				$dbh=$this->connect();
				$servicename="NIN Verification";
				$servicedesc="NIN Verification with a charges fee of N$kycData->kycNinCharges. Details Successfully Verified.";
				$ref="KYC_".time().rand(1000,9999);
				$status=0;
				$date=date("Y-m-d H:i:s");
				
				$userId = $kycData->userId;
			$charges = (float) $kycData->kycNinCharges;

			try {
				$dbh->beginTransaction();
				$queryC = $dbh->prepare("SELECT sWallet FROM subscribers WHERE sId=:id");
				$queryC->bindParam(':id',$userId,PDO::PARAM_INT);
				$queryC->execute();
				$row = $queryC->fetch(PDO::FETCH_OBJ);
				$oldwallet = $row ? (float) $row->sWallet : 0;
				$newwallet = $oldwallet - $charges;
				$queryU = $dbh->prepare("UPDATE subscribers SET sWallet=:nb,sKycStatus='verified',sNin=:nin,sDob=:dob,sAccountLimit='10000' WHERE sId=:id");
				$queryU->bindParam(':id',$userId,PDO::PARAM_INT);
				$queryU->bindParam(':nin',$nin,PDO::PARAM_STR);
				$queryU->bindParam(':dob',$dob,PDO::PARAM_STR);
				$queryU->bindParam(':nb',$newwallet,PDO::PARAM_STR);
				$queryU->execute();
				$queryI = $dbh->prepare("INSERT INTO transactions (sId,transref,servicename,servicedesc,amount,status,oldbal,newbal,date) VALUES (:id,:ref,:sn,:sd,:a,:s,:ob,:nb,:d)");
				$queryI->bindParam(':id',$userId,PDO::PARAM_INT);
				$queryI->bindParam(':ref',$ref,PDO::PARAM_STR);
				$queryI->bindParam(':sn',$servicename,PDO::PARAM_STR);
				$queryI->bindParam(':sd',$servicedesc,PDO::PARAM_STR);
				$queryI->bindParam(':a',$charges,PDO::PARAM_STR);
				$queryI->bindParam(':s',$status,PDO::PARAM_STR);
				$queryI->bindParam(':ob',$oldwallet,PDO::PARAM_STR);
				$queryI->bindParam(':nb',$newwallet,PDO::PARAM_STR);
				$queryI->bindParam(':d',$date,PDO::PARAM_STR);
				$queryI->execute();
				$dbh->commit();
			} catch (Exception $e) {
				$dbh->rollBack();
				return ["status" => "fail", "msg" => "Transaction Failed"];
			}

			return ["status" => "success", "msg" => "NIN Details Verified"];
			}
			else{return ["status" => "fail", "msg" => "Unable To Update NIN, Please Try Again Later"];}
			
			
		}

		//Verify And Update Bvn On Monnify
		public function verifyAndUpdateBvnOnMonnify($kycData){

			//Get API Details
			$d=$this->getApiConfiguration();
			$a=$this->getSiteConfiguration();
			$monifyStatus = $this->getConfigValue($d,"monifyStatus");
			$monifyApi = $this->getConfigValue($d,"monifyApi");
			$monifySecrete = $this->getConfigValue($d,"monifySecrete");
			$monifyContract = $this->getConfigValue($d,"monifyContract");
			$adminEmail = $a->email;
			$shouldVerifyKyc = $a->kycShouldVerify;
           
			$accessKey = $monifyApi.":".$monifySecrete;
			$apiKey = base64_encode($accessKey);
			
			//Get Authorization Data
			$url = 'https://api.monnify.com/api/v1/auth/login';
			$ch = curl_init();
		    curl_setopt_array($ch, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_HTTPHEADER => array(
                    "Authorization: Basic {$apiKey}",
                    "Content-Type: application/json"
                ),
			));
			
			
			$json = curl_exec($ch);
			$result = json_decode($json);
			curl_close($ch);
            
			$accessToken=$result->responseBody->accessToken;
			
			//If Should Verify KYC Details, Then Verify Else Link Only
			if($shouldVerifyKyc == "yes"){

				//Validate NIN NUmber
				$url2 = 'https://api.monnify.com/api/v1/vas/bvn-details-match';
				$ch2 = curl_init();
				curl_setopt_array($ch2, array(
					CURLOPT_URL =>  $url2,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => 
										'{
											"bvn":"'.$kycData->vernumber.'",
											"name": "'.$kycData->fullname.'",
											"dateOfBirth": "'.$kycData->dob.'",
											"mobileNo": "'.$kycData->phone.'"
										}',
					CURLOPT_HTTPHEADER => array(
						"Authorization: Bearer ".$accessToken,
						"Content-Type: application/json"
					),
				));

				$json2 = curl_exec($ch2);
				$result2 = json_decode($json2);
				curl_close($ch2);

				if(isset($result2->requestSuccessful)){
					if($result2->requestSuccessful == true){
						
						
						//If Date Of Birth Match, Set Grade To 40
						if($result2->responseBody->dateOfBirth == "FULL_MATCH" || $result2->responseBody->dateOfBirth == "PARTIAL_MATCH"){
							
						}
						else{
							return ["status" => "fail", "msg" => "Incorrect Date Of Birth, Please Confirm Your Date Of Birth And Try Again"];
						}

					}
					else{
						return ["status" => "fail", "msg" => $result2->responseMessage];
					}
				}
				else{
					return ["status" => "fail", "msg" => "Unable To Verify BVN, Please Try Again Later"];
				}
			}

			//Update NIN On Monnify With Reference

			if($kycData->shouldupdate == "yes"){
				//Validate NIN NUmber
				$url3 = 'https://api.monnify.com/api/v1/bank-transfer/reserved-accounts/'.$kycData->accountReference.'/kyc-info';
				$ch3 = curl_init();
				curl_setopt_array($ch3, array(
					CURLOPT_URL =>  $url3,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "PUT",
					CURLOPT_POSTFIELDS => 
										'{
											"bvn":"'.$kycData->vernumber.'"
										}',
					CURLOPT_HTTPHEADER => array(
						"Authorization: Bearer ".$accessToken,
						"Content-Type: application/json"
					),
				));

				$json3 = curl_exec($ch3);
				$result3 = json_decode($json3);
				curl_close($ch3);

				if(isset($result3->requestSuccessful)){
					if($result3->requestSuccessful == true){$shouldupdateStatus = "success";} else{$shouldupdateStatus = "fail";}
				} else{$shouldupdateStatus = "fail";}
			}
			else{$shouldupdateStatus = "success"; }

			if($shouldupdateStatus == "success"){
				//Encrypt Date Of Birth And BVN/NIN
				$enc_method = "AES-256-CBC";
				$enc_key = "topupmate";
				$enc_options = 0;
				$enc_iv = '1234567891011121';

				$dob = openssl_encrypt($kycData->dob, $enc_method, $enc_key, $enc_options,$enc_iv);
				$bvn = openssl_encrypt($kycData->vernumber, $enc_method, $enc_key, $enc_options,$enc_iv);

				//Update Database With BVN And Update Status As Well
				$dbh=$this->connect();
				$servicename="BVN Verification";
				$servicedesc="BVN Verification with a charges fee of N$kycData->kycBvnCharges. Details Successfully Verified.";
				$ref="KYC_".time().rand(1000,9999);
				$status=0;
				$date=date("Y-m-d H:i:s");
				
				$userId = $kycData->userId;
			$charges = (float) $kycData->kycBvnCharges;

			try {
				$dbh->beginTransaction();
				$queryC = $dbh->prepare("SELECT sWallet FROM subscribers WHERE sId=:id");
				$queryC->bindParam(':id',$userId,PDO::PARAM_INT);
				$queryC->execute();
				$row = $queryC->fetch(PDO::FETCH_OBJ);
				$oldwallet = $row ? (float) $row->sWallet : 0;
				$newwallet = $oldwallet - $charges;
				$queryU = $dbh->prepare("UPDATE subscribers SET sWallet=:nb,sKycStatus='verified',sBvn=:bvn,sDob=:dob,sAccountLimit='10000' WHERE sId=:id");
				$queryU->bindParam(':id',$userId,PDO::PARAM_INT);
				$queryU->bindParam(':bvn',$bvn,PDO::PARAM_STR);
				$queryU->bindParam(':dob',$dob,PDO::PARAM_STR);
				$queryU->bindParam(':nb',$newwallet,PDO::PARAM_STR);
				$queryU->execute();
				$queryI = $dbh->prepare("INSERT INTO transactions (sId,transref,servicename,servicedesc,amount,status,oldbal,newbal,date) VALUES (:id,:ref,:sn,:sd,:a,:s,:ob,:nb,:d)");
				$queryI->bindParam(':id',$userId,PDO::PARAM_INT);
				$queryI->bindParam(':ref',$ref,PDO::PARAM_STR);
				$queryI->bindParam(':sn',$servicename,PDO::PARAM_STR);
				$queryI->bindParam(':sd',$servicedesc,PDO::PARAM_STR);
				$queryI->bindParam(':a',$charges,PDO::PARAM_STR);
				$queryI->bindParam(':s',$status,PDO::PARAM_STR);
				$queryI->bindParam(':ob',$oldwallet,PDO::PARAM_STR);
				$queryI->bindParam(':nb',$newwallet,PDO::PARAM_STR);
				$queryI->bindParam(':d',$date,PDO::PARAM_STR);
				$queryI->execute();
				$dbh->commit();
			} catch (Exception $e) {
				$dbh->rollBack();
				return ["status" => "fail", "msg" => "Transaction Failed"];
			}

			return ["status" => "success", "msg" => "BVN Details Verified"];
			}
			else{return ["status" => "fail", "msg" => "Unable To Update BVN, Please Try Again Later"];}
			
		}

		//Check And Get Monnify Account Reference
		public function checkMonnifyAccountRef($email){
			$dbh=$this->connect();
	    	$c="SELECT accountReference FROM monnify_accounts WHERE email=:e";
	    	$queryC = $dbh->prepare($c);
	    	$queryC->bindParam(':e',$email,PDO::PARAM_STR);
	    	$queryC->execute();
			$result=$queryC->fetch(PDO::FETCH_OBJ);
	      	if($result){

				$c2="UPDATE subscribers SET accountReference=:ref WHERE sEmail=:e";
				$queryC2 = $dbh->prepare($c2);
				$queryC2->bindParam(':ref',$result->accountReference,PDO::PARAM_STR);
				$queryC2->bindParam(':e',$email,PDO::PARAM_STR);
				$queryC2->execute();

				return ["status" => "success","ref" => $result->accountReference];
			}
	      	else{return ["status" => "fail","msg" => "Not Found"];}
		}

			//Create Payvessel Virtual Bank Account
		public function generatePayvesselAccount($id,$bvn,$fname,$lname,$phone,$email){
           
			//Get Authorization Data
			$url = 'https://api.payvessel.com/api/external/request/customerReservedAccount/';
			
			$dbh=$this->connect();
            
            //Get API Details
			$d=$this->getApiConfiguration();
			$payvesselStatus = $this->getConfigValue($d,"payvesselStatus");
			$payvesselApiKey = $this->getConfigValue($d,"payvesselApiKey");
			$payvesselSecret = $this->getConfigValue($d,"payvesselSecret");
			$payvesselBusinessId = $this->getConfigValue($d,"payvesselBusinessId");
			
			$fname=str_replace(" ","",$fname); $fname=trim($fname);
			$lname=str_replace(" ","",$lname); $lname=trim($lname);
            $phone=trim($phone);
            $email=str_replace(" ","",$email);
		
			//Get Token
			
			$ch = curl_init();
		    curl_setopt_array($ch, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS =>'{
                        "email":"'.$email.'",
                        "name":"'.$fname.' '.$lname.'",
                        "phoneNumber":"'.$phone.'",
                        "bankcode":["120001"],
                        "account_type": "STATIC",
                        "businessid":"'.$payvesselBusinessId.'",
                        "bvn":"'.$bvn.'"
                    
                    }',
				CURLOPT_HTTPHEADER => array(
                        "api-key:$payvesselApiKey",
                        "api-secret:Bearer $payvesselSecret",
                        "Content-Type:application/json"
                ),
			));
			
			
			$result = curl_exec($ch);
			curl_close($ch);
			$value = json_decode($result);
			
			file_put_contents("payversal_log.txt",$result);
			
			//Check And Save Account Details
			if(isset($value->banks[0]->accountNumber)){
				$accountNumber = $value->banks[0]->accountNumber;
                
				//Save Account Number
				
			$dbh = $this->connect();
            $status = "yes";
            $c = "UPDATE subscribers SET sPayvesselBank = :pb, pVerify = :status,sAccountLimit = '10000' WHERE sId = :id";
            $queryC = $dbh->prepare($c);
            $queryC->bindParam(':pb', $accountNumber, PDO::PARAM_STR);
            $queryC->bindParam(':status', $status, PDO::PARAM_STR);
            $queryC->bindParam(':id', $id, PDO::PARAM_INT); // Assuming $id is an integer
            if($queryC->execute()) { return 0; } else { return 1;}

			}
		}

   //Update Payvessel Virtual Bank Account
   public function updatePayvesselAccount($id, $bvn, $account) {
    //Get Authorization Data
   
    
    $dbh = $this->connect();
    
    //Get API Details
    $d = $this->getApiConfiguration();
    $payvesselBusinessId = $this->getConfigValue($d, "payvesselBusinessId");
    $payvesselApiKey = $this->getConfigValue($d, "payvesselApiKey");
    $payvesselSecret = $this->getConfigValue($d, "payvesselSecret");
    $url = "https://api.payvessel.com/api/external/request/virtual-account/{$payvesselBusinessId}/{$account}/";
    
    //Get Token
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS =>'{"bvn":"' . $bvn . '"}',
        CURLOPT_HTTPHEADER => array(
            "api-key: $payvesselApiKey",
            "api-secret: Bearer $payvesselSecret",
            "Content-Type: application/json"
        ),
    ));
    
    $result = curl_exec($ch);
    curl_close($ch);
    $value = json_decode($result);
    
    file_put_contents("payversal_log.txt", $result);
   //Check And Save Account Details
   if(isset($value->code) && $value->code === "05") {
    return $value->message;
    }  else {
        $dbh = $this->connect();
        $status = "yes";
        $c = "UPDATE subscribers SET pVerify = :status WHERE sId = :id";
        $queryC = $dbh->prepare($c);
        $queryC->bindParam(':status', $status, PDO::PARAM_STR);
        $queryC->bindParam(':id', $id, PDO::PARAM_INT); 
        $queryC->execute();
    }
}

	//Create Payvessel Dynamic Virtual Bank Account
   public function generatePayvesselDynamic($id, $fname, $lname, $phone, $email){
    $url = 'https://api.payvessel.com/api/external/request/customerReservedAccount/';
    
    // Get API Details
    $d = $this->getApiConfiguration();
    $payvesselApiKey = $this->getConfigValue($d, "payvesselApiKey");
    $payvesselSecret = $this->getConfigValue($d, "payvesselSecret");
    $payvesselBusinessId = $this->getConfigValue($d, "payvesselBusinessId");
    
    $fname = str_replace(" ", "", $fname);
    $fname = trim($fname);
    $lname = str_replace(" ", "", $lname);
    $lname = trim($lname);
    $phone = trim($phone);
    $email = str_replace(" ", "", $email);
    
    // Get Token
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => '{
            "email":"' . $email . '",
            "name":"' . $fname . ' ' . $lname . '",
            "phoneNumber":"' . $phone . '",
            "bankcode":["120001"],
            "account_type": "DYNAMIC",
            "businessid":"' . $payvesselBusinessId . '"
        }',
        CURLOPT_HTTPHEADER => array(
            "api-key:$payvesselApiKey",
            "api-secret:Bearer $payvesselSecret",
            "Content-Type:application/json"
        ),
    ));
    
  // Execute the cURL request
   $result = curl_exec($ch);
   curl_close($ch);
   file_put_contents("payversal_log.txt", $result);
  // Decode the JSON response
  $value = json_decode($result);

    if(isset($value->banks->accountNumber)){
    $accountNumber = $value->banks->accountNumber;
    return $accountNumber; } else { return null; }

    }

		
		//GET LIST Of BANKS
		public function getFullBankList(){
		    
		    //Get API Details
			$d=$this->getApiConfiguration();
			$a=$this->getSiteConfiguration();
			$monifyStatus = $this->getConfigValue($d,"monifyStatus");
			$monifyApi = $this->getConfigValue($d,"monifyApi");
			$monifySecrete = $this->getConfigValue($d,"monifySecrete");
			$monifyContract = $this->getConfigValue($d,"monifyContract");
			$adminEmail = $a->email;
           
			$accessKey = $monifyApi.":".$monifySecrete;
			$apiKey = base64_encode($accessKey);
			
			//Get Authorization Data
			$url = 'https://api.monnify.com/api/v1/auth/login';
			$ch = curl_init();
		    curl_setopt_array($ch, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_HTTPHEADER => array(
                    "Authorization: Basic {$apiKey}",
                    "Content-Type: application/json"
                ),
			));
			
			
			$json = curl_exec($ch);
			$result = json_decode($json);
			curl_close($ch);
            
			$accessToken=$result->responseBody->accessToken;
			
			//Get Authorization Data
			$url2 = 'https://api.monnify.com/api/v1/banks';
			$ch2 = curl_init();
		    curl_setopt_array($ch2, array(
				CURLOPT_URL => $url2,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer {$accessToken}",
                    "Content-Type: application/json"
                ),
			));
			
			
			$json2 = curl_exec($ch2);
			$result2 = json_decode($json2);
			curl_close($ch2);
            
			return $result2;
			
		}
		
		//Verify Bank Account Details
		public function verifyBankAccount($bankcode,$accountno){
		    
		    //Get API Details
			$d=$this->getApiConfiguration();
			$a=$this->getSiteConfiguration();
			$monifyStatus = $this->getConfigValue($d,"monifyStatus");
			$monifyApi = $this->getConfigValue($d,"monifyApi");
			$monifySecrete = $this->getConfigValue($d,"monifySecrete");
			$monifyContract = $this->getConfigValue($d,"monifyContract");
			$adminEmail = $a->email;
           
			$accessKey = $monifyApi.":".$monifySecrete;
			$apiKey = base64_encode($accessKey);
			
			//Get Authorization Data
			$url = 'https://api.monnify.com/api/v1/auth/login';
			$ch = curl_init();
		    curl_setopt_array($ch, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_HTTPHEADER => array(
                    "Authorization: Basic {$apiKey}",
                    "Content-Type: application/json"
                ),
			));
			
			
			$json = curl_exec($ch);
			$result = json_decode($json);
			curl_close($ch);
            
			$accessToken=$result->responseBody->accessToken;
			
			//Get Authorization Data
			$url2 = 'https://api.monnify.com/api/v1/disbursements/account/validate?accountNumber='.$accountno.'&bankCode='.$bankcode;
			$ch2 = curl_init();
		    curl_setopt_array($ch2, array(
				CURLOPT_URL => $url2,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer {$accessToken}",
                    "Content-Type: application/json"
                ),
			));
			
			
			$json2 = curl_exec($ch2);
			$result2 = json_decode($json2);
			curl_close($ch2);
            
			return $result2;
			
		}
		
//create Aspfiy Virtual Account
        public function generateAsfiy($id,$email,$fname,$lname,$aspfiyApi,$phone,$aspfiyWebhook){
            $secondname= explode(" ",$lname);
	        if(isset($secondname[0])): $lname = $secondname[0]; endif;
            if(isset($secondname[1])): $mname = $secondname[1]; else: $mname =""; endif;
            $fname=str_replace(" ","",$fname); 
            $lname=str_replace(" ","",$lname);
            $mname=str_replace(" ","",$mname);
            $fname=trim($fname); 
            $lname=trim($lname);
            $mname=trim($mname); 
            $phone=trim($phone); 
            $email=str_replace(" ","",$email);
            $ref = str_shuffle(substr("ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjklmnpqrstuvwxyz1234567890",0,12));
                $curl = curl_init();
                
                curl_setopt_array($curl, [
                  CURLOPT_URL => "https://api-v1.aspfiy.com/reserve-paga/",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => json_encode([
                    'email' => $email,
                    'firstName' => $fname,
                    'phone' => $phone,
                    'reference' => $ref,
                    'lastName' => $lname,
                    'webhookUrl' => $aspfiyWebhook
                  ]),
                  CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer {$aspfiyApi}",
                    "Content-Type: application/json",
                    "accept: application/json"
                  ],
                ]);
                
                $response = curl_exec($curl);
                $err = curl_error($curl);
                file_put_contents("aspfiy_gen",$response);
                curl_close($curl);
                
                $res = json_decode($response);
                
                if(isset($res->data->account->account_number)){
                $accountNumber = $res->data->account->account_number;
                
				//Save Account Number
				
				$dbh=$this->connect();
				$c="UPDATE subscribers SET sAsfiyBank=:AS WHERE sId=$id";
				$queryC = $dbh->prepare($c);
				$queryC->bindParam(':AS',$accountNumber,PDO::PARAM_STR);
				$queryC->execute();
			
                }
            
        }
	      	
        public function generateAsfiyPalmpay($id,$email,$fname,$lname,$aspfiyApi,$phone,$aspfiyWebhook){
            $secondname= explode(" ",$lname);
	        if(isset($secondname[0])): $lname = $secondname[0]; endif;
            if(isset($secondname[1])): $mname = $secondname[1]; else: $mname =""; endif;
            $fname=str_replace(" ","",$fname); 
            $lname=str_replace(" ","",$lname);
            $mname=str_replace(" ","",$mname);
            $fname=trim($fname); 
            $lname=trim($lname);
            $mname=trim($mname); 
            $phone=trim($phone); 
            $email=str_replace(" ","",$email);
            $ref = str_shuffle(substr("ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjklmnpqrstuvwxyz1234567890",0,12));
                $curl = curl_init();
                
                curl_setopt_array($curl, [
                  CURLOPT_URL => "https://api-v1.aspfiy.com/reserve-palmpay/",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => json_encode([
                    'email' => $email,
                    'firstName' => $fname,
                    'phone' => $phone,
                    'reference' => $ref,
                    'lastName' => $lname,
                    'webhookUrl' => $aspfiyWebhook
                  ]),
                  CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer {$aspfiyApi}",
                    "Content-Type: application/json",
                    "accept: application/json"
                  ],
                ]);
                
                $response = curl_exec($curl);
                $err = curl_error($curl);
                file_put_contents("aspfiy_palm",$response);
                curl_close($curl);
                
                $res = json_decode($response);
                
                if(isset($res->data->account->account_number)){
                $accountNumber = $res->data->account->account_number;
                
				//Save Account Number
				
				$dbh=$this->connect();
				$c="UPDATE subscribers SET sPaga=:AS WHERE sId=$id";
				$queryC = $dbh->prepare($c);
				$queryC->bindParam(':AS',$accountNumber,PDO::PARAM_STR);
				$queryC->execute();
			
                }
                
                
        }

		   	}
?>