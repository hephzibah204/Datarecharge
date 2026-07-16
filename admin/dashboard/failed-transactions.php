<form method="POST">
 <div class="col-12">
    <div class="card recent-sales overflow-auto">
        <div class="card-body">
         <h5 class="card-title">Failed Transactions <span>| <a href="processing-transactions"><b>Processing</b></a></span></h5>
        <p>Select All <input type="checkbox" id="selectAll"> <button name="delete-transaction" class="btn btn-danger float-end">Delete</button></p>
               </div>
        
                <table class="table table-borderless datatable">
                    <thead>
                     <tr>
                        <th scope="col">*</th>
                        <th scope="col">Details</th>
                        <th scope="col">Description</th>
                    </tr>
                    </thead>
                    <tbody>
                         <?php $cnt=1; $results = $data;
                    if(empty($results)) {
                     echo "<tr><td colspan='12' class='text-center'>No transactions available</td></tr>";} 
                     else { foreach($results as $result) { ?>
                           <tr>
                                <td>
                                   <input type="checkbox" name="ref[]" class="recordCheckbox" value="<?php echo $result->transref; ?>" checked>
                                    
                                    </td> 
                                    
                                    <td>
                                    <p>Status: <?php echo $controller->formatTransStatus($result->status); ?> </p>
                                    <p><?php echo $controller->formatDate($result->date); ?></p> <input type="checkbox" id="selectAll">
                                    
                                    </td>
                                 
                                 <td><p><?php echo $result->servicedesc; ?>: <?php echo $result->api_response; ?> </p>
                                 
                                 <p><a href="transaction-details?ref=<?php echo $result->transref; ?>" class="text-primary"><b>View Details</b></a></p></td>
                            </tr>
                            <?php $cnt=$cnt+1;}} ?>
                    </tbody>
                </table>
             
              </div> </div></form>

<script>
    document.getElementById('selectAll').addEventListener('change', function () {
        var checkboxes = document.querySelectorAll('.recordCheckbox');
        checkboxes.forEach(function (checkbox) {
            checkbox.checked = document.getElementById('selectAll').checked;
        });
    });
</script>
