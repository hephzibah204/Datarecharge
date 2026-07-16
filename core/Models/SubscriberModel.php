<?php

	class SubscriberModel extends Model{

		//----------------------------------------------------------------------------------------------------------------
		// Account & Profile Management
		//----------------------------------------------------------------------------------------------------------------
 
		
		//Record Site Traffic
		public function recordTraffic(){
			if(isset($_COOKIE['loginId'])){
				if(!isset($_COOKIE['loginVisit'])){
					$loginId=(float) base64_decode($_COOKIE['loginId']);
					$loginState=base64_decode($_COOKIE['loginState']);
					$visitDate=time();

					$dbh=$this->connect();
					$sql="INSERT INTO uservisits (user,state,visitTime) VALUES (:u,:s,:t)";
					$queryC = $dbh->prepare($sql);
					$queryC->bindParam(':u',$loginId,PDO::PARAM_INT);
					$queryC->bindParam(':s',$loginState,PDO::PARAM_STR);
					$queryC->bindParam(':t',$visitDate,PDO::PARAM_STR);
					$queryC->execute();

					setcookie("loginVisit", "loginVisit", time() + (86400 * 30), "/");
				}
			}
		}

		//Record Last Activity
		public function recordLastActivity($id){
			$id = (float) $id;
			$date = date("Y-m-d H:i:s");
			$dbh=$this->connect();

			//Check User Last Login

			$sqlA="SELECT token FROM userlogin WHERE user = $id ORDER BY id DESC LIMIT 1";
			$queryA = $dbh->prepare($sqlA);
	    	$queryA->execute();
	      	$resultA=$queryA->fetch(PDO::FETCH_OBJ);

			//Validate User Login token
			$curentUserToken = $_SESSION["loginAccToken"];
			$userToken = $resultA->token;

			if($curentUserToken <> $userToken){
				return 1; //Logout User Reponse Code
			}

	    	$sql="UPDATE subscribers SET sLastActivity=:a WHERE sId = $id";
			$queryC = $dbh->prepare($sql);
			$queryC->bindParam(':a',$date,PDO::PARAM_STR);
	    	$queryC->execute();

			return 0;
	    }


		//Profile Info
		public function getProfileInfo($id){
			$id = (float) $id;
			$dbh=$this->connect();
	    	$sql="SELECT * FROM subscribers WHERE sId = $id";
			$queryC = $dbh->prepare($sql);
	    	$queryC->execute();
	      	$result=$queryC->fetch(PDO::FETCH_OBJ);

			//Count Total Referal ForThe User
			$refCheck="Select COUNT(sId) AS refCount FROM subscribers WHERE sReferal=:ref";
			$queryR = $dbh->prepare($refCheck);
			$queryR->bindParam(':ref',$result->sPhone,PDO::PARAM_STR);
			$queryR->execute();
			$resultR=$queryR->fetch(PDO::FETCH_OBJ);
			$refCount=(float) $resultR->refCount;
			$result = (object) array_merge( (array)$result, array( 'refCount' => $refCount ) );
			
			return $result;
		}

		//Update Profile Password
		public function updateProfileKey($id,$oldKey,$newKey){
			
			$dbh=$this->connect();
			$id=(float) $id;
			$hash=substr(sha1(md5($oldKey)), 3, 10);
			$hash2=substr(sha1(md5($newKey)), 3, 10);

			$c="SELECT sPass FROM subscribers WHERE sPass=:p AND sId=$id";
	    	$queryC = $dbh->prepare($c);
	    	$queryC->bindParam(':p',$hash,PDO::PARAM_STR);
	     	$queryC->execute();
	      	$result=$queryC->fetch(PDO::FETCH_ASSOC);

	      	if($result){
	          
	          $sql="UPDATE subscribers SET sPass=:p WHERE sId=$id";
			  $query = $dbh->prepare($sql);
			  $query->bindParam(':p',$hash2,PDO::PARAM_STR);
			  $query->execute();
			  return 0;
	      	}
	      	else{return 1;}
			
		}

		//Update Seller Profile Password
		public function updateTransactionPin($id,$oldKey,$newKey){
			
			$dbh=$this->connect();
			$id=(float) $id;

			$c="SELECT sPin FROM subscribers WHERE sPin=:p AND sId=$id";
	    	$queryC = $dbh->prepare($c);
	    	$queryC->bindParam(':p',$oldKey,PDO::PARAM_STR);
	     	$queryC->execute();
	      	$result=$queryC->fetch(PDO::FETCH_ASSOC);

	      	if($result){
	          
	          $sql="UPDATE subscribers SET sPin=:p WHERE sId=$id";
			  $query = $dbh->prepare($sql);
			  $query->bindParam(':p',$newKey,PDO::PARAM_STR);
			  $query->execute();
			  return 0;
	      	}
	      	else{return 1;}
			
		}

		//Disable User Pin
		public function disableUserPin($id,$oldPin,$status){
			
			$dbh=$this->connect();
			$id=(int) $id;
			$status=(int) $status;

			$c="SELECT sPin FROM subscribers WHERE sPin=:p AND sId=$id";
	    	$queryC = $dbh->prepare($c);
	    	$queryC->bindParam(':p',$oldPin,PDO::PARAM_STR);
	     	$queryC->execute();
	      	$result=$queryC->fetch(PDO::FETCH_ASSOC);

	      	if($result){
	          
	          $sql="UPDATE subscribers SET sPinStatus=:s WHERE sId=$id";
			  $query = $dbh->prepare($sql);
			  $query->bindParam(':s',$status,PDO::PARAM_STR);
			  $query->execute();
			  return 0;
	      	}
	      	else{return 1;}
			
		}

		//----------------------------------------------------------------------------------------------------------------
		// Email Verification Management
		//----------------------------------------------------------------------------------------------------------------
		//Update Seller Profile Password
		public function updateEmailVerificationStatus($id){
			
			$dbh=$this->connect();
			$id=(float) $id;
			$verCode = mt_rand(1000,9999);

			$sql="UPDATE subscribers SET sRegStatus=0,sVerCode=$verCode WHERE sId=$id";
			$query = $dbh->prepare($sql);
			$query->execute();

			$_SESSION["verification"]='YES';

			return 0;
			
		}
		//----------------------------------------------------------------------------------------------------------------
		// Airtime Management
		//----------------------------------------------------------------------------------------------------------------
		
		//Get All Network
		public function getNetworks(){
			$dbh=$this->connect();
			$sql = "SELECT * FROM networkid ORDER BY networkid ASC";
            $query = $dbh->prepare($sql);
            $query->execute();
            $results=$query->fetchAll(PDO::FETCH_OBJ);
            return $results;
		}

		//Get Airtime Discount
		public function getAirtimeDiscount(){
			$dbh=$this->connect();
			$sql = "SELECT * FROM airtime a, networkid b WHERE a.aNetwork=b.nId";
            $query = $dbh->prepare($sql);
            $query->execute();
            $results=$query->fetchAll(PDO::FETCH_OBJ);
            return $results;
		}


		//----------------------------------------------------------------------------------------------------------------
		// Recharge Car Pin Management
		//----------------------------------------------------------------------------------------------------------------
		
		//Get Recharge Pin Discount
		public function getRechargePinDiscount(){
			$dbh=$this->connect();
			$sql = "SELECT * FROM airtimepin a, networkid b WHERE a.aNetwork=b.networkid";
            $query = $dbh->prepare($sql);
            $query->execute();
            $results=$query->fetchAll(PDO::FETCH_OBJ);
            return $results;
		}
		
		public function getRechargePinTokens($id,$ref){
			$dbh=$this->connect();
			$id = (int) $id;
			$sql = "SELECT * FROM airtimetokens WHERE sId=$id AND tRef=:ref";
            $query = $dbh->prepare($sql);
			$query->bindParam(":ref",$ref,PDO::PARAM_STR);
			$query->execute();
            $results=$query->fetch(PDO::FETCH_OBJ);
            return $results;
		}


		//----------------------------------------------------------------------------------------------------------------
		// Data Management
		//----------------------------------------------------------------------------------------------------------------
		
		//Get All Data Plans
		public function getDataPlans(){
			$dbh=$this->connect();

			$networks=$this->getNetworks();
			$status = $networks[0]->manualOrderStatus;

			$sql = "SELECT * FROM dataplans a, networkid b WHERE a.datanetwork = b.nId ORDER BY a.pId ASC";

			if(!empty($status)){
				if($status == "Off" || $status == "off"){
					$sql = "SELECT * FROM dataplans a, networkid b WHERE a.datanetwork = b.nId AND a.name NOT LIKE '%(Manual)%' ORDER BY a.pId ASC";
				}
			}
            $query = $dbh->prepare($sql);
            $query->execute();
            $results=$query->fetchAll(PDO::FETCH_OBJ);
            return $results;
		}

		//----------------------------------------------------------------------------------------------------------------
		// Data Pin Management
		//----------------------------------------------------------------------------------------------------------------
		
		//Get All Data Plans
		public function getDataPins(){
			$dbh=$this->connect();
			$sql = "SELECT * FROM datapins a, networkid b WHERE a.datanetwork = b.nId ORDER BY a.dpId ASC";
            $query = $dbh->prepare($sql);
            $query->execute();
            $results=$query->fetchAll(PDO::FETCH_OBJ);
            return $results;
		}

		public function getDataPinTokens($id,$ref){
			$dbh=$this->connect();
			$id = (int) $id;
			$sql = "SELECT * FROM datatokens WHERE sId=$id AND tRef=:ref";
            $query = $dbh->prepare($sql);
			$query->bindParam(":ref",$ref,PDO::PARAM_STR);
			$query->execute();
            $results=$query->fetch(PDO::FETCH_OBJ);
            return $results;
		}

		//----------------------------------------------------------------------------------------------------------------
		// Alpha Topup Management
		//----------------------------------------------------------------------------------------------------------------
		
		//Get All Alpha Topup Plans
		public function getAlphaTopupPlans(){
			$dbh=$this->connect();
			$sql = "SELECT * FROM alphatopupprice";
            $query = $dbh->prepare($sql);
            $query->execute();
            $results=$query->fetchAll(PDO::FETCH_OBJ);
            return $results;
		}

		//Alpha Topup 
		public function recordAlphaTopupOrder($userId,$walletbal,$amount,$amounttopay,$phone,$transref){
			$dbh=$this->connect();

			$phone=strip_tags($phone); $amount=strip_tags($amount);

			$oldbalance = $walletbal;
            $newbalance = $oldbalance - $amounttopay;
			$servicename = "Alpha Topup";
    		$servicedesc = "Purchase of {$amount} Alpha Topup at N{$amounttopay} for phone number {$phone}";
			$date=date("Y-m-d H:i:s");
			
			//Transaction Status 2 for alpha topup requests
			$status = 2; 
			$profit = $amounttopay - $amount; 
			

			//Record Transaction
			$sql2 = "INSERT INTO transactions 
			SET sId=:user,transref=:ref,servicename=:sn,servicedesc=:sd,amount=:a,status=:s,oldbal=:ob,newbal=:nb,date=:d,profit=:pf";
			$query2 = $dbh->prepare($sql2);
			$query2->bindParam(':user',$userId,PDO::PARAM_INT);
			$query2->bindParam(':ref',$transref,PDO::PARAM_STR);
			$query2->bindParam(':sn',$servicename,PDO::PARAM_STR);
			$query2->bindParam(':sd',$servicedesc,PDO::PARAM_STR);
			$query2->bindParam(':a',$amounttopay,PDO::PARAM_STR);
			$query2->bindParam(':s',$status,PDO::PARAM_INT);
			$query2->bindParam(':ob',$oldbalance,PDO::PARAM_STR);
			$query2->bindParam(':nb',$newbalance,PDO::PARAM_STR);
			$query2->bindParam(':d',$date,PDO::PARAM_STR);
			$query2->bindParam(':pf',$profit,PDO::PARAM_STR);
			$query2->execute();

			$lastInsertId = $dbh->lastInsertId();
			if($lastInsertId)
			{
				//Update Account Type & Balance
				$sql3 = "UPDATE subscribers SET sWallet=:bal WHERE sId=:id";
				$query3 = $dbh->prepare($sql3);
				$query3->bindParam(':id',$userId,PDO::PARAM_INT);
				$query3->bindParam(':bal',$newbalance,PDO::PARAM_STR);
				$query3->execute();
				
				$contact = $this->getSiteSettings();
				$subject="Alpha Topup Request (".$this->sitename.")";
				$message="This is to notify you that there is a new request for Alpha Topup on your website ".$this->sitename.". Order Details : {$servicedesc}";
				$email=$contact->email;
				$check=self::sendMail($email,$subject,$message);
				return 0;
			} 
			else {return 1;}
		}




		//----------------------------------------------------------------------------------------------------------------
		// Upgrade To Agent
		//----------------------------------------------------------------------------------------------------------------
		
		//Upgrade To Agent
		public function upgradeToAgent($userId,$pin,$ref){
			$dbh=$this->connect();
			$sql = "SELECT sFname,sLname,sPhone,sType,sWallet,sPin,sReferal FROM subscribers WHERE sId=:id";
            $query = $dbh->prepare($sql);
			$query->bindParam(':id',$userId,PDO::PARAM_INT);
			$query->execute();
            $results=$query->fetch(PDO::FETCH_OBJ);
			$result2 = $this->getSiteSettings();
			$amount = (float) $result2->agentupgrade;
			
			$referal = $results->sPhone;
			$referalname = $results->sFname . " " . $results->sLname;
			$refearedby = $results->sReferal;
			$refbonus = $result2->referalupgradebonus;
			
			if($_SESSION["pinStatus"] == 1 || $_SESSION["pinStatus"] == "1"){$pinstatus = 1;} else{$pinstatus = 0;}
			
			if($results->sPin == $pin || $pinstatus == 1){
				if($results->sType == 2){return 2;}
				else{
					$balance = (float) $results->sWallet;
					if($balance >= $amount){

						
						$oldbalance = $balance;
            			$newbalance = $oldbalance - $amount;
						$servicename = "Account Upgrade";
    					$servicedesc = "Upgraded Account Type To Agent Account.";
						$status = 0;
						$date=date("Y-m-d H:i:s");

						//Record Transaction
						$sql2 = "INSERT INTO transactions (sId,transref,servicename,servicedesc,amount,status,oldbal,newbal,date) VALUES (:user,:ref,:sn,:sd,:a,:s,:ob,:nb,:d)";
						$query2 = $dbh->prepare($sql2);
						$query2->bindParam(':user',$userId,PDO::PARAM_INT);
						$query2->bindParam(':ref',$ref,PDO::PARAM_STR);
						$query2->bindParam(':sn',$servicename,PDO::PARAM_STR);
						$query2->bindParam(':sd',$servicedesc,PDO::PARAM_STR);
						$query2->bindParam(':a',$amount,PDO::PARAM_STR);
						$query2->bindParam(':s',$status,PDO::PARAM_INT);
						$query2->bindParam(':ob',$oldbalance,PDO::PARAM_STR);
						$query2->bindParam(':nb',$newbalance,PDO::PARAM_STR);
						$query2->bindParam(':d',$date,PDO::PARAM_STR);
						$query2->execute();

						$lastInsertId = $dbh->lastInsertId();
						if($lastInsertId){
							//Update Account Type & Balance
							$sql3 = "UPDATE subscribers SET sType = 2, sWallet=:bal WHERE sId=:id";
							$query3 = $dbh->prepare($sql3);
							$query3->bindParam(':id',$userId,PDO::PARAM_INT);
							$query3->bindParam(':bal',$newbalance,PDO::PARAM_STR);
							$query3->execute();

							$loginAccount=base64_encode("2");
							setcookie("loginAccount", $loginAccount, time() + (2592000 * 30), "/");
							if($refearedby <> ""){
								$this->creditReferalBonus($dbh,$referal,$referalname,$refearedby,$refbonus);
							}
						
							return 0;
						}
						else{return 4;}
						
					}
					else{return 3;}
				}
			}
			else{return 1;}

            return $results;
		}


		//----------------------------------------------------------------------------------------------------------------
		// Upgrade To Vendor
		//----------------------------------------------------------------------------------------------------------------
		
		//Upgrade To Vendor
		public function upgradeToVendor($userId,$pin,$ref){
			$dbh=$this->connect();
			$sql = "SELECT sType,sFname,sLname,sPhone,sWallet,sPin,sReferal FROM subscribers WHERE sId=:id";
            $query = $dbh->prepare($sql);
			$query->bindParam(':id',$userId,PDO::PARAM_INT);
			$query->execute();
            $results=$query->fetch(PDO::FETCH_OBJ);
			$result2 = $this->getSiteSettings();
			$amount = (float) $result2->vendorupgrade;

			$referal = $results->sPhone;
			$referalname = $results->sFname . " " . $results->sLname;
			$refearedby = $results->sReferal;
			$refbonus = $result2->referalupgradebonus;

			if($_SESSION["pinStatus"] == 1 || $_SESSION["pinStatus"] == "1"){$pinstatus = 1;} else{$pinstatus = 0;}
			
			if($results->sPin == $pin || $pinstatus == 1){
				if($results->sType == 3){return 2;}
				else{
					$balance = (float) $results->sWallet;
					if($balance >= $amount){

						
						$oldbalance = $balance;
            			$newbalance = $oldbalance - $amount;
						$servicename = "Account Upgrade";
    					$servicedesc = "Upgraded Account Type To Vendor Account.";
						$status = 0;
						$date=date("Y-m-d H:i:s");

						//Record Transaction
						$sql2 = "INSERT INTO transactions (sId,transref,servicename,servicedesc,amount,status,oldbal,newbal,date) VALUES (:user,:ref,:sn,:sd,:a,:s,:ob,:nb,:d)";
						$query2 = $dbh->prepare($sql2);
						$query2->bindParam(':user',$userId,PDO::PARAM_INT);
						$query2->bindParam(':ref',$ref,PDO::PARAM_STR);
						$query2->bindParam(':sn',$servicename,PDO::PARAM_STR);
						$query2->bindParam(':sd',$servicedesc,PDO::PARAM_STR);
						$query2->bindParam(':a',$amount,PDO::PARAM_STR);
						$query2->bindParam(':s',$status,PDO::PARAM_INT);
						$query2->bindParam(':ob',$oldbalance,PDO::PARAM_STR);
						$query2->bindParam(':nb',$newbalance,PDO::PARAM_STR);
						$query2->bindParam(':d',$date,PDO::PARAM_STR);
						$query2->execute();

						$lastInsertId = $dbh->lastInsertId();
						if($lastInsertId){
							//Update Account Type & Balance
							$sql3 = "UPDATE subscribers SET sType = 3, sWallet=:bal WHERE sId=:id";
							$query3 = $dbh->prepare($sql3);
							$query3->bindParam(':id',$userId,PDO::PARAM_INT);
							$query3->bindParam(':bal',$newbalance,PDO::PARAM_STR);
							$query3->execute();

							$loginAccount=base64_encode("3");
							setcookie("loginAccount", $loginAccount, time() + (2592000 * 30), "/");
							if($refearedby <> ""){
								$this->creditReferalBonus($dbh,$referal,$referalname,$refearedby,$refbonus);
							}
							return 0;
						}
						else{return 4;}
						
					}
					else{return 3;}
				}
			}
			else{return 1;}

            return $results;
		}

		//----------------------------------------------------------------------------------------------------------------
		// Referal Bonus
		//----------------------------------------------------------------------------------------------------------------
		
		public function creditReferalBonus($dbh,$referal,$referalname,$refearedby,$refbonus){
			$sql = "SELECT sId,sRefWallet FROM subscribers WHERE sPhone=:phone";
            $query = $dbh->prepare($sql);
			$query->bindParam(':phone',$refearedby,PDO::PARAM_STR);
			$query->execute();
            $result=$query->fetch(PDO::FETCH_OBJ);
			
			if($result){

				//Get User Balance
				$userId= $result->sId;
				$balance = (float) $result->sRefWallet;
				$oldbalance = $balance;
				$amount = (float) $refbonus;
            	$newbalance = $oldbalance + $amount;
				$servicename = "Referral Bonus";
    			$servicedesc = "Referral Bonus Of N{$amount} For Referring {$referalname} ({$referal}). Bonus For Account Upgrade.";
				$status = 0;
				$date=date("Y-m-d H:i:s");
				$ref = "REF-".time();

				//Record Transaction
				$sql2 = "INSERT INTO transactions (sId,transref,servicename,servicedesc,amount,status,oldbal,newbal,date) VALUES (:user,:ref,:sn,:sd,:a,:s,:ob,:nb,:d)";
				$query2 = $dbh->prepare($sql2);
				$query2->bindParam(':user',$userId,PDO::PARAM_INT);
				$query2->bindParam(':ref',$ref,PDO::PARAM_STR);
				$query2->bindParam(':sn',$servicename,PDO::PARAM_STR);
				$query2->bindParam(':sd',$servicedesc,PDO::PARAM_STR);
				$query2->bindParam(':a',$amount,PDO::PARAM_STR);
				$query2->bindParam(':s',$status,PDO::PARAM_INT);
				$query2->bindParam(':ob',$oldbalance,PDO::PARAM_STR);
				$query2->bindParam(':nb',$newbalance,PDO::PARAM_STR);
				$query2->bindParam(':d',$date,PDO::PARAM_STR);
				$query2->execute();

				$lastInsertId = $dbh->lastInsertId();
				if($lastInsertId){
					//Update Account Type & Balance
					$sql3 = "UPDATE subscribers SET sRefWallet=:bal WHERE sId=:id";
					$query3 = $dbh->prepare($sql3);
					$query3->bindParam(':id',$userId,PDO::PARAM_INT);
					$query3->bindParam(':bal',$newbalance,PDO::PARAM_STR);
					$query3->execute();
					return 0;
				}
			
			}
		}

		//----------------------------------------------------------------------------------------------------------------
		// Contact Management
		//----------------------------------------------------------------------------------------------------------------
		//Get Site Setting
		public function getSiteSettings(){
			$dbh=$this->connect();
			$sql = "SELECT * FROM sitesettings WHERE sId=1";
            $query = $dbh->prepare($sql);
            $query->execute();
            $results=$query->fetch(PDO::FETCH_OBJ);
            return $results;
		}

		

		//----------------------------------------------------------------------------------------------------------------
		//	Exam Pin Management
		//----------------------------------------------------------------------------------------------------------------
		
		//Get All Exam Pin Provider
		public function getExamProvider(){
			$dbh=$this->connect();
			$sql = "SELECT * FROM examid ORDER BY eId ASC";
            $query = $dbh->prepare($sql);
            $query->execute();
            $results=$query->fetchAll(PDO::FETCH_OBJ);
            return $results;
		}

		//Get User Exam Pin Transactions for Check Result page
		public function getExamPinTransactions($userId){
			$dbh=$this->connect();
			$sql = "SELECT * FROM transactions WHERE sId = :userId AND (servicename LIKE '%Exam%' OR servicename LIKE '%JAMB%' OR servicedesc LIKE '%PIN%' OR servicedesc LIKE '%pin%') ORDER BY tId DESC LIMIT 20";
            $query = $dbh->prepare($sql);
            $query->bindParam(':userId',$userId,PDO::PARAM_INT);
            $query->execute();
            $results=$query->fetchAll(PDO::FETCH_OBJ);
            return $results;
		}

		//----------------------------------------------------------------------------------------------------------------
		//	Electricity Management
		//----------------------------------------------------------------------------------------------------------------
		
		//Get All Electricity Provider
		public function getElectricityProvider(){
			$dbh=$this->connect();
			$sql = "SELECT * FROM electricityid ORDER BY provider ASC";
            $query = $dbh->prepare($sql);
            $query->execute();
            $results=$query->fetchAll(PDO::FETCH_OBJ);
            return $results;
		}

		//----------------------------------------------------------------------------------------------------------------
		//	Cable Plan Management
		//----------------------------------------------------------------------------------------------------------------
		
		//Get All Cable Provider
		public function getCableProvider(){
			$dbh=$this->connect();
			$sql = "SELECT * FROM cableid ORDER BY cableid ASC";
            $query = $dbh->prepare($sql);
            $query->execute();
            $results=$query->fetchAll(PDO::FETCH_OBJ);
            return $results;
		}

		//Get Cable Plans
		public function getCablePlans(){
			$dbh=$this->connect();
			$sql = "SELECT * FROM cableplans a, cableid b WHERE a.cableprovider=b.cableid";
            $query = $dbh->prepare($sql);
            $query->execute();
            $results=$query->fetchAll(PDO::FETCH_OBJ);
            return $results;
		}

		//----------------------------------------------------------------------------------------------------------------
		// Transaction Management
		//----------------------------------------------------------------------------------------------------------------
	

		//Get All Transactions
		public function getAllTransaction($userId,$limit){
			$dbh=$this->connect();
			$addon="";
			
			if(isset($_GET["search"])){
    			
				$search=(isset($_GET["search"])) ? $_GET["search"] : "";  
				$searchfor = (isset($_GET["searchfor"])) ? $_GET["searchfor"] : ""; 

    			if($search == ""){
        			if($searchfor == "all"){$addon="";}
        			if($searchfor == "wallet"){$addon=" AND b.servicename ='Wallet Credit' ";}
        			if($searchfor == "monnify"){$addon=" AND b.transref LIKE '%MNFY%' ";}
        			if($searchfor == "paystack"){$addon=" AND b.servicedesc LIKE '%Paystack%' ";}
        			if($searchfor == "airtime"){$addon=" AND b.servicename LIKE '%Airtime%' ";}
        			if($searchfor == "data"){$addon=" AND b.servicename LIKE '%Data%' ";}
        			if($searchfor == "cable"){$addon=" AND b.servicename LIKE '%Cable%' ";}
        			if($searchfor == "electricity"){$addon=" AND b.servicename LIKE '%Electricity%' ";}
        			if($searchfor == "exam"){$addon=" AND b.servicename LIKE '%Exam%' ";}
        			if($searchfor == "reference"){$addon=" AND b.transref LIKE :search ";}
    			}
    			else{
        			
        			if($searchfor == "all"){$addon=" AND b.servicedesc LIKE :search";}
        			if($searchfor == "wallet"){$addon=" AND (b.servicedesc LIKE :search AND b.servicename ='Wallet Credit') ";}
        			if($searchfor == "monnify"){$addon=" AND (b.servicedesc LIKE :search AND b.transref LIKE '%MNFY%') ";}
        			if($searchfor == "paystack"){$addon=" AND (b.servicedesc LIKE :search AND b.servicedesc LIKE '%Paystack%') ";}
					if($searchfor == "airtime"){$addon=" AND (b.servicedesc LIKE :search AND b.servicename LIKE '%Airtime%') ";}
        			if($searchfor == "data"){$addon=" AND (b.servicedesc LIKE :search AND b.servicename LIKE '%Data%') ";}
        			if($searchfor == "cable"){$addon=" AND (b.servicedesc LIKE :search AND b.servicename LIKE '%Cable%') ";}
        			if($searchfor == "electricity"){$addon=" AND (a.servicedesc LIKE :search AND b.servicename LIKE '%Electricity%') ";}
        			if($searchfor == "exam"){$addon=" AND (b.servicedesc LIKE :search AND b.servicename LIKE '%Exam%') ";}
        			if($searchfor == "reference"){$addon=" AND b.transref LIKE :search ";}
    			}
			}
			
			$sql = "SELECT a.sFname,a.sPhone,a.sEmail,a.sType,b.* FROM subscribers a, transactions b WHERE a.sId=b.sId ";
			$sql.=$addon." AND a.sId=:id ORDER BY b.date DESC LIMIT $limit, 100";
            $query = $dbh->prepare($sql);
			$query->bindParam(':id',$userId,PDO::PARAM_INT);
            if(isset($_GET["search"])): if($search <> ""): $query->bindValue(':search','%'.$search.'%'); endif; endif;
            $query->execute();
            $results=$query->fetchAll(PDO::FETCH_OBJ);
            return $results;
		}

		//Verify Transaction Pin
		public function verifyTransactionPin($userId,$transkey){
			$dbh=$this->connect();
			
			if(isset($_SESSION["pinStatus"])){
				if($_SESSION["pinStatus"] == 1 || $_SESSION["pinStatus"] == "1"){
					$sql = "SELECT sApiKey,sWallet,sType FROM subscribers WHERE sId=:id";
					$query = $dbh->prepare($sql);
					$query->bindParam(':id',$userId,PDO::PARAM_INT);
					$query->execute();
					$results=$query->fetch(PDO::FETCH_OBJ);
					if($results){return $results;} else{return 1;}
				}
				else{
					$sql = "SELECT sApiKey,sWallet,sType FROM subscribers WHERE sId=:id AND sPin=:p";
					$query = $dbh->prepare($sql);
					$query->bindParam(':id',$userId,PDO::PARAM_INT);
					$query->bindParam(':p',$transkey,PDO::PARAM_STR);
					$query->execute();
					$results=$query->fetch(PDO::FETCH_OBJ);
					if($results){return $results;} else{return 1;}
				}
			}

			return 1;

		}

		//Get Transaction Details
		public function getTransactionDetails($ref){
			$dbh=$this->connect();
			$sql = "SELECT * FROM transactions WHERE transref=:ref";
            $query = $dbh->prepare($sql);
			$query->bindParam(':ref',$ref,PDO::PARAM_STR);
            $query->execute();
            $result=$query->fetch(PDO::FETCH_OBJ);
            return $result;
		}
		
        
        
         //Get Total Transactions 
	function TotalTransactions($userId) {
    $dbh = $this->connect(); 
    $sql = "SELECT COUNT(*) AS total_transactions FROM transactions WHERE sId = ?";
    $query = $dbh->prepare($sql);
    $query->bindParam(1, $userId, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result['total_transactions'];
      
	}
	
  //Get Total Fund 
    function getTotalFund($userId) {
    $dbh = $this->connect(); 
    $sql = "SELECT SUM(amount) AS total_fund FROM transactions WHERE servicename IN ('Wallet Topup', 'Wallet Credit') AND sId = ?";
    $query = $dbh->prepare($sql);
    $query->bindParam(1, $userId, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($result === false || $result['total_fund'] === null) {
        return 0;
    }
    return $result['total_fund'];
}

// Get Total Amount Spent daily, weekly and monthly.

function getSpent($period, $userId) {
    $dbh = $this->connect();
    $today = date('Y-m-d');

    switch ($period) {
        case 'daily':
            $startOfDay = date('Y-m-d 00:00:00', strtotime($today));
            $endOfDay = date('Y-m-d 23:59:59', strtotime($today));
            $sql = "SELECT SUM(amount) AS total_amount FROM transactions WHERE date BETWEEN ? AND ? AND sId = ? AND servicename IN ('Data', 'Airtime') AND status = 0";

            $query = $dbh->prepare($sql);
            $query->bindParam(1, $startOfDay);
            $query->bindParam(2, $endOfDay);
            $query->bindParam(3, $userId, PDO::PARAM_INT);
            break;

        case 'weekly':
            $startOfWeek = date('Y-m-d', strtotime('last Sunday', strtotime($today)));
            $endOfWeek = date('Y-m-d', strtotime('next Sunday', strtotime($today)));
            $sql = "SELECT SUM(amount) AS total_amount FROM transactions WHERE date BETWEEN ? AND ? AND sId = ? AND servicename IN ('Data', 'Airtime') AND status = 0";

            $query = $dbh->prepare($sql);
            $query->bindParam(1, $startOfWeek);
            $query->bindParam(2, $endOfWeek);
            $query->bindParam(3, $userId, PDO::PARAM_INT);
            break;

        case 'monthly':
            $startOfMonth = date('Y-m-01', strtotime($today));
            $endOfMonth = date('Y-m-t', strtotime($today));
            $sql = "SELECT SUM(amount) AS total_amount FROM transactions WHERE date BETWEEN ? AND ? AND sId = ? AND servicename IN ('Data', 'Airtime') AND status = 0";

            $query = $dbh->prepare($sql);
            $query->bindParam(1, $startOfMonth);
            $query->bindParam(2, $endOfMonth);
            $query->bindParam(3, $userId, PDO::PARAM_INT);
            break;

        default:
            return null;
    }

    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($result === false || $result['total_amount'] === null) {
        return 0;
    }
    $totalAmountSpent = $result['total_amount'];
    return $totalAmountSpent;
}

		//----------------------------------------------------------------------------------------------------------------
		// Perform Wallet To Wallet Transfer
		//----------------------------------------------------------------------------------------------------------------
		
		//Perform Wallet Transfer
		public function performWalletTransfer($userId,$email,$amount,$amounttopay,$transref1,$transref2){
			$dbh=$this->connect();

			$email=strip_tags($email); $amount=strip_tags($amount);
			$sql = "SELECT sType,sWallet,sPin,sEmail FROM subscribers WHERE sId=:id";
            $query = $dbh->prepare($sql);
			$query->bindParam(':id',$userId,PDO::PARAM_INT);
			$query->execute();
            $results=$query->fetch(PDO::FETCH_OBJ);
			$result2 = $this->getSiteSettings();
			$senderEmail = $results->sEmail;
			$walletcharges= (float) $result2->wallettowalletcharges;
			$amounttopay = $amount + $walletcharges;
			
			$c="SELECT sId,sEmail,sWallet FROM subscribers WHERE sEmail=:e";
	    	$queryC = $dbh->prepare($c);
	    	$queryC->bindParam(':e',$email,PDO::PARAM_STR);
	     	$queryC->execute(); 
	      	$resultC=$queryC->fetch(PDO::FETCH_OBJ);
	      	if($resultC){
	      	    $receiverID = $resultC->sId;
	      	    $receiverEmail = $resultC->sEmail;
	      	    $receiverOldBal = (float) $resultC->sWallet;
	      	    $receiverNewBal = $receiverOldBal + $amount;
	      	    $servicename2 = "Wallet Transfer";
    			$servicedesc2 = "Wallet To Wallet Transfer Of N{$amount} From User {$senderEmail} To {$receiverEmail}. New Balance Is {$receiverNewBal}.";
	      	}
	      	else{return 2;}
		
			if($senderEmail == $receiverEmail || $userId == $receiverID){return 5;}
			$balance = (float) $results->sWallet;
			if($balance >= $amounttopay){

						
						$oldbalance = $balance;
            			$newbalance = $oldbalance - $amounttopay;
						$servicename = "Wallet Transfer";
    					$servicedesc = "Wallet To Wallet Transfer Of N{$amount} To User {$email}. Total Amount With Charges Is {$amounttopay}. New Balance Is {$newbalance}.";
						$status = 0;
						$date=date("Y-m-d H:i:s");

						//Record Transaction
						$sql2 = "INSERT INTO transactions (sId,transref,servicename,servicedesc,amount,status,oldbal,newbal,date) VALUES (:user,:ref,:sn,:sd,:a,:s,:ob,:nb,:d)";
						$query2 = $dbh->prepare($sql2);
						$query2->bindParam(':user',$userId,PDO::PARAM_INT);
						$query2->bindParam(':ref',$transref1,PDO::PARAM_STR);
						$query2->bindParam(':sn',$servicename,PDO::PARAM_STR);
						$query2->bindParam(':sd',$servicedesc,PDO::PARAM_STR);
						$query2->bindParam(':a',$amounttopay,PDO::PARAM_STR);
						$query2->bindParam(':s',$status,PDO::PARAM_INT);
						$query2->bindParam(':ob',$oldbalance,PDO::PARAM_STR);
						$query2->bindParam(':nb',$newbalance,PDO::PARAM_STR);
						$query2->bindParam(':d',$date,PDO::PARAM_STR);
						$query2->execute();

						$lastInsertId = $dbh->lastInsertId();
						if($lastInsertId){
							//Update Account Type & Balance
							$sql3 = "UPDATE subscribers SET sWallet=:bal WHERE sId=:id";
							$query3 = $dbh->prepare($sql3);
							$query3->bindParam(':id',$userId,PDO::PARAM_INT);
							$query3->bindParam(':bal',$newbalance,PDO::PARAM_STR);
							$query3->execute();
						}
						else{return 4;}
						
						//Record Transaction
						$sql3 = "INSERT INTO transactions (sId,transref,servicename,servicedesc,amount,status,oldbal,newbal,date) VALUES (:user,:ref,:sn,:sd,:a,:s,:ob,:nb,:d)";
						$query3 = $dbh->prepare($sql3);
						$query3->bindParam(':user',$receiverID,PDO::PARAM_INT);
						$query3->bindParam(':ref',$transref2,PDO::PARAM_STR);
						$query3->bindParam(':sn',$servicename2,PDO::PARAM_STR);
						$query3->bindParam(':sd',$servicedesc2,PDO::PARAM_STR);
						$query3->bindParam(':a',$amount,PDO::PARAM_STR);
						$query3->bindParam(':s',$status,PDO::PARAM_INT);
						$query3->bindParam(':ob',$receiverOldBal,PDO::PARAM_STR);
						$query3->bindParam(':nb',$receiverNewBal,PDO::PARAM_STR);
						$query3->bindParam(':d',$date,PDO::PARAM_STR);
						$query3->execute();

						$lastInsertId = $dbh->lastInsertId();
						if($lastInsertId){
							//Update Account Type & Balance
							$sql3 = "UPDATE subscribers SET sWallet=:bal WHERE sId=:id";
							$query3 = $dbh->prepare($sql3);
							$query3->bindParam(':id',$receiverID,PDO::PARAM_INT);
							$query3->bindParam(':bal',$receiverNewBal,PDO::PARAM_STR);
							$query3->execute();
						}
						else{return 4;}
						
						return 0;
						
			}
			else{return 3;}
			
            return $results;
		}

		//----------------------------------------------------------------------------------------------------------------
		// Perform Referal To Wallet Transfer
		//----------------------------------------------------------------------------------------------------------------
		
		//Upgrade To Agent
		public function performReferralTransfer($userId,$amount,$amounttopay,$transref1,$transref2){
			$dbh=$this->connect();

			$amount=strip_tags($amount);

			$sql = "SELECT sType,sWallet,sRefWallet,sPin,sEmail FROM subscribers WHERE sId=:id";
            $query = $dbh->prepare($sql);
			$query->bindParam(':id',$userId,PDO::PARAM_INT);
			$query->execute();
            $results=$query->fetch(PDO::FETCH_OBJ);
			$result2 = $this->getSiteSettings();
			$senderEmail = $results->sEmail;
			$balance = (float) $results->sWallet;
			$refbalance = (float) $results->sRefWallet;
			
			if($refbalance >= $amounttopay){

						//Credit Referal Bonus
						$oldbalance = $balance;
            			$newbalance = $oldbalance + $amount;
						$servicename = "Wallet Transfer";
    					$servicedesc = "Referral To Wallet Transfer Of N{$amount} from referral wallet to main wallet. New Balance Is {$newbalance}.";
						$status = 0;
						$date=date("Y-m-d H:i:s");

						//Record Transaction
						$sql2 = "INSERT INTO transactions (sId,transref,servicename,servicedesc,amount,status,oldbal,newbal,date) VALUES (:user,:ref,:sn,:sd,:a,:s,:ob,:nb,:d)";
						$query2 = $dbh->prepare($sql2);
						$query2->bindParam(':user',$userId,PDO::PARAM_INT);
						$query2->bindParam(':ref',$transref1,PDO::PARAM_STR);
						$query2->bindParam(':sn',$servicename,PDO::PARAM_STR);
						$query2->bindParam(':sd',$servicedesc,PDO::PARAM_STR);
						$query2->bindParam(':a',$amount,PDO::PARAM_STR);
						$query2->bindParam(':s',$status,PDO::PARAM_INT);
						$query2->bindParam(':ob',$oldbalance,PDO::PARAM_STR);
						$query2->bindParam(':nb',$newbalance,PDO::PARAM_STR);
						$query2->bindParam(':d',$date,PDO::PARAM_STR);
						$query2->execute();

						$refoldbalance = $refbalance;
            			$refnewbalance = $refoldbalance - $amounttopay;
						$servicename = "Referral Debit";
    					$servicedesc = "Referral To Wallet Transfer Of N{$amount} from referral wallet to main wallet. Total Amount With Charges Is {$amounttopay}. New Balance Is {$refnewbalance}.";
						$status = 0;
						$date=date("Y-m-d H:i:s");

						//Record Transaction
						$sql3 = "INSERT INTO transactions (sId,transref,servicename,servicedesc,amount,status,oldbal,newbal,date) VALUES (:user,:ref,:sn,:sd,:a,:s,:ob,:nb,:d)";
						$query3 = $dbh->prepare($sql3);
						$query3->bindParam(':user',$userId,PDO::PARAM_INT);
						$query3->bindParam(':ref',$transref2,PDO::PARAM_STR);
						$query3->bindParam(':sn',$servicename,PDO::PARAM_STR);
						$query3->bindParam(':sd',$servicedesc,PDO::PARAM_STR);
						$query3->bindParam(':a',$amounttopay,PDO::PARAM_STR);
						$query3->bindParam(':s',$status,PDO::PARAM_INT);
						$query3->bindParam(':ob',$refoldbalance,PDO::PARAM_STR);
						$query3->bindParam(':nb',$refnewbalance,PDO::PARAM_STR);
						$query3->bindParam(':d',$date,PDO::PARAM_STR);
						$query3->execute();

						

						$lastInsertId = $dbh->lastInsertId();
						if($lastInsertId){
							//Update Account Type & Balance
							$sql3 = "UPDATE subscribers SET sWallet=:bal,sRefWallet=:refbal WHERE sId=:id";
							$query3 = $dbh->prepare($sql3);
							$query3->bindParam(':id',$userId,PDO::PARAM_INT);
							$query3->bindParam(':bal',$newbalance,PDO::PARAM_STR);
							$query3->bindParam(':refbal',$refnewbalance,PDO::PARAM_STR);
							$query3->execute();

							return 0;
						}
						else{return 4;}
						
			}
			else{return 3;}
			
            return $results;
		}

		//----------------------------------------------------------------------------------------------------------------
		// Wallet Funding Management
		//---------------------------------------------------------------------------------------------------------------

		//Initilize Paystack Payment
		public function initializePayStack($siteurl,$email,$amount){

			$dbh=$this->connect();
			$d=$this->getApiConfiguration();
			$key = $this->getConfigValue($d,"paystackApi");
			$$amount = (float) $amount;
			$theresponse = array();

			$email=strip_tags($email);
			$amount=strip_tags($amount);

			$amounttopass = urlencode(base64_encode($amount));
		    $amount = $amount."00";  //Amount
		      //Amount

		    // url to go to after payment
		    $callback_url = $siteurl ."webhook/paystack/index.php?email=$email&ama=$amounttopass";  
			$curl = curl_init();
		    curl_setopt_array($curl, array(
				CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => json_encode([
				  'amount'=>$amount,
				  'email'=>$email,
				  'callback_url' => $callback_url
				]),
				CURLOPT_HTTPHEADER => [
				  "authorization: Bearer ".$key, //replace this with your own test key
				  "content-type: application/json",
				  "cache-control: no-cache"
				],
			));

		    $response = curl_exec($curl);
		    $err = curl_error($curl);

		    if($err){
		      // there was an error contacting the Paystack API
			  $theresponse["status"]="fail";
			  $theresponse["msg"]=' Curl Returned Error: ' . $err;
		      return $theresponse;
		    }

		    $tranx = json_decode($response, true);

		    if(!$tranx['status']){
		      // there was an error from the API
			  $theresponse["status"]="fail";
			  $theresponse["msg"]='API Returned Error: ' . $tranx['message'];
		      return $theresponse;
		      
		    }

			$theresponse["status"]="success";
			$theresponse["msg"]=$tranx['data']['authorization_url'];
			return $theresponse;

		}

		//----------------------------------------------------------------------------------------------------------------
		 // Notification Management
		 //----------------------------------------------------------------------------------------------------------------
		
		//Get All Notification
		public function getAllNotification($userType){
			$dbh=$this->connect();
			$sql = "SELECT * FROM notifications WHERE msgFor=:ut OR msgFor=3 ORDER BY msgId DESC LIMIT 20";
            $query = $dbh->prepare($sql);
			$query->bindParam(':ut',$userType,PDO::PARAM_INT);
			$query->execute();
            $results=$query->fetchAll(PDO::FETCH_OBJ);
            return $results;
		}

		//Get Home Notification
		public function getHomeNotification(){
			$dbh=$this->connect();
			$sql = "SELECT * FROM notifications WHERE msgFor=3 ORDER BY msgId DESC LIMIT 1";
            $query = $dbh->prepare($sql);
			$query->execute();
            $results=$query->fetch(PDO::FETCH_OBJ);
            return $results;
		}
		
		//----------------------------------------------------------------------------------------------------------------
		// Contact Management
		//----------------------------------------------------------------------------------------------------------------
		

		//Post Form Contact Message
		public function postContact($name,$email,$subject,$msg){
			$dbh=$this->connect();

			$name=strip_tags($name); $email=strip_tags($email);
			$subject=strip_tags($subject); $msg=strip_tags($msg);
			
			$sql = "INSERT INTO contact  SET name=:n,contact=:c,subject=:s,message=:m";
            $query = $dbh->prepare($sql);
            $query->bindParam(':n',$name,PDO::PARAM_STR);
            $query->bindParam(':c',$email,PDO::PARAM_STR);
            $query->bindParam(':s',$subject,PDO::PARAM_STR);
            $query->bindParam(':m',$msg,PDO::PARAM_STR);
            $query->execute();

            $lastInsertId = $dbh->lastInsertId();
			if($lastInsertId){return 0;}else{return 1;}
		}

		
		//----------------------------------------------------------------------------------------------------------------
		// PAGA ACCOUNT MANAGEMENT By Sunusi Kiru
		//----------------------------------------------------------------------------------------------------------------
		
		//Generate paga Account
		
		public function generateAsfiyAccount($id){
		    
		
			$id = (float) $id;
			$dbh=$this->connect();
	    	$sql="SELECT * FROM subscribers WHERE sId = $id";
			$queryC = $dbh->prepare($sql);
	    	$queryC->execute();
	      	$result=$queryC->fetch(PDO::FETCH_OBJ);
	      	
	      	//Get User Details
	      	
	      
			if(empty($result->sAsfiyBank)){
				
				$obj = new Account;

				//Get API Details
				
				$d=$this->getApiConfiguration();
				$aspfiyStatus = $this->getConfigValue($d,"asfiyStatus");
				$aspfiyApi = $this->getConfigValue($d,"asfiyApi");
				$aspfiyWebhook = $this->getConfigValue($d,"asfiyWebhook");
				

				//If Kuda Is Active, Create Virtual Account For User
				if($aspfiyStatus == "On"){
					$obj->generateAsfiy($id,$result->sEmail,$result->sFname,$result->sLname,$aspfiyApi,$result->sPhone,$aspfiyWebhook);
				}
				
			}
			
			return null;
		}
		public function generateAsfiyPalmpay($id){
		    
		
			$id = (float) $id;
			$dbh=$this->connect();
	    	$sql="SELECT * FROM subscribers WHERE sId = $id";
			$queryC = $dbh->prepare($sql);
	    	$queryC->execute();
	      	$result=$queryC->fetch(PDO::FETCH_OBJ);
	      	
	      	//Get User Details
	      	
	      
			if(empty($result->sPaga)){
				
				$obj = new Account;

				//Get API Details
				
				$d=$this->getApiConfiguration();
				$aspfiyStatus = $this->getConfigValue($d,"asfiyStatus");
				$aspfiyApi = $this->getConfigValue($d,"asfiyApi");
				$aspfiyWebhook = $this->getConfigValue($d,"asfiyWebhook");
				

				//If Kuda Is Active, Create Virtual Account For User
				if($aspfiyStatus == "On"){
					$obj->generateAsfiyPalmpay($id,$result->sEmail,$result->sFname,$result->sLname,$aspfiyApi,$result->sPhone,$aspfiyWebhook);
				}
				
			}
			
			return null;
		}
		
		//----------------------------------------------------------------------------------------------------------------
		// Airtime TO Cash
		//----------------------------------------------------------------------------------------------------------------
		
		public function submitAirtimeToCashRequest($userid,$wallet,$airtimetocashnetwork,$airtimetocashphone,$airtimetocashamount,$transref){
			    
			$dbh=$this->connect();
			$d=$this->getApiConfiguration();
			$airtimetocashphone=strip_tags($airtimetocashphone);
			$airtimetocashnetwork=strip_tags($airtimetocashnetwork);
			$transref=strip_tags($transref);
			$per = (float) $this->getConfigValue($d,"airtime2cash".strtolower($airtimetocashnetwork)."rate");
			$airtimetocashamount =(float) $airtimetocashamount;
			$amounttocredit = ($airtimetocashamount * $per) / 100;
			$profit = $airtimetocashamount - $amounttocredit;
			
		
			$servicename = "Airtime To Cash";
			$servicedesc = "$airtimetocashnetwork Airtime To Cash Request Of N{$airtimetocashamount} At The Rate Of N{$amounttocredit} From Phone Number {$airtimetocashphone}.";
			$status = 0;
			$date=date("Y-m-d H:i:s");
			$status = 5;
			
			//Record Transaction
			$sql2 = "INSERT INTO transactions (sId,transref,servicename,servicedesc,amount,status,oldbal,newbal,date) VALUES (:user,:ref,:sn,:sd,:a,:s,:ob,:nb,:d),profit) VALUES (:user,:ref,:sn,:sd,:a,:s,:ob,:nb,:d,:profit";
			$query2 = $dbh->prepare($sql2);
			$query2->bindParam(':user',$userid,PDO::PARAM_INT);
			$query2->bindParam(':ref',$transref,PDO::PARAM_STR);
			$query2->bindParam(':sn',$servicename,PDO::PARAM_STR);
			$query2->bindParam(':sd',$servicedesc,PDO::PARAM_STR);
			$query2->bindParam(':a',$amounttocredit,PDO::PARAM_STR);
			$query2->bindParam(':s',$status,PDO::PARAM_INT);
			$query2->bindParam(':ob',$wallet,PDO::PARAM_STR);
			$query2->bindParam(':nb',$wallet,PDO::PARAM_STR);
			$query2->bindParam(':profit',$profit,PDO::PARAM_STR);
			$query2->bindParam(':d',$date,PDO::PARAM_STR);
			$query2->execute();
			
			return 0;
	    }
	    
	    
	    //Get Number Of Available Pins
		public function getNumberOfAvailablePins(){
			$dbh=$this->connect();
			$available = array();
			
			$rechargeCardPlans = $this->getRechargePinDiscount();
			foreach($rechargeCardPlans AS $plans){
			        
			        $network = $plans->networkid;
			        $amount = $plans->planSize;
			        
			    	$sql = "SELECT COUNT(tId) AS availablepins FROM airtimepinstock WHERE network=:n AND amount=:am AND status = 'Unused' ";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':n',$network,PDO::PARAM_STR);
                    $query->bindParam(':am',$amount,PDO::PARAM_STR);
                    $query->execute();
                    $results=$query->fetch(PDO::FETCH_OBJ);
                    $availablepins = $results->availablepins;
                    
                    array_push($available,["network"=>$plans->network,"amount"=>$amount,"pins"=>$availablepins]);
                    
			}
			
			return $available;
		}

		//Get All Smile  Data Plans
		public function getSmileDataPlans(){
			$dbh=$this->connect();
			$sql = "SELECT * FROM smiledata ORDER BY price ASC";
            $query = $dbh->prepare($sql);
            $query->execute();
            $results=$query->fetchAll(PDO::FETCH_OBJ);
            return $results;
		}

		//----------------------------------------------------------------------------------------------------------------
		// Payvessel ACCOUNT MANAGEMENT
		//----------------------------------------------------------------------------------------------------------------
		
		//Generate Payvessel Account
		
		public function generatePayvesselAccount($id,$bvn){
		    
		
			$id = (float) $id;
			$dbh=$this->connect();
	    	$sql="SELECT * FROM subscribers WHERE sId = $id";
			$queryC = $dbh->prepare($sql);
	    	$queryC->execute();
	      	$result=$queryC->fetch(PDO::FETCH_OBJ);
	      	
	      	//Get User Details
	      	
	      
			if(empty($result->sPayvesselBank)){
				
				$obj = new Account;

				//Get API Details
				
				$d=$this->getApiConfiguration();
				$payvesselStatus = $this->getConfigValue($d,"payvesselStatus");
				

				//If Payvessel Status Is Active, Create Virtual Account For User
				if($payvesselStatus == "On"){
				return $obj->generatePayvesselAccount($id,$bvn,$result->sFname,$result->sLname,$result->sPhone,$result->sEmail);
				}
				
			}
			
			return null;
		}//Generate Payvessel Account
		
		public function updatePayvesselAccount($id,$bvn){
		    
		
			$id = (float) $id;
			$dbh=$this->connect();
	    	$sql="SELECT * FROM subscribers WHERE sId = $id";
			$queryC = $dbh->prepare($sql);
	    	$queryC->execute();
	      	$result=$queryC->fetch(PDO::FETCH_OBJ);
	      	
	      	//Get User Details
				
				$obj = new Account;

				//Get API Details
				
				$d=$this->getApiConfiguration();
				$payvesselStatus = $this->getConfigValue($d,"payvesselStatus");

				//If Payvessel Status Is Active, Create Virtual Account For User
				if($payvesselStatus == "On"){
				return $obj->updatePayvesselAccount($id,$bvn,$result->sPayvesselBank);
				
				
			}
			
			return null;
		}

      //Generate Payvessel Dynamic
		public function generatePayvesselDynamic($id){
		    
		
			$id = (float) $id;
			$dbh=$this->connect();
	    	$sql="SELECT * FROM subscribers WHERE sId = $id";
			$queryC = $dbh->prepare($sql);
	    	$queryC->execute();
	      	$result=$queryC->fetch(PDO::FETCH_OBJ);
	      	
	      	//Get User Details
	      		if(empty($result->pVerify)){
				$obj = new Account;

				//Get API Details
				
				$d=$this->getApiConfiguration();
				$payvesselStatus = $this->getConfigValue($d,"payvesselStatus");
				
				//If Payvessel Status Is Active, Create Virtual Account For User
				if($payvesselStatus == "On"){
				return	$obj->generatePayvesselDynamic($id,$result->sFname,$result->sLname,$result->sPhone,$result->sEmail);
				}
				
	      		}
			
			return null;
		}

	// Check Daily Spending Limit
  public function checkDailySpendingLimit($userId, $amounttopay) {
    $dbh = $this->connect();

    // Query to fetch AccountLimit for the user
    $sqlAccountLimit = "SELECT sAccountLimit FROM subscribers WHERE sId = :userId";
    $queryAccountLimit = $dbh->prepare($sqlAccountLimit);
    $queryAccountLimit->bindParam(':userId', $userId, PDO::PARAM_INT);
    $queryAccountLimit->execute();
    $accountLimit = $queryAccountLimit->fetch(PDO::FETCH_ASSOC);

    // Query to fetch total_amount for the user's transactions today
    $sqlTotalAmountToday = "SELECT SUM(amount) AS total_amount 
                            FROM transactions 
                            WHERE sId = :userId
                            AND DATE(date) = CURDATE() 
                            AND servicename IN ('Data', 'Airtime')
                            AND status = 0";
    $queryTotalAmountToday = $dbh->prepare($sqlTotalAmountToday);
    $queryTotalAmountToday->bindParam(':userId', $userId, PDO::PARAM_INT);
    $queryTotalAmountToday->execute();
    $totalAmountToday = $queryTotalAmountToday->fetch(PDO::FETCH_ASSOC);

    if ($accountLimit !== false && $totalAmountToday !== false) {
        $accountLimitValue = $accountLimit['sAccountLimit'];
        $totalAmountTodayValue = $totalAmountToday['total_amount'];
        $difference = $accountLimitValue - $totalAmountTodayValue;
        if ($difference > $amounttopay) { return true; } else { return false; }} 
        else { return null; }
       }

 // Check Daily Airtime Limit
  public function checkDailyAirtimeLimit($amount) {
    $dbh = $this->connect();

    // Query to fetch AccountLimit for the user
    $sqlAirtimeLimit = "SELECT airtimedaily FROM sitesettings";
    $queryAirtimeLimit = $dbh->prepare($sqlAirtimeLimit);
    $queryAirtimeLimit->execute();
    $airtimeLimit = $queryAirtimeLimit->fetch(PDO::FETCH_ASSOC);
 
    // Query to fetch total_amount for the user's transactions today
    $sqlTotalAmountToday = "SELECT SUM(amount) AS total_amount 
                            FROM transactions 
                            WHERE sId = :userId
                            AND DATE(date) = CURDATE() 
                            AND servicename IN ('Airtime')
                            AND status = 0";
    $queryTotalAmountToday = $dbh->prepare($sqlTotalAmountToday);
    $queryTotalAmountToday->bindParam(':userId', $userId, PDO::PARAM_INT);
    $queryTotalAmountToday->execute();
    $totalAmountToday = $queryTotalAmountToday->fetch(PDO::FETCH_ASSOC);

    if ($airtimeLimit !== false && $totalAmountToday !== false) {
        $airtimeLimitValue = $airtimeLimit['airtimedaily'];
        $totalAmountTodayValue = $totalAmountToday['total_amount'];
        $difference = $airtimeLimitValue - $totalAmountTodayValue;
        if ($difference > $amount) { return true; } else { return false; }} 
        else { return null; }
      
       }

 // NETWORK STRENGTH	
  //----------------------------------------------------------------------------------------------------------------
	
  function mtnStatus() {
    $dbh = $this->connect();
    $sql1 = "SELECT COUNT(status) AS success 
            FROM (SELECT * FROM transactions WHERE servicedesc LIKE '%MTN%' AND status = 0 ORDER BY tId DESC LIMIT 20) AS first20";

    $query1 = $dbh->prepare($sql1);
    $query1->execute();
    $result1 = $query1->fetch(PDO::FETCH_ASSOC);
    $successStatus = $result1['success'];
    
    $sql2 = "SELECT COUNT(*) AS failed FROM (
                SELECT * FROM transactions ORDER BY tId DESC LIMIT 20
            ) AS first20 WHERE servicedesc LIKE '%MTN%' AND status = 1";
    $query2 = $dbh->prepare($sql2);
    $query2->execute();
    $result2 = $query2->fetch(PDO::FETCH_ASSOC);
    $failedStatus = $result2['failed'];
    $finalStatus = $successStatus - $failedStatus;
    return $finalStatus;
   } 
   
  function mtnsmeStatus() {
    $dbh = $this->connect();
    $sql1 = "SELECT COUNT(status) AS success 
            FROM (SELECT * FROM transactions WHERE servicedesc LIKE '%MTN SME%' AND status = 0 ORDER BY tId DESC LIMIT 20) AS first20";

    $query1 = $dbh->prepare($sql1);
    $query1->execute();
    $result1 = $query1->fetch(PDO::FETCH_ASSOC);
    $successStatus = $result1['success'];
    
    $sql2 = "SELECT COUNT(*) AS failed FROM (
                SELECT * FROM transactions ORDER BY tId DESC LIMIT 20
            ) AS first20 WHERE servicedesc LIKE '%MTN SME%' AND status = 1";
    $query2 = $dbh->prepare($sql2);
    $query2->execute();
    $result2 = $query2->fetch(PDO::FETCH_ASSOC);
    $failedStatus = $result2['failed'];
    $finalStatus = $successStatus - $failedStatus;
    return $finalStatus;
   } 
   
   function mtncgStatus() {
    $dbh = $this->connect();
    $sql1 = "SELECT COUNT(status) AS success 
            FROM (SELECT * FROM transactions WHERE servicedesc LIKE '%MTN Corporate%' AND status = 0 ORDER BY tId DESC LIMIT 20) AS first20";

    $query1 = $dbh->prepare($sql1);
    $query1->execute();
    $result1 = $query1->fetch(PDO::FETCH_ASSOC);
    $successStatus = $result1['success'];
    
    $sql2 = "SELECT COUNT(*) AS failed FROM (
                SELECT * FROM transactions ORDER BY tId DESC LIMIT 20
            ) AS first20 WHERE servicedesc LIKE '%MTN Corporate%' AND status = 1";
    $query2 = $dbh->prepare($sql2);
    $query2->execute();
    $result2 = $query2->fetch(PDO::FETCH_ASSOC);
    $failedStatus = $result2['failed'];
    $finalStatus = $successStatus - $failedStatus;
    return $finalStatus;
   } 


  function airtelStatus() {
    $dbh = $this->connect();
    $sql1 = "SELECT COUNT(status) AS success 
            FROM (SELECT * FROM transactions WHERE servicedesc LIKE '%AIRTEL%' AND status = 0 ORDER BY tId DESC LIMIT 20) AS first20";

    $query1 = $dbh->prepare($sql1);
    $query1->execute();
    $result1 = $query1->fetch(PDO::FETCH_ASSOC);
    $successStatus = $result1['success'];
    
    $sql2 = "SELECT COUNT(*) AS failed FROM (
                SELECT * FROM transactions ORDER BY tId DESC LIMIT 20
            ) AS first20 WHERE servicedesc LIKE '%AIRTEL%' AND status = 1";
    $query2 = $dbh->prepare($sql2);
    $query2->execute();
    $result2 = $query2->fetch(PDO::FETCH_ASSOC);
    $failedStatus = $result2['failed'];
    $finalStatus = $successStatus - $failedStatus;
    return $finalStatus;
  }

  function gloStatus() {
    $dbh = $this->connect();
    $sql1 = "SELECT COUNT(status) AS success 
            FROM (SELECT * FROM transactions WHERE servicedesc LIKE '%GLO%' AND status = 0 ORDER BY tId DESC LIMIT 20) AS first20";

    $query1 = $dbh->prepare($sql1);
    $query1->execute();
    $result1 = $query1->fetch(PDO::FETCH_ASSOC);
    $successStatus = $result1['success'];
    
    $sql2 = "SELECT COUNT(*) AS failed FROM (
                SELECT * FROM transactions ORDER BY tId DESC LIMIT 20
            ) AS first20 WHERE servicedesc LIKE '%GLO%' AND status = 1";
    $query2 = $dbh->prepare($sql2);
    $query2->execute();
    $result2 = $query2->fetch(PDO::FETCH_ASSOC);
    $failedStatus = $result2['failed'];
    $finalStatus = $successStatus - $failedStatus;
    return $finalStatus;
}

  function mobileStatus() {
    $dbh = $this->connect();
    $sql1 = "SELECT COUNT(status) AS success 
            FROM (SELECT * FROM transactions WHERE servicedesc LIKE '%9mobile%' AND status = 0 ORDER BY tId DESC LIMIT 20) AS first20";

    $query1 = $dbh->prepare($sql1);
    $query1->execute();
    $result1 = $query1->fetch(PDO::FETCH_ASSOC);
    $successStatus = $result1['success'];
    
    $sql2 = "SELECT COUNT(*) AS failed FROM (
                SELECT * FROM transactions ORDER BY tId DESC LIMIT 20
            ) AS first20 WHERE servicedesc LIKE '%9mobile%' AND status = 1";
    $query2 = $dbh->prepare($sql2);
    $query2->execute();
    $result2 = $query2->fetch(PDO::FETCH_ASSOC);
    $failedStatus = $result2['failed'];
    $finalStatus = $successStatus - $failedStatus;
    return $finalStatus;
}

function mtnAirStatus() {
    $dbh = $this->connect();
    $sql1 = "SELECT COUNT(status) AS success 
            FROM (SELECT * FROM transactions WHERE servicedesc LIKE '%MTN Airtime%' AND status = 0 ORDER BY tId DESC LIMIT 20) AS first20";

    $query1 = $dbh->prepare($sql1);
    $query1->execute();
    $result1 = $query1->fetch(PDO::FETCH_ASSOC);
    $successStatus = $result1['success'];
    
    $sql2 = "SELECT COUNT(*) AS failed FROM (
                SELECT * FROM transactions ORDER BY tId DESC LIMIT 20
            ) AS first20 WHERE servicedesc LIKE '%MTN Airtime%' AND status = 1";
    $query2 = $dbh->prepare($sql2);
    $query2->execute();
    $result2 = $query2->fetch(PDO::FETCH_ASSOC);
    $failedStatus = $result2['failed'];
    $finalStatus = $successStatus - $failedStatus;
    return $finalStatus;
   } 


  function airtelAirStatus() {
    $dbh = $this->connect();
    $sql1 = "SELECT COUNT(status) AS success 
            FROM (SELECT * FROM transactions WHERE servicedesc LIKE '%AIRTE AirtimeL%' AND status = 0 ORDER BY tId DESC LIMIT 20) AS first20";

    $query1 = $dbh->prepare($sql1);
    $query1->execute();
    $result1 = $query1->fetch(PDO::FETCH_ASSOC);
    $successStatus = $result1['success'];
    
    $sql2 = "SELECT COUNT(*) AS failed FROM (
                SELECT * FROM transactions ORDER BY tId DESC LIMIT 20
            ) AS first20 WHERE servicedesc LIKE '%AIRTEL Airtime%' AND status = 1";
    $query2 = $dbh->prepare($sql2);
    $query2->execute();
    $result2 = $query2->fetch(PDO::FETCH_ASSOC);
    $failedStatus = $result2['failed'];
    $finalStatus = $successStatus - $failedStatus;
    return $finalStatus;
  }

  function gloAirStatus() {
    $dbh = $this->connect();
    $sql1 = "SELECT COUNT(status) AS success 
            FROM (SELECT * FROM transactions WHERE servicedesc LIKE '%GLO Airtime%' AND status = 0 ORDER BY tId DESC LIMIT 20) AS first20";

    $query1 = $dbh->prepare($sql1);
    $query1->execute();
    $result1 = $query1->fetch(PDO::FETCH_ASSOC);
    $successStatus = $result1['success'];
    
    $sql2 = "SELECT COUNT(*) AS failed FROM (
                SELECT * FROM transactions ORDER BY tId DESC LIMIT 20
            ) AS first20 WHERE servicedesc LIKE '%GLO Airtime%' AND status = 1";
    $query2 = $dbh->prepare($sql2);
    $query2->execute();
    $result2 = $query2->fetch(PDO::FETCH_ASSOC);
    $failedStatus = $result2['failed'];
    $finalStatus = $successStatus - $failedStatus;
    return $finalStatus;
}

  function mobileAirStatus() {
    $dbh = $this->connect();
    $sql1 = "SELECT COUNT(status) AS success 
            FROM (SELECT * FROM transactions WHERE servicedesc LIKE '%9mobile Airtime%' AND status = 0 ORDER BY tId DESC LIMIT 20) AS first20";

    $query1 = $dbh->prepare($sql1);
    $query1->execute();
    $result1 = $query1->fetch(PDO::FETCH_ASSOC);
    $successStatus = $result1['success'];
    
    $sql2 = "SELECT COUNT(*) AS failed FROM (
                SELECT * FROM transactions ORDER BY tId DESC LIMIT 20
            ) AS first20 WHERE servicedesc LIKE '%9mobile Airtime%' AND status = 1";
    $query2 = $dbh->prepare($sql2);
    $query2->execute();
    $result2 = $query2->fetch(PDO::FETCH_ASSOC);
    $failedStatus = $result2['failed'];
    $finalStatus = $successStatus - $failedStatus;
    return $finalStatus;
}
 
	//Save Beneficary
   
   public function saveBeneficiary($userid, $name, $phone) { 
    $dbh = $this->connect();
    
    $sql_check = "SELECT COUNT(*) FROM beneficiary WHERE name = :name OR phone = :phone AND sId = $userid";
    $query_check = $dbh->prepare($sql_check);
    $query_check->bindParam(':name', $name, PDO::PARAM_STR);
    $query_check->bindParam(':phone', $phone, PDO::PARAM_STR);
    $query_check->execute();
    $count = $query_check->fetchColumn();

    if ($count > 0) {
        return false;
    } else {
        $sql_insert = "INSERT INTO beneficiary (sId,name,phone) VALUES (:id,:name,:phone)";
        $query_insert = $dbh->prepare($sql_insert);
        $query_insert->bindParam(':id', $userid, PDO::PARAM_INT);
        $query_insert->bindParam(':name', $name, PDO::PARAM_STR);
        $query_insert->bindParam(':phone', $phone, PDO::PARAM_STR);
        $result = $query_insert->execute();
        return $result;
    }
}


    //GEt Beneficary
	public function getBeneficiary($userid) { 
    $dbh = $this->connect();
    
    $sql = "SELECT id, name, phone FROM beneficiary WHERE sId = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $userid, PDO::PARAM_INT); 
    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    return $result;
    }

 // Delete Beneficiary
   public function deleteBeneficiary($id, $userid) { 
    $dbh = $this->connect();
    
    $sql = "DELETE FROM beneficiary WHERE id = :id AND sId = :sId";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->bindParam(':sId', $userid, PDO::PARAM_STR);
    $query->execute();
    $affectedRows = $query->rowCount();
    
    return ($affectedRows > 0);
    }

// Storing a query in the database and sending an auto-reply.
function storeQuery($sId, $ref, $queryContent) {
    $dbh = $this->connect();
    
    // Check the status of the transaction and the response log
    $sql_check_transaction = "SELECT status, api_response_log FROM transactions WHERE transref = :ref";
    $query_check_transaction = $dbh->prepare($sql_check_transaction);
    $query_check_transaction->bindParam(':ref', $ref, PDO::PARAM_STR);
    $query_check_transaction->execute();
    $transaction_data = $query_check_transaction->fetch(PDO::FETCH_ASSOC);
    $transaction_status = $transaction_data['status'];
    $api_responselog = $transaction_data['api_response_log'];
    
    
    // Define the response based on the transaction status and response log
    $response = "";
    if ($transaction_status == 0 && strpos($api_responselog, 'have successfully') !== false) {
        $response = "Please be informed that this Transaction is successful and delivered. Kindly check balance. For MTN: *323*3#, *323*4#, *323*1#. For Airtel, 9mobile, Glo *323# And for Airtime *310#.";

    } elseif (strpos($queryContent, 'Failed') !== false) {
        $response = "Transaction failed. Kindly try again later. it might be the network issue. Please check network status for the strength";
    } elseif (strpos($queryContent, 'Processing') !== false) {
        $response = "Alright boss. Please wait while we check the transaction status. We are sorry for the inconvenience caused.";
    } else {
        $response = "Thanks for your message. We will reply as soon as possible.";
    }
    
    
    // Insert the query into the issues table
    $sql_insert_query = "INSERT INTO issues (sId, ref, query, userEmail) VALUES (:sId, :ref, :queryContent, (SELECT sEmail FROM subscribers WHERE sId = :sId))";
    $query_insert_query = $dbh->prepare($sql_insert_query);
    $query_insert_query->bindParam(':sId', $sId, PDO::PARAM_STR);
    $query_insert_query->bindParam(':ref', $ref, PDO::PARAM_STR);
    $query_insert_query->bindParam(':queryContent', $queryContent, PDO::PARAM_STR);
    $query_insert_query->execute();
    
        $sql_insert_reply = "INSERT INTO replies (issue_id, reply, replyby) VALUES (LAST_INSERT_ID(), :response, 'Admin')";
        $query_insert_reply = $dbh->prepare($sql_insert_reply);
        $query_insert_reply->bindParam(':response', $response, PDO::PARAM_STR);
        $query_insert_reply->execute();
        
        
         // Send email notification if not successfully
        $contact = $this->getSiteSettings();
        $subject = "New User Query";
        $message = "Kindly Look Into This Transaction. Ref: $ref, $queryContent";
        $email = $contact->email;
        $check = self::sendMail($email, $subject, $message); 
    
}

 // Storing a reply in the database
 function storeReply($issueId, $replyContent, $replyby) {
    $dbh = $this->connect();
    $sql = "INSERT INTO replies (issue_id, reply, replyby) VALUES (:issueId, :replyContent, :replyby)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':issueId', $issueId, PDO::PARAM_INT);
    $query->bindParam(':replyContent', $replyContent, PDO::PARAM_STR);
    $query->bindParam(':replyby', $replyby, PDO::PARAM_STR);
    $query->execute();
    
    $sqlUpdate = "UPDATE issues SET admin_read = 0 WHERE id = :issueId";
    $queryUpdate = $dbh->prepare($sqlUpdate);
    $queryUpdate->bindParam(':issueId', $issueId, PDO::PARAM_INT);
    $queryUpdate->execute();
}


 // Retrieving queries.
  
  function getQueries($id) {
    $dbh = $this->connect();
 $sql = "SELECT i.*, r.* 
        FROM issues i 
        LEFT JOIN replies r 
        ON i.id = r.issue_id 
        AND r.id = (SELECT MAX(id) FROM replies WHERE issue_id = i.id) 
        WHERE i.sId = :id 
        ORDER BY r.id DESC";
   $query = $dbh->prepare($sql); 
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

 // Retrieving queries and their replies from the database
 function getQueriesAndReplies($id) {
    $dbh = $this->connect();
    $sql = "SELECT * FROM issues WHERE id = :id ORDER BY id DESC LIMIT 1";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();
    $issues = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($issues as &$issue) {
        $sql = "SELECT * FROM replies WHERE issue_id = :issueId";
        $query = $dbh->prepare($sql);
        $query->bindParam(':issueId', $issue['id'], PDO::PARAM_INT);
        $query->execute();
        $issue['replies'] = $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update the status of each message to mark it as read
    foreach ($issues as $issue) {
        $issueId = $issue['id'];
        $sqlUpdate = "UPDATE issues SET user_read = 1 WHERE id = :issueId";
        $queryUpdate = $dbh->prepare($sqlUpdate);
        $queryUpdate->bindParam(':issueId', $issueId, PDO::PARAM_INT);
        $queryUpdate->execute();
    }

    return $issues;
}

// Retrieve the count of unread messages.
 function getUnread($id) {
    $dbh = $this->connect();
    $sql = "SELECT COUNT(*) AS unread_count FROM issues WHERE sId = :id AND user_read = 0";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result['unread_count'];
} 



 
public function registerBusiness($userId, $certType, $comp_name, $alt_comp_name, $share_cap, $comp_addr, $res_addr, $bus_nature, $dir_id_card_path, $passport_photo_path, $phone_num, $status) {
    $dbh = $this->connect();
 
  // Retrieve the CAC charges
    $sqlApiStatus = "SELECT name, value FROM apiconfigs WHERE name IN ('CACcharge1', 'CACcharge2')";
    $queryApiStatus = $dbh->prepare($sqlApiStatus);
    $queryApiStatus->execute();
    $resultApiStatus = $queryApiStatus->fetchAll(PDO::FETCH_ASSOC);

    // Extract charges
    $charges = array_column($resultApiStatus, 'value', 'name');
    $cacCharge1 = $charges['CACcharge1'] ?? 0;
    $cacCharge2 = $charges['CACcharge2'] ?? 0;

    // Determine the amount to pay based on certificate type
    if ($certType == "biz") {
        $amounttopay = $cacCharge1;
    } else {
        $amounttopay = $cacCharge2;
    }
    // Retrieve subscriber information
    $sqlSubscriber = "SELECT * FROM subscribers WHERE sId = :sId";
    $querySubscriber = $dbh->prepare($sqlSubscriber);
    $querySubscriber->bindParam(':sId', $userId, PDO::PARAM_INT); // Assuming sId is an integer
    $querySubscriber->execute();
    $resultSubscriber = $querySubscriber->fetch(PDO::FETCH_OBJ);
    

    if ($resultSubscriber->sWallet >= $amounttopay) {
        // Insert business registration details into CAC table
        $sql = "INSERT INTO CAC (sId, certType, comp_name, alt_comp_name, share_cap, comp_addr, res_addr, bus_nature, dir_id_card, passport_photo, phone_num, status, submit_date) 
                VALUES (:id, :certType, :comp_name, :alt_comp_name, :share_cap, :comp_addr, :res_addr, :bus_nature, :dir_id_card, :passport_photo, :phone_num, :status, CURRENT_DATE)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':certType', $certType);
        $query->bindParam(':id', $userId);
        $query->bindParam(':comp_name', $comp_name); 
        $query->bindParam(':alt_comp_name', $alt_comp_name);
        $query->bindParam(':share_cap', $share_cap);
        $query->bindParam(':comp_addr', $comp_addr);
        $query->bindParam(':res_addr', $res_addr);
        $query->bindParam(':bus_nature', $bus_nature);
        $query->bindParam(':dir_id_card', $dir_id_card_path);
        $query->bindParam(':passport_photo', $passport_photo_path);
        $query->bindParam(':phone_num', $phone_num);
        $query->bindParam(':status', $status);
        $submit = $query->execute();

        if ($submit) {
            // Record the transaction
            $ref = 'CAC' . bin2hex(random_bytes(8));
            $oldbalance = $resultSubscriber->sWallet;
            $newbalance = $oldbalance - $amounttopay;
            $servicename = "Wallet Debit";
            $servicedesc = "Wallet Debit of N{$amounttopay} for CAC registration.";

            // Check if the reference already exists
            $sqlCheckRef = "SELECT COUNT(*) FROM transactions WHERE transref = :ref";
            $queryCheckRef = $dbh->prepare($sqlCheckRef);
            $queryCheckRef->bindParam(':ref', $ref, PDO::PARAM_STR);
            $queryCheckRef->execute();
            $countRef = $queryCheckRef->fetchColumn();

            if ($countRef > 0) {
                return 1;
            }

            // Update subscriber wallet
            $sqlUpdateWallet = "UPDATE subscribers SET sWallet = :newbalance WHERE sId = :id";
            $queryUpdateWallet = $dbh->prepare($sqlUpdateWallet);
            $queryUpdateWallet->bindParam(':newbalance', $newbalance, PDO::PARAM_STR);
            $queryUpdateWallet->bindParam(':id', $resultSubscriber->sId, PDO::PARAM_INT);
            $queryUpdateWallet->execute();

            // Record transaction
            $tstatus = 0;
            $date = date('Y-m-d H:i:s');
            $sqlRecordTransaction = "INSERT INTO transactions (sId, transref, servicename, servicedesc, amount, status, oldbal, newbal, date) 
                                     VALUES (:user, :ref, :sn, :sd, :amount, :status, :oldbal, :newbal, :date)";
            $queryRecordTransaction = $dbh->prepare($sqlRecordTransaction);
            $queryRecordTransaction->bindParam(':user', $resultSubscriber->sId, PDO::PARAM_INT);
            $queryRecordTransaction->bindParam(':ref', $ref, PDO::PARAM_STR);
            $queryRecordTransaction->bindParam(':sn', $servicename, PDO::PARAM_STR);
            $queryRecordTransaction->bindParam(':sd', $servicedesc, PDO::PARAM_STR);
            $queryRecordTransaction->bindParam(':amount', $amounttopay, PDO::PARAM_STR);
            $queryRecordTransaction->bindParam(':status', $tstatus, PDO::PARAM_INT);
            $queryRecordTransaction->bindParam(':oldbal', $oldbalance, PDO::PARAM_STR);
            $queryRecordTransaction->bindParam(':newbal', $newbalance, PDO::PARAM_STR);
            $queryRecordTransaction->bindParam(':date', $date, PDO::PARAM_STR);
            $queryRecordTransaction->execute();

            $lastInsertId = $dbh->lastInsertId();
            return $lastInsertId ? 0 : 1;
        }
    }
    return 5;
}

function getCAC($userid) {
    $dbh = $this->connect();
    
    $sql = "SELECT * FROM CAC WHERE sId = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $userid, PDO::PARAM_INT); 
    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_OBJ);
    return $result;
}

function getVIN($userid) {
    $dbh = $this->connect();
    
    $sql = "SELECT * FROM VIN WHERE sId = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $userid, PDO::PARAM_INT); 
    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_OBJ);
    return $result;
}

function getDriversLicense($userid) {
    $dbh = $this->connect();
    
    $sql = "SELECT * FROM drivers_license WHERE sId = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $userid, PDO::PARAM_INT); 
    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_OBJ);
    return $result;
}

function getPassport($userid) {
    $dbh = $this->connect();
    
    $sql = "SELECT * FROM passport WHERE sId = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $userid, PDO::PARAM_INT); 
    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_OBJ);
    return $result;
}
    
}

?>