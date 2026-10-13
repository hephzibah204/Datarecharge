<?php
    $jobCtrl = new JobRequestController();

    // Handle pick up action
    if (isset($_GET['pickup-job']) && isset($_GET['source']) && isset($_GET['id'])) {
        $pickRes = $jobCtrl->pickUpJob($_GET['source'], (int)$_GET['id'], $_GET['ref'] ?? '');
        echo '<div class="alert alert-' . ($pickRes['status'] === 'success' ? 'success' : 'danger') . '">' . $pickRes['msg'] . '</div>';
    }

    // Handle review submission
    if (isset($_POST['submit-job-review'])) {
        $reviewRes = $jobCtrl->completeJobReview($_POST, $_FILES);
        echo '<div class="alert alert-' . ($reviewRes['status'] === 'success' ? 'success' : 'danger') . '">' . $reviewRes['msg'] . '</div>';
    }

    $activeCategory = $_GET['category'] ?? 'all';
    $activeStatus = $_GET['status'] ?? 'all';
    $counts = $jobCtrl->getJobCounts();
    $jobs = $jobCtrl->getJobRequests($activeCategory, $activeStatus);
?>

<div class="row">
    <div class="col-12">

        <!-- Header Summary Cards -->
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                <div class="box box-body border-left-warning" style="border-left: 4px solid #f39c12;">
                    <h6 class="text-uppercase text-muted">All Pending Jobs</h6>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="font-size-28 font-weight-bold text-warning"><?php echo $counts['total_pending']; ?></span>
                        <i class="fa fa-clock-o font-size-36 text-warning opacity-60"></i>
                    </div>
                    <small class="text-muted">Awaiting Admin Pick Up</small>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="box box-body border-left-info" style="border-left: 4px solid #00c0ef;">
                    <h6 class="text-uppercase text-muted">NIN Modifications & Affidavits</h6>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="font-size-28 font-weight-bold text-info"><?php echo $counts['nin_pending']; ?></span>
                        <i class="fa fa-id-card font-size-36 text-info opacity-60"></i>
                    </div>
                    <small class="text-muted">Pending User Requests</small>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="box box-body border-left-primary" style="border-left: 4px solid #3c8dbc;">
                    <h6 class="text-uppercase text-muted">CAC Registrations</h6>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="font-size-28 font-weight-bold text-primary"><?php echo $counts['cac_pending']; ?></span>
                        <i class="fa fa-briefcase font-size-36 text-primary opacity-60"></i>
                    </div>
                    <small class="text-muted">Pending Business Name / Ltd</small>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="box box-body border-left-success" style="border-left: 4px solid #00a65a;">
                    <h6 class="text-uppercase text-muted">In Review / Processing</h6>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="font-size-28 font-weight-bold text-success"><?php echo $counts['in_review']; ?></span>
                        <i class="fa fa-cogs font-size-36 text-success opacity-60"></i>
                    </div>
                    <small class="text-muted">Currently Handled by Admins</small>
                </div>
            </div>
        </div>

        <!-- Main Job Requests Queue -->
        <div class="box">
            <div class="box-header with-border d-flex align-items-center justify-content-between flex-wrap">
                <div>
                    <h3 class="box-title"><i class="fa fa-tasks text-primary"></i> User Job Requests & Picking Queue</h3>
                    <p class="text-muted mb-0 font-size-12">Claim, inspect, process, and complete manual user service submissions in real-time.</p>
                </div>

                <!-- Quick Filter Tabs -->
                <div class="d-flex align-items-center flex-wrap mt-2 mt-md-0" style="gap: 8px;">
                    <a href="job-requests?category=all&status=<?php echo urlencode($activeStatus); ?>" class="btn btn-sm <?php echo $activeCategory === 'all' ? 'btn-primary' : 'btn-default'; ?>">
                        All (<?php echo count($jobs); ?>)
                    </a>
                    <a href="job-requests?category=nin&status=<?php echo urlencode($activeStatus); ?>" class="btn btn-sm <?php echo $activeCategory === 'nin' ? 'btn-info' : 'btn-default'; ?>">
                        <i class="fa fa-id-card"></i> NIN Modifications
                    </a>
                    <a href="job-requests?category=cac&status=<?php echo urlencode($activeStatus); ?>" class="btn btn-sm <?php echo $activeCategory === 'cac' ? 'btn-primary' : 'btn-default'; ?>">
                        <i class="fa fa-briefcase"></i> CAC Reg
                    </a>
                    <a href="job-requests?category=affidavit&status=<?php echo urlencode($activeStatus); ?>" class="btn btn-sm <?php echo $activeCategory === 'affidavit' ? 'btn-warning' : 'btn-default'; ?>">
                        <i class="fa fa-file-text"></i> Affidavits
                    </a>
                    <a href="job-requests?category=ipe&status=<?php echo urlencode($activeStatus); ?>" class="btn btn-sm <?php echo $activeCategory === 'ipe' ? 'btn-success' : 'btn-default'; ?>">
                        <i class="fa fa-check-square"></i> IPE Clearance
                    </a>
                </div>
            </div>

            <!-- Filters Bar -->
            <div class="box-body border-bottom bg-light py-2">
                <form method="get" class="form-inline d-flex align-items-center justify-content-between flex-wrap" style="gap: 10px;">
                    <input type="hidden" name="url" value="job-requests">
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($activeCategory); ?>">

                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <label class="font-weight-bold">Filter Status:</label>
                        <select name="status" class="form-control input-sm" onchange="this.form.submit()">
                            <option value="all" <?php echo $activeStatus === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                            <option value="pending" <?php echo $activeStatus === 'pending' ? 'selected' : ''; ?>>Pending (Unassigned)</option>
                            <option value="in_review" <?php echo $activeStatus === 'in_review' ? 'selected' : ''; ?>>In Review / Processing</option>
                            <option value="approved" <?php echo $activeStatus === 'approved' ? 'selected' : ''; ?>>Approved / Completed</option>
                            <option value="declined" <?php echo $activeStatus === 'declined' ? 'selected' : ''; ?>>Declined / Rejected</option>
                        </select>
                    </div>

                    <div>
                        <a href="job-requests" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i> Reset Filters</a>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="box-body table-responsive p-0">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr class="bg-light">
                            <th style="width: 130px;">Reference</th>
                            <th style="width: 140px;">Category</th>
                            <th>Service Title & Details</th>
                            <th>Customer Info</th>
                            <th>Fee</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th style="width: 220px;" class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($jobs)): foreach($jobs as $job): ?>
                        <tr>
                            <td>
                                <strong class="text-primary font-monospace"><?php echo htmlspecialchars($job['ref']); ?></strong>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo $job['category_badge']; ?>">
                                    <?php echo htmlspecialchars($job['category']); ?>
                                </span>
                            </td>
                            <td>
                                <strong class="font-size-14"><?php echo htmlspecialchars($job['service_title']); ?></strong>
                                <div class="text-muted font-size-12 mt-1">
                                    <?php echo htmlspecialchars(substr($job['details'], 0, 90)); ?><?php echo strlen($job['details']) > 90 ? '...' : ''; ?>
                                </div>
                            </td>
                            <td>
                                <div><strong><?php echo htmlspecialchars($job['customer_name']); ?></strong></div>
                                <small class="text-muted"><i class="fa fa-phone"></i> <?php echo htmlspecialchars($job['customer_phone']); ?></small>
                                <?php if(!empty($job['customer_email'])): ?>
                                    <br><small class="text-muted"><i class="fa fa-envelope"></i> <?php echo htmlspecialchars($job['customer_email']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($job['fee'] > 0): ?>
                                    <strong class="text-success">N<?php echo number_format($job['fee'], 2); ?></strong>
                                <?php else: ?>
                                    <span class="text-muted">&mdash;</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="label label-<?php echo $jobCtrl->getStatusBadgeClass($job['status']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($job['status'])); ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted"><?php echo htmlspecialchars(date('d M Y, h:i A', strtotime($job['date_created'] ?: 'now'))); ?></small>
                            </td>
                            <td class="text-right">
                                <?php if($job['status'] === 'pending'): ?>
                                    <a href="job-requests?pickup-job=1&source=<?php echo $job['source']; ?>&id=<?php echo $job['id']; ?>&ref=<?php echo urlencode($job['ref']); ?>&category=<?php echo urlencode($activeCategory); ?>" 
                                       class="btn btn-warning btn-xs" 
                                       onclick="return confirm('Pick up and claim this job request (<?php echo htmlspecialchars($job['ref']); ?>)? Status will change to In Review.');">
                                        <i class="fa fa-hand-paper-o"></i> <strong>Pick Job</strong>
                                    </a>
                                <?php endif; ?>

                                <a href="<?php echo $job['view_url']; ?>" class="btn btn-info btn-xs" title="View Full Details">
                                    <i class="fa fa-eye"></i> Details
                                </a>

                                <button type="button" class="btn btn-primary btn-xs open-review-modal" 
                                        data-source="<?php echo $job['source']; ?>"
                                        data-id="<?php echo $job['id']; ?>"
                                        data-ref="<?php echo htmlspecialchars($job['ref']); ?>"
                                        data-title="<?php echo htmlspecialchars($job['service_title']); ?>"
                                        data-customer="<?php echo htmlspecialchars($job['customer_name']); ?>"
                                        data-email="<?php echo htmlspecialchars($job['customer_email']); ?>"
                                        data-status="<?php echo htmlspecialchars($job['status']); ?>"
                                        data-notes="<?php echo htmlspecialchars($job['admin_notes']); ?>"
                                        data-docs='<?php echo htmlspecialchars($job['documents']); ?>'>
                                    <i class="fa fa-check-circle"></i> Review
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fa fa-inbox font-size-40 d-block mb-2 opacity-50"></i>
                                <strong>No job requests found for the selected category/status filter.</strong>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>

<!-- Unified Job Review & Fulfillment Modal -->
<div class="modal fade" id="jobReviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="submit-job-review" value="1">
                <input type="hidden" name="source" id="m_source">
                <input type="hidden" name="id" id="m_id">
                <input type="hidden" name="ref" id="m_ref">
                <input type="hidden" name="email" id="m_email">

                <div class="modal-header bg-primary text-white">
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-gavel"></i> Review Job Request: <span id="m_title_display"></span></h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Job Reference:</label>
                                <input type="text" id="m_ref_display" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Customer:</label>
                                <input type="text" id="m_customer_display" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <div id="m_docs_container" style="display:none; margin-bottom: 15px;">
                        <label>Uploaded Customer Documents:</label>
                        <div id="m_docs_list" class="p-2 bg-light border rounded"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Update Status <span class="text-danger">*</span></label>
                                <select name="status" id="m_status_select" class="form-control" required>
                                    <option value="in_review">In Review (Claimed & Processing)</option>
                                    <option value="approved">Approved / Completed</option>
                                    <option value="declined">Declined / Rejected</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Upload Completed Document / Certificate / Report</label>
                                <input type="file" name="report_file" class="form-control" accept=".pdf,image/*">
                                <small class="text-muted">Attach PDF or image to complete user's request (e.g. Sworn Affidavit, CAC doc, NIN slip).</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Admin Notes / Reason to User:</label>
                        <textarea name="admin_notes" id="m_notes" class="form-control" rows="3" placeholder="Enter comments, tracking number, or reason for approval/rejection (sent to customer email if applicable)..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save & Update Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.open-review-modal').click(function() {
        var source = $(this).data('source');
        var id = $(this).data('id');
        var ref = $(this).data('ref');
        var title = $(this).data('title');
        var customer = $(this).data('customer');
        var email = $(this).data('email');
        var status = $(this).data('status');
        var notes = $(this).data('notes');
        var docs = $(this).data('docs');

        $('#m_source').val(source);
        $('#m_id').val(id);
        $('#m_ref').val(ref);
        $('#m_email').val(email);
        $('#m_ref_display').val(ref);
        $('#m_customer_display').val(customer + (email ? ' (' + email + ')' : ''));
        $('#m_title_display').text(ref + ' - ' + title);
        $('#m_notes').val(notes);

        if (status === 'approved' || status === 'completed') {
            $('#m_status_select').val('approved');
        } else if (status === 'declined' || status === 'rejected') {
            $('#m_status_select').val('declined');
        } else {
            $('#m_status_select').val('in_review');
        }

        // Parse uploaded documents
        var docsContainer = $('#m_docs_container');
        var docsList = $('#m_docs_list');
        docsList.empty();

        if (docs && docs.length > 0) {
            try {
                var docArray = typeof docs === 'string' ? JSON.parse(docs) : docs;
                if (Array.isArray(docArray) && docArray.length > 0) {
                    docsContainer.show();
                    docArray.forEach(function(d) {
                        var dType = d.type || 'Document';
                        var dPath = d.path || '#';
                        docsList.append('<div style="margin-bottom: 5px;"><strong>' + dType + ':</strong> <a href="' + dPath + '" target="_blank" class="btn btn-xs btn-info ml-2"><i class="fa fa-external-link"></i> View / Download</a></div>');
                    });
                } else {
                    docsContainer.hide();
                }
            } catch (e) {
                docsContainer.hide();
            }
        } else {
            docsContainer.hide();
        }

        $('#jobReviewModal').modal('show');
    });
});
</script>
