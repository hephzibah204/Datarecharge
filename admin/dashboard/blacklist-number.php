<div class="row">
<div class="col-12">
<div class="card recent-sales overflow-auto">
        
        
                <table class="table table-borderless datatable">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">Number</th>
                        <th scope="col">Delete</th>
                        
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $cnt=1; $limit = 200; 
                        $results = $controller->getBlacklistNumber($limit);
                      foreach ($results as $result) { ?>
                            <tr>
                                <td><?php echo htmlentities($cnt);?></td>
                                <td><?php echo $result['bPhone'];?></td>
                                <td><form method="POST">
                                <input type="hidden" name="id" value="<?php echo $result['id'];?>">
                               <button type="submit" name="delete-blacklist-number" class="btn btn-danger"><b>Delete</b></button></form>
                               </td>
                            </tr>
                          <?php $cnt=$cnt+1;} ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
