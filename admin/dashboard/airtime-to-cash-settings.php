<div class="box">
        <div class="box-header with-border">
          <h4 class="box-title">Airtime2Cash Settings</h4>
        </div>
        
        <!-- /.box-header -->
        <div class="box-body">
        <form  method="post" class="form-submit">
        
                
                <div class="form-group">
                    <label for="examid" class="control-label">Airtime2Cash Status</label>
                    <div class="">
                        <select name="airtime2cashstatus" id="airtime2cashstatus" class="form-control" >
                            <?php if($controller->getConfigValue($data,"airtime2cashstatus") == "On"): ?>
                                <option value="On" selected>Enable</option>
                                <option value="Off">Disable</option>
                            <?php else: ?>
                                <option value="On">Enable</option>
                                <option value="Off" selected>Disable</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <hr/> <div><img src="../../assets/images/mtn.png" class="img-fluid" style="width:80px;" /></div> <hr/>
                
                <div class="form-group">
                    <label for="airtime2cashmtnno" class="control-label">MTN Number</label>
                    <div class="">
                        <input type="number" name="airtime2cashmtnno" value="<?php echo $controller->getConfigValue($data,"airtime2cashmtnno"); ?>" class="form-control" required="required" placeholder="MTN Number" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="airtime2cashmtnrate" class="control-label">MTN Rate (In Percentage Eg:80)</label>
                    <div class="">
                        <input type="text" pattern="[0-9]*\.?[0-9]*" name="airtime2cashmtnrate" value="<?php echo $controller->getConfigValue($data,"airtime2cashmtnrate"); ?>" class="form-control" required="required" placeholder="MTN Rate" />
                    </div>
                </div>
                
                <div><img src="../../assets/images/airtel.png" class="img-fluid" style="width:80px;" /></div> <hr/>
                
                <div class="form-group">
                    <label for="airtime2cashairtelno" class="control-label">Airtel Number</label>
                    <div class="">
                        <input type="number" name="airtime2cashairtelno" value="<?php echo $controller->getConfigValue($data,"airtime2cashairtelno"); ?>" class="form-control" required="required" placeholder="Airtel Number" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="airtime2cashairtelrate" class="control-label">Airtel Rate (In Percentage Eg:80)</label>
                    <div class="">
                        <input type="text" pattern="[0-9]*\.?[0-9]*" name="airtime2cashairtelrate" value="<?php echo $controller->getConfigValue($data,"airtime2cashairtelrate"); ?>" class="form-control" required="required" placeholder="Airtel Rate" />
                    </div>
                </div>
                
                <div><img src="../../assets/images/glo.png" class="img-fluid" style="width:80px;" /></div> <hr/>
                
                <div class="form-group">
                    <label for="airtime2cashglono" class="control-label">GLO Number</label>
                    <div class="">
                        <input type="number" name="airtime2cashglono" value="<?php echo $controller->getConfigValue($data,"airtime2cashglono"); ?>" class="form-control" required="required" placeholder="GLO Number" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="airtime2cashglorate" class="control-label">GLO Rate (In Percentage Eg:80)</label>
                    <div class="">
                        <input type="text" pattern="[0-9]*\.?[0-9]*" name="airtime2cashglorate" value="<?php echo $controller->getConfigValue($data,"airtime2cashglorate"); ?>" class="form-control" required="required" placeholder="GLO Rate" />
                    </div>
                </div>
                
                <div><img src="../../assets/images/9mobile.png" class="img-fluid" style="width:80px;" /></div> <hr/>
                
                <div class="form-group">
                    <label for="airtime2cash9mobileno" class="control-label">9Mobile Number</label>
                    <div class="">
                        <input type="number" name="airtime2cash9mobileno"  value="<?php echo $controller->getConfigValue($data,"airtime2cash9mobileno"); ?>"class="form-control" required="required" placeholder="9Mobile Number" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="airtime2cash9mobilerate" class="control-label">9Mobile Rate (In Percentage Eg:80)</label>
                    <div class="">
                        <input type="text" pattern="[0-9]*\.?[0-9]*" name="airtime2cash9mobilerate" value="<?php echo $controller->getConfigValue($data,"airtime2cash9mobilerate"); ?>" class="form-control" required="required" placeholder="9Mobile Rate" />
                    </div>
                </div>

                

                <div class="form-group">
                    <div class="">
                       <button type="submit" name="update-airtime-cash-setting" class="btn btn-info btn-submit"><i class="fa fa-save" aria-hidden="true"></i> Update Details</button>
                        <a class="btn btn-success" href="airtime-to-cash"><i class="fa fa-home"></i> Back</a>
                    </div>
                </div>
               
              
        </form>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->