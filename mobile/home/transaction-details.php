<div class="page-content header-clear-medium">
        
        <div class="card card-style" >
            <div class="content">
                <div class="text-center"><img src="../../assets/images/icons/success2.png" style="width:100px; height:100px;" /></div>
                <p class="mb-0 font-600 text-dark text-center">KEYTOPUP NIGEIA LTD</p>
                <h1 class="text-center"><?php echo $controller->formatStatus($data->status); ?></h1>
                <h4 class="text-center"><?php echo $data->servicedesc; ?></h4>
                <hr/>
                <table class="table">
                    <tr>
                        <td><b>Ref No:</b></td>
                        <td align="right"><?php echo $data->transref; ?></td>
                    </tr>
                    <tr>
                        <td><b>Date:</b></td>
                        <td align="right"><?php echo $controller->formatDate($data->date); ?></td>
                    </tr>
                    <!--tr>
                        <td><b>Service:</b></td>
                        <td align="right"><?php echo $data->servicename; ?></td>
                    </tr>
                    <tr>
                        <td><b>Description:</b></td>
                        <td align="right"><?php echo $data->servicedesc; ?></td>
                    </tr-->
                    <?php if(!isset($_GET["receipt"])): ?>
                    <tr>
                        <td><b>Amount:</b></td>
                        <td align="right">N<?php echo $data->amount; ?></td>
                    </tr>
                    <tr>
                        <td><b>Old Balance:</b></td>
                        <td align="right">N<?php echo $data->oldbal; ?></td>
                    </tr>
                     <tr>
                        <td><b>New Balance:</b></td>
                        <td align="right">N<?php echo $data->newbal; ?></td>
                    </tr>
                    <?php endif; ?>
                                </table>

            <!--div data-menu="helpcode-box" class="input-style py-2 ps-2 pe-2 bg-light border rounded-sm d-flex justify-content-between align-items-center">
                        <p class="color-highlight my-0 py-0"><i class="fa fa-phone-book"></i> 
                        <b id="mycontactname">View Help Codes</b>
                        <br/>
                        <b class="text-dark"><small>Help Codes To Check Your Balance</small></b>
                    </p>
                        <p class="color-highlight my-0 py-0 font-20"><i class="fa fa-info-circle"></i></p>
                    </div-->

            <div class="text-center">
                                    <form method="post" id="issueForm">
                        <input type="hidden" name="ref" value="<?php echo $data->transref; ?>">
                        <input type="hidden" name="queryContent" value="(Report)<?php echo $data->servicedesc; ?> With Ref <?php echo $data->transref; ?>">
                        <input type="hidden" name="queryreport" />
                    </form>
                    
                    

                </table> 
               <div class="text-center">
                    <?php if(!isset($_GET["receipt"])): ?>
                    <a href="transaction-details?receipt&ref=<?php echo $_GET["ref"]; ?>" class="btn btn-success btn-sm" style="border-radius:5px;" >
                        <b>Customers Receipt</b>
                    </a>
                    <?php endif; ?>
                     <!--a href="#" onClick="printPage()" class="btn btn-success btn-sm float-end" style="border-radius:5px; margin-left:15px;">
                        <b>Download Receipt</b>
                    </a-->
                    <?php if($data->servicename == "Recharge Pin" && $data->status == 0): ?>
                    <a href="view-recharge-pins?ref=<?php echo $_GET["ref"]; ?>" class="btn btn-primary btn-sm" style="border-radius:5px; margin-left:15px;">
                        <b>View Pins</b>
                    </a>
                    <?php endif; ?>
                    <?php
                    if($data->servicename == "ID Verification"){
                        $conn = mysqli_connect("localhost","keytopup_vc","keytopup_vc","keytopup_vc");   
                        $report  = $_GET["ref"];
                        $pdf = "";
                        $check = mysqli_query($conn, "SELECT * FROM reports WHERE transid = '$report'");
                        if(mysqli_num_rows($check) == 1){
                            $report = mysqli_fetch_assoc($check);
                            $pdf = $report["pdf"];    
                        }
                    }   
                    ?>
                    
                    <?php if($data->servicename == "ID Verification"): ?>
                        <a href="<?php echo $pdf; ?>" class="btn btn-primary btn-sm" style="border-radius:5px; margin-left:15px;">
                            <b>Download Slip</b>
                        </a>
                    <?php endif; ?>

                    <?php if($data->servicename == "Data Pin" && $data->status == 0): ?>
                    <a href="view-pins?ref=<?php echo $_GET["ref"]; ?>" class="btn btn-primary btn-sm" style="border-radius:5px; margin-left:15px;">
                        <b>View Pins</b>
                    </a>
                    <?php endif; ?>
                    <!--button  class="btn btn-danger btn-sm mt-2" style="border-radius:5px;" id="issuebtn" onclick="$('#issuebtn').removeClass('btn-warning'); $('#issuebtn').addClass('btn-secondary'); $('#issuebtn').html('<i class=\'fa fa-spinner fa-spin\'></i> Submitting...'); $('#issueForm').submit();">
                        <b>Report</b>
                    </button-->
                    
                    </div>
        </div>

    </div>

</div>

<div id="helpcode-box" class="menu menu-box-bottom rounded-l" data-menu-effect="menu-over" style="display: block; height: 90vh; background:#ffffff;">
    <div class="menu-title">
        <h1 class="font-24 mb-0 pb-0">Help Codes</h1>
        <a href="#" class="close-menu font-25"><i class="fa fa-times-circle"></i></a>
    </div>
    <hr />
    <div class="mb-0 mt-0 row">

                <div class="col-6">
            <div class="card card-style m-0 mb-1 mt-1 ms-1 me-1">
                <div class="content mb-1 mt-1 text-center">
                    <h4>
                        <span class="icon icon-l rounded-l py-1 px-1" style="background:#f2f2f2;">
                            <img src="../../assets/images/phone.png" width="25" height="25" class="rounded-l" />
                        </span>
                    </h4>

                    <h6 class="font-12">9MOBILE Corporate</h6>
                    <p class="font-13 text-dark"><b>*323#</b></p>

                    
                </div>
            </div>

        </div>
                <div class="col-6">
            <div class="card card-style m-0 mb-1 mt-1 ms-1 me-1">
                <div class="content mb-1 mt-1 text-center">
                    <h4>
                        <span class="icon icon-l rounded-l py-1 px-1" style="background:#f2f2f2;">
                            <img src="../../assets/images/phone.png" width="25" height="25" class="rounded-l" />
                        </span>
                    </h4>

                    <h6 class="font-12">GLO Corporate</h6>
                    <p class="font-13 text-dark"><b>*323#</b></p>

                    
                </div>
            </div>

        </div>
                <div class="col-6">
            <div class="card card-style m-0 mb-1 mt-1 ms-1 me-1">
                <div class="content mb-1 mt-1 text-center">
                    <h4>
                        <span class="icon icon-l rounded-l py-1 px-1" style="background:#f2f2f2;">
                            <img src="../../assets/images/phone.png" width="25" height="25" class="rounded-l" />
                        </span>
                    </h4>

                    <h6 class="font-12">AIRTEL Corporate</h6>
                    <p class="font-13 text-dark"><b>*323#</b></p>

                    
                </div>
            </div>

        </div>
                <div class="col-6">
            <div class="card card-style m-0 mb-1 mt-1 ms-1 me-1">
                <div class="content mb-1 mt-1 text-center">
                    <h4>
                        <span class="icon icon-l rounded-l py-1 px-1" style="background:#f2f2f2;">
                            <img src="../../assets/images/phone.png" width="25" height="25" class="rounded-l" />
                        </span>
                    </h4>

                    <h6 class="font-12">MTN Corporate</h6>
                    <p class="font-13 text-dark"><b>*323#</b></p>

                    
                </div>
            </div>

        </div>
                <div class="col-6">
            <div class="card card-style m-0 mb-1 mt-1 ms-1 me-1">
                <div class="content mb-1 mt-1 text-center">
                    <h4>
                        <span class="icon icon-l rounded-l py-1 px-1" style="background:#f2f2f2;">
                            <img src="../../assets/images/phone.png" width="25" height="25" class="rounded-l" />
                        </span>
                    </h4>

                    <h6 class="font-12">MTN AWOOF</h6>
                    <p class="font-13 text-dark"><b>Send 2 To 323</b></p>

                    
                </div>
            </div>

        </div>
                <div class="col-6">
            <div class="card card-style m-0 mb-1 mt-1 ms-1 me-1">
                <div class="content mb-1 mt-1 text-center">
                    <h4>
                        <span class="icon icon-l rounded-l py-1 px-1" style="background:#f2f2f2;">
                            <img src="../../assets/images/phone.png" width="25" height="25" class="rounded-l" />
                        </span>
                    </h4>

                    <h6 class="font-12">MTN SME</h6>
                    <p class="font-13 text-dark"><b>*323*4#</b></p>

                    
                </div>
            </div>

        </div>
            </div>
</div>    <!-- Page content ends here-->
