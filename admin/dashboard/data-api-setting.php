<div class="row">
<div class="col-12">
    
    <div class="box">
        <div class="box-header with-border d-flex align-items-center justify-content-between">
            <h4 class="box-title">Data API</h4>
            <a class="btn btn-info btn-rounded text-white" href="configurations">
                <i class="fa fa-plug" aria-hidden="true"></i> Back
            </a>
        </div>
        <div class="box-header with-border">
            <div class="d-flex justify-content-between mt-2">
                <a class="mr-2" href="data-api-setting?network=MTN"><img src="../../assets/images/mtn.png" class="img-fluid" style="width:80px;" /></a> 
                <a class="mr-2" href="data-api-setting?network=AIRTEL"><img src="../../assets/images/airtel.png" class="img-fluid" style="width:80px;" /></a> 
                <a class="mr-2" href="data-api-setting?network=GLO"><img src="../../assets/images/glo.png" class="img-fluid" style="width:80px;" /></a> 
                <a class="mr-2" href="data-api-setting?network=9MOBILE"><img src="../../assets/images/9mobile.png" class="img-fluid" style="width:80px;" /></a> 
            </div> 
        </div>
        <!-- /.box-header -->
        <div class="box-body">
        <form  method="post" class="form-submit">
                    
                <div class="form-group">
                    <label for="success" class="control-label"><?php echo $_GET["network"]; ?> Api Key (SME DATA)</label>
                    <div class="">
                        
                        
                        
                    <input type="password" name="<?php echo strtolower($_GET["network"]); ?>SmeApi" value="<?php echo $controller->getConfigValue($data[0],strtolower($_GET["network"])."SmeApi"); ?>" placeholder="API Key" class="form-control" required>
                    </div>
                </div>
                
                
                
                <div class="form-group">
                    <label for="success" class="control-label"><?php echo $_GET["network"]; ?> Api Provider (SME DATA)</label>
                    <div class="">
                    <select name="<?php echo strtolower($_GET["network"]); ?>SmeProvider" class="form-control" required>
                        <option value="">Select Api Provider</option>
                        <?php $SmeProvider=$controller->getConfigValue($data[0],strtolower($_GET["network"])."SmeProvider"); ?>
                        
                        <?php foreach($data[1] AS $apiLinks): if($apiLinks->type == "Data"): ?>
                        <?php if($SmeProvider == $apiLinks->value): ?>
                            <option value="<?php echo $apiLinks->value; ?>" selected><?php echo $apiLinks->name; ?></option>
                        <?php else: ?>
                            <option value="<?php echo $apiLinks->value; ?>"><?php echo $apiLinks->name; ?></option>
                        <?php endif; endif; endforeach; ?>

                    </select>
                    </div>
                </div>


<div class="form-group">
                    <label for="success" class="control-label"><?php echo $_GET["network"]; ?> Api Key (SME2 DATA)</label>
                    <div class="">
                        
                        
                        
                    <input type="password" name="<?php echo strtolower($_GET["network"]); ?>Sme2Api" value="<?php echo $controller->getConfigValue($data[0],strtolower($_GET["network"])."Sme2Api"); ?>" placeholder="API Key" class="form-control" required>
                    </div>
                </div>
                
                
                
                <div class="form-group">
                    <label for="success" class="control-label"><?php echo $_GET["network"]; ?> Api Provider (SME2 DATA)</label>
                    <div class="">
                    <select name="<?php echo strtolower($_GET["network"]); ?>Sme2Provider" class="form-control" required>
                        <option value="">Select Api Provider</option>
                        <?php $Sme2Provider=$controller->getConfigValue($data[0],strtolower($_GET["network"])."Sme2Provider"); ?>
                        
                        <?php foreach($data[1] AS $apiLinks): if($apiLinks->type == "Data"): ?>
                        <?php if($Sme2Provider == $apiLinks->value): ?>
                            <option value="<?php echo $apiLinks->value; ?>" selected><?php echo $apiLinks->name; ?></option>
                        <?php else: ?>
                            <option value="<?php echo $apiLinks->value; ?>"><?php echo $apiLinks->name; ?></option>
                        <?php endif; endif; endforeach; ?>

                    </select>
                    </div>
                </div>








                <div class="form-group">
                    <label for="success" class="control-label"><?php echo $_GET["network"]; ?> Api Key (Gifting Data)</label>
                    <div class="">
                    <input type="password" name="<?php echo strtolower($_GET["network"]); ?>GiftingApi" value="<?php echo $controller->getConfigValue($data[0],strtolower($_GET["network"])."GiftingApi"); ?>" placeholder="API Key" class="form-control" required>
                    </div>
                </div>
                

                <div class="form-group">
                    <label for="success" class="control-label"><?php echo $_GET["network"]; ?> Api Provider (Gifting Data)</label>
                    <div class="">
                    <select name="<?php echo strtolower($_GET["network"]); ?>GiftingProvider" class="form-control" required>
                        <option value="">Select Api Provider</option>
                        <?php $GiftingProvider=$controller->getConfigValue($data[0],strtolower($_GET["network"])."GiftingProvider"); ?>
                        
                        <?php foreach($data[1] AS $apiLinks): if($apiLinks->type == "Data"): ?>
                        <?php if($GiftingProvider == $apiLinks->value): ?>
                            <option value="<?php echo $apiLinks->value; ?>" selected><?php echo $apiLinks->name; ?></option>
                        <?php else: ?>
                            <option value="<?php echo $apiLinks->value; ?>"><?php echo $apiLinks->name; ?></option>
                        <?php endif; endif; endforeach; ?>

                    </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label"><?php echo $_GET["network"]; ?> Api Key (Corporate Data)</label>
                    <div class="">
                    <input type="password" name="<?php echo strtolower($_GET["network"]); ?>CorporateApi" value="<?php echo $controller->getConfigValue($data[0],strtolower($_GET["network"])."CorporateApi"); ?>" placeholder="API Key" class="form-control" required>
                    </div>
                </div>
                

                <div class="form-group">
                    <label for="success" class="control-label"><?php echo $_GET["network"]; ?> Api Provider (Corporate Data)</label>
                    <div class="">
                    <select name="<?php echo strtolower($_GET["network"]); ?>CorporateProvider" class="form-control" required>
                        <option value="">Select Api Provider</option>
                        <?php $CorporateProvider=$controller->getConfigValue($data[0],strtolower($_GET["network"])."CorporateProvider"); ?>
                        
                        <?php foreach($data[1] AS $apiLinks): if($apiLinks->type == "Data"): ?>
                        <?php if($CorporateProvider == $apiLinks->value): ?>
                            <option value="<?php echo $apiLinks->value; ?>" selected><?php echo $apiLinks->name; ?></option>
                        <?php else: ?>
                            <option value="<?php echo $apiLinks->value; ?>"><?php echo $apiLinks->name; ?></option>
                        <?php endif; endif; endforeach; ?>

                    </select>
                    </div>
                </div>
                
                
                
                
                
                
                
                <div class="form-group">
                    <label for="success" class="control-label"><?php echo $_GET["network"]; ?> Api Key (Coupon Data)</label>
                    <div class="">
                    <input type="password" name="<?php echo strtolower($_GET["network"]); ?>CouponApi" value="<?php echo $controller->getConfigValue($data[0],strtolower($_GET["network"])."CouponApi"); ?>" placeholder="API Key" class="form-control" required>
                    </div>
                </div>
                

                <div class="form-group">
                    <label for="success" class="control-label"><?php echo $_GET["network"]; ?> Api Provider (Coupon Data)</label>
                    <div class="">
                    <select name="<?php echo strtolower($_GET["network"]); ?>CouponProvider" class="form-control" required>
                        <option value="">Select Api Provider</option>
                        <?php $CouponProvider=$controller->getConfigValue($data[0],strtolower($_GET["network"])."CouponProvider"); ?>
                        
                        <?php foreach($data[1] AS $apiLinks): if($apiLinks->type == "Data"): ?>
                        <?php if($CouponProvider == $apiLinks->value): ?>
                            <option value="<?php echo $apiLinks->value; ?>" selected><?php echo $apiLinks->name; ?></option>
                        <?php else: ?>
                            <option value="<?php echo $apiLinks->value; ?>"><?php echo $apiLinks->name; ?></option>
                        <?php endif; endif; endforeach; ?>

                    </select>
                    </div>
                </div>
                

                
                <div class="form-group">
                    <div class="">
                       <button type="submit" name="update-api-config" class="btn btn-info btn-submit"><i class="fa fa-save" aria-hidden="true"></i> Update Details</button>
                    </div>
                </div>
        </form>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
</div>
</div>