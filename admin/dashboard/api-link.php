<div class="col-12">
<div class="card recent-sales overflow-auto">
        <div class="card-body">
         <h5 class="card-title">API Links <a class="btn btn-primary btn-sm btn-rounded text-white ml-2 float-end" href="add-me-some-api">
                        <i class="fa fa-plug" aria-hidden="true"></i> Add New API </a></h5>
        </div>
        
                <table class="table table-borderless datatable">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">API Name</th>
                        <th scope="col">API Type</th>
                        <th scope="col">API Value</th>
                        <th scope="col">Edit API</th>
                        <th scope="col">Delete API</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $cnt=1; $limit = 1000; 
                        $results = $controller->getApiLink($limit);
                      foreach ($results as $result) {
    ?>
                            <tr>
                                <td><?php echo htmlentities($cnt);?></td>
                                
                                <td><?php echo $result->name;?></td>
                                 <td><?php echo $result->type;?></td>
                                <td><?php echo $result->value;?></td>
                                <td>
                                <a href="edit-api-link?name=<?php echo urlencode($result->name); ?>&type=<?php echo urlencode($result->type); ?>&value=<?php echo urlencode($result->value); ?>&aId=<?php echo urlencode($result->aId); ?>" class="btn btn-primary"><b>Edit</b></a>
                                </td>
                                <td><form method="POST">
                                <input type="hidden" name="id" value="<?php echo $result->aId;?>">
                               <button type="submit" name="delete-api-link" class="btn btn-danger"><b>Delete</b></button></form>
                               </td>
                               
                            </tr>
                          <?php $cnt=$cnt+1;} ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>