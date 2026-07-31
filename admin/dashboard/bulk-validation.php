<div class="row">
    <div class="col-md-12">
        <?php
        $ctrl = new BulkValidationController();
        
        if (isset($_POST['update-batch-status'])) {
            $res = $ctrl->updateBatchStatus($_POST);
            echo '<div class="alert alert-' . ($res['status'] == 'success' ? 'success' : 'danger') . '">' . $res['msg'] . '</div>';
        } elseif (isset($_POST['update-item-details'])) {
            $res = $ctrl->updateItemDetails($_POST);
            echo '<div class="alert alert-' . ($res['status'] == 'success' ? 'success' : 'danger') . '">' . $res['msg'] . '</div>';
        }
        
        if (isset($_GET['view'])) {
            echo $ctrl->displayBatchDetails((int)$_GET['view']);
        } else {
        ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Bulk NIN Validation Requests</h3>
                <div class="box-tools">
                    <form method="get" class="form-inline">
                        <input type="hidden" name="url" value="bulk-validation">
                        <select name="status" class="form-control input-sm" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo (isset($_GET['status']) && $_GET['status'] == 'processing') ? 'selected' : ''; ?>>Processing</option>
                            <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                            <option value="failed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'failed') ? 'selected' : ''; ?>>Failed</option>
                        </select>
                    </form>
                </div>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Subscriber</th>
                            <th>Validation Type</th>
                            <th>Total NINs</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $ctrl->displayBulkValidationRequests(); ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<!-- Batch Status Modal -->
<div class="modal fade" id="batchModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Update Batch Status</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="batch_id" id="modalBatchId">
                    <input type="hidden" name="update-batch-status" value="1">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="modalBatchStatus" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Item Update Modal -->
<div class="modal fade" id="itemModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Update NIN Item: <span id="modalNinNumber"></span></h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="item_id" id="modalItemId">
                    <input type="hidden" name="update-item-details" value="1">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="modalItemStatus" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Admin Reply / Result</label>
                        <textarea name="admin_reply" id="modalItemReply" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Result Document (PDF / Image)</label>
                        <input type="file" name="report_file" class="form-control" accept=".pdf,image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateBatchStatusModal(id, status) {
    $('#modalBatchId').val(id);
    $('#modalBatchStatus').val(status);
    $('#batchModal').modal('show');
}

function editItem(id, nin, status, reply) {
    $('#modalItemId').val(id);
    $('#modalNinNumber').text(nin);
    $('#modalItemStatus').val(status);
    $('#modalItemReply').val(reply);
    $('#itemModal').modal('show');
}
</script>