<div class="row">
<div class="col-12">
    
    <div class="box">
        <div class="box-header with-border d-flex align-items-center justify-content-between">
            <h4 class="box-title">Billstack API</h4>

            <div>
                        <a class="btn btn-info btn-sm btn-rounded text-white ml-2" href="payment-gateway">
                            <i class="fa fa-plug" aria-hidden="true"></i> Back
                        </a>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
        <form  method="post" class="form-submit">
                    
                <div class="form-group">
                    <label for="success" class="control-label">Billstack Secret Key</label>
                    <div class="">
                    <input type="password" name="billstackSecret" value="<?php echo $controller->getConfigValue($data,"billstackSecret"); ?>" class="form-control" required="required">
                    </div>
                </div>

                <div class="form-group">
                    <label for="success" class="control-label">Wallet Topup Charges</label>
                    <div class="">
                    <input type="text" name="billstackCharges" pattern="^\d*(\.\d{0,3})?$" value="<?php echo $controller->getConfigValue($data,"billstackCharges"); ?>" class="form-control" required="required">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="success" class="control-label">Billstack Activation</label>
                    <div class="">
                        <select name="billstackStatus" class="form-control" required="required">
                        <?php if($controller->getConfigValue($data,"billstackStatus") == "On"): ?>
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
                       <button type="submit" name="update-billstack-config" class="btn btn-info btn-submit"><i class="fa fa-save" aria-hidden="true"></i> Update Details</button>
                    </div>
                </div>
        </form>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
</div>
</div>