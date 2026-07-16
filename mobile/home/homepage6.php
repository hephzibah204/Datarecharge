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
            padding: 5px 10px;
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
	right:0px;
	background-color:#25d366;
	color:#FFF;
	border-radius:50px 0px 0px 50px;
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
<a href="https://wa.link/szd1jx" class="float" target="_blank">
<i class="fa fa-whatsapp my-float"></i>
</a>
</head>
<body>
<div class="page-content header-clear">
<div class="content" style="margin-bottom: 0px;">
<div class="cardy">
    <div class="avbal"><b>Available Balance</b></div>
    <a href="transactions" class="transaction-history"><font color="#ffffff"><b>Transaction History</b></font></a>
    <div class="balance-container">
        <div class="balance"><span id="hideEyeDiv" style="display:none;">&#8358; <?php echo number_format($profileDetails->sWallet); ?></span>
                    <span id="openEyeDiv" >&#8358; *****</span>
                
                    <span id="hideEye"><i class="fa fa-eye-slash" style="margin-left:20px;" aria-hidden="true"></i></span>
                    <span id="openEye" style="display:none; margin-left:20px;"><i class="fa fa-eye" aria-hidden="true"></i></span>
                    </div>
        <a href="fund-wallet" class="add-money">
            <button><i class="fa fa-plus" aria-hidden="true"></i> Add Money</button>
        </a>
    </div>
</div>
</div>
        <div class="card card-style mt-3" style="background-color: #ffffff; 
; border-radius: 20px; margin-bottom: 10px;">
    <div class="content mb-2 mt-3">
        <div class="row text-center mb-0">
            <a href="buy-data" class="col-3">
                <span class="icon icon-l rounded-sm" style="background-color: #ffffff; color:<?php echo $sitecolor; ?>;">
                    <i class="fas fa-wifi" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13" style="color: <?php echo $sitecolor; ?>;">Data</p>
            </a>
            <a href="buy-airtime" class="col-3">
                <span class="icon icon-l rounded-sm" style="background-color: #ffffff; color:<?php echo $sitecolor; ?>;">
                    <i class="fas fa-mobile-alt" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13 " style="color: <?php echo $sitecolor; ?>;">Airtime</p>
            </a>
            <a href="electricity" class="col-3">
                <span class="icon icon-l rounded-sm" style="background-color: #ffffff; color:<?php echo $sitecolor; ?>;">
                    <i class="fas fa-bolt" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13" style="color: <?php echo $sitecolor; ?>;">Bills</p>
            </a>
            <a href="cable-tv" class="col-3">
                <span class="icon icon-l rounded-sm" style="background-color: #ffffff; color :<?php echo $sitecolor; ?>;">
                    <i class="fas fa-tv" style="font-size: 20px;"></i>
                </span>
                <p class="mb-0 pt-1 font-13" style="color: <?php echo $sitecolor; ?>;">Cable Tv</p>
            </a>
        </div>
    </div>
</div>
<!--        </div>
        <div class="mt-3 splide single-slider slider-no-arrows slider-no-dots splide--loop splide--ltr splide--draggable is-active mb-1" id="single-slider-1" style="visibility: visible;">
            <div class="splide__arrows"><button class="splide__arrow splide__arrow--prev" type="button" aria-controls="single-slider-1-track" aria-label="Go to last slide"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" width="40" height="40"><path d="m15.5 0.932-4.3 4.38 14.5 14.6-14.5 14.5 4.3 4.4 14.6-14.6 4.4-4.3-4.4-4.4-14.6-14.6z"></path></svg></button><button class="splide__arrow splide__arrow--next" type="button" aria-controls="single-slider-1-track" aria-label="Next slide"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" width="40" height="40"><path d="m15.5 0.932-4.3 4.38 14.5 14.6-14.5 14.5 4.3 4.4 14.6-14.6 4.4-4.3-4.4-4.4-14.6-14.6z"></path></svg></button></div>
            <div class="splide__track" id="single-slider-1-track">
                    <div class="splide__list" id="single-slider-1-list" style="transform: translateX(-624px);">
                            
                            <div class="splide__slide splide__slide--clone" aria-hidden="true" tabindex="-1" style="width: 312px;">
                                <div class="card card-style bg-20" data-card-height="120" style="height: 190px;">
                                    <img class="img-fluid" style="height: 190px;" src="../../assets/img/ads/ads1.png" />
                                </div>
                            </div>
                           
                            <div class="splide__slide" id="single-slider-1-slide02" aria-hidden="true" tabindex="-1" style="width: 312px;">
                               <div class="card card-style bg-20" data-card-height="120" style="height: 190px;">
                                    <img class="img-fluid" style="height: 190px;" src="../../assets/img/ads/ads2.png" />
                                </div>
                            </div>
                            
                            <div class="splide__slide" id="single-slider-1-slide03" aria-hidden="true" tabindex="-1" style="width: 312px;">
                               <div class="card card-style bg-20" data-card-height="120" style="height: 190px;">
                                    <img class="img-fluid" style="height: 190px;" src="../../assets/img/ads/ads3.png" />
                                </div>
                            </div>
                            </div>
                            
                            </div>
        
        <!--<div class="card card-style mt-3">-->
            
            
        <!--    <div class="content" style="color:#7a0090; mb-2 mt-3">-->
        <!--    <div>-->
        <!--        <h5>Payments</h5>-->
        <!--        <hr/>-->
        <!--       </div>-->
        <!--        <div class="row text-center mb-0">-->
        <!--            <a href="buy-data" class="col-3">-->
        <!--                <span class="icon icon-l rounded-sm" style="color:#7a0090;">-->
        <!--                    <i class="fa fa-wifi font-25"></i>-->
        <!--                </span>-->
        <!--                <p class="badge " style="background-color:#7a0090; mb-0 pt-1 font-11">Buy Data</p>-->
        <!--            </a>-->
        <!--            <a href="buy-airtime" class="col-3">-->
        <!--                <span class="icon icon-l rounded-sm" style="color:#7a0090; color:#7a0090;">-->
        <!--                    <i class="fa fa-signal font-25"></i>-->
        <!--                </span>-->
        <!--                <p class="badge " style="background-color:#7a0090; mb-0 pt-1 font-11">Buy Airtime</p>-->
        <!--            </a>-->
        <!--            <a href="electricity" class="col-3">-->
        <!--                <span class="icon icon-l rounded-sm" style="color:#7a0090;">-->
        <!--                    <i class="fa fa-bolt font-25"></i>-->
        <!--                </span>-->
        <!--                <p class="badge " style="background-color:#7a0090; mb-0 pt-1 font-11">Nepa Bills</p>-->
        <!--            </a>-->
        <!--            <a href="transfer" class="col-3">-->
        <!--                <span class="icon icon-l rounded-sm" style="color:#7a0090;">-->
        <!--                    <i class="fa fa-wallet font-25"></i>-->
        <!--                </span>-->
        <!--                <p class="badge " style="background-color:#7a0090; mb-0 pt-1 font-11">Transfers</p>-->
        <!--            </a>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
<div class="card card-style mt-3">
            
            <div class="content" style="color:#4d006e; mb-2 mt-3">
            <div>
                <h5>More Services</h5>
                <hr />
                </div>
               
        <div class="d-flex justify-content-between align-content-center mb-2">

        
        <a href="exam-pins" class="card text-center shadow-l" style="width:100% ; margin-right:10px;  margin-top:-5px;">
            <span class="icon pt-2" style="color:#00cc00;">
                <i class="fa fa-graduation-cap font-20"></i>
            </span>
            <p class="mb-2 pt-1 font-10">Education</p>
        </a>
        
        <a href="cac" class="card text-center shadow-l" style="width:100% ; margin-right:10px;  margin-top:-5px;">
            <span class="icon pt-2" style="color:#00cc00;">
                <i class="fa fa-certificate font-20"></i>
            </span>
            <p class="mb-2 pt-1 font-10">CAC Reg</p>
        </a>

        
        <a href="referrals" class="card text-center shadow-l" style="width:100% ; margin-top:-5px;">
            <span class="icon pt-2" style="color:#cc0066;">
                <i class="fa fa-users font-20"></i>
            </span>
            <p class="mb-2 pt-1 font-10">Referrals</p>
        </a>

        <a href="issues" class="card text-center shadow-l" style="width:100% ; margin-right:10px; margin-top:-5px;">
            <span class="icon pt-2" style="color:#cc0066;">
                <i class="fa fa-envelope font-20"></i>
            </span>
            <p class="mb-2 pt-1 font-10">Message</p>
        </a>

    </div>

    <div class="d-flex justify-content-between align-content-center mb-2 mt-n4">

        
        <a href="status" class="card text-center shadow-l" style="width:100% ; margin-right:10px; margin-top:-5px;">
            <span class="icon pt-2" style="color:#0066ff;">
                <i class="fa fa-spinner font-20"></i>
            </span>
            <p class="mb-2 pt-1 font-10">Net Status</p>
        </a>
        
        <a href="pricing" class="card text-center shadow-l" style="width:100% ; margin-right:10px;  margin-top:-5px;">
            <span class="icon pt-2" style="color:#00cc00;">
                <i class="fa fa-list font-20"></i>
            </span>
            <p class="mb-2 pt-1 font-10">Pricing</p>
        </a>

        <a href="https://app-store.unitedapi.ng/apps/Pytelecom" class="card text-center shadow-l" style="width:100% ; margin-right:10px; margin-top:-5px;">
            <span class="icon pt-2" style="color:#cc0066;">
                <i class="fa fa-mobile font-20"></i>
            </span>
            <p class="mb-2 pt-1 font-10">Get App</p>
        </a>
        
        <a href="logout" class="card text-center shadow-l" style="width:100% ; margin-top:-5px;">
            <span class="icon pt-2" style="color:#cc0066;">
                <i class="fa fa-lock font-20"></i>
            </span>
            <p class="mb-2 pt-1 font-10">Logout</p>
        </a>
        </div>
                
        <hr/>
          <div>
              <?php
$data = $controller->getAllTransaction($limit);
$threeTransactions = array_slice($data, 0, 3);
?>
<h5>Last 3 Transactions:</h5>
</div>
<div class="content">
    <?php if (!empty($threeTransactions)) : ?>
        <?php foreach ($threeTransactions as $list) : ?>
            <a href="transaction-details?ref=<?php echo $list->transref; ?>" class="d-flex">
                <div class="align-self-center">
                    <?php if ($list->servicename == "Airtime") : ?>
                        <span class="icon icon-s gradient-green color-white rounded-sm shadow-xxl"><i class="fa fa-phone font-15"></i></span>
                    <?php elseif ($list->servicename == "Data") : ?>
                        <span class="icon icon-s gradient-blue color-white rounded-sm shadow-xxl"><i class="fa fa-wifi font-15"></i></span>
                    <?php elseif ($list->servicename == "Cable TV") : ?>
                        <span class="icon icon-s gradient-brown color-white rounded-sm shadow-xxl"><i class="fa fa-tv font-15"></i></span>
                    <?php elseif ($list->servicename == "Electricity Bill") : ?>
                        <span class="icon icon-s gradient-yellow color-white rounded-sm shadow-xxl"><i class="fa fa-bolt font-15"></i></span>
                    <?php elseif ($list->servicename == "Exam Pin") : ?>
                        <span class="icon icon-s gradient-green color-white rounded-sm shadow-xxl"><i class="fa fa-graduation-cap font-15"></i></span>
                    <?php elseif ($list->servicename == "Cable TV") : ?>
                        <span class="icon icon-s gradient-blue color-white rounded-sm shadow-xxl"><i class="fa fa-tv font-15"></i></span>
                    <?php elseif ($list->servicename == "Wallet Transfer") : ?>
                        <span class="icon icon-s gradient-pink color-white rounded-sm shadow-xxl"><i class="fa fa-arrow-up font-15"></i></span>
                    <?php elseif ($list->servicename == "Referral Bonus") : ?>
                        <span class="icon icon-s gradient-pink color-white rounded-sm shadow-xxl"><i class="fa fa-user font-15"></i></span>
                    <?php elseif ($list->servicename == "Referral Debit") : ?>
                        <span class="icon icon-s gradient-pink color-white rounded-sm shadow-xxl"><i class="fa fa-user font-15"></i></span>
                    <?php else : ?>
                        <span class="icon icon-s gradient-pink color-white rounded-sm shadow-xxl"><i class="fa fa-list font-15"></i></span>
                    <?php endif; ?>
                </div>
                <div class="align-self-center">
                    <h5 class="ps-3 mb-n1 font-15"><?php echo $list->servicename; ?></h5>
                    <h6 class="ps-3 font-12 mt-3 color-theme opacity-70"><?php echo $list->servicedesc; ?></h6>
                    <span class="ps-3 font-10 color-theme opacity-70"><?php echo "Ref: " . $list->transref; ?></span>
                </div>
                <div class="ms-auto text-end align-self-center">
                    <h5 class="color-theme font-15 font-700 d-block mb-n1">N<?php echo $list->amount; ?></h5>
                    <?php if ($list->status == 0) : ?>
                        <span class="color-green-dark font-10"><?php echo $controller->formatDate2($list->date); ?> <i class="fa fa-check-circle"></i></span>
                    <?php elseif ($list->status == 5 || $list->status == 2) : ?>
                        <span class="color-blue-dark font-10"><?php echo $controller->formatDate2($list->date); ?> <i class="fa fa-exclamation-circle"></i></span>
                    <?php else : ?>
                        <span class="color-red-dark font-10"><?php echo $controller->formatDate2($list->date); ?> <i class="fa fa-exclamation-circle"></i></span>
                    <?php endif; ?>
                </div>
            </a>
            <div class="divider my-3"></div>
        <?php endforeach; ?>
    <?php else : ?>
        <h3 class="text-danger">No Transaction To Display</h3>
    <?php endif; ?>
</div>
<div class="content">
    <div class="d-flex justify-content-between">
       
    </div>
</div>
        
</div>