<div class="row">
<div class="col-12">
    
    <div class="box">
        <div class="box-header with-border d-flex align-items-center justify-content-between">
            <h4 class="box-title">NIN API Settings</h4>
            <a class="btn btn-info btn-rounded text-white" href="configurations">
                <i class="fa fa-plug" aria-hidden="true"></i> Back
            </a>
        </div>
        <div class="box-body">
        <form method="post" class="form-submit">
                    
                <div class="form-group">
                    <label class="control-label">NIN API Status</label>
                    <div class="">
                    <select name="ninStatus" class="form-control" required>
                        <option value="">Select Status</option>
                        <?php $ninStatus=$controller->getConfigValue($data[0],"ninStatus"); ?>
                        <option value="On" <?php echo ($ninStatus=="On")?"selected":""; ?>>On</option>
                        <option value="Off" <?php echo ($ninStatus=="Off")?"selected":""; ?>>Off</option>
                    </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">NIN API Key</label>
                    <div class="">
                    <input type="password" name="ninApi" value="<?php echo $controller->getConfigValue($data[0],"ninApi"); ?>" placeholder="NIN API Key / Bearer Token" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">NIN API URL</label>
                    <div class="">
                    <input type="text" name="ninProvider" value="<?php echo $controller->getConfigValue($data[0],"ninProvider"); ?>" placeholder="https://api.example.com/nin/" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="">
                       <button type="submit" name="update-api-config" class="btn btn-info btn-submit"><i class="fa fa-save" aria-hidden="true"></i> Update Details</button>
                    </div>
                </div>
        </form>
        </div>
      </div>
</div>
</div>
