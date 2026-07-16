<div class="page-content header-clear-medium">
        
        
        <div class="card card-style bg-theme pb-0">
            <div class="content" id="tab-group-1">
                <div class="tab-controls tabs-small tabs-rounded" data-highlight="bg-highlight" >
                    <a href="#" data-active data-bs-toggle="collapse" data-bs-target="#tab-1" >Automatics</a>
                    <!--a href="#" data-bs-toggle="collapse" data-bs-target="#tab-2">Card</a-->
                    <a href="#" data-bs-toggle="collapse" data-bs-target="#tab-3">Manual</a>
                </div>
                <div class="clearfix mb-3"></div>
                <div data-bs-parent="#tab-group-1" class="collapse show" id="tab-1">
                <div class="text-center">
                    <!--p class="text-center">
                        <span class="icon icon-l gradient-blue shadow-l rounded-sm">
                            <i class="fa fa-arrow-up font-30 color-white"></i>
                        </span>
                    </p>
                    <h4 class="text-primary">FUND WALLET</h4>
                    
                    <!-- BILLSTACK BANK START-->
<?php 
if ($controller->getConfigValue($data2, "billstackStatus") == "On"): 
    $billstackCharges = $controller->getConfigValue($data2, "billstackCharges");
    $billstackChargesType = $controller->getConfigValue($data2, "billstackChargesType");
    $billstackChargesText = ($billstackChargesType == "flat") ? "N" . $billstackCharges : $billstackCharges . "%";
?>

    <?php if (empty($data->sPalmpayBank)): ?>
        <p class="mb-2 text-danger font-600 font-15">Get a Palmpay Bank Account for Automated Transfer. Generate a dedicated account number now. Funding attracts <?php echo $billstackChargesText; ?> only.</p>
        <form method="POST" id="billstackForm">
            <input type="hidden" name="generate-billstack-account" value="YES" />
        </form>
        <button class="btn btn-primary font-700 rounded-xl mt-3" id="billstackBtn" onclick="$('#billstackBtn').removeClass('btn-primary').addClass('btn-secondary').html('<i class=\'fa fa-spinner fa-spin\'></i> Processing ...'); $('#billstackForm').submit();">Generate Account</button>
    <?php else: ?>
        <p class="mb-2 text-dark font-600 font-16"><b>Bank Name: </b>Palmpay Bank</p>
        <p class="mb-2 text-dark font-600 font-16"><b>Account No: </b><?php echo $data->sPalmpayBank; ?></p>
        <p class="mb-2 text-danger font-600 font-15"><b>Note: </b> Automated bank transfer attracts additional charges of <?php echo $billstackChargesText; ?> only.</p>
        <button class="btn btn-primary font-700 rounded-xl mt-3" onclick="copyToClipboard('<?php echo $data->bsprovidus; ?>')">Copy Account No</button>
    <?php endif; ?>

    <hr />
<?php endif; ?>
<!-- BILLSTACK BANK END -->
                    
                    
                    <!-- PAYVESSEL BANK START-->
                  <?php if($controller->getConfigValue($data2,"payvesselStatus") == "On"): ?>
                  <?php $payvesCharges = $controller->getConfigValue($data2,"payvesselCharges"); 
                  $payvesChargesType = $controller->getConfigValue($data2,"payvesselChargesType"); 
                  $payvesChargesText = ($payvesChargesType == "flat") ? "N".$payvesCharges : $payvesCharges."%";?>
                  <?php $generatedAccountNumber = $controller->generatePayvesselDynamic();?> 
                 <?php  if($data->pVerify == "yes"): ?>
                 <p class="mb-2 text-dark font-600 font-16"><b>Bank Name: </b>9Payment Service Bank (9PSB)</p>
                 <p class="mb-2 text-dark font-600 font-16"><b>Account No: </b><?php echo $data->sPayvesselBank; ?></p>
                 <p class="mb-2 text-danger font-600 font-15"><b>Note: </b> Automated bank transfer attracts additional charges of <?php echo $payvesChargesText; ?> only.</p>
                 <button class="btn btn-primary font-700 rounded-xl mt-3" onclick="copyToClipboard('<?php echo $data->sPayvesselBank; ?>')">Copy Account No</button>
                 <?php else: ?>

                 <?php if(empty($generatedAccountNumber)): ?>
                 <p class="mb-2 text-danger font-600 font-15">Get 9Payment Account. <?php echo $payvesChargesText; ?> Charge only.</p>
                 <p class="mb-2"><b>Note: </b> This is dynamic account and is a TEMPORARY account for funding, can only be used ONE TIME. <?php echo $payvesChargesText; ?> Charge only. <br> <a href="payvessel-verify" class="text-danger"> <b>[ Or Get Permanent 9Payment Account ]</b></a></p>
                 <form method="POST" id="payvesform"><input type="hidden" name="generate-payvessel-dynamic" value="YES" />
                 <input type="hidden" name="id" value="<?php echo $data->sId;?>" /></form>
                 <button class="btn btn-primary font-700 rounded-xl mt-3" id="payvesbtn" onclick="$('#payvesbtn').removeClass('btn-primary'); $('#payvesbtn').addClass('btn-secondary'); $('#payvesbtn').html('<i class=\'fa fa-spinner fa-spin\'></i> Processing ...'); $('#payvesform').submit();">Generate Account</button>
                 <?php else: ?>
                 
                 <p class="mb-2 text-dark font-600 font-16"><b>Bank Name: </b>9Payment Service Bank (9PSB)</p>
                 <p class="mb-2 text-dark font-600 font-16"><b>Account No: </b><?php echo $generatedAccountNumber; ?></p>
                 <p class="mb-2"><b>Note: </b> Do not save this account as beneficiary, can only be used ONE TIME. <br> <a href="payvessel-verify" class="text-danger"> <b>[ Or Get Permanent 9Payment Account ]</b></a> </p>
                <button class="btn btn-primary font-700 rounded-xl mt-3" onclick="copyToClipboard('<?php echo $generatedAccountNumber; ?>')">Copy Account No</button>
                <?php endif; ?><?php endif; ?><?php endif; ?><hr/>

                <!-- PAYVESSEL BANK END -->
                    
                    <!-- ASPFIY BANK START-->
                    <?php
                    
                    //print_r($data2);
                    //die();
                    
                    if($controller->getConfigValue($data2,"asfiyStatus") == "On"): ?>
                    <?php 
                    $asfiyCharges = $controller->getConfigValue($data2,"asfiyCharges");
                    $asfiyChargesType = $controller->getConfigValue($data2,"asfiyChargesType"); ?>
                    <?php $asfiyChargesText = ($asfiyChargesType == "flat") ? "N".$asfiyCharges : $asfiyCharges."%"; ?>
                    <?php if(empty($data->sPaga)): ?>
                        <p class="mb-2 text-danger font-600 font-15">Get A Palmpay Account For Automated Transfer, Generate A Dedicated Account Number Now. Funding Attracts <?php echo $asfiyChargesText; ?> only.</p>
                        <form method="POST" id="aspfiyform1"><input type="hidden" name="generate-aspfiy-palmpay" value="YES" /></form>
                        <button class="btn btn-primary font-700  mt-3" id="aspfiybtn1" onclick="$('#aspfiybtn1').removeClass('btn-primary'); $('#aspfiybtn1').addClass('btn-secondary'); $('#aspfiybtn1').html('<i class=\'fa fa-spinner fa-spin\'></i> Processing ...'); $('#aspfiyform1').submit();" style="background:#f2f2f2; color :<?php echo $sitecolor; ?>;">Generate Account</button>
                    <?php else: ?>
                    <p class="mb-2 text-dark font-600 font-16"><b>Bank Name: </b>Palmpay Microfinance Bank</p>
                    
                    <p class="mb-2 text-dark font-600 font-16"><b>Account No: </b><?php echo $data->sPaga; ?></p>
                    
                    <p class="mb-2 text-danger font-600 font-15"><b>Note: </b> Automated bank transfer attracts additional charges of <?php echo $asfiyChargesText; ?> only.</p>
                    <button class="btn btn-primary font-700  mt-3" onclick="copyToClipboard('<?php echo $data->sPaga; ?>')" style="background:#f2f2f2; color :<?php echo $sitecolor; ?>;">Copy Account No</button>
                    <?php endif; ?>
                    <hr/>
                    <?php endif; ?>
                    
                    <?php
                    
                    //print_r($data2);
                    //die();
                    
                    if($controller->getConfigValue($data2,"asfiyStatus") == "On"): ?>
                    <?php 
                    $asfiyCharges = $controller->getConfigValue($data2,"asfiyCharges");
                    $asfiyChargesType = $controller->getConfigValue($data2,"asfiyChargesType"); ?>
                    <?php $asfiyChargesText = ($asfiyChargesType == "flat") ? "N".$asfiyCharges : $asfiyCharges."%"; ?>
                    <?php if(empty($data->sAsfiyBank)): ?>
                        <p class="mb-2 text-danger font-600 font-15">Get A Paga Account For Automated Transfer, Generate A Dedicated Account Number Now. Funding Attracts <?php echo $asfiyChargesText; ?> only.</p>
                        <form method="POST" id="aspfiyform"><input type="hidden" name="generate-aspfiy-account" value="YES" /></form>
                        <button class="btn btn-primary font-700  mt-3" id="aspfiybtn" onclick="$('#aspfiybtn').removeClass('btn-primary'); $('#aspfiybtn').addClass('btn-secondary'); $('#aspfiybtn').html('<i class=\'fa fa-spinner fa-spin\'></i> Processing ...'); $('#aspfiyform').submit();" style="background:#f2f2f2; color :<?php echo $sitecolor; ?>;">Generate Account</button>
                    <?php else: ?>
                    <p class="mb-2 text-dark font-600 font-16"><b>Bank Name: </b>Paga Microfinance Bank</p>
                    <p class="mb-2 text-dark font-600 font-16"><b>Account No: </b><?php echo $data->sAsfiyBank; ?></p>
                    <p class="mb-2 text-danger font-600 font-15"><b>Note: </b> Automated bank transfer attracts additional charges of <?php echo $asfiyChargesText; ?> only.</p>
                    <button class="btn btn-primary font-700  mt-3"  onclick="copyToClipboard('<?php echo $data->sAsfiyBank; ?>')" style="background:#f2f2f2; color :<?php echo $sitecolor; ?>;">Copy Account No</button>
                    <?php endif; ?>
                    <hr/>
                    <?php endif; ?>
                    
                    <?php  if($profileDetails->sKycStatus <> "verified" && $siteSettings->kycShouldEnable == "yes"): ?>
                        

                        <div id="kycNoteBox" class="border" style="padding:20px;">
                            <img src="../../assets/images/icons/user-verify.png" style="width:50px; height:50px;" />

                            <p class="mb-0 font-600 color-highlight">KYC Verification</p>
                            <h1>Account Verification</h1>
                            <h6 class="color-highlight">Get A Dedicated Bank Account For Fast And Automatic Funding</h6>
                            <hr />
                            <p class="mb-1 font-600 text-danger">As Required By The Central Bank Of Nigeria (CBN), Before You Can Fund Your Wallet With A Virtual Account, We Would Need To Verify Your Identity Using Your NIN or BVN. This Process Is Automatic And You Would Be Able To Fund Your Wallet Once Verified.</p>
                            
                            <a href="kyc-verification" style="width: 100%;" class="the-form-btn btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                            Start Verification
                            </a>

                        </div>
                    <?php else: ?>


                     
                    <!-- MONNIFY BANK START -->
                    <?php if($controller->getConfigValue($data2,"monifyStatus") == "On"): ?>
                        <?php $chargesText = $controller->getConfigValue($data2,"monifyCharges"); ?>
                        <?php if($chargesText == 50 || $chargesText == "50"){$chargesText = "N".$chargesText;} else {$chargesText = $chargesText."%";} ?>
                        <?php if($controller->getConfigValue($data2,"monifyGtStatus") == "On"): ?>
                        <?php if(empty($data->sGtBank)): ?>
                            <p class="mb-2 text-danger font-600 font-15">Get A GT Bank Account For Automated Transfer, Generate A Dedicated Account Number Now. Funding Attracts <?php echo $chargesText; ?> only.</p>
                            <form method="POST" id="gtbankform"><input type="hidden" name="generate-gtbank-account" value="YES" /></form>
                            <button class="btn btn-primary font-700 rounded-xl mt-3" id="gtbankbtn" onclick="$('#gtbankbtn').removeClass('btn-primary'); $('#gtbankbtn').addClass('btn-secondary'); $('#gtbankbtn').html('<i class=\'fa fa-spinner fa-spin\'></i> Processing ...'); $('#gtbankform').submit();">Generate Account</button>
                        <?php else: ?>
                            <p class="mb-2 text-dark font-600 font-16"><b>Bank Name: </b>GT Bank</p>
                            <p class="mb-2 text-dark font-600 font-16"><b>Account No: </b><?php echo $data->sGtBank; ?></p>
                            <p class="mb-2 text-danger font-600 font-15"><b>Note: </b> Automated bank transfer attracts additional charges of <?php echo $chargesText; ?> only.</p>
                            <button class="btn btn-primary font-700 rounded-xl mt-3" onclick="copyToClipboard('<?php echo $data->sGtBank; ?>')">Copy Account No</button>
                        <?php endif; ?>
                        <hr/>
                        <?php endif; if($controller->getConfigValue($data2,"monifyFeStatus") == "On"): ?>
                        <p class="mb-2 text-dark font-600 font-16"><b>Bank Name: </b>Fidelity  Bank</p>
                        <p class="mb-2 text-dark font-600 font-16"><b>Account No: </b><?php echo $data->sFidelityBank; ?></p>
                        <p class="mb-2 text-danger font-600 font-15"><b>Note: </b> Automated bank transfer attracts additional charges of <?php echo $chargesText; ?> only.</p>
                        <button class="btn btn-primary font-700 rounded-xl mt-3" onclick="copyToClipboard('<?php echo $data->sFidelityBank; ?>')">Copy Account No</button>
                        <hr/>
                        <?php endif; if($controller->getConfigValue($data2,"monifyMoStatus") == "On"): ?>
                        <p class="mb-2 text-dark font-600 font-16"><b>Bank Name: </b>Moniepoint Bank</p>
                        <p class="mb-2 text-dark font-600 font-16"><b>Account No: </b><?php echo $data->sRolexBank; ?></p>
                        <p class="mb-2 text-danger font-600 font-15"><b>Note: </b> Automated bank transfer attracts additional charges of <?php echo $chargesText; ?> only.</p>
                        <button class="btn btn-primary font-700 rounded-xl mt-3" onclick="copyToClipboard('<?php echo $data->sRolexBank; ?>')">Copy Account No</button>
                        <hr/>
                        <?php endif; if($controller->getConfigValue($data2,"monifyWeStatus") == "On"): ?>
                        <p class="mb-2 text-dark font-600 font-16"><b>Bank Name: Wema Bank</p>
                        <p class="mb-2 text-dark font-600 font-16"><b>Account No: </b><?php echo $data->sBankNo; ?></p>
                        <p class="mb-2 text-danger font-600 font-15"><b>Note: </b> Automated bank transfer attracts additional charges of <?php echo $chargesText; ?> only.</p>
                        <button class="btn btn-primary font-700 rounded-xl mt-3" onclick="copyToClipboard('<?php echo $data->sBankNo; ?>')">Copy Account No</button>
                        <hr/>
                        <?php endif; if($controller->getConfigValue($data2,"monifySaStatus") == "On"): ?>
                        <p class="mb-2 text-dark font-600 font-16"><b>Bank Name: </b>Sterling Bank</p>
                        <p class="mb-2 text-dark font-600 font-16"><b>Account No: </b><?php echo $data->sSterlingBank; ?></p>
                        <p class="mb-2 text-danger font-600 font-15"><b>Note: </b> Automated bank transfer attracts additional charges of <?php echo $chargesText; ?> only.</p>
                        <button class="btn btn-primary font-700 rounded-xl mt-3" onclick="copyToClipboard('<?php echo $data->sSterlingBank; ?>')">Copy Account No</button>
                        <?php endif; ?>
                    <?php endif; ?>
                    <!-- MONNIFY BANK END -->
                    <?php endif; ?>
                </div>
                </div>

                <div data-bs-parent="#tab-group-1" class="collapse" id="tab-2">
                        <!--div class="text-center">
                            <p class="text-center">
                                <span class="icon icon-l gradient-blue shadow-l rounded-sm">
                                    <i class="fa fa-arrow-up font-30 color-white"></i>
                                </span>
                            </p>
                            <h4 class="text-primary">FUND WALLET</h4>
                            <p class="mb-2 text-dark font-600 font-16">
                                Pay with card, bank transfer, ussd, or bank deposit. Secured by Paystack
                            </p>
                    
                        </div-->
                        
                        <?php if($controller->getConfigValue($data2,"paystackStatus") == "On"): ?>
                        <form  method="post">
                        <div class="mt-5 mb-3">
                            
                            <div class="input-style has-borders no-icon input-style-always-active mb-4">
                                <input type="hidden" value="<?php echo $controller->getConfigValue($data2,"paystackCharges"); ?>" id="paystackcharges" name="paystackcharges" />
                                <input type="number" onkeyup="calculatePaystackCharges()" class="form-control" id="amount" name="amount" placeholder="Amount" required>
                                <label for="amount" class="color-highlight">Amount</label>
                                <em>(required)</em>
                            </div>
                            <div class="input-style has-borders no-icon input-style-always-active  mb-4">
                                <input type="text" class="form-control" id="charges" placeholder="Charges" readonly>
                                <label for="charges" class="color-highlight">Charges</label>
                                <em>(required)</em>
                            </div>
                            <div class="input-style has-borders no-icon input-style-always-active  mb-4">
                                <input type="text" class="form-control" id="amounttopay" placeholder="You Would Get" readonly>
                                <label for="amounttopay" class="color-highlight">You Would Get</label>
                                <em>(required)</em>
                            </div>

                            <input type="hidden" name="email" value="<?php echo $data->sEmail; ?>" />
                        </div>

                        <div class="text-center"><img src="../../assets/img/paystack.png" /></div>
                        <button type="submit" id="fund-with-paystack" name="fund-with-paystack" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                Pay Now
                        </button>
                        </form>
                        <?php else : ?>
                            <h3 class="text-center text-danger">Opps!! Paystack Payment Is Disabled, Please Contact Admin</h3>
                        <?php endif; ?>
                </div>

                <div data-bs-parent="#tab-group-1" class="collapse" id="tab-3">
                <div class="text-center">
                    <!--p class="text-center">
                        <span class="icon icon-l gradient-blue shadow-l rounded-sm">
                            <i class="fa fa-arrow-up font-30 color-white"></i>
                        </span>
                    </p>
                    <h4 class="text-primary">FUND WALLET</h4-->
                    <p class="mb-2 text-dark font-600 font-16"><b>Bank Name: </b><?php echo $data3->bankname; ?></p>
                    <p class="mb-2 text-dark font-600 font-16"><b>Account Name: </b><?php echo $data3->accountname; ?></p>
                    <p class="mb-2 text-dark font-600 font-16"><b>Account No: </b><?php echo $data3->accountno; ?></p>
                    <p class="mb-2 text-danger font-600 font-15"><b>Note: </b> Please contact admin before making any transfer.</p>
                    <button class="btn btn-primary font-700  mt-3" onclick="copyToClipboard('<?php echo $data3->accountno; ?>')" style="background:#f2f2f2; color :<?php echo $sitecolor; ?>;">Copy Account No</button>
                    <a class="btn btn-success font-700  mt-3" href="https://wa.me/234<?php echo $data3->whatsapp; ?>" style="background:#f2f2f2; color :<?php echo $sitecolor; ?>;">Contact Admin</a>
                    
                </div>
                </div>

                
                
            </div>
        </div> 

</div>

