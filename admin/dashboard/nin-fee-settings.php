<?php
    $settings = $data["settings"] ?? (is_object($data) ? $data : (object)[]);
    $slips = $data["slips"] ?? (is_array($data) ? $data : []);
    $currentUrl = $url ?? "nin-fee-settings";
?>

<div class="row">
<div class="col-12">

    <!-- Nav Tabs -->
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab_fees" data-toggle="tab"><i class="fa fa-money"></i> Modification & Verification Fees</a></li>
            <li><a href="#tab_slips" data-toggle="tab"><i class="fa fa-tags"></i> NIN Slip Pricing</a></li>
            <li class="pull-right">
                <a class="btn btn-info btn-rounded text-white" href="nin-modifications" style="margin-top: 5px; margin-right: 10px; padding: 4px 12px;">
                    <i class="fa fa-id-card" aria-hidden="true"></i> Back to NIN Advanced Services
                </a>
            </li>
        </ul>
        <div class="tab-content" style="padding: 20px; background: #fff;">

            <!-- Tab 1: Modification & Verification Fees -->
            <div class="tab-pane active" id="tab_fees">
                <form method="post" action="<?php echo $currentUrl; ?>#tab_fees" class="form-submit">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="box-title text-primary"><i class="fa fa-pencil-square-o"></i> Individual Modification Fees</h4>
                            <hr>
                            <div class="form-group">
                                <label class="control-label">Name Modification (₦)</label>
                                <input type="number" name="fee_name_mod" value="<?php echo $settings->fee_name_mod ?? 5000; ?>" class="form-control" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Phone Modification (₦)</label>
                                <input type="number" name="fee_phone_mod" value="<?php echo $settings->fee_phone_mod ?? 5000; ?>" class="form-control" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Address Modification (₦)</label>
                                <input type="number" name="fee_address_mod" value="<?php echo $settings->fee_address_mod ?? 4000; ?>" class="form-control" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Email Modification (₦)</label>
                                <input type="number" name="fee_email_mod" value="<?php echo $settings->fee_email_mod ?? 4000; ?>" class="form-control" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Date of Birth Modification (₦)</label>
                                <input type="number" name="fee_dob_mod" value="<?php echo $settings->fee_dob_mod ?? 28574; ?>" class="form-control" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">LGA Modification (₦)</label>
                                <input type="number" name="fee_lga_mod" value="<?php echo $settings->fee_lga_mod ?? 3000; ?>" class="form-control" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Gender Modification (₦)</label>
                                <input type="number" name="fee_gender_mod" value="<?php echo $settings->fee_gender_mod ?? 8000; ?>" class="form-control" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Marital Status Modification (₦)</label>
                                <input type="number" name="fee_marital_mod" value="<?php echo $settings->fee_marital_mod ?? 6000; ?>" class="form-control" step="0.01" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h4 class="box-title text-success"><i class="fa fa-check-circle"></i> Verification & Validation Fees</h4>
                            <hr>
                            <div class="form-group">
                                <label class="control-label">Single NIN Verification Fee (₦)</label>
                                <input type="number" name="fee_nin_verification" value="<?php echo $settings->fee_nin_verification ?? 1000; ?>" class="form-control" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Bulk NIN Validation Fee (₦/record)</label>
                                <input type="number" name="fee_bulk_validation" value="<?php echo $settings->fee_bulk_validation ?? 500; ?>" class="form-control" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">IPE Clearance Fee (₦/tracking ID)</label>
                                <input type="number" name="fee_ipe_clearance" value="<?php echo $settings->fee_ipe_clearance ?? 1000; ?>" class="form-control" step="0.01" required>
                            </div>

                            <h4 class="box-title text-info mt-4"><i class="fa fa-users"></i> Bulk Modification Fees</h4>
                            <hr>
                            <div class="form-group">
                                <label class="control-label">Bulk NIN Modification Fee (₦)</label>
                                <input type="number" name="fee_bulk_nin_modification" value="<?php echo $settings->fee_bulk_nin_modification ?? 5000; ?>" class="form-control" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Bulk DOB Modification Fee (₦)</label>
                                <input type="number" name="fee_bulk_dob_modification" value="<?php echo $settings->fee_bulk_dob_modification ?? 5000; ?>" class="form-control" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Bulk Phone Modification Fee (₦)</label>
                                <input type="number" name="fee_bulk_phone_modification" value="<?php echo $settings->fee_bulk_phone_modification ?? 5000; ?>" class="form-control" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Bulk Address Modification Fee (₦)</label>
                                <input type="number" name="fee_bulk_address_modification" value="<?php echo $settings->fee_bulk_address_modification ?? 5000; ?>" class="form-control" step="0.01" required>
                            </div>

                            <h4 class="box-title text-warning mt-4"><i class="fa fa-file-text-o"></i> Document Supporting Services</h4>
                            <hr>
                            <div class="form-group">
                                <label class="control-label">Court Affidavit (₦)</label>
                                <input type="number" name="fee_affidavit" value="<?php echo $settings->fee_affidavit ?? 5000; ?>" class="form-control" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Birth Certificate / Attestation (₦)</label>
                                <input type="number" name="fee_birth_certificate" value="<?php echo $settings->fee_birth_certificate ?? 10000; ?>" class="form-control" step="0.01" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <button type="submit" name="update-nin-fee-settings" class="btn btn-success btn-submit">
                            <i class="fa fa-save"></i> Save Fee Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tab 2: NIN Slip Pricing -->
            <div class="tab-pane" id="tab_slips">
                <form method="post" action="<?php echo $currentUrl; ?>#tab_slips" class="form-submit">
                    <h4 class="box-title text-info"><i class="fa fa-tags"></i> Existing NIN Slip Types & Pricing</h4>
                    <p class="text-muted">Configure buying cost and selling prices for NIN slip types (Regular, Standard, Premium, etc.).</p>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Slip Name</th>
                                    <th>Buying Cost (₦)</th>
                                    <th>User Selling Price (₦)</th>
                                    <th style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $cnt = 1; if(isset($slips) && is_array($slips) && count($slips) > 0): foreach($slips as $row): ?>
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
                                    <td class="text-center">
                                        <a href="<?php echo $currentUrl; ?>?delete=<?php echo $row->id; ?>#tab_slips" class="btn btn-danger btn-sm" onclick="return confirm('Delete this slip pricing?')">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr><td colspan="5" class="text-center">No slip pricing configured yet. Add one below.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group mt-2">
                        <button type="submit" name="update-nin-slip-pricing" class="btn btn-success btn-submit">
                            <i class="fa fa-save"></i> Save Slip Pricing Changes
                        </button>
                    </div>
                </form>

                <hr style="margin: 30px 0;">

                <!-- Add New Slip Type in its own form to avoid conflict -->
                <div class="box box-solid box-default" style="border: 1px solid #e1e6ef; border-radius: 4px;">
                    <div class="box-header with-border" style="background: #f8f9fa;">
                        <h5 class="box-title font-size-15"><i class="fa fa-plus-circle text-success"></i> Add New NIN Slip Type</h5>
                    </div>
                    <div class="box-body" style="padding: 15px;">
                        <form method="post" action="<?php echo $currentUrl; ?>#tab_slips" class="form-submit2">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Slip Name</label>
                                        <input type="text" name="new_slip_name" class="form-control" placeholder="e.g. Plastic Slip" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Buying Cost (₦)</label>
                                        <input type="number" name="new_buying_price" class="form-control" step="0.01" placeholder="e.g. 500" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>User Selling Price (₦)</label>
                                        <input type="number" name="new_user_price" class="form-control" step="0.01" placeholder="e.g. 1000" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" name="add-nin-slip" class="btn btn-success btn-block btn-submit2">
                                            <i class="fa fa-plus"></i> Add Slip
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>
</div>

<script>
    $(document).ready(function() {
        // Keep active tab on reload or hash
        var hash = window.location.hash;
        if (hash) {
            $('.nav-tabs a[href="' + hash + '"]').tab('show');
        }
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            if (history.replaceState) {
                history.replaceState(null, null, e.target.hash);
            } else {
                window.location.hash = e.target.hash;
            }
        });
    });
</script>
