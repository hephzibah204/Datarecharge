<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border d-flex align-items-center justify-content-between">
              <h4 class="box-title">All Plans</h4>
			  <a class="btn btn-success btn-rounded text-white" data-toggle="modal" data-target="#addDataPlans">
				  <i class="fa fa-plus" aria-hidden="true"></i> Add New
			  </a>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
				<div class="table-responsive">
				  <table id="example1" class="table table-sm table-bordered table-striped">
				     
					<thead>
						<tr>
							<th>#</th>
			                <th>Plan</th>
			                <th>Plan Id</th>
			                <th>Price</th>
			                <th>Action</th>
			                <th>Action</th>
						</tr>
					</thead>
					<tbody>
					
					<?php 
                        $cnt=1; $results=$data[0];
                        if($results <> "" && $results <> 1){foreach($results as $result){   ?>
                        <tr>
                            <td><?php echo htmlentities($cnt);?></td>
                            <td><?php echo $result->description; ?> (<?php echo $result->validity; ?>)</td>
                            <td><?php echo $result->BundleTypeCode; ?></td>
                            <td>N <?php echo $result->price; ?></td>
                           
                            <td>
                                <a href="#" onclick="editSmileDataPlanDetails('<?php echo $result->id; ?>','<?php echo $result->description; ?>','<?php echo $result->BundleTypeCode; ?>','<?php echo $result->price; ?>' ,'<?php echo $result->validity; ?>')" class="btn btn-primary"><i class="fa fa-edit"></i></a> 
						    </td>
                            <td>
                                <a href="#" onclick="deleteSmileDataPlan(<?php echo $result->id;?>)" class="btn btn-danger"><i class="fa fa-trash"></i></a> 
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
<div class="modal fade" data-backdrop="false" id="addDataPlans" tabindex="-1">
				  <div class="modal-dialog modal-lg">
					<div class="modal-content border">
					  <div class="modal-header bg-info">
						<h5 class="modal-title">Add New Plan</h5>
					</div>
					  <div class="modal-body">
					  <form  method="post" class="form-submit">
                        <div class="row">


                            
                            <div class="col-md-6 form-group">
                                <label for="success" class="control-label">Description</label>
                                <div class="">
                                <input type="text" placeholder="eg 1.5GB Bigga" name="dataname" class="form-control" required="required" >
                                </div>
                            </div>

                           

                            
                            <div class="col-md-6 form-group">
                                <label for="success" class="control-label">Bundle Type Code</label>
                                <div class="">
                                <input type="number" placeholder="eg 454" name="planid" class="form-control" required="required" >
                                </div>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="success" class="control-label">Duration In Days</label>
                                <div class="">
                                <input type="text" placeholder="eg 30 Days" name="duration" class="form-control" required="required" >
                                </div>
                            </div>
                            
                            <div class="col-md-12 form-group">
                                <label for="success" class="control-label">Buying Price</label>
                                <div class="">
                                <input type="number" placeholder="Price" name="price" class="form-control" required="required" >
                                </div>
                            </div>

                          

                           
                            </div>

                            <div class="form-group">
                            <div class="d-flex justify-content-between">
                            <button type="submit" name="add-smile-data-plan" class="btn btn-info btn-submit"><i class="fa fa-plus" aria-hidden="true"></i> Add Plan</button>
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
<div class="modal fade" data-backdrop="false" id="editDataPlan" tabindex="-1">
				  <div class="modal-dialog modal-lg">
					<div class="modal-content border">
					  <div class="modal-header bg-info">
						<h5 class="modal-title">Edit Plan</h5>
					</div>
					  <div class="modal-body">
					  <form  method="post" class="form-submit">

                      <div class="row">
                            <input type="hidden" id="plan" name="plan" />
                            

                            
                            <div class="col-md-6 form-group">
                                <label for="success" class="control-label">Description</label>
                                <div class="">
                                <input type="text" id="dataname" placeholder="Name" name="dataname" class="form-control" required="required" >
                                </div>
                            </div>

                            

                            
                            <div class="col-md-6 form-group">
                                <label for="success" class="control-label">Bundle Code</label>
                                <div class="">
                                <input type="number"  id="planid" placeholder="Plan Id" name="planid" class="form-control" required="required" >
                                </div>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="success" class="control-label">Duration In Days</label>
                                <div class="">
                                <input type="text" id="duration" placeholder="Days" name="duration" class="form-control" required="required" >
                                </div>
                            </div>
                            
                            <div class="col-md-12 form-group">
                                <label for="success" class="control-label">Buying Price</label>
                                <div class="">
                                <input type="number" id="price" placeholder="Price" name="price" class="form-control" required="required" >
                                </div>
                            </div>

                            



                            </div>

                       <div class="form-group">
                        <div class="d-flex justify-content-between">
                           <button type="submit" name="update-smile-data-plan" class="btn btn-info btn-submit"><i class="fa fa-save" aria-hidden="true"></i> Update Plan</button>
						   <button type="button" class="btn btn-bold btn-pure btn-secondary" data-dismiss="modal">Close</button>
						</div>
                        </div>
                      </form>
					  </div>
					</div>
				  </div>
</div>
<!-- /.modal -->


