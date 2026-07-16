    <!-- Page content start here-->
        
<head>
    <style>
        
        .cardy {
            background-color: <?php echo $sitecolor; ?>;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            padding: 20px;
            position: relative;
        }
        .transaction-history {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 12px;
            color: #ffffff;
            cursor: pointer;
        }
        .avbal {
            position: absolute;
            top: 10px;
            font-size: 12px;
            color: #ffffff;
            cursor: pointer;
        }
        .balance-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
        }
        .balance {
            color: #ffffff;
            font-size: 24px;
        }
        .add-money button {
            background-color: white;
            color: <?php echo $sitecolor; ?>;
            border: none;
            
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
<style>
.float{
	position:fixed;
	width:60px;
	height:60px;
	bottom:80px;
	right:5px;
	background-color:#25d366;
	color:#FFF;
	border-radius:80px 80px 80px 80px;
	text-align:center;
    font-size:30px;
	box-shadow: 2px 2px 3px #999;
  z-index:100;
}

.my-float{
	margin-top:15px;
}
</style>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<a href="https://wa.me/234<?php echo $data3->whatsapp; ?>" class="float" target="_blank">
<i class="fa fa-whatsapp my-float"></i>
</a>
</head>
<body>
<div class="page-content header-clear">
<div class="content" style="margin-bottom: 0px;">
<div class="cardy">
    <div class="avbal"><b>Available Balance</b>
     <span id="hideEye"><i class="fa fa-eye-slash" style="margin-left:8px;" aria-hidden="true"></i></span>
                    <span id="openEye" style="display:none; margin-left:8px;"><i class="fa fa-eye" aria-hidden="true"></i></span>
   </div>
    <a href="transactions" class="transaction-history"><font color="#ffffff"><b>Transaction History ></b></font></a>
    <div class="balance-container">
        <div class="balance"><span id="hideEyeDiv" style="display:none;">&#8358; <?php echo number_format($profileDetails->sWallet); ?></span>
                    <span id="openEyeDiv" >&#8358; ****</span>
                    </div>
        <a href="fund-wallet" class="add-money">
            <button><i class="fa fa-plus" aria-hidden="true"></i> Add Money</button>
        </a>
    </div>
</div>
</div>
        <div class="card card-style mt-3" style="background-color: #ffffff; 
; border-radius: 15px; margin-bottom: 10px;">
    <div class="content mb-2 mt-3">
        <div class="row text-center mb-0">
            <a href="buy-data" class="col-3">
                <span class="icon icon-l rounded-sm" style="background:#f2f2f2; color:<?php echo $sitecolor; ?>;">
                    <i class="	fa fa-exchange " style="font-size: 20px;"></i>
                </span>
                <p class="badge " style="background-color:<?php echo $sitecolor; ?>; mb-0 pt-1 font-11">Data</p>
            </a>
            <a href="buy-airtime" class="col-3">
                <span class="icon icon-l rounded-sm" style="background:#f2f2f2; color:<?php echo $sitecolor; ?>;">
                    <i class="fa fa-bar-chart" style="font-size: 20px;"></i>
                </span>
                <p class="badge " style="background-color:<?php echo $sitecolor; ?>; mb-0 pt-1 font-11">Airtime</p>
            </a>
            <a href="loan" class="col-3">
                <span class="icon icon-l rounded-sm" style="background:#f2f2f2; color:<?php echo $sitecolor; ?>;">
                    <i class="fa fa-money" style="font-size: 20px;"></i>
                </span>
                <p class="badge " style="background-color:<?php echo $sitecolor; ?>; mb-0 pt-1 font-11">Loan</p>
            </a>
            <a href="transfer" class="col-3">
                <span class="icon icon-l rounded-sm" style="background:#f2f2f2; color :<?php echo $sitecolor; ?>;">
                    <i class="fa fa-university" style="font-size: 20px;"></i>
                </span>
                <p class="badge " style="background-color:<?php echo $sitecolor; ?>; mb-0 pt-1 font-11">Transfer</p>
            </a>
        </div>
    </div>
</div>
        
        <div class="card card-style mt-3" style="background-color: #ffffff; 
; border-radius: 15px; margin-bottom: 10px;">
    <div class="content mb-2 mt-3">
        <div class="row text-center mb-0">
            <a href="verify-nin" class="col-3">
                <span class="icon icon-l rounded-l" style="background:#f2f2f2; border-radius: 30px; color:<?php echo $sitecolor; ?>;">
                    <i class="fa fa-credit-card-alt" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13" >NIN Verify</p>
            </a>
            <a href="nin_modifications" class="col-3">
                <span class="icon icon-l " style="background:#f2f2f2; border-radius: 30px; color:<?php echo $sitecolor; ?>;">
                    <i class="fa fa-barcode" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13 " >NIN Modify</p>
            </a>
            <a href="cac" class="col-3">
                <span class="icon icon-l " style="background:#f2f2f2;  border-radius: 30px; color :<?php echo $sitecolor; ?>;">
                    <i class="fa fa-building" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13" >CAC Verify</p>
            </a>
            <a href="vin" class="col-3">
                <span class="icon icon-l " style="background:#f2f2f2;  border-radius: 30px; color :<?php echo $sitecolor; ?>;">
                    <i class="fa fa-id-card" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13" >Voters Card</p>
            </a>
        </div>
        <div class="row text-center mb-0">
            <a href="drivers-license" class="col-3">
                <span class="icon icon-l " style="background:#f2f2f2; border-radius: 30px; color:<?php echo $sitecolor; ?>;">
                    <i class="fa fa-car" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13" >Driver Lic</p>
            </a>
            <a href="passport" class="col-3">
                <span class="icon icon-l " style="background:#f2f2f2; border-radius: 30px; color:<?php echo $sitecolor; ?>;">
                    <i class="fa fa-globe" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13 " >Passport</p>
            </a>
            <a href="verify-bvn" class="col-3">
                <span class="icon icon-l " style="background:#f2f2f2; border-radius: 30px; color:<?php echo $sitecolor; ?>;">
                    <i class="fa fa-university" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13" >BVN Slip</p>
            </a>
            <a href="giftcard" class="col-3">
                <span class="icon icon-l " style="background:#f2f2f2; border-radius: 30px; color :<?php echo $sitecolor; ?>;">
                    <i class="fa fa-credit-card" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13" >Giftcard</p>
            </a>
        </div>
        <div class="row text-center mb-0">
            <a href="statistics" class="col-3">
                <span class="icon icon-l " style="background:#f2f2f2; border-radius: 30px; color:<?php echo $sitecolor; ?>;">
                    <i class="fa fa-bar-chart" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13" >Statistics</p>
            </a>
            <a href="status" class="col-3">
                <span class="icon icon-l " style="background:#f2f2f2; border-radius: 30px; color:<?php echo $sitecolor; ?>;">
                    <i class="fa fa-spinner" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13 " >Signal</p>
            </a>
            <a href="#" class="col-3">
                <span class="icon icon-l " style="background:#f2f2f2; border-radius: 30px; color:<?php echo $sitecolor; ?>;">
                    <i class="fa fa-mobile" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13">Use App</p>
            </a>
            <a href="more-services" class="col-3">
                <span class="icon icon-l " style="background:#f2f2f2; border-radius: 30px; color :<?php echo $sitecolor; ?>;">
                    <i class="fa fa-th-large" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13" >More</p>
            </a>
            <a href="electricity " class="col-3">
                <span class="icon icon-l rounded-sm" style="background:#f2f2f2; color:<?php echo $sitecolor; ?>;">
                    <i class="fa fa-bolt " style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13" >Electricity</p>
            </a>
            <a href="exam-pins" class="col-3">
                <span class="icon icon-l rounded-sm" style="background:#f2f2f2; color:<?php echo $sitecolor; ?>;">
                    <i class="fa fa-university" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13 " >Exam pin</p>
            </a>
            <a href="cable-tv" class="col-3">
                <span class="icon icon-l rounded-sm" style="background:#f2f2f2; color:<?php echo $sitecolor; ?>;">
                    <i class="fa fa-tv" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13" >TV</p>
            </a>
            <a href="buy-data-pin" class="col-3">
                <span class="icon icon-l rounded-sm" style="background:#f2f2f2; color :<?php echo $sitecolor; ?>;">
                    <i class="fa fa-signal" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13" >Data Pin</p>
            </a>
        </div>
        <div class="row text-center mb-0">
            <a href="issues" class="col-3">
                <span class="icon icon-l rounded-sm" style="background:#f2f2f2; color:<?php echo $sitecolor; ?>;">
                    <i class="fa fa-envelope" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13" >Massage</p>
            </a>
            <a href="pricing" class="col-3">
                <span class="icon icon-l rounded-sm" style="background:#f2f2f2; color:<?php echo $sitecolor; ?>;">
                    <i class="fa fa-list" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13 " >Pricing</p>
            </a>
            <a href="#agent-upgrade-modal" id="upgrade-agent-btn" data-menu="agent-upgrade-modal" class="col-3">
                <span class="icon icon-l rounded-sm" style="background:#f2f2f2; color:<?php echo $sitecolor; ?>;">
                    <i class="fa fa-user-secret" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13" >Agent</p>
            </a>
            <a href="/own_a_vtuportal.php" class="col-3">
                <span class="icon icon-l rounded-sm" style="background:#f2f2f2; color :<?php echo $sitecolor; ?>;">
                    <i class="fa fa-mobile" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13" >Own VTU</p>
            </a>
        </div>
        
    </div>
</div>

        </div>

        
</div>