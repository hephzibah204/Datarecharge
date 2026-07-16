<div class="row">
<div class="col-12">
    
    <div class="box">
        <div class="box-header with-border d-flex align-items-center justify-content-between">
            <h4 class="box-title">Site Details</h4>
            <a class="btn btn-info btn-rounded text-white" href="configurations">
                <i class="fa fa-plug" aria-hidden="true"></i> Back
            </a>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
        <form  method="post" class="form-submit">
                    
               <div class="form-group">
                    <label for="success" class="control-label">Website Name</label>
                    <div class="">
                    <input type="text" name="sitename" value="<?php echo $data->sitename; ?>" class="form-control" >
                    </div>
                </div>
                <div class="form-group">
                    <label for="success" class="control-label">Website Url</label>
                    <div class="">
                    <input type="text" name="siteurl" value="<?php echo $data->siteurl; ?>" class="form-control" >
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Api Documentation Link</label>
                    <div class="">
                    <input type="text" name="apidocumentation" value="<?php echo $data->apidocumentation; ?>" class="form-control" >
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Bank Name (For Manual Funding)</label>
                    <div class="">
                    <input type="text" name="bankname" value="<?php echo $data->bankname; ?>" class="form-control" >
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Account Name (For Manual Funding)</label>
                    <div class="">
                    <input type="text" name="accountname" value="<?php echo $data->accountname; ?>" class="form-control" >
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Account Number (For Manual Funding)</label>
                    <div class="">
                    <input type="number" name="accountno" value="<?php echo $data->accountno; ?>" class="form-control" >
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="success" class="control-label">Electricity Charges</label>
                    <div class="">
                    <input type="text" name="electricitycharges" value="<?php echo $data->electricitycharges; ?>" class="form-control" >
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Airtime Purchase (Minimum)</label>
                    <div class="">
                    <input type="text" name="airtimemin" value="<?php echo $data->airtimemin; ?>" class="form-control" >
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Airtime Purchase (Maximum)</label>
                    <div class="">
                    <input type="text" name="airtimemax" value="<?php echo $data->airtimemax; ?>" class="form-control" >
                    </div>
                </div>

                 <div class="form-group">
                    <label for="success" class="control-label">Airtime Maximum Purchase Daily</label>
                    <div class="">
                    <input type="text" name="airtimedaily" value="<?php echo $data->airtimedaily; ?>" class="form-control" >
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="success" class="control-label">Agent Upgrade Fee</label>
                    <div class="">
                    <input type="text" name="agentupgrade" value="<?php echo $data->agentupgrade; ?>" class="form-control" >
                    </div>
                </div>
                <div class="form-group">
                    <label for="success" class="control-label">Vendor Upgrade Fee</label>
                    <div class="">
                    <input type="text" name="vendorupgrade" value="<?php echo $data->vendorupgrade; ?>" class="form-control" >
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Wallet to Wallet Transfer Charges</label>
                    <div class="">
                    <input type="text" name="wallettowalletcharges" value="<?php echo $data->wallettowalletcharges; ?>" class="form-control" >
                    </div>
                </div> 

                <div class="form-group">
                    <label for="success" class="control-label">Referral Bonus (Account Upgrade)</label>
                    <div class="">
                    <input type="text" name="referalupgradebonus" value="<?php echo $data->referalupgradebonus; ?>" class="form-control" >
                    </div>
                </div> 

                <div class="form-group">
                    <label for="success" class="control-label">Referral Bonus (Airtime Purchase)</label>
                    <div class="">
                    <input type="text" name="referalairtimebonus" value="<?php echo $data->referalairtimebonus; ?>" class="form-control" >
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Referral Bonus (Data Purchase)</label>
                    <div class="">
                    <input type="text" name="referaldatabonus" value="<?php echo $data->referaldatabonus; ?>" class="form-control" >
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Referral Bonus (Cable Tv)</label>
                    <div class="">
                    <input type="text" name="referalcablebonus" value="<?php echo $data->referalcablebonus; ?>" class="form-control" >
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Referral Bonus (Exam Pin)</label>
                    <div class="">
                    <input type="text" name="referalexambonus" value="<?php echo $data->referalexambonus; ?>" class="form-control" >
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Referral Bonus (Electricity Bill)</label>
                    <div class="">
                    <input type="text" name="referalmeterbonus" value="<?php echo $data->referalmeterbonus; ?>" class="form-control" >
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Referral Bonus (Wallet Funding)</label>
                    <div class="">
                    <input type="text" name="referalwalletbonus" value="<?php echo $data->referalwalletbonus; ?>" class="form-control" >
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Smile Discount (Percentage)</label>
                    <div class="">
                    <input type="text" name="smilediscount" value="<?php echo $data->smilediscount; ?>" class="form-control" >
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Enable/Disable KYC Verification</label>
                    <div class="">
                    <select name="kycShouldEnable" class="form-control" >
                        <option value="">Select Option</option>
                        
                       <?php if($data->kycShouldEnable == 'yes'):
                                    echo '<option value="yes" selected>Enable</option>'; 
                                    echo '<option value="no">Disable</option>';
                                else:
                                    echo '<option value="no" selected>Disable</option>'; 
                                    echo '<option value="yes">Enable</option>'; 
                                endif;
                        ?>
                        
                    </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">KYC Verification/Link</label>
                    <div class="">
                    <select name="kycShouldVerify" class="form-control" >
                        <option value="">Select Option</option>
                        
                        <?php if($data->kycShouldVerify == 'yes'):
                                    echo '<option value="yes" selected>Verify & Link Details</option>'; 
                                    echo '<option value="no">Link Details Only</option>'; 
                                else:
                                    echo '<option value="no" selected>Link Details Only</option>'; 
                                    echo '<option value="yes">Verify & Link Details</option>'; 
                                endif;
                        ?>

                    </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">KYC Verification Option</label>
                    <div class="">
                    <select name="kycoption" class="form-control" >
                        <option value="">Select Option</option>
                        
                        <?php if($data->kycOption == 'nin'):
                                    echo '<option value="nin" selected>Require NIN Only</option>'; 
                                else:
                                    echo '<option value="nin">Require NIN Only</option>'; 
                                endif;
                        ?>

                        <?php if($data->kycOption == 'bvn'):
                                    echo '<option value="bvn" selected>Require BVN Only</option>'; 
                                else:
                                    echo '<option value="bvn">Require BVN Only</option>'; 
                                endif;
                        ?>

                        <?php if($data->kycOption == 'both'):
                                    echo '<option value="both" selected>Give Option For NIN or BVN</option>'; 
                                else:
                                    echo '<option value="both">Give Option For NIN or BVN</option>'; 
                                endif;
                        ?>
                        
                    </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">KYC BVN Verification Charges</label>
                    <div class="">
                    <input type="text" name="kycbvncharges" value="<?php echo $data->kycBvnCharges; ?>" class="form-control" >
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">KYC NIN Verification Charges</label>
                    <div class="">
                    <input type="text" name="kycnincharges" value="<?php echo $data->kycNinCharges; ?>" class="form-control" >
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="">
                       <button type="submit" name="update-site-setting" class="btn btn-info btn-submit"><i class="fa fa-save" aria-hidden="true"></i> Update Details</button>
                    </div>
                </div>
        </form>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
</div>
</div>



