
<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border d-flex align-items-center justify-content-between">
              <h4 class="box-title">All Discount</h4>
              <div>
                  <a class="btn btn-primary btn-rounded text-white" href="airtime-pin-stock">
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i> Stock
                  </a>
                  <a class="btn btn-success btn-rounded text-white" data-toggle="modal" data-target="#addAirtimeDiscount">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add New
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
			                <th>Buying Price</th>
			                <th>User Gets</th>
			                <th>Agent Gets</th>
			                <th>Vendor Gets</th>
			                <th>Action</th>
						</tr>
					</thead>
					<tbody>
					
					<?php 
                        $cnt=1; $results=$data[0];
                        if($results <> "" && $results <> 1){foreach($results as $result){   ?>
                        <tr>
                            <td><?php echo htmlentities($cnt);?></td>
                            <td><?php echo $result->network; ?> <?php echo $result->planSize; ?></td>
                            <td>N<?php echo $result->aBuyPrice; ?></td>
                            <td>N<?php echo $result->aUserPrice; ?></td>
                            <td>N<?php echo $result->aAgentPrice; ?></td>
                            <td>N<?php echo $result->aVendorPrice; ?></td>
                            <td>
                                <a href="#" onclick="editAirtimePinDiscount('<?php echo $result->aNetwork; ?>','<?php echo $result->planSize; ?>','<?php echo $result->aBuyPrice; ?>','<?php echo $result->aUserPrice; ?>','<?php echo $result->aAgentPrice; ?>','<?php echo $result->aVendorPrice; ?>','<?php echo $result->loadpin; ?>','<?php echo $result->checkbalance; ?>','<?php echo $result->planid; ?>')" class="btn btn-primary"><i class="fa fa-edit"></i> Edit</a> 
                            
						    </td>
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
<div class="modal modal-fill fade" data-backdrop="false" id="addAirtimeDiscount" tabindex="-1">
				  <div class="modal-dialog">
					<div class="modal-content border">
					  <div class="modal-header bg-info">
						<h5 class="modal-title">Add New Discount</h5>
					</div>
					  <div class="modal-body">
					  <form  method="post" class="form-submit row">

                        <div class="form-group col-12">
                            <label for="success" class="control-label">Network</label>
                            <div class="">
                            <select name="network" class="form-control" id="default" required="required">
                              <option value="">Select Network</option>
                              <?php $results=$data[1]; if($results <> "" && $results <> 1){foreach($results as $result){   ?>
                                <option value="<?php echo $result->rId; ?>" ><?php echo $result->network; ?></option>
                              <?php }} ?>
                              </select>
                            </div>
                        </div>

                        
                        <div class="form-group col-6">
                            <label for="success" class="control-label">Plan ID</label>
                            <div class="">
                            <input type="number" placeholder="Plan Id" name="planid" class="form-control" required="required" id="success">
                            </div>
                        </div>
                        
                        <div class="form-group col-6">
                            <label for="success" class="control-label">Amount</label>
                            <div class="">
                            <input type="text" pattern="[0-9]*\.?[0-9]*" placeholder="Amount" name="amount" class="form-control" required="required" id="success">
                            </div>
                        </div>
                        
                        <div class="form-group col-6">
                            <label for="success" class="control-label">Buying Price</label>
                            <div class="">
                            <input type="text" pattern="[0-9]*\.?[0-9]*" placeholder="User Discount" name="buyprice" class="form-control" required="required" id="success">
                            </div>
                        </div>
                        
                        <div class="form-group col-6">
                            <label for="success" class="control-label">User Gets</label>
                            <div class="">
                            <input type="text" pattern="[0-9]*\.?[0-9]*" placeholder="User Discount" name="userdiscount" class="form-control" required="required" id="success">
                            </div>
                        </div>

                        <div class="form-group col-6">
                            <label for="success" class="control-label">Agent Gets</label>
                            <div class="">
                            <input type="text" pattern="[0-9]*\.?[0-9]*" placeholder="Agent Discount" name="agentdiscount" class="form-control" required="required" id="success">
                            </div>
                        </div>

                        <div class="form-group col-6">
                            <label for="success" class="control-label">Vendor Gets</label>
                            <div class="">
                            <input type="text" pattern="[0-9]*\.?[0-9]*" placeholder="Vendor Discount" name="vendordiscount" class="form-control" required="required" id="success">
                            </div>
                        </div>
                        
                         <div class="form-group col-6">
                            <label for="success" class="control-label">Loading Pin (Eg *555*)</label>
                            <div class="">
                            <input type="text" placeholder="Load Pin" name="loadpin" class="form-control" required="required" id="success">
                            </div>
                        </div>
                        
                         <div class="form-group col-6">
                             <label for="success" class="control-label">Check Balance (Eg *556#)</label
                            <div class="">
                            <input type="text" placeholder="Check Balance" name="checkbal" class="form-control" required="required" id="success">
                            </div>
                        </div>

                       <div class="form-group col-12">
                        <div class="d-flex justify-content-between ">
                           <button type="submit" name="add-recharge-pin-discount" class="btn btn-info btn-submit"><i class="fa fa-plus" aria-hidden="true"></i> Add Discount</button>
						   <button type="button" class="btn btn-bold btn-pure btn-secondary" data-dismiss="modal">Close</button>
						</div>
                        </div>
                      </form>
					  </div>
					</div>
				  </div>
</div>
<!-- /.modal -->

<!-- Edit Category Modal -->
<div class="modal modal-fill fade" data-backdrop="false" id="editAirtimeDicount" tabindex="-1">
				  <div class="modal-dialog">
					<div class="modal-content border">
					  <div class="modal-header bg-info">
						<h5 class="modal-title">Edit Discount</h5>
					</div>
					  <div class="modal-body">
					  <form  method="post" class="form-submit row">

                      <div class="form-group col-12">
                            <label for="success" class="control-label">Network</label>
                            <div class="">
                            <select name="network" id="network" class="form-control" required="required">
                              <option value="">Select Network</option>
                              <?php $results=$data[1]; if($results <> "" && $results <> 1){foreach($results as $result){   ?>
                                <option value="<?php echo $result->rId; ?>" ><?php echo $result->network; ?></option>
                              <?php }} ?>
                              </select>
                            </div>
                        </div>
                        
                        <div class="form-group col-6">
                            <label for="success" class="control-label">Plan ID</label>
                            <div class="">
                            <input type="number" placeholder="Plan Id" name="planid" class="form-control" required="required" id="planid">
                            </div>
                        </div>


                         <div class="form-group col-6">
                            <label for="success" class="control-label">Amount</label>
                            <div class="">
                            <input type="text" pattern="[0-9]*\.?[0-9]*" placeholder="Amount" name="amount" class="form-control" required="required" id="amount">
                            </div>
                        </div>
                        
                        <div class="form-group col-6">
                            <label for="success" class="control-label">Buying Price</label>
                            <div class="">
                            <input type="text" pattern="[0-9]*\.?[0-9]*" placeholder="User Discount" name="buyprice" class="form-control" required="required" id="buyprice">
                            </div>
                        </div>
                        
                        <div class="form-group col-6">
                            <label for="success" class="control-label">User Gets</label>
                            <div class="">
                            <input type="text" pattern="[0-9]*\.?[0-9]*" placeholder="User Discount" name="userdiscount" class="form-control" required="required" id="userpay">
                            </div>
                        </div>

                        <div class="form-group col-6">
                            <label for="success" class="control-label">Agent Gets</label>
                            <div class="">
                            <input type="text" pattern="[0-9]*\.?[0-9]*" placeholder="Agent Discount" name="agentdiscount" class="form-control" required="required" id="agentpay">
                            </div>
                        </div>

                        <div class="form-group col-6">
                            <label for="success" class="control-label">Vendor Gets</label>
                            <div class="">
                            <input type="text" pattern="[0-9]*\.?[0-9]*" placeholder="Vendor Discount" name="vendordiscount" class="form-control" required="required" id="vendorpay">
                            </div>
                        </div>
                        
                         <div class="form-group col-6">
                            <label for="success" class="control-label">Loading Pin (Eg *555*)</label>
                            <div class="">
                            <input type="text" placeholder="Load Pin" name="loadpin" class="form-control" required="required" id="loadpin">
                            </div>
                        </div>
                        
                         <div class="form-group col-6">
                             <label for="success" class="control-label">Check Balance (Eg *556#)</label
                            <div class="">
                            <input type="text" placeholder="Check Balance" name="checkbal" class="form-control" required="required" id="checkbal">
                            </div>
                        </div>

                       <div class="form-group col-12">
                        <div class="d-flex justify-content-between">
                           <button type="submit" name="update-recharge-pin-discount" class="btn btn-info btn-submit"><i class="fa fa-save" aria-hidden="true"></i> Update Discount</button>
						   <button type="button" class="btn btn-bold btn-pure btn-secondary" data-dismiss="modal">Close</button>
						</div>
                        </div>
                      </form>
					  </div>
					</div>
				  </div>
</div>
<!-- /.modal -->


