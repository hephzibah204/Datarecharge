<div class="page-content header-clear-medium">
    <div class="card card-style">
        <div class="content mb-0">
            <p class="mb-0 text-center font-600 color-highlight">NIN Services</p>
            <h1 class="text-center">NIN New Enrollment</h1>
            <p class="text-center mb-3">Submit details for new NIN enrollment</p>
        </div>
    </div>
    <div class="card card-style">
        <div class="content">
            <form method="post" enctype="multipart/form-data">
                <fieldset>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Surname</label>
                        <input type="text" name="surname" placeholder="Surname" class="round-small" required>
                    </div>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">First Name</label>
                        <input type="text" name="first_name" placeholder="First Name" class="round-small" required>
                    </div>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Middle Name</label>
                        <input type="text" name="middle_name" placeholder="Middle Name" class="round-small">
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Date of Birth</label>
                                <input type="date" name="dob" class="round-small" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Sex</label>
                                <select name="sex" class="form-control" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Phone Number</label>
                        <input type="tel" name="phone" placeholder="Phone Number" class="round-small" required>
                    </div>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">State of Origin</label>
                        <input type="text" name="state_origin" placeholder="State of Origin" class="round-small" required>
                    </div>
                    <div class="mb-4">
                        <label class="color-theme opacity-80 font-700 font-12 d-block mb-1">Upload Passport Photograph</label>
                        <input type="file" name="passport_photo" class="form-control" accept="image/*" required>
                    </div>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Amount</label>
                        <input type="text" value="₦10,000" class="round-small" readonly>
                    </div>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Transaction PIN</label>
                        <input type="number" name="transkey" placeholder="Enter Transaction PIN" class="round-small" required>
                    </div>
                    <input name="transref" type="hidden" value="<?php echo $transRef; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    <input name="modification_type_code" type="hidden" value="new_enrollment">
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
