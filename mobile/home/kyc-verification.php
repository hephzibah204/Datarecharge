<div class="page-content header-clear-medium">


    <div class="card card-style">

        <div class="content text-center">
            <img src="../../assets/images/icons/user-verify.png" style="width:100px; height:100px;" />

            <p class="mb-0 font-600 color-highlight">KYC Verification</p>
            <h1>Account Verification</h1>

            <div id="kycNoteBox">
                <hr />
                <p class="mb-1 font-600 text-danger">As Required By The Central Bank Of Nigeria (CBN), Before You Can Fund Your Wallet With A Virtual Account, We Would Need To Verify Your Identity Using Your NIN or BVN. This Process Is Automatic And You Would Be Able To Fund Your Wallet Once Verified.</p>
               
                <button onclick="$('#kycNoteBox').hide(); $('#kycVerBox').show();" style="width: 100%;" class="the-form-btn btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                Start Verification
                </button>

            </div>

            <div id="kycVerBox" style="display: none;">
                <form method="post" class="contactForm the-submit-form">


                    
                    <fieldset>
                        
                        <?php if($siteSettings->kycOption == 'both'){ ?>
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="kycoptionselect" class="color-theme opacity-80 font-700 font-12">Select Option</label>
                                <select id="kycoptionselect" name="kycoptionselect">
                                    <option value="" disabled="" selected="">Select Option</option>
                                    <option value="nin" >Use NIN (N<?php echo $siteSettings->kycNinCharges; ?> Charges)</option>
                                    <option value="bvn" >Use BVN (N<?php echo $siteSettings->kycBvnCharges; ?> Charges)</option>
                                </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>
                        <?php }?>

                        

                        <div class="input-style input-style-always-active has-borders validate-field">
                            <?php if($siteSettings->kycOption == 'nin'){ ?>
                                <label for="vernumber" class="color-theme opacity-80 font-700 font-12">Enter Your NIN Number (N<?php echo $siteSettings->kycNinCharges; ?> Charges)</label>
                            <?php } elseif($siteSettings->kycOption == 'bvn'){ ?>
                                <label for="vernumber" class="color-theme opacity-80 font-700 font-12">Enter Your BVN Number (N<?php echo $siteSettings->kycBvnCharges; ?> Charges)</label>
                            <?php } else { ?>
                                <label for="vernumber" class="color-theme opacity-80 font-700 font-12">Enter The Number Below</label>
                            <?php } ?>
                            <input type="number" name="vernumber" placeholder="Number"  class="round-small" id="vernumber" required />
                        </div>

                        <div class="input-style input-style-always-active has-borders validate-field">
                            <label for="dob" class="color-theme opacity-80 font-700 font-12">Enter Date Of Birth (Month/Day/Year)</label>
                            <input type="date" name="dob" placeholder="Date Of Birth"  class="round-small" id="dob" required />
                        </div>

                        <input type="hidden" name="setkycoption" value="<?php echo $siteSettings->kycOption; ?>" />
                        <input type="hidden" name="accountref" value="<?php echo $profileDetails->accountReference; ?>" />
                        <input type="hidden" name="firstname" value="<?php echo $profileDetails->sFname; ?>" />
                        <input type="hidden" name="lastname" value="<?php echo $profileDetails->sLname; ?>" />
                        <input type="hidden" name="phone" value="<?php echo $profileDetails->sPhone; ?>" />
                        <input type="hidden" name="email" value="<?php echo $profileDetails->sEmail; ?>" />
                        <input type="hidden" name="shouldupdate" value="<?php if(!empty($profileDetails->sBankNo) || !empty($profileDetails->sRolexBank)){ echo "yes"; } else {echo "no"; } ?>" />
                        

                       

                        <div class="form-button">
                            <button type="submit" name="kyc-verification" style="width: 100%;" class="the-form-btn btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                Verify Details
                            </button>
                        </div>
                    </fieldset>
                </form>
            </div>

            
        </div>

    </div>

</div>