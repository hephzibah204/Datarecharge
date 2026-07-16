<div class="row">
    <div class="col-12">
        <div class="box">
        <div class="box-header with-border d-flex align-items-center justify-content-between">
              <h4 class="box-title">Pending </h4>
              
              <div class="d-flex align-items-center justify-content-end">
                <a class="btn btn-success btn-rounded text-white mr-2" href="airtime-to-cash-requests">
                  <i class="fa fa-eye" aria-hidden="true"></i> All
                </a>
            
                <a class="btn btn-success btn-rounded text-white" href="airtime-to-cash-settings">
                  <i class="fa fa-cog" aria-hidden="true"></i> Settings
                </a>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
				<div class="table-responsiv">
				  <table id="example1" class="table table-sm table-bordered table-striped">
					<thead>
						<tr>
							<th>Airtime To Cash Requests</th>
					</thead>
					<tbody>
					
					<?php 
                $cnt=1; $results=$data;
                if($results <> "" && $results <> 1){foreach($results as $result){   ?>
                <tr>
                    <td class="text-center">
                           <h5 class="text-primary"><b><?php echo strtoupper($result->sFname . " " . $result->sLname); ?></b></h5>
                           <h6><b>(<?php echo $result->sEmail?></b>)</h6>
                           <p style="text-wrap: wrap;"><?php echo $result->servicedesc;?></p>
                           <p><?php echo $controller->formatDate($result->date);?></p>

                            <p>
                                <button class="btn btn-info mt-2" onclick="updateAirtimeToCashStatus(<?php echo $result->tId;?>,0)"><i class="fa fa-plus"></i></button> 
                                <button class="btn btn-success mt-2" onclick="updateAirtimeToCashStatus(<?php echo $result->tId;?>,2)"><i class="fa fa-check"></i></button> 
                                <button class="btn btn-danger mt-2" onclick="updateAirtimeToCashStatus(<?php echo $result->tId;?>,1)"><i class="fa fa-close"></i></button>
                            </p>
                    </td>
                </tr>
          <?php }} ?>
						
					</tbody>
					</table>
				</div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
    </div>
</div>



