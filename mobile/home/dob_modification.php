<div class="page-content header-clear-medium">
    <div class="card card-style">
        <div class="content mb-0">
            <p class="mb-0 text-center font-600 color-highlight">NIN Profile Management</p>
            <h1 class="text-center">Date of Birth Modification</h1>
            <p class="text-center mb-3">Request a DOB change & Attestation Letter</p>
        </div>
    </div>
    <div class="card card-style">
        <div class="content">
            <form method="post" enctype="multipart/form-data">
                <fieldset>
                    <h5 class="mb-3 font-700 color-highlight">Applicant Details</h5>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Old Date of Birth</label>
                                <input type="date" name="old_dob" class="round-small" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">New Date of Birth</label>
                                <input type="date" name="new_dob" class="round-small" required>
                            </div>
                        </div>
                    </div>

                    <div class="input-style input-style-always-active has-borders mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">NIN</label>
                        <input type="number" name="nin" placeholder="Enter 11-digit NIN" class="round-small" required>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Surname</label>
                                <input type="text" name="surname" placeholder="Surname" class="round-small" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">First Name</label>
                                <input type="text" name="first_name" placeholder="First Name" class="round-small" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Middle Name</label>
                                <input type="text" name="middle_name" placeholder="Middle Name" class="round-small">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Sex</label>
                                <select name="sex" class="form-control" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Marital Status</label>
                                <select name="marital_status" class="form-control" required>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Divorced">Divorced</option>
                                    <option value="Widowed">Widowed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">State of Origin</label>
                                <input type="text" name="state_origin" placeholder="State of Origin" class="round-small" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Local Government of Origin</label>
                                <input type="text" name="lga_origin" placeholder="LGA of Origin" class="round-small" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Town/Village of Origin</label>
                                <input type="text" name="town_origin" placeholder="Town/Village of Origin" class="round-small" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Mode of Identification</label>
                                <input type="text" name="id_mode" placeholder="e.g., Driver's License" class="round-small" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Valid Identification Number</label>
                                <input type="text" name="id_number" placeholder="ID Number" class="round-small" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Email Address</label>
                                <input type="email" name="email" placeholder="Email Address" class="round-small" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Phone Number</label>
                                <input type="tel" name="phone" placeholder="Phone Number" class="round-small" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Place of Birth</label>
                                <input type="text" name="place_of_birth" placeholder="Place of Birth" class="round-small" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Local Government of Birth</label>
                                <input type="text" name="lga_of_birth" placeholder="LGA of Birth" class="round-small" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">State of Birth</label>
                                <input type="text" name="state_of_birth" placeholder="State of Birth" class="round-small" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Highest Level of Education</label>
                                <input type="text" name="edu_level" placeholder="Highest Education Level" class="round-small" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Occupation</label>
                                <input type="text" name="occupation" placeholder="Occupation" class="round-small" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Registration State</label>
                                <input type="text" name="reg_state" placeholder="State where you registered" class="round-small" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Current Registration Address</label>
                                <input type="text" name="reg_address" placeholder="NIN Registration Address" class="round-small" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Workplace Address</label>
                                <input type="text" name="workplace_address" placeholder="Workplace Address" class="round-small">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Company Address</label>
                                <input type="text" name="company_address" placeholder="Company Address" class="round-small">
                            </div>
                        </div>
                    </div>

                    <div class="input-style input-style-always-active has-borders mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Reason for Request</label>
                        <textarea name="reason" placeholder="Explain why you need this change" class="round-small" rows="3" required></textarea>
                    </div>

                    <!-- Parents' Details -->
                    <h5 class="mt-4 mb-3 font-700 color-highlight">Parents' Details</h5>
                    
                    <h6 class="font-600 mb-2">Father's Information</h6>
                    <div class="row">
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Father's Surname</label>
                                <input type="text" name="father_surname" placeholder="Father's Surname" class="round-small" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Father's First Name</label>
                                <input type="text" name="father_first_name" placeholder="Father's First Name" class="round-small" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Father's State of Origin</label>
                                <input type="text" name="father_state" placeholder="State" class="round-small" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Father's LGA of Origin</label>
                                <input type="text" name="father_lga" placeholder="LGA" class="round-small" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Father's Village/Town</label>
                                <input type="text" name="father_town" placeholder="Town" class="round-small" required>
                            </div>
                        </div>
                    </div>

                    <h6 class="font-600 mb-2 mt-3">Mother's Information</h6>
                    <div class="row">
                        <div class="col-4">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Mother's Surname</label>
                                <input type="text" name="mother_surname" placeholder="Mother's Surname" class="round-small" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Mother's First Name</label>
                                <input type="text" name="mother_first_name" placeholder="First Name" class="round-small" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Mother's Maiden Name</label>
                                <input type="text" name="mother_maiden" placeholder="Maiden Name" class="round-small" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Mother's State of Origin</label>
                                <input type="text" name="mother_state" placeholder="State" class="round-small" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Mother's LGA of Origin</label>
                                <input type="text" name="mother_lga" placeholder="LGA" class="round-small" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label class="color-theme opacity-80 font-700 font-12">Mother's Village/Town</label>
                                <input type="text" name="mother_town" placeholder="Town" class="round-small" required>
                            </div>
                        </div>
                    </div>

                    <!-- Required Uploads -->
                    <h5 class="mt-4 mb-3 font-700 color-highlight">Required Uploads</h5>
                    
                    <div class="mb-4">
                        <label class="color-theme opacity-80 font-700 font-12 d-block mb-1">Good-quality passport photograph</label>
                        <input type="file" name="passport_photo" class="form-control" accept="image/*" required>
                    </div>

                    <div class="mb-4">
                        <label class="color-theme opacity-80 font-700 font-12 d-block mb-1">Affidavit (Upload)</label>
                        <input type="file" name="affidavit" class="form-control" accept="image/*,application/pdf" required>
                    </div>

                    <div class="mb-4">
                        <label class="color-theme opacity-80 font-700 font-12 d-block mb-1">NIN Slip Upload (Supporting Document)</label>
                        <input type="file" name="nin_slip" class="form-control" accept="image/*,application/pdf" required>
                    </div>

                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Amount</label>
                        <input type="text" value="₦28,574" class="round-small" readonly>
                    </div>

                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Transaction PIN</label>
                        <input type="number" name="transkey" placeholder="Enter Transaction PIN" class="round-small" required>
                    </div>

                    <input name="transref" type="hidden" value="<?php echo $transRef; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    <input name="modification_type_code" type="hidden" value="dob">

                    <div class="form-button">
                        <button type="submit" name="submit-nin-modification" class="btn btn-info btn-lg btn-block">Submit Request</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>
