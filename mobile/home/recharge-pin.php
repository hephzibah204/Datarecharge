<div class="page-content header-clear-medium">
        
        <div class="card card-style">
            
            <div class="content">
                <p class="mb-0 text-center font-600 color-highlight">Recharge Card Pin</p>
                <h1 class="text-center">Recharge Card</h1>
                
                <form method="post" class="rechargepinForm" id="rechargepinForm" action="recharge-pin">
                        <fieldset>
                                
                            <input name="transkey" id="transkey" type="hidden" />
                            <div class="row" style="display:none;">
                				<?php
                				
                				   if(!empty($data3)){
                				        foreach($data3 AS $plans){
                				           
                    				        if(is_array($plans)){echo "<div id='{$plans["network"]}{$plans["amount"]}pins'><b>{$plans["pins"]} {$plans["network"]} Pins Available</b></div>";}
                    				        if(is_object($plans)){echo "<div id='{$plans->network}{$plans->amount}pins'><b>{$plans->pins} {$plans->network} Pins Available</b></div>";}
                        				}
                				    }
                				?>
                            </div>
                            
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="businessname" class="color-theme opacity-80 font-700 font-12">Name To Display On Card</label>
                                <input type="text" name="businessname" placeholder="Name To Display On Card" value="" class="round-small" id="businessname"  required  />
                            </div>

                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="networkid" class="color-theme opacity-80 font-700 font-12">Network</label>
                                <select id="airtimepin-networkid" name="network">
                                    <option value="" enabled="" selected="">Select Network</option>
                                    <?php foreach($data AS $network): if($network->networkStatus == "On" && $network->airtimepinStatus == "On"): ?>
                                        <option value="<?php echo $network->rId; ?>" networkname="<?php echo $network->network; ?>"><?php echo $network->network; ?></option>
                                    <?php endif; endforeach; ?>
                                </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>
                            
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="amount" class="color-theme opacity-80 font-700 font-12">Amount</label>
                                 <select id="airtimepin-amount" name="amount">
                                    <option value="" disabled="" selected="">Select Amount</option>
                                </select>
                            </div>
                            
                            <p class="text-danger" style="display:none;" id="available-pins"></p>
                            
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="quantity" class="color-theme opacity-80 font-700 font-12">Quantity</label>
                                <input type="number" id="airtimepin-quantity" name="quantity" placeholder="Quantity" value="" class="round-small" required  />
                            </div>

                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="amount" class="color-theme opacity-80 font-700 font-12">Amount To Pay</label>
                                <input type="text" placeholder="Amount" value="" class="round-small" id="amounttopay"  required readonly  />
                            </div>
                            
                            <input name="transref" type="hidden" value="<?php echo $transRef; ?>" />
                           
                           
                          
                            <div class="form-button">
                            <button type="submit" id="rechargepin-btn" name="purchase-recharge-pin" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                   Purchase Pin
                            </button>
                            </div>
                        </fieldset>
                    </form>        
            </div>

        </div>

</div>





