<div class="card">
    <div class="card-body">
      <h5 class="card-title">Search Filter</h5>
       <form  method="POST" class="form-submit row">
      <div class="form-group  col-md-6">
                  <label for="success" class="control-label">Date From</label>
                  <div class="">
                    <input type="date" name="datefrom" placeholder="Date From" class="form-control" required="required"><br/>
                  </div>
                </div><br/>

                <div class="form-group  col-md-6">
                  <label for="success" class="control-label">Date To</label>
                  <div class="">
                    <input type="date" name="dateto" placeholder="Date To" class="form-control" required="required"><br/>
                  </div>
                </div><br/>
                <div class="form-group col-12">
                  <div class="d-flex justify-content-between">
                    <button type="submit" name="filterSales" class="btn btn-primary btn-submit"><i class="fa fa-search" aria-hidden="true"></i> Search</button> <a href="sale-analysis" class="btn btn-primary float-end">Back</a>
                  </div>
                </div>
                </div></div>

  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Sales By User</h5>

      <!-- Bar Chart -->
      <div id="barChart"></div>

      <script>
        document.addEventListener("DOMContentLoaded", () => {
          new ApexCharts(document.querySelector("#barChart"), {
            series: [{
              data: [
                <?php
                $limit = 10;
                $results = $controller->getSpent($limit);
                
                foreach ($results as $result) {
                  echo $result['total_amount'] . ", ";
                }
                ?>
              ]
            }],
            chart: {
              type: 'bar',
              height: 350
            },
            plotOptions: {
              bar: {
                borderRadius: 4,
                horizontal: true,
              }
            },
            dataLabels: {
              enabled: false
            },
            xaxis: {
              categories: [
                <?php
                $cnt = 1;
                foreach ($results as $result) {
                  echo "'". $result['sLname'] . "', ";
                 
                }
                ?>
              ],
            }
          }).render();
        });
      </script>
      <!-- End Bar Chart -->

    </div>
  </div>



 <div class="row">
<div class="col-12">
<div class="card recent-sales overflow-auto">
        <div class="card-body">
        </div>
        
                <table class="table table-borderless datatable">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Account</th>
                        <th scope="col">Email</th>
                        <th scope="col">Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $cnt=1; $limit = 1000; 
                        $results = $controller->getSpent($limit);
                      foreach ($results as $result) {
    ?>
                            <tr>
                                <td><?php echo htmlentities($cnt);?></td>
                                
                                <td><?php echo $result['sFname'] ." ". $result['sLname'];?></td>
                                 <td>N<?php echo number_format( $result['total_amount']);?></td>
                                <td><?php echo $result['sEmail'];?></td> 
                                 <td>N<?php echo number_format( $result['total_profit']);?></td> 
                            </tr>
                          <?php $cnt=$cnt+1;} ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>