<div class="row">
<div class="col-12">
    
    <div class="box">
        <div class="box-header with-border d-flex align-items-center justify-content-between">
            <h4 class="box-title">Payment Point API Settings</h4>

            <div>
                        <a class="btn btn-info btn-sm btn-rounded text-white ml-2" href="payment-gateway">
                            <i class="fa fa-plug" aria-hidden="true"></i> Back
                        </a>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
        <form method="post" class="form-submit">
                    
                <div class="form-group">
                    <label for="success" class="control-label">Payment Point Business Id</label>
                    <div class="">
                    <input type="text" name="paymentpointBusinessId" value="<?php echo $controller->getConfigValue($data,"paymentpointBusinessId"); ?>" class="form-control" required="required">
                    </div>
                </div>
                <div class="form-group">
                    <label for="success" class="control-label">Payment Point Api Key</label>
                    <div class="">
                    <input type="password" name="paymentpointApiKey" value="<?php echo $controller->getConfigValue($data,"paymentpointApiKey"); ?>" class="form-control" required="required">
                    </div>
                </div>
                <div class="form-group">
                    <label for="success" class="control-label">Payment Point Secret Key</label>
                    <div class="">
                    <input type="password" name="paymentpointSecret" value="<?php echo $controller->getConfigValue($data,"paymentpointSecret"); ?>" class="form-control" required="required">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="success" class="control-label">Topup Charges Type</label>
                    <div class="">
                        <select name="paymentpointChargesType" class="form-control" required="required">
                        <?php if($controller->getConfigValue($data,"paymentpointChargesType") == "flat"): ?>
                            <option value="flat" selected>Flat Rate</option>
                            <option value="per">Percentage</option>
                        <?php else: ?>
                            <option value="flat">Flat Rate</option>
                            <option value="per" selected>Percentage</option>
                        <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Wallet Topup Charges</label>
                    <div class="">
                    <input type="text" name="paymentpointCharges" pattern="^\d*(\.\d{0,3})?$" value="<?php echo $controller->getConfigValue($data,"paymentpointCharges"); ?>" class="form-control" required="required">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="success" class="control-label">Payment Point Activation Status</label>
                    <div class="">
                        <select name="paymentpointStatus" class="form-control" required="required">
                        <?php if($controller->getConfigValue($data,"paymentpointStatus") == "On"): ?>
                            <option value="On" selected>On</option>
                            <option value="Off">Off</option>
                        <?php else: ?>
                            <option value="On">On</option>
                            <option value="Off" selected>Off</option>
                        <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="">
                       <button type="submit" name="update-paymentpoint-config" class="btn btn-info btn-submit"><i class="fa fa-save" aria-hidden="true"></i> Update Details</button>
                    </div>
                </div>
        </form>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
</div>
</div>
