


<div class="page-content header-clear-medium">
        
        <div class="card card-style">
            
            <div class="content">
                <div class="text-center"><img src="../../assets/images/icons/smile.png" width="60" height="60"></div>
                <h1 class="text-center mt-3">Buy Smile</h1>
                
               
                
                <hr/>
                <form method="post" class="smiledataplanForm" id="smiledataplanForm" action="smile-data">
                        <fieldset>

                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="actype" class="color-theme opacity-80 font-700 font-12">Account Type</label>
                                <select id="actype" name="actype">
                                    <option value="" disabled="" selected="">Select Account Type</option>
                                    <option value='PhoneNumber'>Phone Number</option>
                                     <option value='AccountNumber'>Account Number</option>
                                    
                                </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>
                            
                           
                             <div class="input-style has-borders validate-field mb-4 input-box">
                                  <label for="phone" class="color-theme opacity-80 font-700 font-12 smile-phonet">Phone Number</label>
                                  <label for="phone" class="color-theme opacity-80 font-700 font-12 smile-act">Account Number(10 digit)</label>
                              
                                <input type="text" onkeyup="verifyNetwork()" name="phone" placeholder="" value="" class="round-small smile-phone" id="phone"  required   />
                                <input type="hidden" value="<?php echo $data2; ?>" id="smilediscount" />
                            </div>
                            
                            
                            
                            
                            
                            
                            

                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="smiledataplan" class="color-theme opacity-80 font-700 font-12">Product</label>
                                <select id="smiledataplan" name="dataplan" required>
                                    
                                    <option value="" disabled="" selected="">Select Smile Product</option>
                                    
                                    <?php $res = json_decode($data, true); foreach ($res as $plan): ?>
                                        <option value='<?php echo $plan['BundleTypeCode'];?>' dataname='<?php echo $plan['description'];?>' networkname='SMILE' dataprice='<?php echo $plan['price'];?>'><?php echo $plan['description'].' for  ₦'.$plan['price'].' Validity ' .$plan['validity'];?></option>
                                    <?php endforeach; ?>
                                 
                                </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>
 
                        

                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="smileamounttopay" class="color-theme opacity-80 font-700 font-12">Bonus You Will Get (<?php echo $data2; ?>%)</label>
                                <input type="text" name="smileamounttopay" placeholder="Bonus" value="" class="round-small" id="smileamounttopay"  required readonly />
                            </div>

                            <div class="form-check icon-check">
                                <input class="form-check-input" type="checkbox" name="ported_number" id="ported_number">
                                <label class="form-check-label" for="ported_number">Disable Number Validator</label>
                                <i class="icon-check-1 fa fa-square color-gray-dark font-16"></i>
                                <i class="icon-check-2 fa fa-check-square font-16 color-highlight"></i>
                            </div>

                            <input name="transkey" id="transkey" type="hidden" />

                            
                            <div class="form-button">
                            <button type="submit" id="data-btn" name="purchase-smile-data" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                   Buy Smile
                            </button>
                            </div>
                        </fieldset>
                    </form>        
            </div>

        </div>

</div>





