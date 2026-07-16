    <!-- Page content start here-->
        <div class="page-content header-clear-medium">

    <div class="card card-style">
        <div class="content">

            <div class="text-center">
                <img src="../../assets/images/icons/user-smile.png" style="border-radius:5rem; width:80px; height:80px;">
                <h1 class="mb-0 pb-0 font-20"><?php echo $profileDetails->sFname . " (".$controller->formatUserType($profileDetails->sType).")"; ?></h1>

            </div>


            <div class="accordion" id="accordion-1">

                <div class="card  shadow-0 bg-gray-light mb-1">
                    <button class="btn accordion-btn no-effect color-theme " data-bs-toggle="collapse" data-bs-target="#collapse1">
                        <i class="fa fa-user  me-2 "></i>
                        <span class="font-15">Personal Information</span>
                        <i class="fa fa-chevron-down font-10 accordion-icon "></i>
                    </button>
                    <div id="collapse1" class="collapse" data-bs-parent="#accordion-1">
                        <div class="pt-1 pb-2 ps-3 pe-3 bg-white border">
                            <div class="list-group list-custom-small">
                                <a href="#">
                                    <i class="fa font-14 fa-user rounded-xl shadow-xl color-highlight"></i>
                                    <span><b>Name: </b> <?php echo $data->sFname. " " . $data->sLname; ?></span>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                                <a href="#">
                                    <i class="fa font-14 fa-envelope rounded-xl shadow-xl color-highlight"></i>
                                    <span><b>Email: </b> <?php echo $data->sEmail; ?></span>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                                <a href="#">
                                    <i class="fa font-14 fa-phone rounded-xl shadow-xl color-highlight"></i>
                                    <span><b>Phone: </b> <?php echo $data->sPhone; ?></span>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                                <a href="#">
                                    <i class="fa font-14 fa-globe rounded-xl shadow-xl color-highlight"></i>
                                    <span><b>State: </b> <?php echo $data->sState; ?></span>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                                <a href="#">
                                    <i class="fa font-14 fa-list-alt rounded-xl shadow-xl color-highlight"></i>
                                    <span><b>Airtime Limit: </b> ₦1,000</span>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                                <a href="#">
                                    <i class="fa font-14 fa-receipt rounded-xl shadow-xl color-highlight"></i>
                                    <span><b>Account Limit: </b> ₦<?php echo $data->sAccountLimit; ?></span>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                                <a href="#">
                                    <i class="fa font-14 fa-certificate rounded-xl shadow-xl color-highlight"></i>
                                    <span><b>KYC Status: </b> No</span>
                                    <i class="fa fa-angle-right"></i>
                                </a>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="card  shadow-0 bg-gray-light mb-1">
                    <a class="btn accordion-btn no-effect color-theme " href="referrals">
                        <i class="fa fa-users  me-2 "></i>
                        <span class="font-15">Referral Link</span>
                        <i class="fa fa-chevron-down font-10 accordion-icon "></i>
                    </a>
                </div>

                <div class="card  shadow-0 bg-gray-light mb-1">
                    <button class="btn accordion-btn no-effect color-theme " data-bs-toggle="collapse" data-bs-target="#collapse2">
                        <i class="fa fa-lock  me-2 "></i>
                        <span class="font-15">Update Password</span>
                        <i class="fa fa-chevron-down font-10 accordion-icon "></i>
                    </button>
                    <div id="collapse2" class="collapse" data-bs-parent="#accordion-1">
                        <div class="pt-1 pb-2 ps-3 pe-3 bg-white border">
                            <form id="passForm" method="post">
                                <div class="mt-4 mb-4">

                                    <div class="input-style has-borders no-icon input-style-always-active mb-4">
                                        <input type="password" class="form-control" id="old-pass" name="oldpass" placeholder="Old Password" required>
                                        <label for="old-pass" class="color-highlight">Old Password</label>
                                        <em>(required)</em>
                                    </div>
                                    <div class="input-style has-borders no-icon input-style-always-active  mb-4">
                                        <input type="password" class="form-control" id="new-pass" name="newpass" placeholder="New Password" required>
                                        <label for="new-pass" class="color-highlight">New Password</label>
                                        <em>(required)</em>
                                    </div>

                                    <div class="input-style has-borders no-icon input-style-always-active mb-4">
                                        <input type="password" class="form-control" id="retype-pass" placeholder="Retype Password" required>
                                        <label for="retype-pass" class="color-highlight">Retype Password</label>
                                        <em>(required)</em>
                                    </div>
                                </div>
                                <button type="submit" id="update-pass-btn" class="btn btn-info btn-lg btn-block" style="background-color:<?php echo $sitecolor; ?>;">
                                    Update Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

              <div class="card  shadow-0 bg-gray-light mb-1">
                    <button class="btn accordion-btn no-effect color-theme " data-bs-toggle="collapse" data-bs-target="#collapse3">
                        <i class="fa fa-lock  me-2 "></i>
                        <span class="font-15">Update Account Pin</span>
                        <i class="fa fa-chevron-down font-10 accordion-icon "></i>
                    </button>
                    <div id="collapse3" class="collapse" data-bs-parent="#accordion-1">
                        <div class="pt-1 pb-2 ps-3 pe-3 bg-white border">
                            <form id="pinaccessCodeForm" method="post" class="my-4">
                                <p class="font-600 text-dark">
                                    <b>Note: </b> You You Need An Access Code To Change Your PIN. Please Use The Button Below.
                                    <a type="submit" onclick="$('#pinaccessCodeForm').submit();" id="access-code" class="text-danger">Click Here To Get Access Code</a>
                                </p>
                            </form>

                            <form id="pinForm" method="post" class="mt-4 mb-4">
                                <div>
                                    <div class="input-style has-borders no-icon input-style-always-active mb-4">
                                        <input type="number" class="form-control" id="access-key" name="vcode" placeholder="Access Code" required>
                                        <label for="access-code" class="color-highlight">Access Code</label>
                                        <em>(required)</em>
                                    </div>
                                    <div class="input-style has-borders no-icon input-style-always-active  mb-4">
                                        <input type="number" class="form-control" id="new-pin" name="newpin" placeholder="New Pin" required>
                                        <label for="new-pin" class="color-highlight">New Pin</label>
                                        <em>(required)</em>
                                    </div>

                                    <div class="input-style has-borders no-icon input-style-always-active mb-4">
                                        <input type="number" class="form-control" id="retype-pin" placeholder="Retype Pin" required>
                                        <label for="retype-pin" class="color-highlight">Retype Pin</label>
                                        <em>(required)</em>
                                    </div>
                                </div>
                                <button type="submit" id="update-pin-btn" class="btn btn-info btn-lg btn-block" style="background-color:<?php echo $sitecolor; ?>;">
                                    Update Pin
                                </button>
                            </form>


                        </div>
                    </div>
                </div>
                <!--div class="card  shadow-0 bg-gray-light mb-1">
                    <button class="btn accordion-btn no-effect color-theme " data-bs-toggle="collapse" data-bs-target="#collapse3">
                        <i class="fa fa-lock  me-2 "></i>
                        <span class="font-15">Update Account Pin</span>
                        <i class="fa fa-chevron-down font-10 accordion-icon "></i>
                    </button>
                    <!--div id="collapse3" class="collapse" data-bs-parent="#accordion-1">
                        <div class="pt-1 pb-2 ps-3 pe-3 bg-white border">
                            <form id="pinaccessCodeForm" method="post" class="my-4">
                                <p class="font-600 text-dark">
                                    <b>Note: </b> You You Need An Access Code To Change Your PIN. Please Use The Button Below.
                                    <a type="submit" onclick="$('#pinaccessCodeForm').submit();" id="access-code" class="text-danger">Click Here To Get Access Code</a>
                                </p>
                            </form>

                            <form id="pinForm" method="post" class="mt-4 mb-4">
                                <div>
                                    <div class="input-style has-borders no-icon input-style-always-active mb-4">
                                        <input type="number" class="form-control" id="access-key" name="vcode" placeholder="Access Code" required>
                                        <label for="access-code" class="color-highlight">Access Code</label>
                                        <em>(required)</em>
                                    </div>
                                    <div class="input-style has-borders no-icon input-style-always-active  mb-4">
                                        <input type="number" class="form-control" id="new-pin" name="newpin" placeholder="New Pin" required>
                                        <label for="new-pin" class="color-highlight">New Pin</label>
                                        <em>(required)</em>
                                    </div>

                                    <div class="input-style has-borders no-icon input-style-always-active mb-4">
                                        <input type="number" class="form-control" id="retype-pin" placeholder="Retype Pin" required>
                                        <label for="retype-pin" class="color-highlight">Retype Pin</label>
                                        <em>(required)</em>
                                    </div>
                                </div>
                                <button type="submit" id="update-pin-btn" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                    Update Pin
                                </button>
                            </form>


                        </div>
                    </div-->
                </div>

                <div class="card  shadow-0 bg-gray-light mb-1">
                    <button class="btn accordion-btn no-effect color-theme " data-bs-toggle="collapse" data-bs-target="#collapse4">
                        <i class="fa fa-lock  me-2 "></i>
                        <span class="font-15">Disable Pin</span>
                        <i class="fa fa-chevron-down font-10 accordion-icon "></i>
                    </button>
                    <div id="collapse4" class="collapse" data-bs-parent="#accordion-1">
                        <div class="pt-1 pb-2 ps-3 pe-3 bg-white border">

                            <form class="the-submit-form mb-4" method="post">
                                <div class="mt-3 mb-3">
                                    <p class="text-danger"><b>Note: </b> Only Disable Pin When You Are Sure About The Security Of Your Phone And Your Account Is Secured With A Strong Password. </p>
                                    <div class="input-style has-borders no-icon input-style-always-active mb-4">
                                        <input type="number" maxlength="4" class="form-control" id="old-pin" name="oldpin" placeholder="Old Pin" required>
                                        <label for="old-pin" class="color-highlight">Old Pin</label>
                                        <em>(required)</em>
                                    </div>
                                    <div class="input-style has-borders no-icon input-style-always-active text-dark   mb-4">
                                        <select name="pinstatus">
                                            <option value="">Change Status</option>
                                                                                            <option value="0" selected>Enable</option>
                                                <option value="1">Disable</option>
                                                                                    </select><label for="new-pin" class="color-highlight">Change Status</label>
                                        <em>(required)</em>
                                    </div>
                                </div>
                                <button type="submit" name="disable-user-pin" class="btn btn-info btn-lg btn-block" style="background-color:<?php echo $sitecolor; ?>;">
                                    Update Pin
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="card  shadow-0 bg-gray-light mb-1">
                    <button class="btn accordion-btn no-effect color-theme " data-bs-toggle="collapse" data-bs-target="#collapse5">
                        <i class="fa fa-code  me-2 "></i>
                        <span class="font-15">API Access</span>
                        <i class="fa fa-chevron-down font-10 accordion-icon "></i>
                    </button>
                    <div id="collapse5" class="collapse" data-bs-parent="#accordion-1">
                        <div class="pt-1 pb-2 ps-3 pe-3 bg-white border">
                        
                            <p class="mb-n1 mt-2 color-highlight font-600 font-12">Developer</p>
                            <h4>Api Documentation</h4>
                                                        <p class="font-600 pb-0 mb-2"><a href="pricing" class="text-danger">Click Here To View All Services & Price</a></p>
                            <div class="list-group list-custom-small">
                                <a href="#">
                                    <input type="text" class="form-control" readonly value="<?php echo $data->sApiKey; ?>" />
                                </a>
                               
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <button class="btn btn-primary btn-sm" style="width:32% !important;" onclick="copyToClipboard('<?php echo $data->sApiKey; ?>')">Copy Key</button>
                                                                            <button class="btn btn-success btn-sm" style="width:32% !important;" onclick="window.location.href='<?php echo $data2->apidocumentation; ?>'">Api Doc</button>
                                                                    </div>
                                

                            </div>
                                                    </div>
                    </div>
                </div>

                <div class="card  shadow-0 bg-gray-light mb-1">
                    <a class="btn accordion-btn no-effect color-theme " href="contact-us">
                        <i class="fa fa-phone  me-2 "></i>
                        <span class="font-15">Contact Us</span>
                        <i class="fa fa-chevron-down font-10 accordion-icon "></i>
                    </a>
                    
                </div>

                <div class="card  shadow-0 bg-gray-light mb-1">
                    <a class="btn accordion-btn no-effect color-theme " href="logout">
                        <i class="fa fa-power-off  me-2 "></i>
                        <span class="font-15">Logout</span>
                        <i class="fa fa-chevron-down font-10 accordion-icon "></i>
                    </a>
                    
                </div>


            </div>


        </div>

    </div>



</div>    <!-- Page content ends here-->
