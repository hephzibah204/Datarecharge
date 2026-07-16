<div class="row">
    <div class="col-12">
         <div class="box">
            <div class="box-header with-border d-flex justify-content-between">
              <h4 class="box-title">All Airtime To Cash Requests</h4>
              <a class="btn btn-info btn-sm" href="airtime-to-cash-requests?page=<?php echo $pageCount; ?>">Next 1000</a>
			</div>
            <!-- /.box-header -->
            <div class="box-body">
				<div class="table-responsive">
				  <table id="example1" class="table table-sm table-bordered table-striped">
					<thead>
						<tr>
							<th>#</th>
			                <th>User</th>
			                <th>Request</th>
			                <th>Status</th>
			                <th>Date</th>
			            </tr>
					</thead>
					<tbody>
					
					<?php 
                        $cnt=1; $results=$data;
                        if($results <> "" && $results <> 1){foreach($results as $result){   ?>
                        <tr>
                            <td><?php echo htmlentities($cnt);?></td>
                            <td><?php echo $result->sEmail;?></td>
                            <td><?php echo $result->servicedesc;?></td>
                            <td><?php echo $controller->formatTransStatus($result->status); ?></td>
                            <td><?php echo $controller->formatDate($result->date);?></td>
                        </tr>
                        <?php $cnt=$cnt+1;}} ?>
						
					</tbody>
					</table>
				</div>
            </div>
            <!-- /.box-body -->
          </div>
    </div>
</div>




