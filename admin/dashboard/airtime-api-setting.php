<div class="row">
<div class="col-12">
    
    <div class="box">
        <div class="box-header with-border d-flex align-items-center justify-content-between">
            <h4 class="box-title">Airtime API</h4>
            <a class="btn btn-info btn-rounded text-white" href="configurations">
                <i class="fa fa-plug" aria-hidden="true"></i> Back
            </a>
        </div>
        <div class="box-header with-border">
            <div class="d-flex justify-content-between mt-2">
                <a class="mr-2" href="airtime-api-setting?network=MTN"><img src="../../assets/images/mtn.png" class="img-fluid" style="width:80px;" /></a> 
                <a class="mr-2" href="airtime-api-setting?network=AIRTEL"><img src="../../assets/images/airtel.png" class="img-fluid" style="width:80px;" /></a> 
                <a class="mr-2" href="airtime-api-setting?network=GLO"><img src="../../assets/images/glo.png" class="img-fluid" style="width:80px;" /></a> 
                <a class="mr-2" href="airtime-api-setting?network=9MOBILE"><img src="../../assets/images/9mobile.png" class="img-fluid" style="width:80px;" /></a> 
            </div> 
        </div>
        <!-- /.box-header -->
        <div class="box-body">
        <form  method="post" class="form-submit">
                    
                <div class="form-group">
                    <label for="success" class="control-label"><?php echo $_GET["network"]; ?> Api Key (VTU)</label>
                    <div class="">
                    <input type="password" name="<?php echo strtolower($_GET["network"]); ?>VtuKey" value="<?php echo $controller->getConfigValue($data[0],strtolower($_GET["network"])."VtuKey"); ?>" placeholder="API Key" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label"><?php echo $_GET["network"]; ?> Api Provider (VTU)</label>
                    <div class="">
                    <select name="<?php echo strtolower($_GET["network"]); ?>VtuProvider" class="form-control" required>
                        <option value="">Select Api Provider</option>
                        <?php $VtuProvider=$controller->getConfigValue($data[0],strtolower($_GET["network"])."VtuProvider"); ?>
                        
                        <?php foreach($data[1] AS $apiLinks): if($apiLinks->type == "Airtime"): ?>
                        <?php if($VtuProvider == $apiLinks->value): ?>
                            <option value="<?php echo $apiLinks->value; ?>" selected><?php echo $apiLinks->name; ?></option>
                        <?php else: ?>
                            <option value="<?php echo $apiLinks->value; ?>"><?php echo $apiLinks->name; ?></option>
                        <?php endif; endif; endforeach; ?>

                    </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label"><?php echo $_GET["network"]; ?> Api Key (Share & Sell)</label>
                    <div class="">
                    <input type="password" name="<?php echo strtolower($_GET["network"]); ?>SharesellKey" value="<?php echo $controller->getConfigValue($data[0],strtolower($_GET["network"])."SharesellKey"); ?>" placeholder="API Key" class="form-control" required>
                    </div>
                </div>
                

                <div class="form-group">
                    <label for="success" class="control-label"><?php echo $_GET["network"]; ?> Api Provider (Share & Sell)</label>
                    <div class="">
                    <select name="<?php echo strtolower($_GET["network"]); ?>SharesellProvider" class="form-control" required>
                        <option value="">Select Api Provider</option>
                        <?php $SharesellProvider=$controller->getConfigValue($data[0],strtolower($_GET["network"])."SharesellProvider"); ?>
                        
                        <?php foreach($data[1] AS $apiLinks): if($apiLinks->type == "Airtime"): ?>
                        <?php if($SharesellProvider == $apiLinks->value): ?>
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
