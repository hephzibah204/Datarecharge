<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border d-flex align-items-center justify-content-between">
              <h4 class="box-title">Available Pins</h4>
			  </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
    				<?php
    				    foreach($data[2] AS $plans){
    				        echo "<div class='col-4 text-center mt-3'><div class='bg-light' style='padding:10px;'><b class='text-primary'>".$plans["network"]." ".$plans["amount"]."</b><br/><b>".$plans["pins"]. " Pins</b></div></div>"; 
    				    }
    				?>
                </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border d-flex align-items-center justify-content-between">
              <h4 class="box-title">All Uploaded Pin</h4>
			  <div>
			      <a class="btn btn-info btn-sm btn-rounded text-white" href="recharge-pin-discount">
    				  <i class="fa fa-sign-out" aria-hidden="true"></i> Back
    			  </a>
    			  <a class="btn btn-danger btn-sm btn-rounded text-white" href="javascript:clearUsedAirtimePin()">
    				  <i class="fa fa-trash" aria-hidden="true"></i> Clear
    			  </a>
    			  <a class="btn btn-success btn-sm btn-rounded text-white" data-toggle="modal" data-target="#addAirtimePins">
    				  <i class="fa fa-plus" aria-hidden="true"></i> Upload
    			  </a>
			  </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
				<div class="table-responsive">
				  <table id="example1" class="table table-sm table-bordered table-striped">
					<thead>
						<tr>
							<th>#</th>
			                <th>Network</th>
			                <th>Pin</th>
			                <th>Serial</th>
			                <th>Status</th>
			                <th>Sold To</th>
						</tr>
					</thead>
					<tbody>
					
					<?php 
                        $cnt=1; $results=$data[0];
                        if($results <> "" && $results <> 1){foreach($results as $result){   ?>
                        <tr>
                            <td><?php echo htmlentities($cnt);?></td>
                            <td><?php echo $result->network; ?> <?php echo $result->amount; ?></td>
                            <td><?php echo $result->tokens; ?></td>
                            <td><?php echo $result->serial; ?></td>
                            <td><?php echo ($result->status == 'Unused') ? "<b class='text-info'>Unused</b>" : "<b class='text-danger'>Bought</b>"; ?></td>
                            <td><?php echo "<b>".$result->soldto."</b>"; ?></td>
                       </tr>
                        <?php $cnt=$cnt+1;}} ?>
						
					</tbody>
					</table>
				</div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
    </div>
</div>


<!-- Add Category Modal -->
<div class="modal modal-fill fade" data-backdrop="false" id="addAirtimePins" tabindex="-1">
				  <div class="modal-dialog">
					<div class="modal-content border">
					  <div class="modal-header bg-info">
						<h5 class="modal-title">Add New E-Pins</h5>
					</div>
					  <div class="modal-body">
					  <form  method="post" class="form-submit row" enctype="multipart/form-data">

                        <div class="form-group col-12">
                            <label for="success" class="control-label">Network</label>
                            <div class="">
                            <select name="network" class="form-control" id="default" required="required">
                              <option value="">Select Network</option>
                              <?php $results=$data[1]; if($results <> "" && $results <> 1){foreach($results as $result){   ?>
                                <option value="<?php echo $result->networkid; ?>" ><?php echo $result->network; ?></option>
                              <?php }} ?>
                              </select>
                            </div>
                        </div>

                        
                        
                        <div class="form-group col-6">
                            <label for="success" class="control-label">Amount</label>
                            <div class="">
                            <input type="number" placeholder="Amount" name="amount" class="form-control" required="required" id="success">
                            </div>
                        </div>
                        
                        <div class="form-group col-6">
                            <label for="success" class="control-label">Pin File (csv,xls,txt)</label>
                            <div class="">
                            <input type="file" name="pinfile" class="form-control" required="required" id="success" />
                            </div>
                        </div>
                        
                        
                        <div class="form-group col-6">
                            <label for="success" class="control-label">Pin Column</label>
                            <div class="">
                                <select name="pincolumn" class="form-control" required="required" id="pincolumn">
                                    <option value="">Select Column</option>
                                    <option value="1">Column 1</option>
                                    <option value="2">Column 2</option>
                                    <option value="3">Column 3</option>
                                    <option value="4">Column 4</option>
                                    <option value="5">Column 5</option>
                                    <option value="6">Column 6</option>
                                    <option value="7">Column 7</option>
                                    <option value="8">Column 8</option>
                                    <option value="9">Column 9</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group col-6">
                            <label for="success" class="control-label">Serial Number Column</label>
                            <div class="">
                               <select name="serialnocolumn" class="form-control" required="required" id="serialnocolumn">
                                    <option value="">Select Column</option>
                                    <option value="1">Column 1</option>
                                    <option value="2">Column 2</option>
                                    <option value="3">Column 3</option>
                                    <option value="4">Column 4</option>
                                    <option value="5">Column 5</option>
                                    <option value="6">Column 6</option>
                                    <option value="7">Column 7</option>
                                    <option value="8">Column 8</option>
                                    <option value="9">Column 9</option>
                                </select>
                            </div>
                        </div>

                        

                       <div class="form-group col-12">
                        <div class="d-flex justify-content-between ">
                           <button type="submit" name="upload-epins" class="btn btn-info btn-submit"><i class="fa fa-plus" aria-hidden="true"></i> Upload Pins</button>
						   <button type="button" class="btn btn-bold btn-pure btn-secondary" data-dismiss="modal">Close</button>
						</div>
                        </div>
                      </form>
					  </div>
					</div>
				  </div>
</div>
<!-- /.modal -->



