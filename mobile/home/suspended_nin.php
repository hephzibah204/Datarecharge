<div class="page-content header-clear-medium">
    <div class="card card-style">
        <div class="content mb-0">
            <p class="mb-0 text-center font-600 color-highlight">NIN Services</p>
            <h1 class="text-center">Suspended NIN</h1>
            <p class="text-center mb-3">Submit request to reactivate/restore suspended NIN</p>
        </div>
    </div>
    <div class="card card-style">
        <div class="content">
            <form method="post" enctype="multipart/form-data">
                <fieldset>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">NIN</label>
                        <input type="number" name="nin" placeholder="Enter 11-digit NIN" class="round-small" required>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Surname</label>
                                <input type="text" name="surname" placeholder="Surname" class="round-small" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">First Name</label>
                                <input type="text" name="first_name" placeholder="First Name" class="round-small" required>
                            </div>
                        </div>
                    </div>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Reason for Suspension (If known)</label>
                        <textarea name="reason" placeholder="Explain the issue with your NIN" class="round-small" rows="3" required></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="color-theme opacity-80 font-700 font-12 d-block mb-1">Upload NIN Slip or ID Card</label>
                        <input type="file" name="nin_slip" class="form-control" accept="image/*,application/pdf" required>
                    </div>
                    <div class="mb-4">
                        <label class="color-theme opacity-80 font-700 font-12 d-block mb-1">Upload Passport Photo</label>
                        <input type="file" name="passport_photo" class="form-control" accept="image/*" required>
                    </div>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Amount</label>
                        <input type="text" value="₦15,000" class="round-small" readonly>
                    </div>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Transaction PIN</label>
                        <input type="number" name="transkey" placeholder="Enter Transaction PIN" class="round-small" required>
                    </div>
                    <input name="transref" type="hidden" value="<?php echo $transRef; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    <input name="modification_type_code" type="hidden" value="suspended_nin">
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
