<div class="row"> 
<div class="col-12"> 
<div class="card recent-sales overflow-auto"> 
        <div class="card-body"> 
         <h5 class="card-title">Monthly Referal Contest</h5> 
        </div> 
         
                <table class="table table-borderless datatable"> 
                    <thead> 
                        <tr> 
                        <th scope="col">#</th> 
                        <th scope="col">Email</th> 
                        <th scope="col">Amount</th> 
                        <th scope="col">No.</th> 
                        <th scope="col">Reffers</th> 
                        </tr> 
                    </thead> 
                    <tbody> 
                        <?php  
                        $cnt=1; $limit = 10000; 
                       $results = $controller->referralContest($limit); 
                      foreach ($results as $result) { 
    ?> 
                            <tr> 
                                <td><?php echo htmlentities($cnt);?></td> 
                                 
                                <td><?php echo $result->sEmail;?></td> 
                                 <td>N<?php echo number_format($result->total_amount);?></td> 
                                <td><?php echo $result->refer_count;?></td>  
                                <td><?php echo $result->referals;?></td> 
                            </tr> 
                          <?php $cnt=$cnt+1;} ?> 
                    </tbody> 
                </table> 
            </div> 
        </div> 
    </div>  
     
    <div class="row"> 
<div class="col-12"> 
<div class="card recent-sales overflow-auto"> 
        <div class="card-body"> 
         <h5 class="card-title">Refer fund</h5> 
        </div> 
         
                <table class="table table-borderless datatable"> 
                    <thead> 
                        <tr> 
                        <th scope="col">#</th> 
                        <th scope="col">Email</th> 
                        <th scope="col">Amount</th> 
                        <th scope="col">No.</th> 
                        <th scope="col">Reffers</th> 
                        </tr> 
                    </thead> 
                    <tbody> 
                        <?php  
                        $cnt=1; $limit = 10000; 
                       $results = $controller->getTopRefer($limit); 
                      foreach ($results as $result) { 
    ?> 
                            <tr> 
                                <td><?php echo htmlentities($cnt);?></td> 
                                 
                                <td><?php echo $result->sEmail;?></td> 
                                 <td>N<?php echo number_format($result->total_amount);?></td> 
                                <td><?php echo $result->refer_count;?></td>  
                                <td style="white-space: nowrap;"><?php echo $result->referals; ?></td>
                            </tr> 
                          <?php $cnt=$cnt+1;} ?> 
                    </tbody> 
                </table> 
            </div> 
        </div> 
    </div>