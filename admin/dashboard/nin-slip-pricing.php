<div class="row">
<div class="col-12">

    <div class="box">
        <div class="box-header with-border d-flex align-items-center justify-content-between">
            <h4 class="box-title">NIN Slip Pricing</h4>
            <a class="btn btn-info btn-rounded text-white" href="ni-modifications">
                <i class="fa fa-id-card" aria-hidden="true"></i> Back to NIN Modifications
            </a>
        </div>
        <div class="box-body">
        <form method="post" class="form-submit">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Slip Name</th>
                            <th>Buying Price (N)</th>
                            <th>User Price (N)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $cnt = 1; if(isset($data) && is_array($data)): foreach($data as $row): ?>
                        <tr>
                            <td><?php echo $cnt++; ?></td>
                            <td>
                                <input type="hidden" name="id[]" value="<?php echo $row->id; ?>">
                                <input type="text" name="slip_name[]" value="<?php echo htmlentities($row->slip_name); ?>" class="form-control" required>
                            </td>
                            <td>
                                <input type="number" name="buying_price[]" value="<?php echo $row->buying_price; ?>" class="form-control" step="0.01" required>
                            </td>
                            <td>
                                <input type="number" name="user_price[]" value="<?php echo $row->user_price; ?>" class="form-control" step="0.01" required>
                            </td>
                            <td>
                                <a href="nin-slip-pricing?delete=<?php echo $row->id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this slip pricing?')">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                        <tr>
                            <td>--</td>
                            <td><input type="text" name="new_slip_name" class="form-control" placeholder="New slip name"></td>
                            <td><input type="number" name="new_buying_price" class="form-control" step="0.01" placeholder="Buying price"></td>
                            <td><input type="number" name="new_user_price" class="form-control" step="0.01" placeholder="User price"></td>
                            <td><button type="submit" name="add-nin-slip" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Add</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group mt-2">
                <button type="submit" name="update-nin-slip-pricing" class="btn btn-success">
                    <i class="fa fa-save"></i> Save Changes
                </button>
            </div>
        </form>
        </div>
    </div>

</div>
</div>
