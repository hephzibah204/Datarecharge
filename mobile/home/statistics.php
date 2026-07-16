</style>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<!-- Page content start here-->
        <div class="page-content header-clear-medium">
<div class="card card-style">
    <div class="content"><h1 taxt=center class="card-tittle">Statistics</h1>
          <h5>
         <a class="" href="#sta"><i class="fa fa-pie-chart"> </i>  Statistics</a>
         </h5>
         <hr>
            </div>

            <table class="table">
            
                <tr>
                  <td><b id="sta"><i class="fa fa-bar-chart"></i>  Total Transactions</b></td>
                   <td><b><?php echo $controller->TotalTransactions();?></b></td>
                </tr>
            
                <tr>
                 <td><b><i class="fa fa-line-chart"></i>  Amount Spent This Week</b></td>
                 <td><b>N<?php echo number_format($controller->weeklySpent()); ?></b></td>
                 </tr>
             
                <tr>
                 <td><b><i class="fa fa-line-chart"></i>  Amount Spent This Month</b></td>
                 <td><b>N<?php echo number_format($controller->monthlySpent()); ?></b></td>
                 </tr>
                
                 <tr>
                 <td><b><i class="fa fa-line-chart"></i>  Total Spent</b></td>
                 <td><b>N<?php
                 $totalFund = $controller->getTotalFund();
                 $balance = $data->sWallet;
                 $totalSpent = $totalFund - $balance;
                 
                  echo number_format($totalSpent); ?></b></td>
                 </tr>
            
                  <tr>
                    <td><b><i class="fa fa-money"></i>   Total Funding</b></td>
                    <td><b>N<?php echo number_format($controller->getTotalFund());?></b></td>
                 </tr>

                
                  <tr>
                    <td><b><i class="fa fa-money"></i>  Available Balance </b></td>
                    <td><b>N<?php echo number_format($profileDetails->sWallet); ?></b></td>
                  </tr>

                
            </table> 
</div> 