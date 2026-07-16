<div class="row">
<div class="col-12">
    
    <div class="box">
        <div class="box-header with-border d-flex align-items-center justify-content-between">
            <h4 class="box-title">Aspfiy (Kuda) API</h4>

            <div>
                      
                        <a class="btn btn-info btn-sm btn-rounded text-white ml-2" href="configurations">
                            <i class="fa fa-plug" aria-hidden="true"></i> Back
                        </a>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
        <form  method="post" class="form-submit">
             
                <div class="form-group">
                    <label for="success" class="control-label">Aspfiy Api Key</label>
                    <div class="">
                    <input type="password" name="asfiyApi" value="<?php echo $controller->getConfigValue($data,"asfiyApi"); ?>" class="form-control" required="required">
                    </div>
                </div>
                <div class="form-group">
                    <label for="success" class="control-label">Aspfiy Webhook</label>
                    <div class="">
                    <input type="text" name="asfiyWebhook" value="<?php echo $controller->getConfigValue($data,"asfiyWebhook"); ?>" class="form-control" required="required">
                    </div>
                </div>
               
                <div class="form-group">
                    <label for="success" class="control-label">Aspfiy Charges Type</label>
                    <div class="">
                        <select name="asfiyChargesType" class="form-control" required="required">
                        <?php if($controller->getConfigValue($data,"asfiyChargesType") == "flat"): ?>
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
                    <label for="success" class="control-label">Aspfiy Topup Charges</label>
                    <div class="">
                    <input type="text" name="asfiyCharges" pattern="^\d*(\.\d{0,3})?$" value="<?php echo $controller->getConfigValue($data,"asfiyCharges"); ?>" class="form-control" required="required">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="success" class="control-label">Aspfiy Activation</label>
                    <div class="">
                        <select name="asfiyStatus" class="form-control" required="required">
                        <?php if($controller->getConfigValue($data,"asfiyStatus") == "On"): ?>
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
                       <button type="submit" name="update-aspfiy-config" class="btn btn-info btn-submit"><i class="fa fa-save" aria-hidden="true"></i> Update Details</button>
                    </div>
                </div>
        </form>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
</div>
</div>



