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
                <form method="post" class="form-submit">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="box-title text-primary"><i class="fa fa-pencil-square-o"></i> Modification Fees</h4>
                            <hr>
                            <div class="form-group">
                                <label class="control-label">Name Modification (N)</label>
                                <input type="number" name="fee_name_mod" value="<?php echo $settings->fee_name_mod ?? 5000; ?>" class="form-control" step="0.01">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Phone Modification (N)</label>
                                <input type="number" name="fee_phone_mod" value="<?php echo $settings->fee_phone_mod ?? 5000; ?>" class="form-control" step="0.01">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Address Modification (N)</label>
                                <input type="number" name="fee_address_mod" value="<?php echo $settings->fee_address_mod ?? 4000; ?>" class="form-control" step="0.01">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Email Modification (N)</label>
                                <input type="number" name="fee_email_mod" value="<?php echo $settings->fee_email_mod ?? 4000; ?>" class="form-control" step="0.01">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Date of Birth Modification (N)</label>
                                <input type="number" name="fee_dob_mod" value="<?php echo $settings->fee_dob_mod ?? 28574; ?>" class="form-control" step="0.01">
                            </div>
                            <div class="form-group">
                                <label class="control-label">LGA Modification (N)</label>
                                <input type="number" name="fee_lga_mod" value="<?php echo $settings->fee_lga_mod ?? 3000; ?>" class="form-control" step="0.01">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Gender Modification (N)</label>
                                <input type="number" name="fee_gender_mod" value="<?php echo $settings->fee_gender_mod ?? 8000; ?>" class="form-control" step="0.01">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Marital Status Modification (N)</label>
                                <input type="number" name="fee_marital_mod" value="<?php echo $settings->fee_marital_mod ?? 6000; ?>" class="form-control" step="0.01">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h4 class="box-title text-success"><i class="fa fa-check-circle"></i> Verification & Validation Fees</h4>
                            <hr>
                            <div class="form-group">
                                <label class="control-label">NIN Verification Fee (N)</label>
                                <input type="number" name="fee_nin_verification" value="<?php echo $settings->fee_nin_verification ?? 1000; ?>" class="form-control" step="0.01">
                            </div>

                            <h4 class="box-title text-warning mt-4"><i class="fa fa-file-text-o"></i> Document Supporting Services</h4>
                            <hr>
                            <div class="form-group">
                                <label class="control-label">Court Affidavit (N)</label>
                                <input type="number" name="fee_affidavit" value="<?php echo $settings->fee_affidavit ?? 5000; ?>" class="form-control" step="0.01">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Birth Certificate / Attestation (N)</label>
                                <input type="number" name="fee_birth_certificate" value="<?php echo $settings->fee_birth_certificate ?? 10000; ?>" class="form-control" step="0.01">
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
                <form method="post" class="form-submit">
                    <h4 class="box-title text-info"><i class="fa fa-tags"></i> NIN Slip Types & Pricing</h4>
                    <p class="text-muted">Configure buying and selling prices for various NIN slip downloads (Standard, Premium, Plastic, etc.).</p>
                    <hr>
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
                                    <td>
                                        <a href="<?php echo $currentUrl; ?>?delete=<?php echo $row->id; ?>#tab_slips" class="btn btn-danger btn-sm" onclick="return confirm('Delete this slip pricing?')">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr><td colspan="5" class="text-center">No slip pricing configured yet. Add one below.</td></tr>
                                <?php endif; ?>
                                <tr class="bg-light">
                                    <td><strong>New</strong></td>
                                    <td><input type="text" name="new_slip_name" class="form-control" placeholder="New slip name (e.g. Premium Slip)"></td>
                                    <td><input type="number" name="new_buying_price" class="form-control" step="0.01" placeholder="Buying price"></td>
                                    <td><input type="number" name="new_user_price" class="form-control" step="0.01" placeholder="User price"></td>
                                    <td><button type="submit" name="add-nin-slip" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Add</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group mt-2">
                        <button type="submit" name="update-nin-slip-pricing" class="btn btn-success btn-submit">
                            <i class="fa fa-save"></i> Save Slip Pricing Changes
                        </button>
                    </div>
                </form>
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
            window.location.hash = e.target.hash;
        });
    });
</script>
