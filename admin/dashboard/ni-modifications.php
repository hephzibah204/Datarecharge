<div class="row">
    <div class="col-md-12">
        <?php
        // Handle status update
        if (isset($_GET['update-status'])) {
            $ctrl = new NINModificationController();
            $result = $ctrl->updateModificationStatus($_REQUEST);
            echo '<div class="alert alert-' . ($result['status'] === 'success' ? 'success' : 'danger') . '">' . $result['msg'] . '</div>';
        }

        // Handle view details
        if (isset($_GET['view'])) {
            $ctrl = new NINModificationController();
            echo $ctrl->displayModificationRequestDetails();
            echo '<a href="ni-modifications" class="btn btn-default mt-3">Back to List</a>';
        } else {
        ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">NIN Modification Requests</h3>
                <div class="box-tools">
                    <div class="input-group input-group-sm" style="width: 150px;">
                        <select class="form-control" id="status-filter">
                            <option value="">All Statuses</option>
                            <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="in_review" <?php echo (isset($_GET['status']) && $_GET['status'] == 'in_review') ? 'selected' : ''; ?>>In Review</option>
                            <option value="approved" <?php echo (isset($_GET['status']) && $_GET['status'] == 'approved') ? 'selected' : ''; ?>>Approved</option>
                            <option value="declined" <?php echo (isset($_GET['status']) && $_GET['status'] == 'declined') ? 'selected' : ''; ?>>Declined</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>User</th>
                            <th>Phone</th>
                            <th>Type</th>
                            <th>Fee</th>
                            <th>Status</th>
                            <th>Date Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="requests-table">
                        <?php
                        $ctrl = new NINModificationController();
                        echo $ctrl->displayModificationRequests();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<div class="modal fade" id="review-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Review NIN Modification Request</h4>
            </div>
            <div class="modal-body">
                <form id="review-form">
                    <input type="hidden" id="review-ref" name="ref" value="">
                    <div class="form-group">
                        <label for="review-status">Status</label>
                        <select class="form-control" id="review-status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="in_review">In Review</option>
                            <option value="approved">Approved</option>
                            <option value="declined">Declined</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="admin-notes">Admin Notes</label>
                        <textarea class="form-control" id="admin-notes" name="admin_notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submit-review">Submit Review</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#status-filter').change(function() {
            var status = $(this).val();
            window.location.href = 'ni-modifications' + (status ? '?status=' + status : '');
        });
        
        $(document).on('click', '.view-request', function(e) {
            e.preventDefault();
            var ref = $(this).data('ref');
            window.location.href = 'ni-modifications?view=' + ref;
        });
        
        $(document).on('click', '.review-request', function(e) {
            e.preventDefault();
            var ref = $(this).data('ref');
            $('#review-ref').val(ref);
            $('#review-modal').modal('show');
        });
        
        $('#submit-review').click(function() {
            var ref = $('#review-ref').val();
            var status = $('#review-status').val();
            var adminNotes = $('#admin-notes').val();
            if (!status) { alert('Please select a status'); return; }
            window.location.href = 'ni-modifications?update-status=1&ref=' + ref + '&status=' + status + '&admin_notes=' + encodeURIComponent(adminNotes);
        });
    });
</script>
