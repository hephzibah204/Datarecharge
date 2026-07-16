<div class="row">
<div class="col-12">

    <div class="box">
        <div class="box-header with-border d-flex align-items-center justify-content-between">
            <h4 class="box-title">NIN Modification & Verification Fees</h4>
            <a class="btn btn-info btn-rounded text-white" href="ni-modifications">
                <i class="fa fa-id-card" aria-hidden="true"></i> Back to NIN Modifications
            </a>
        </div>
        <div class="box-body">
        <form method="post" class="form-submit">

            <div class="row">
                <div class="col-md-6">
                    <h5 class="box-title">Modification Fees</h5>
                    <hr>
                    <div class="form-group">
                        <label class="control-label">Name Modification (N)</label>
                        <input type="number" name="fee_name_mod" value="<?php echo $data->fee_name_mod ?? 5000; ?>" class="form-control" step="0.01">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Phone Modification (N)</label>
                        <input type="number" name="fee_phone_mod" value="<?php echo $data->fee_phone_mod ?? 5000; ?>" class="form-control" step="0.01">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Address Modification (N)</label>
                        <input type="number" name="fee_address_mod" value="<?php echo $data->fee_address_mod ?? 4000; ?>" class="form-control" step="0.01">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Email Modification (N)</label>
                        <input type="number" name="fee_email_mod" value="<?php echo $data->fee_email_mod ?? 4000; ?>" class="form-control" step="0.01">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Date of Birth Modification (N)</label>
                        <input type="number" name="fee_dob_mod" value="<?php echo $data->fee_dob_mod ?? 28574; ?>" class="form-control" step="0.01">
                    </div>
                    <div class="form-group">
                        <label class="control-label">LGA Modification (N)</label>
                        <input type="number" name="fee_lga_mod" value="<?php echo $data->fee_lga_mod ?? 3000; ?>" class="form-control" step="0.01">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Gender Modification (N)</label>
                        <input type="number" name="fee_gender_mod" value="<?php echo $data->fee_gender_mod ?? 8000; ?>" class="form-control" step="0.01">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Marital Status Modification (N)</label>
                        <input type="number" name="fee_marital_mod" value="<?php echo $data->fee_marital_mod ?? 6000; ?>" class="form-control" step="0.01">
                    </div>
                </div>
                <div class="col-md-6">
                    <h5 class="box-title">Verification Fees</h5>
                    <hr>
                    <div class="form-group">
                        <label class="control-label">NIN Verification Fee (N)</label>
                        <input type="number" name="fee_nin_verification" value="<?php echo $data->fee_nin_verification ?? 1000; ?>" class="form-control" step="0.01">
                    </div>

                    <h5 class="box-title mt-4">Document Services</h5>
                    <hr>
                    <div class="form-group">
                        <label class="control-label">Court Affidavit (N)</label>
                        <input type="number" name="fee_affidavit" value="<?php echo $data->fee_affidavit ?? 5000; ?>" class="form-control" step="0.01">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Birth Certificate / Attestation (N)</label>
                        <input type="number" name="fee_birth_certificate" value="<?php echo $data->fee_birth_certificate ?? 10000; ?>" class="form-control" step="0.01">
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <button type="submit" name="update-nin-fee-settings" class="btn btn-success">
                    <i class="fa fa-save"></i> Save Fee Settings
                </button>
            </div>

        </form>
        </div>
    </div>

</div>
</div>
