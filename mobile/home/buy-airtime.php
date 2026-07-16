<!-- Page content start here-->
        <div class="page-content header-clear-medium">

    <div class="card card-style">

        <div class="content">
            <p class="mb-0 text-center font-600 color-highlight">Airtime For All Network</p>
            <h1 class="text-center">Buy Airtime</h1>

            <div class="row text-center mb-2">

                <a href="javascript:selectNetworkByIcon('MTN');" class="col-3 mt-2">
                    <span class="icon icon-l rounded-l py-1 px-1" style="background:#f2f2f2;">
                        <img src="../../assets/images/icons/mtn.png" width="40" height="40" class="rounded-l" />
                    </span>
                </a>

                <a href="javascript:selectNetworkByIcon('AIRTEL');" class="col-3 mt-2">
                    <span class="icon icon-l rounded-l py-1 px-1" style="background:#f2f2f2;">
                        <img src="../../assets/images/icons/airtel.png" width="40" height="40" class="rounded-l" />
                    </span>
                </a>

                <a href="javascript:selectNetworkByIcon('GLO');" class="col-3 mt-2">
                    <span class="icon icon-l rounded-l py-1 px-1" style="background:#f2f2f2;">
                        <img src="../../assets/images/icons/glo.png" width="40" height="40" class="rounded-l" />
                    </span>
                </a>

                <a href="javascript:selectNetworkByIcon('9MOBILE');" class="col-3 mt-2">
                    <span class="icon icon-l rounded-l py-1 px-1" style="background:#f2f2f2;">
                        <img src="../../assets/images/icons/9mobile.png" width="40" height="40" class="rounded-l" />
                    </span>
                </a>
                </div>
                <hr/>
                
                <form method="post" class="airtimeForm" id="airtimeForm" action="buy-airtime">
                        <fieldset>
 
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="networkid" class="color-theme opacity-80 font-700 font-12">Network</label>
                                <select id="networkid" name="network">
                                    <option value="" disabled="" selected="">~Select Network~</option>
                                    <?php foreach($data AS $network): if($network->networkStatus == "On"): ?>
                                        <option value="<?php echo $network->nId; ?>" networkname="<?php echo $network->network; ?>" vtu="<?php echo $network->vtuStatus; ?>" sharesell="<?php echo $network->sharesellStatus; ?>" momo="<?php echo $network->momoStatus; ?>"><?php echo $network->network; ?></option>
                                    <?php endif; endforeach; ?>
                                </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>
                            <div data-menu="contact-list-box" class="input-style py-2 ps-2 pe-2 bg-light border rounded-sm d-flex justify-content-between">
    <p class="color-highlight my-0 py-0">
        <i class="fa fa-phone-book"></i> <b id="mycontactname">Select From Contact</b>
    </p>
    <p class="color-highlight my-0 py-0"><i class="fa fa-users"></i></p>
</div>

<div class="input-style input-style-always-active has-borders validate-field mb-4">
    <label for="phone" class="color-theme opacity-80 font-700 font-12">Enter Phone Number</label>
    <input type="number" onkeyup="verifyNetwork()" name="phone" placeholder="~Phone Number~" value="" class="round-small" id="phone" required />
</div>
                     
                            <p id="verifyer"></p>
        
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="networktype" class="color-theme opacity-80 font-700 font-12">Type</label>
                                <select id="networktype" name="networktype">
                                    <option value="VTU">~VTU~</option>
                                    <option value="Share And Sell">Share And Sell</option>
                                    <option value="Momo">Momo Airtime</option>
                                </select>
                                <span><i class="fa fa-chevron-down"></i></span>
                                <i class="fa fa-check disabled valid color-green-dark"></i>
                                <i class="fa fa-check disabled invalid color-red-dark"></i>
                                <em></em>
                            </div>
                            
                            <div class="input-style input-style-always-active has-borders mb-4">
                                <label for="airtimeamount" class="color-theme opacity-80 font-700 font-12">Amount</label>
                                <input type="number" name="amount" placeholder="~Amount~" value="" class="round-small" id="airtimeamount" required  />
                            </div>

                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="amounttopay" class="color-theme opacity-80 font-700 font-12">Amount To Pay</label>
                                <input type="number" name="amounttopay" placeholder="~Amount To Pay~" value="" class="round-small" id="amounttopay" readonly required  />
                            </div>

                            <div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="discount" class="color-theme opacity-80 font-700 font-12">Discount</label>
                                <input type="text" name="discount" placeholder="~Discount~" value="" class="round-small" id="discount" readonly required  />
                            </div>

                            <div class="form-check icon-check" style="display: none;">
                                <input class="form-check-input" type="checkbox" name="ported_number" id="ported_number" checked>
                                <label class="form-check-label" for="ported_number">Disable Number Validator</label>
                                <i class="icon-check-1 fa fa-toggle-off color-gray-dark font-25"></i>
                                <i class="icon-check-2 fa fa-toggle-on font-25 color-highlight"></i>
                            </div>

                            <input name="transref" type="hidden" value="<?php echo $transRef; ?>" />
                            <input name="transkey" id="transkey" type="hidden" />
                            
                            <div data-menu="network-status-box" class="input-style py-2 ps-2 pe-2 bg-light border rounded-sm d-flex justify-content-between align-items-center">
                        <p class="color-highlight my-0 py-0"><i class="fa fa-phone-book"></i> 
                        <b>View Network Status</b>
                        <br/>
                        <b class="text-dark"><small>Check Network Status Before Purchase</small></b>
                    </p>
                        <p class="color-highlight my-0 py-0 font-20"><i class="fa fa-signal"></i></p>
                    </div>

                            
                            <div class="form-button">
                            <button type="submit" id="airtime-btn" name="purchase-airtime" class="btn btn-info btn-lg btn-block" style="background-color:<?php echo $sitecolor; ?>;">
                                   Buy Airtime
                            </button>
                            </div>
                        </fieldset>
                    </form>        
            </div>

        </div>

</div>


<?php $beneficiaries = $controller->getBeneficiary(); ?>
<div id="contact-list-box" class="menu menu-box-bottom rounded-l" data-menu-effect="menu-over" style="display: block; height: 90vh; background:#ffffff;">
    <div class="menu-title">
        <h1 class="font-24 mb-0 pb-0">Contact List</h1>
        <a href="#" class="close-menu"><i class="fa fa-times-circle"></i></a>
    </div>
    <hr />
    <div class="content mb-0 mt-0">

        <div class="d-flex justify-content-between mb-2">
            <a href="#" id="addContactBoxBtn" onclick="$('#addContactBoxBtn').hide(); $('#closeContactBoxBtn').show(); $('#contactBox').hide(); $('#addContactBox').show();" class="btn btn-secondary form-control me-4"><i class="fa fa-plus"></i> Add New</a>
            <a href="#" id="closeContactBoxBtn" style="display:none;" onclick="$('#closeContactBoxBtn').hide(); $('#addContactBoxBtn').show(); $('#addContactBox').hide(); $('#contactBox').show();" class="btn btn-secondary  form-control me-4"><i class="fa fa-eye"></i> Show Beneficiaries</a>
            <a href="beneficiary" class="btn btn-secondary  form-control close-menu"><i class="fa fa-arrow-circle-right"></i> View All</a>
        </div>

        <div id="addContactBox" style="display:none;">
            <h6 class="py-2">Add New Contact</h6>
            <form method="POST" class="py-2 the-submit-form">
                <div class="input-style input-style-always-active has-borders validate-field mb-4">
                    <label for="name" class="color-theme opacity-80 font-700 font-12">Name:</label>
                    <input type="text" name="name" placeholder="Name" required>
                </div>

                <div class="input-style input-style-always-active has-borders validate-field mb-4" id="phoneInput">
                    <label for="phone" class="color-theme opacity-80 font-700 font-12">Phone Number:</label>
                    <input type="text" name="phone" placeholder="Phone Number" required>
                </div>
                <div class="form-button">
                    <button type="submit" id="airtime-btn" name="save-beneficiary" style="width: 100%;" class="the-form-btn btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s"><i class="fa fa-plus"></i> Add Contact</button>
                </div>
            </form>
        </div>

        <div id="contactBox">

    <form method="POST" id="delete-beneficiaryBoxForm">
        <input type="hidden" name="contact" id="delete-beneficiaryBoxId">
        <input type="hidden" name="delete-beneficiary" />
    </form>

    <div class="search-box search-dark border bg-theme rounded-sm bottom-0">
        <i class="fa fa-search"></i>
        <input type="text" class="border-0" placeholder="Search Contact" data-search>
    </div>

    <div class="search-results mt-3">
        <div class="list-group list-custom-large">
            <?php if (!empty($beneficiaries)) { ?>
                <?php foreach ($beneficiaries as $bv) { ?>
                    <li class="py-0 list-group-item d-flex justify-content-between align-items-center" data-filter-item data-filter-name="<?php echo htmlspecialchars($bv['name'] . ' ' . $bv['phone']); ?>">
                        <div class="close-menu" onclick="$('#phone').val('<?php echo htmlspecialchars($bv['phone']); ?>'); $('#mycontactname').text('<?php echo htmlspecialchars($bv['name']); ?> (Change)'); verifyNetwork();">
                            <div class="d-flex justify-content-start align-items-center">
                                <img src="../../assets/img/user.png" alt="User Image" />
                                <div>
                                    <span><?php echo htmlspecialchars($bv['name']); ?></span>
                                    <br/>
                                    <strong><?php echo htmlspecialchars($bv['phone']); ?></strong>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="delete-beneficiaryBoxBtn<?php echo $bv['id']; ?>" onclick="$('#deleteContactBoxId').val('<?php echo $bv['id']; ?>'); $('#delete-beneficiaryBoxBtn<?php echo $bv['id']; ?>').html('<i class=\'fa fa-spinner fa-spin\'></i>'); $('#delete-beneficiaryBoxForm').submit();" class="text-danger font-20"><i class="fa fa-trash"></i></button>
                    </li>
                <?php } ?>
            <?php } else { ?>
                <h6 class='border py-2 my-4 text-center'>No Contact Added Yet</h6>
            <?php } ?>
        </div>
    </div>
</div>
            <div class="search-no-results disabled mt-4">
                <div class="card card-style mx-0">
                    <div class="content">
                        <p class="mb-n1 font-600 color-highlight">Sorry, there are</p>
                        <h1>No Results</h1>
                        <p class="mb-2">Search the name or phone number</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="network-status-box" class="menu menu-box-bottom rounded-l" data-menu-effect="menu-over" style="display: block; height: 90vh; background:#ffffff;">
    <div class="menu-title">
        <h1 class="font-24 mb-0 pb-0">Airtime Status</h1>
        <a href="#" class="close-menu font-25"><i class="fa fa-times-circle"></i></a>
    </div>
    <hr />
    <div class="mb-0 mt-0 row">
        
        <?php 
            // Fetch status from controller
            $networks = [
                'MTN' => $controller->mtnAirStatus(),
                'AIRTEL' => $controller->airtelAirStatus(),
                'GLO' => $controller->gloAirStatus(),
                '9MOBILE' => $controller->mobileAirStatus()
            ];

            // Loop through networks to generate progress bars
            foreach ($networks as $network => $status) {
                // Set progress color and percentage based on status
                if ($status == 20) {
                    $progressStatus = 100;
                    $progressColor = 'bg-success';
                } elseif ($status >= 16 && $status <= 19) {
                    $progressStatus = 85;
                    $progressColor = 'bg-success';
                } elseif ($status >= 11 && $status <= 15) {
                    $progressStatus = 50;
                    $progressColor = 'bg-warning text-dark';
                } elseif ($status >= 6 && $status <= 10) {
                    $progressStatus = 40;
                    $progressColor = 'bg-warning text-dark';
                } elseif ($status >= 2 && $status <= 5) {
                    $progressStatus = 20;
                    $progressColor = 'bg-danger';
                } else {
                    $progressStatus = 5;
                    $progressColor = 'bg-danger';
                }
        ?>

        <div class="col-6">
            <div class="card card-style me-0 mb-1 mt-1">
                <div class="content">
                    <h4>
                        <span class="icon icon-l rounded-l py-1 px-1" style="background:#f2f2f2;">
                            <img src="../../assets/images/icons/<?php echo strtolower(str_replace(' ', '', $network)); ?>.png" width="25" height="25" class="rounded-l" />
                        </span>
                    </h4>

                    <h6 class="font-12"><?php echo $network; ?></h6>

                    <div class="progress mb-4" style="height:18px;">
                        <div class="progress-bar border-0 <?php echo $progressColor; ?> text-start ps-2" role="progressbar" style="width: <?php echo $progressStatus; ?>%" aria-valuenow="<?php echo $progressStatus; ?>" aria-valuemin="0" aria-valuemax="100">
                            <span class="position-absolute color-white"><?php echo $progressStatus; ?>% Stable</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php } ?>
        
            <div class="content">
       <div class="content">
 <table class="table text-white">
              <tr class="bg-success text-white">
                <td>Green Status Means</td>
                <td>Airtime is fully available</td>
            </tr>  <br/>
            <tr class="bg-warning text-white">
                <td>Yellow Status Means</td>
                <td>Available but not stable</td>
            </tr> <br/>
            <tr class="bg-danger text-white">
                <td>Red Status Means</td>
                <td>Down (Airtime is not available)</td>
            </tr>
                </table>
  </div>
                    </div>
                </div>
            </div>
        </div>