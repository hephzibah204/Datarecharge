<div class="row">
    <div class="col-12">
        
        <div class="box">
            <div class="box-header with-border d-flex align-items-center justify-content-between">
                <h4 class="box-title">Other Api Settings</h4>
                <a class="btn btn-info btn-rounded text-white" href="configurations">
                    <i class="fa fa-plug" aria-hidden="true"></i> Back
                </a>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
            <form  method="post" class="form-submit">
                        
                    <div class="form-group">
                        <label for="success" class="control-label">Cable Api Key</label>
                        <div class="">
                        <input type="password" name="cableVerificationApi" value="<?php echo $controller->getConfigValue($data[0],"cableVerificationApi"); ?>" class="form-control" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="success" class="control-label">Cable TV IUC Verification Url</label>
                        <div class="">
                        <select name="cableVerificationProvider" class="form-control" required>
                            <option value="">Select Api Provider</option>
                            <?php $cableVerificationProvider=$controller->getConfigValue($data[0],"cableVerificationProvider"); ?>
                            
                            <?php foreach($data[1] AS $apiLinks): if($apiLinks->type == "CableVer"): ?>
                            <?php if($cableVerificationProvider == $apiLinks->value): ?>
                                <option value="<?php echo $apiLinks->value; ?>" selected><?php echo $apiLinks->name; ?></option>
                            <?php else: ?>
                                <option value="<?php echo $apiLinks->value; ?>"><?php echo $apiLinks->name; ?></option>
                            <?php endif; endif; endforeach; ?>

                        </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="success" class="control-label">Cable Api Key</label>
                        <div class="">
                        <input type="password" name="cableApi" value="<?php echo $controller->getConfigValue($data[0],"cableApi"); ?>" class="form-control" >
                        </div>
                    </div>
                        
                    <div class="form-group">
                        <label for="success" class="control-label">Cable TV API Url</label>
                        <div class="">
                            <select name="cableProvider" class="form-control" required>
                                <option value="">Select Api Provider</option>
                                <?php $cableProvider=$controller->getConfigValue($data[0],"cableProvider"); ?>
                                
                                <?php foreach($data[1] AS $apiLinks): if($apiLinks->type == "Cable"): ?>
                                <?php if($cableProvider == $apiLinks->value): ?>
                                    <option value="<?php echo $apiLinks->value; ?>" selected><?php echo $apiLinks->name; ?></option>
                                <?php else: ?>
                                    <option value="<?php echo $apiLinks->value; ?>"><?php echo $apiLinks->name; ?></option>
                                <?php endif; endif; endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="success" class="control-label">Electricity Meter Api Key</label>
                        <div class="">
                        <input type="password" name="meterVerificationApi" value="<?php echo $controller->getConfigValue($data[0],"meterVerificationApi"); ?>" class="form-control" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="success" class="control-label">Electricity Meter Verification Url</label>
                        <div class="">
                            <select name="meterVerificationProvider" class="form-control" required>
                                <option value="">Select Api Provider</option>
                                <?php $meterVerificationProvider=$controller->getConfigValue($data[0],"meterVerificationProvider"); ?>
                                
                                <?php foreach($data[1] AS $apiLinks): if($apiLinks->type == "ElectricityVer"): ?>
                                <?php if($meterVerificationProvider == $apiLinks->value): ?>
                                    <option value="<?php echo $apiLinks->value; ?>" selected><?php echo $apiLinks->name; ?></option>
                                <?php else: ?>
                                    <option value="<?php echo $apiLinks->value; ?>"><?php echo $apiLinks->name; ?></option>
                                <?php endif; endif; endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="success" class="control-label">Electricity Meter Api Key</label>
                        <div class="">
                        <input type="password" name="meterApi" value="<?php echo $controller->getConfigValue($data[0],"meterApi"); ?>" class="form-control" >
                        </div>
                    </div>

                        
                    <div class="form-group">
                        <label for="success" class="control-label">Electricity API Url</label>
                        <div class="">
                            <select name="meterProvider" class="form-control" required>
                                <option value="">Select Api Provider</option>
                                <?php $meterProvider=$controller->getConfigValue($data[0],"meterProvider"); ?>
                                
                                <?php foreach($data[1] AS $apiLinks): if($apiLinks->type == "Electricity"): ?>
                                <?php if($meterProvider == $apiLinks->value): ?>
                                    <option value="<?php echo $apiLinks->value; ?>" selected><?php echo $apiLinks->name; ?></option>
                                <?php else: ?>
                                    <option value="<?php echo $apiLinks->value; ?>"><?php echo $apiLinks->name; ?></option>
                                <?php endif; endif; endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="success" class="control-label">Exam Api Key</label>
                        <div class="">
                        <input type="password" name="examApi" value="<?php echo $controller->getConfigValue($data[0],"examApi"); ?>" class="form-control" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="success" class="control-label">Exam Checker API Url</label>
                        <div class="">
                            <select name="examProvider" class="form-control" required>
                                <option value="">Select Api Provider</option>
                                <?php $examProvider=$controller->getConfigValue($data[0],"examProvider"); ?>
                            
                                <?php foreach($data[1] AS $apiLinks): if($apiLinks->type == "Exam"): ?>
                                <?php if($examProvider == $apiLinks->value): ?>
                                    <option value="<?php echo $apiLinks->value; ?>" selected><?php echo $apiLinks->name; ?></option>
                                <?php else: ?>
                                    <option value="<?php echo $apiLinks->value; ?>"><?php echo $apiLinks->name; ?></option>
                                <?php endif; endif; endforeach; ?>
                            </select>
                        </div>
                    </div>

                                         <div class="form-group">
                        <label for="success" class="control-label">Recharge Card API Key</label>
                        <div class="">
                        <input type="password" name="rechargePinApi" value="<?php echo $controller->getConfigValue($data[0],"rechargePinApi"); ?>" class="form-control" >
                        </div>
                    </div><br>

                    <div class="form-group">
                        <label for="success" class="control-label">Recharge Card API Url</label>
                        <div class="">
                        <input type="text" name="rechargePinProvider" value="<?php echo $controller->getConfigValue($data[0],"rechargePinProvider"); ?>" class="form-control" >
                        </div>
                    </div><br>
                        
                    <div class="form-group">
                        <label for="success" class="control-label">Data Pin Api Key</label>
                        <div class="">
                        <input type="password" name="dataPinApi" value="<?php echo $controller->getConfigValue($data[0],"dataPinApi"); ?>" class="form-control" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="success" class="control-label">Data Pin API Url</label>
                        <div class="">
                            <select name="dataPinProvider" class="form-control">
                                <option value="">Select Api Provider</option>
                                <?php $dataPinProvider=$controller->getConfigValue($data[0],"dataPinProvider"); ?>
                            
                                <?php foreach($data[1] AS $apiLinks): if($apiLinks->type == "Data Pin"): ?>
                                <?php if($dataPinProvider == $apiLinks->value): ?>
                                    <option value="<?php echo $apiLinks->value; ?>" selected><?php echo $apiLinks->name; ?></option>
                                <?php else: ?>
                                    <option value="<?php echo $apiLinks->value; ?>"><?php echo $apiLinks->name; ?></option>
                                <?php endif; endif; endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="success" class="control-label">Alpha Topup API Key</label>
                        <div class="">
                        <input type="password" name="alphaApi" value="<?php echo $controller->getConfigValue($data[0],"alphaApi"); ?>" class="form-control" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="success" class="control-label">Alpha Topup API Url</label>
                        <div class="">
                        <input type="text" name="alphaProvider" value="<?php echo $controller->getConfigValue($data[0],"alphaProvider"); ?>" class="form-control" >
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