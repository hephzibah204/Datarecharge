<?php
$wallet = (isset($data) && is_array($data) && isset($data[1]) && is_array($data[1])) ? $data[1] : [];
$stats = (isset($data) && is_array($data) && isset($data[0]) && is_array($data[0])) ? $data[0] : [];
?>
<div class="row">
						
					
<div class="col-12">
    <div class="box">
      <div class="row no-gutters py-2">
        <div class="col-12 col-lg-4">
          <div class="box-body br-1 border-light no-radius">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <i class="fa fa-chain text-info font-size-50"></i><br>
                </div>
                <div>										
                      <h2 class="text-info my-0 text-right">N<?php echo $wallet["walletOneBalance"] ?? "0.00"; ?></h2>
                    <p class="mb-0 text-muted text-right">Wallet Balance</p>
                    <p class="mb-0 text-dark text-right">(<b><?php echo $wallet["walletOneProvider"] ?? "N/A"; ?></b>)</p>
                </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-4">
          <div class="box-body br-1 border-light no-radius">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <i class="fa fa-chain text-info font-size-50"></i><br>
                </div>
                <div>										
                      <h2 class="text-info my-0 text-right">N<?php echo $wallet["walletTwoBalance"] ?? "0.00"; ?> </h2>
                    <p class="mb-0 text-muted text-right">Wallet Balance</p>
                    <p class="mb-0 text-dark text-right">(<b><?php echo $wallet["walletTwoProvider"] ?? "N/A"; ?></b>)</p>
                </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-4">
          <div class="box-body br-1 border-light no-radius">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <i class="fa fa-chain text-info font-size-50"></i><br>
                </div>
                <div>										
                      <h2 class="text-info my-0 text-right">N<?php echo $wallet["walletThreeBalance"] ?? "0.00"; ?> </h2>
                    <p class="mb-0 text-muted text-right">Wallet Balance</p>
                    <p class="mb-0 text-dark text-right">(<b><?php echo $wallet["walletThreeProvider"] ?? "N/A"; ?></b>)</p>
                </div>
            </div>
          </div>
        </div>

      </div>
    </div>
</div>

<div class="col-12">
    <div class="box">
      <div class="row no-gutters py-2">
        
        <div class="col-12 col-lg-3">
          <div class="box-body br-1 border-light no-radius">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <i class="fa fa-money text-success font-size-50"></i><br>
                </div>
                <div>										
                      <h2 class="text-success my-0 text-right">N<?php echo number_format($stats["uwCount"] ?? 0); ?> </h2>
                    <p class="mb-0 text-muted text-right">User</p>
                    <p class="mb-0 text-dark text-right">(Wallet)</p>
                </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-3">
          <div class="box-body br-1 border-light no-radius">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <i class="fa fa-money text-success font-size-50"></i><br>
                </div>
                <div>										
                      <h2 class="text-success my-0 text-right">N<?php echo number_format($stats["awCount"] ?? 0); ?></h2>
                    <p class="mb-0 text-muted text-right">Agent</p>
                    <p class="mb-0 text-dark text-right">(Wallet)</p>
                </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-3">
          <div class="box-body br-1 border-light no-radius">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <i class="fa fa-money text-success font-size-50"></i><br>
                </div>
                <div>										
                      <h2 class="text-success my-0 text-right">N<?php echo number_format($stats["vwCount"] ?? 0); ?></h2>
                    <p class="mb-0 text-muted text-right">Vendors</p>
                    <p class="mb-0 text-dark text-right">(Wallet)</p>
                </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-3">
          <div class="box-body br-1 border-light no-radius">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <i class="fa fa-money text-success font-size-50"></i><br>
                </div>
                <div>										
                      <h2 class="text-success my-0 text-right">N<?php echo number_format($stats["rwCount"] ?? 0); ?></h2>
                    <p class="mb-0 text-muted text-right">Referrals</p>
                    <p class="mb-0 text-dark text-right">(Wallet)</p>
                </div>
            </div>
          </div>
        </div>

      </div>
    </div>
</div>

<div class="col-md-3">
    <div class="box">
        <div class="box-body">
            <div class="d-flex align-items-center justify-content-between">
                <div class="">
                    <h2 class="mb-2"> <?php echo $stats["sCount"] ?? 0; ?> <small>Subscribers</small></h2>
                    <p class="text-muted mb-0"><span class="badge badge-primary">Users</span></p>
                </div>
                <div class="fa fa-user text-primary font-size-30"></div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="box">
        <div class="box-body">
            <div class="d-flex align-items-center justify-content-between">
                <div class="">
                    <h2 class="mb-2"><?php echo $stats["aCount"] ?? 0; ?> <small>Agents</small></h2>
                    <p class="text-muted mb-0"><span class="badge badge-primary">Users</span></p>
                </div>
                <div class="fa fa-users text-primary font-size-30"></div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="box">
        <div class="box-body">
            <div class="d-flex align-items-center justify-content-between">
                <div class="">
                    <h2 class="mb-2"><?php echo $stats["vCount"] ?? 0; ?> <small>Vendors</small></h2>
                    <p class="text-muted mb-0"><span class="badge badge-primary">Users</span></p>
                </div>
                <div class="fa fa-users text-primary font-size-30"></div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="box">
        <div class="box-body">
            <div class="d-flex align-items-center justify-content-between">
                <div class="">
                    <h2 class="mb-2"> <?php echo $stats["rCount"] ?? 0; ?> <small>Referrals</small></h2>
                    <p class="text-muted mb-0"><span class="badge badge-primary">Referrals</span></p>
                </div>
                <div class="fa fa-group text-primary font-size-30"></div>
            </div>
        </div>
    </div>
</div>



<div class="col-12 col-lg-3">
    <div class="box">
        <div class="box-body">
            <div class="d-flex align-items-center justify-content-between">
                <div class="">
                    <h2 class="mb-2"> <?php echo $stats["alphaCount"] ?? 0; ?> <small>Alpha</small></h2>
                    <p class="text-muted mb-0"><span class="badge badge-danger">Alpha Request</span></p>
                </div>
                <div class="fa fa-list-alt text-danger font-size-30"></div>
            </div>
        </div>
    </div>
</div>

<div class="col-12 col-lg-3">
    <div class="box">
        <div class="box-body">
            <div class="d-flex align-items-center justify-content-between">
                <div class="">
                    <h2 class="mb-2"> <?php echo $stats["tCount"] ?? 0; ?> <small>Tran</small></h2>
                    <p class="text-muted mb-0"><span class="badge badge-danger">Transactions</span></p>
                </div>
                <div class="fa fa-list-alt text-danger font-size-30"></div>
            </div>
        </div>
    </div>
</div>

<!-- Traffic Sta -->
<div class="col-12 col-lg-3">
    <div class="box">
        <div class="box-body">
            <div class="d-flex align-items-center justify-content-between">
                <div class="">
                    <h2 class="mb-2"> <b><?php echo $stats["mCount"] ?? 0; ?> <small>Unread</small> </b></h2>
                    <p class="text-muted mb-0"><span class="badge badge-danger">Message</span></p>
                </div>
                <div class="fa fa-envelope text-danger font-size-30"></div>
            </div>
        </div>
    </div>
</div>

<div class="col-12 col-lg-3">
    <div class="box">
        <div class="box-body">
            <div class="d-flex align-items-center justify-content-between">
                <div class="">
                    <h2 class="mb-2"> <b><?php echo $stats["visitCount"] ?? 0; ?> <small>Visit Today</small> </b></h2>
                    <p class="text-muted mb-0"><span class="badge badge-danger">Total Visit Today</span></p>
                </div>
                <div class="fa fa-eye text-danger font-size-30"></div>
            </div>
        </div>
    </div>
</div>

<!-- Traffic Sta -->
<div class="col-md-12">
    <div class="box">
        <div class="box-body bg-info text-white">
        <h5 class="box-title"><b>Last 50 Transactions</b></h5>
        </div>
        <!-- /.box-header -->
        <div class="">
        <div class="table-responsive">
<table id="example1" class="table table-sm table-bordered table-striped">
<thead>
    <tr>
        <th>#</th>
        <th>Ref Id</th>
        <th>User</th>
        <th>User Type</th>
        <th>Phone</th>
        <th>Service</th>
        <th>Description</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Date</th>
    </tr>
</thead>
<tbody>

<?php 
    $cnt=1; $results=$stats["transactions"] ?? [];
    if($results <> "" && $results <> 1){foreach($results as $result){   ?>
    <tr>
        <td><?php echo htmlentities($cnt);?></td>
        <td><a href="transaction-details?ref=<?php echo $result->transref; ?>" class="text-info"><b><?php echo $result->transref; ?></b></a></td>
        <td><?php echo $result->sEmail;?></td>
        <td><?php echo $controller->formatUserType($result->sType);?></td>
        <td><?php echo $result->sPhone;?></td>
        <td><?php echo $result->servicename;?></td>
        <td><?php echo $result->servicedesc;?></td>
        <td>N<?php echo $result->amount;?></td>
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
    <!-- /.box -->
</div>
</div>
