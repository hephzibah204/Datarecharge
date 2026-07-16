<!-- Page content start here-->
        <div class="page-content header-clear-medium">
        
        <div class="card card-style">
            
            <div class="content">
                
                <p class="mb-0 text-center font-600 color-highlight">Verify and Print NIN Slips</p>
                <h1 class="text-center">NIN With Phone Number </h1>

                <div class="row text-center mb-2">
                    
                    <a style="display: none;" id="regular" onclick="document.getElementById('networkid').value='regular'; getPo();" class="col-md-3 col-12 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                            <img src="https://app.beensade.com/img/regular.png" height="60" />
                            <i class="fa fa-check valid color-green-dark"></i>
                        </span>
                    </a>
                    
                    <a style="display: none;" id="standard" onclick="document.getElementById('networkid').value='standard'; getPo();" class="col-md-3 col-12 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                            <img src="https://app.beensade.com/img/standard.png"  height="60" />
                            <i class="fa fa-check valid color-green-dark"></i>
                        </span>
                    </a>
                    
                    <a style="display: none;" id="premium" onclick="document.getElementById('networkid').value='premium'; getPo();" class="col-md-3 col-12 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                            <img src="https://app.beensade.com/img/premium.png" height="60" />
                            <i class="fa fa-check valid color-green-dark"></i>
                        </span>
                    </a>
                    <a style="display: none;" id="placeholder" onclick="document.getElementById('networkid').value='premium'; getPo();" class="col-md-3 col-12 mt-2">
                        <span class="icon icon-l rounded-sm py-2 px-2" style="background:#f2f2f2;">
                            <img id="placeholderImg" src="https://app.beensade.com/img/premium.png" height="60" />
                            <i class="fa fa-check valid color-green-dark"></i>
                        </span>
                    </a>
                   
                    
                </div>
                <hr/>
               
                <form method="post" class="verifyForm" id="verifyForm" action="verify-pnv">
                        <fieldset>
 
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="networkid" class="color-theme opacity-80 font-700 font-12">Slip Design</label>
                                <select onchange="getPo()" id="networkid" name="network">
                                    <option value="" disabled="" selected="">~Select Slip Design~</option>
                                        <option data-price=500 value="regular">Regular Slip</option>
                                        <option data-price=1000 value="standard">Standard Slip</option>
                                        <option data-price=1500 value="premium">Premium Slip</option>
                                </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>

                            
 
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="phone" class="color-theme opacity-80 font-700 font-12">Phone Number</label>
                                <input type="number" name="phone" placeholder="~Phone Number~" value="" class="round-small" id="phone" required  />
                            </div>

                            <p id="verifyer"></p>

                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="payable" class="color-theme opacity-80 font-700 font-12">Amount To Pay</label>
                                <input type="text" name="payable" placeholder="~Amount To Pay~" value="" class="round-small" id="payable" readonly required  />
                            </div>


                            <input name="transref" type="hidden" value="<?php echo $transRef; ?>" />
                            <input name="transkey" id="transkey" type="hidden" />

                            
                            <div class="form-button">
                            <button type="submit" id="verify-pnv" name="verify-pnv"  class="btn btn-info btn-lg btn-block" style="background-color:<?php echo $sitecolor; ?>;">
                                   Verify Phone Number
                            </button>
                            </div>
                        </fieldset>
                    </form>        
            </div>

        </div>

</div>
<script>
    function getPo(){
        var sel = document.getElementById("networkid").options[document.getElementById("networkid").selectedIndex].dataset.price;
        document.getElementById('payable').value = '₦'+sel; 
        document.getElementById('placeholder').style.display = 'block'; 
        document.getElementById('placeholderImg').src = 'https://app.beensade.com/img/'+document.getElementById("networkid").options[document.getElementById("networkid").selectedIndex].value+'.png'; 
    }
   
</script>


    <!-- Page content ends here-->
