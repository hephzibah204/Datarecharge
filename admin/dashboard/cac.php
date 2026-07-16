<div class="row">
<div class="col-12">
    
    <div class="box">
        <div class="box-header with-border d-flex align-items-center justify-content-between">
            <h4 class="box-title">CAC Registrations</h4>

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
                    <label for="success" class="control-label">Business Name Registration Charges</label>
                    <div class="">
                    <input type="number" name="CACcharge1" value="<?php echo $controller->getConfigValue($data[0],"CACcharge1"); ?>" class="form-control" required="required">
                    </div>
                </div>
                
              <div class="form-group">
                    <label for="success" class="control-label">Limited Liability Registration Charges</label>
                    <div class="">
                    <input type="number" name="CACcharge2" value="<?php echo $controller->getConfigValue($data[0],"CACcharge2"); ?>" class="form-control" required="required">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="success" class="control-label">CAC Activation</label>
                    <div class="">
                        <select name="CACstatus" class="form-control" required="required">
                        <?php if($controller->getConfigValue($data[0],"CACstatus") == "On"): ?>
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
                       <button type="submit" name="update-cac-charge" class="btn btn-info btn-submit"><i class="fa fa-save" aria-hidden="true"></i> Update Details</button>
                    </div>
                </div>
        </form>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
</div>
</div>

<div class="">
<div class="card recent-sales overflow-auto">
        <div class="card-body">
         <h5 class="card-title">All CAC Registration Request </h5>
        </div>
        
                <table class="table table-borderless datatable">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">Business</th>
                        <th scope="col">Address</th>
                        <th scope="col">Image Link</th>
                        <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $cnt=1; 
                        $results = $controller->getCAC();
                      foreach ($results as $result) { 
                           $type = $result->certType == "biz" ? "Business Name Registration" : "Limited Liability Registration";
    ?>
                            <tr>
                                <td><?php echo htmlentities($cnt);?></td>
                                
                                <td>
                                    <?php echo $type;?> <br>
                                    <?php echo $result->comp_name;?>(1), 
                                    <?php echo $result->alt_comp_name;?>(2)<br>
                                    <?php echo "<b>Nature of Business:</b> " .$result->bus_nature;?>
                                
                                </td>
                                
                                 <td>
                                     <?php echo "<b>Company Adress:</b> ". $result->comp_addr;?> <br>
                                     <?php echo "<b>Resident Address:</b> ". $result->res_addr;?>
                                     <?php echo "<b>Phone Number:</b>  ". $result->phone_num;?>
                                     </td>
                               
                                 <td>
                                     <b>Directir id card:</b> <a href="<?php echo $result->dir_id_card;?>" class="text-primary">Open Image</a> <br>
                                     <b>Passport Photo:</b> <a href="<?php echo $result->passport_photo;?>" class="text-primary">Open Image</a> <br>
                                </td>
                               
                               <td><form method="POST">
                                <input type="hidden" name="id" value="<?php echo $result->id;?>">
                                <input type="hidden" name="email" value="<?php echo $result->email;?>">
                                <select name="status" class="form-control">
                                    <option value="Approved"> Approved </option>
                                    <option value="Rejected"> Rejected </option>
                                </select>
                                <input type="text" name="note" placeholder="Reason for the action" class="form-control p-2">
                               <button type="submit" name="update-cac-status" class="btn btn-primary mt-2"><b>Change Status</b></button></form>
                               </td>
                               
                            </tr>
                          <?php $cnt=$cnt+1;} ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

