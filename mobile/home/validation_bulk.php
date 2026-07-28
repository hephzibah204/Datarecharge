<div class="page-content header-clear-medium">
    <div class="card card-style">
        <div class="content mb-0">
            <p class="mb-0 text-center font-600 color-highlight">NIN Services</p>
            <h1 class="text-center">NIN Validation (Bulk)</h1>
            <p class="text-center mb-3">Upload bulk NINs for validation</p>
        </div>
    </div>
    <div class="card card-style">
        <div class="content">
            <form method="post" enctype="multipart/form-data">
                <fieldset>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Batch/Reference Name</label>
                        <input type="text" name="batch_name" placeholder="e.g., Staff Validation July" class="round-small" required>
                    </div>
                    <div class="mb-4">
                        <label class="color-theme opacity-80 font-700 font-12 d-block mb-1">Upload CSV/Excel List (Containing NINs)</label>
                        <input type="file" name="nin_list" class="form-control" accept=".csv,.xlsx,.xls,.txt" required>
                    </div>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Reason/Notes</label>
                        <textarea name="reason" placeholder="Notes or instructions" class="round-small" rows="3" required></textarea>
                    </div>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Amount (Base Fee)</label>
                        <input type="text" value="₦2,000" class="round-small" readonly>
                    </div>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Transaction PIN</label>
                        <input type="number" name="transkey" placeholder="Enter Transaction PIN" class="round-small" required>
                    </div>
                    <input name="transref" type="hidden" value="<?php echo $transRef; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    <input name="modification_type_code" type="hidden" value="validation_bulk">
                    <div class="form-button">
                        <button type="submit" name="submit-nin-modification" class="btn btn-info btn-lg btn-block">
                            Submit Request
                        </button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>
