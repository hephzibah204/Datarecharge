<?php

    /*

        * Route For Admin
        * Login
        * Posts
        * Forms

    */
     
    session_start();
    ini_set('display_errors', 0);
    error_reporting(E_ALL & ~E_DEPRECATED);

    //Verify User Is Logged In
    if(!isset($_SESSION["sysId"]) && !isset($_GET["login"])){header("Location: ../"); exit(); }
    
    //Auto Load Classes
    require_once("auto_loader.php");

    //Global Variables
    date_default_timezone_set('Africa/Lagos');
    $assetsLoc="../../assets";
    $urlAddon="./";
    $data="";
    $page="dashboard.php";
    $title="Home";
    $msg="";
    $link=explode("/",$_GET["url"] ?? "");
    $url=$link[0];
    $arg1= (!empty($link[1])) ? $link[1] : 0; 
    $arg2= (!empty($link[2])) ? $link[2] : 0; 
    $arg3= (!empty($link[3])) ? $link[3] : 0; 
    $controller="";

    //Page feature flags (for conditional asset loading)
    $needsDataTable = false;
    $needsChart = false;
    $needsSummernote = false;
    $needsSweetAlert = false;

    $limit = 1000;
	$pageCount = (isset($_GET["page"])) ? $_GET["page"] : 1;
	$pageCount = (float) $pageCount;
    $limit = $pageCount * $limit;
    $limit = $limit - 1000;
    $pageCount++;
    

    //Admin Login
    if(isset($_GET["login"])){
        $controller = new AccountAccess;
        echo $controller->verifyAdminLogin();
        exit();
    }
    else{
        global $controller;
        $controller = new AdminController;
    }

    //Admin Logout
    if($url =="logout"){
        $controller->logoutUser();
    }

    //Block System User
    if(isset($_GET["block-user"])){
        echo $controller->updateAccountStatus();
        exit();
    }

    //Delete User Account
    if(isset($_GET["delete-user-account"])){
        echo $controller->terminateUserAccount();
        exit();
    }

    //Reset Api Key For User
    if(isset($_GET["reset-user-api-key"])){
        echo $controller->resetAccountApiKey();
        exit();
    }

    //Add New Api Details
    if(isset($_POST["add-new-api-details"])){
        $msg=$controller->addNewApiDetails();
    }

    //Update Exam pin Settings
    if(isset($_POST["update-exam-pin-setting"])){
        $msg=$controller->updateExamPin();
    }

     //Update Electricity Bill Settings
     if(isset($_POST["update-electricity-bill-setting"])){
        $msg=$controller->updateElectricityBill();
    }

    //Add New System User
    if(isset($_POST["add-new-user"])){
        $msg=$controller->createAccount();
    }

    //Update System User Details
    if(isset($_POST["update-account"])){
        $msg=$controller->updateAdminAccount();
    }

    //Delete A Contact Message
    if(isset($_GET["delete-message"])){
        $msg=$controller->deleteContact();
        exit();
    }

    //Update Api Configurations
    if(isset($_POST["update-api-config"])){
        $msg=$controller->updateApiConfiguration();
    }

    //Update Monnify Configurations
    if(isset($_POST["update-monnify-config"])){
        $msg=$controller->updateApiConfiguration();
    }

    //Update Paystack Configurations
    if(isset($_POST["update-paystack-config"])){
        $msg=$controller->updateApiConfiguration();
    }

    //Update Network Settings
    if(isset($_POST["update-network-setting"])){
        $msg=$controller->updateNetworkSetting();
    }

    //Update Contact Settings
    if(isset($_POST["update-contact-setting"])){
        $msg=$controller->updateContactSetting();
    }

    //Update Site Settings
    if(isset($_POST["update-site-setting"])){
        $msg=$controller->updateSiteSetting();
    }

    //Update Site Style Settings
    if(isset($_POST["update-site-style"])){
        $msg=$controller->updateSiteStyleSetting();
    }

    //Add Airtime Discount
    if(isset($_POST["add-airtime-discount"])){
        $msg=$controller->addAirtimeDiscount();
     }

    //Update Airtime Discount
    if(isset($_POST["update-airtime-discount"])){
        $msg=$controller->updateAirtimeDiscount();
    }  
    //Add Alpha Topup
    if(isset($_POST["add-alpha-topup"])){
        $msg=$controller->addAlphaTopup();
    } 

    //Delete Alpha Topup
    if(isset($_GET["delete-alpha-topup"])){
        $msg=$controller->deleteAlphaTopup();
        exit();
    }

      //Update Alpha Topup
    if(isset($_POST["update-alpha-topup"])){
        $msg=$controller->updateAlphaTopup();
    }

     //Add Recharge Card Pin Discount
    if(isset($_POST["add-recharge-pin-discount"])){
        $msg=$controller->addRechargeCardPinDiscount();
     }

    //Update Recharge Card Pin Discount
    if(isset($_POST["update-recharge-pin-discount"])){
        $msg=$controller->updateRechargeCardPinDiscount();
     }
     
     
    //Upload Recharge Card Pins
    if(isset($_POST["upload-epins"])){
        $msg=$controller->uploadRechargeCardPins();
     }
     
     //Cleared Used Recharge Card Pins
    if(isset($_GET["clear-used-airtimepin"])){
        $msg=$controller->clearUsedRechargeCardPins();
        exit();
     }

     //Upload Data Card Pins
    if(isset($_POST["upload-datapins"])){
        $msg=$controller->uploadDataCardPins();
     }
     
     //Cleared Used Data Card Pins
    if(isset($_GET["clear-used-datapin"])){
        $msg=$controller->clearUsedDataCardPins();
        exit();
     }


     //Add Data Plan
    if(isset($_POST["add-data-plan"])){
        $msg=$controller->addDataPlan();
    }

    //Update Data Plan
    if(isset($_POST["update-data-plan"])){
        $msg=$controller->updateDataPlan();
    }

    //Delete A Data Plan
    if(isset($_GET["delete-data-plan"])){
        $msg=$controller->deleteDataPlan();
        exit();
    }

    //Add Data Pin
    if(isset($_POST["add-data-pin"])){
            $msg=$controller->addDataPin();
    }
    
    //Update Data Pin
    if(isset($_POST["update-data-pin"])){
            $msg=$controller->updateDataPin();
    }

    //Delete A Data Pin
    if(isset($_GET["delete-data-pin"])){
        $msg=$controller->deleteDataPin();
        exit();
    }

     //Add Cable Plan
    if(isset($_POST["add-cable-plan"])){
        $msg=$controller->addCablePlan();
     }

    //Update Cable Plan
    if(isset($_POST["update-cable-plan"])){
        $msg=$controller->updateCablePlan();
     }

    //Delete A Cable Plan
    if(isset($_GET["delete-cable-plan"])){
        $msg=$controller->deleteCablePlan();
        exit();
    }

    //Add Notification
    if(isset($_POST["add-notification"])){
        $msg=$controller->addNotification();
    }

    //Delete A Notification
    if(isset($_GET["delete-notification"])){
        $msg=$controller->deleteNotification();
        exit();
    }

     //Update Notification Status
     if(isset($_POST["update-notification-status"])){
        $msg=$controller->updateNotificationStatus();
     }

    //Add NotificationCredit Debit User
    if(isset($_POST["credit-debit-user"])){
        $msg=$controller->creditDebitUser();
    }

    //Update Subscriber
    if(isset($_POST["update-subscriber-pass"])){
        $msg=$controller->updateSubscriberPass();
    }
    
    //Update Subscriber
    if(isset($_POST["update-subscriber"])){
        $msg=$controller->updateSubscriber();
     }

     //Add New Subscriber
    if(isset($_POST["add-new-subscriber"])){
        $msg=$controller->createSubscriberAccount();
    }

    //Update transaction Status
    if(isset($_POST["update-trans-status"])){
        $msg=$controller->updateTransactionStatus();
    }

    //Send Email
    if(isset($_POST["send-user-email"])){
        $msg=$controller->sendEmailToUser();
    }

    //Complete Alpha Order
    if(isset($_GET["complete-alpha-order"])){
        $msg=$controller->completeAlphaTopupRequest();
        exit();
    }
    

    //Update Airtime To Cash Transactions
    if(isset($_GET["update-airtimetocash"])){
        $msg=$controller->updateAirtimeToCashStatus();
        exit();
    }
    
    //Update Airtime To Cash Settings
    if(isset($_POST["update-airtime-cash-setting"])){
        $msg=$controller->updateApiConfiguration();
    }
    
    //Update Payvessel Configurations
    if(isset($_POST["update-payvessel-config"])){
        $msg=$controller->updateApiConfiguration();
    }
    
    
    //Update Aspfiy Configurations
    if(isset($_POST["update-aspfiy-config"])){
        $msg=$controller->updateApiConfiguration();
    }
    
    //Delate transaction 
    
    if(isset($_POST["delete-transaction"])){
        $msg=$controller->deleteTransaction();
    } 
    
    //Delete Api Link
    if(isset($_POST["delete-api-link"])){
        $msg=$controller->deleteApiLink();
    }
 
   //Edit Api Link
    if(isset($_POST["edit-api-link"])){
        $msg=$controller->editApiLink();
    }

 // Blacklist Number
   if(isset($_POST["blacklist-number"])){
    $msg = $controller->blacklistNumber();
   }

  // Delete Blacklisted Number
  if(isset($_POST["delete-blacklist-number"])){
    $msg = $controller->deleteBlacklistNumber();
    }
    
     //Query Report
  if(isset($_POST["queryreport"])){
    $msg=$controller->storeQuery();
    }
        
   //Reply Report
    if(isset($_POST["replyQuery"])){
        $msg=$controller->storeReply();
    }
    
    //Update CAC charge and status
    if(isset($_POST["update-cac-charge"])){
        $msg=$controller->updateApiConfiguration();
    }  
    
      //Update CAC reg status
    if(isset($_POST["update-cac-status"])){
        $msg=$controller->updateCACStatus();
    }
    

    //Provider CRUD
    if(isset($_POST["provider-action"])){
        $providerCtrl = new ProviderController;
        $result = $providerCtrl->handleProviderRequest($_POST["provider-action"], $_POST["id"] ?? null);
        if($result["status"] == "success"){
            $msg = '<div class="alert alert-success">'.$result["message"].'</div>';
        } else {
            $msg = '<div class="alert alert-danger">'.$result["message"].'</div>';
        }
    }

    //NIN Modification Status Update
    if(isset($_GET["update-mod-status"])){
        $ninCtrl = new NINModificationController;
        echo json_encode($ninCtrl->updateModificationStatus($_REQUEST));
        exit();
    }

    //NIN Fee Settings Update
    if(isset($_POST["update-nin-fee-settings"])){
        $msg = $controller->updateNINFeeSettings();
    }

    //NIN Slip Pricing Update
    if(isset($_POST["update-nin-slip-pricing"])){
        $msg = $controller->updateNINSlipPricing();
    }

    //NIN Slip Pricing Add
    if(isset($_POST["add-nin-slip"])){
        $msg = $controller->addNINSlip();
    }

    //NIN Slip Pricing Delete
    if($url == "nin-slip-pricing" && isset($_GET["delete"])){
        $msg = $controller->deleteNINSlip($_GET["delete"]);
    }

    //Fetch The View Of The Page To Be Displayed
    createView($url);
    if($page == "dashboard.php"){createView("dashboard");}

    //Set page feature flags for conditional asset loading
    $needsDataTable = false;
    $needsChart = false;
    $needsSweetAlert = false;
    $tablePages = ["transactions","processing-transactions","manual-orders","failed-transactions",
                   "subscribers","system-users","airtime-discount","recharge-pin-discount",
                   "data-plans","data-pins","cable-tv","notifications","messages",
                   "alpha-topup","alpha-request","airtime-to-cash","airtime-to-cash-requests",
                   "agents","dashboard-alpha","airtime-pin-stock","data-pin-stock",
                   "issues","smile-order","smile-plans","transactionss",
                   "api-link","blacklist-number","cac","referral","sales-by-user"];
    $sweetAlertPages = ["system-users","data-plans","data-pins","cable-tv","notifications","messages",
                        "alpha-topup","alpha-request","site-setting"];
    if(in_array($url, $tablePages)){ $needsDataTable = true; }
    if($url == "sales-by-user"){ $needsChart = true; }
    if(in_array($url, $sweetAlertPages)){ $needsSweetAlert = true; }

    function createView($url){
        $file = __DIR__ . "/../" . $url . ".php";
        if(file_exists($file)){
            global $title,$data,$page;
            $title=str_replace("-"," ",$url);
            $title=ucwords($title);
            $page=$url.".php";
            $data=getDataIfAny($url);
        }
        
    }

    function getDataIfAny($page){
        global $urlAddon,$limit;
        $controller = new AdminController;
        
        switch($page){
            case "system-users":
                return $controller->getAccounts();
                break;
                
            case "manage-account":
                return $controller->getMyAdminDetails();
                break;
        
            case "airtime-discount":
                $data = array();
                $data[0]=$controller->getAirtimeDiscount();
                $data[1]=$controller->getNetworks();
                return $data;
                break;

            case "recharge-pin-discount":
                $data=array();
                $data[0]=$controller->getRechargeCardPinDiscount();
                $data[1]=$controller->getNetworks();
                return $data;
                break;

            case "data-plans":
                $data=array();
                $data[0]= $controller->getDataPlans();
                $data[1]=$controller->getNetworks();
                return $data;
                break;

            case "data-pins":
                $data=array();
                $data[0]= $controller->getDataPins();
                $data[1]=$controller->getNetworks();
                return $data;
                break;

            case "cable-tv":
                $data=array();
                $data[0]= $controller->getCablePlans();
                $data[1]=$controller->getCableProvider();
                return $data;
                break;

            case "notifications":
                $data=array();
                $data[0]=$controller->getNotificationStatus();
                $data[1]=$controller->getNotifications();
                return $data;
                break;

            case "messages":
                return $controller->getContact();
                break;
            
            case "subscribers":
                return $controller->getSubscribers($limit);
                break;

            case "subscriber-details":
                if(isset($_GET["apo"])){
                   return $controller->getSubscribersDetails(urldecode(base64_decode($_GET["apo"])));
                }
                else{header("Location: subscribers"); }
                break;

            case "transactions":
                return $controller->getTransactions($limit);
                break;
        
            case "processing-transactions":
                return $controller->getProcessingTransactions($limit);
                break;
            
            case "manual-orders":
                return $controller->getManualTransactions($limit);
                break;
                
           case "failed-transactions":
                return $controller->getFailedTransactions($limit);
                break;
            
            case "transaction-details":
                return $controller->getTransactionDetails();
                break;
            
            case "dashboard":
                $data=array();
                $data[0]=$controller->getGeneralSiteReports();
                $data[1]=$controller->getWalletBalance();
                return $data;
                break;
                
            case "wallet-balance":
                $data=array();
                $data[0]=$controller->getGeneralSiteReports();
                $data[1]=$controller->getWalletBalance();
                return $data;
                break;

            case "nin-fee-settings":
                return $controller->getNINFeeSettings();
                break;

            case "nin-slip-pricing":
                return $controller->getNINSlipPricing();
                break;
            
            case "data-sale-analysis":
                return $controller->getSaleTransactions();
                break;
                
            case "sale-analysis":
                return $controller->getSaleTransactions();
                break;

            case "api-setting":
                $data=array();
                $data[0] = $controller->getApiConfiguration();
                $data[1] = $controller->getApiConfigurationLinks();
                return $data;
                break;

            case "monnify-setting":
                return $controller->getApiConfiguration();
                break;

            case "paystack-setting":
                return $controller->getApiConfiguration();
                break;

            case "site-setting":
                return $controller->getSiteSettings();
                break;
            
            case "website-style":
                return $controller->getSiteSettings();
                break;

            case "contact-setting":
                return $controller->getSiteSettings();
                break;

             case "nin-api-setting":
                $data=array();
                $data[0] = $controller->getApiConfiguration();
                $data[1] = $controller->getApiConfigurationLinks();
                return $data;
                break;

             case "network-setting":
                if(!isset($_GET["network"])){$_GET["network"] = "MTN"; }
                return $controller->getNetworks();
                break;

            case "airtime-api-setting":
                if(!isset($_GET["network"])){$_GET["network"] = "MTN"; }
                $data=array();
                $data[0] = $controller->getApiConfiguration();
                $data[1] = $controller->getApiConfigurationLinks();
                return $data;
                break;
            
            case "data-api-setting":
                if(!isset($_GET["network"])){$_GET["network"] = "MTN"; }
                $data=array();
                $data[0] = $controller->getApiConfiguration();
                $data[1] = $controller->getApiConfigurationLinks();
                return $data;
                break;
            
            case "wallet-api-setting":
                $data=array();
                $data[0] = $controller->getApiConfiguration();
                $data[1] = $controller->getApiConfigurationLinks();
                return $data;
                break;

            case "credit-user":
                if(isset($_GET["apo"])){
                   return urldecode(base64_decode($_GET["apo"]));
                }
                else{header("Location: subscribers"); }
                break;
            
            case "exam-pin":
                if(!isset($_GET["exam"])){$_GET["exam"] = "WAEC"; }
                return $controller->getExamPinDetails();
                break;

            case "electricity-bill":
                if(!isset($_GET["electricity"])){$_GET["electricity"] = "IE"; }
                return $controller->getElectricityBillDetails();
                break;

            case "alpha-topup":
               
                $data=$controller->getAlphaTopup();
                return $data;
                break;

            case "alpha-request":
                return $controller->getPendingAlphaOrder();
                break;
                
            case "airtime-to-cash-settings":
                return $controller->getApiConfiguration();
                break;
            
            case "airtime-to-cash":
               
                $data=$controller->getPendingAirtimeToCash();
                return $data;
                break;
                
            case "airtime-to-cash-requests":
               
                $data=$controller->getAllAirtimeToCash($limit);
                return $data;
                break;
                
                
            case "payvessel-settings":
                return $controller->getApiConfiguration();
                break;
                
            case "aspfiy-setting":
                return $controller->getApiConfiguration();
                break;
                
            case "cac": 
                $data=array();
                $data[0]=$controller->getApiConfiguration();
                $data[1]=$controller->getCAC();
                return $data;
                    break;
           
            default:
                return "";
        }
    }


?>