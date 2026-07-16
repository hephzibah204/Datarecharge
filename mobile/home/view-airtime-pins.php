<div class="page-content header-clear-medium">
        
        <div class="card card-style">
            <div class="content">
                <div class="d-flex justify-content-between mb-0">
                    <div>
                        <p class="mb-0 font-600 color-highlight">Transaction Details</p>
                        <h1>Recharge Card</h1>
                    </div>
                    <div>
                        <a href="print-airtime-pin?ref=<?php echo $_GET["ref"]; ?>&printsize=mobile" class="btn btn-sm btn-info mt-2"><i class="fa fa-print"></i> Print</a>
                        <a href="print-airtime-pin?ref=<?php echo $_GET["ref"]; ?>&printsize=a4" class="btn btn-sm btn-info mt-2"><i class="fa fa-print"></i> A4 Size</a>
                    </div>
                </div>
                <p class="mb-0 font-600 text-danger">Click On The Pin To Copy</p>
                
                <div>
                    <?php if(!empty($data)) : $pins=explode(",",$data->tokens); $sn=explode(",",$data->serial); ?>
                    <?php $network=$data->network; $datasize=$data->amount; ?>
                    <?php if($network == "AIRTEL"){$cardColor="#ff1a1a"; $cardLogo="airtel.png"; $textColor="#ffffff"; $checkBal="*140#";} 
                         elseif($network == "GLO"){$cardColor="#60cf06"; $cardLogo="glo.png"; $textColor="#ffffff"; $checkBal="*140#";} 
                         elseif($network == "9MOBILE"){$cardColor="##047d0c"; $cardLogo="9mobile.png"; $textColor="#ffffff"; $checkBal="*140#";} 
                         else {$cardColor="#ffcc00"; $cardLogo="mtn.png"; $textColor="#000000"; $checkBal="*461*4#";} 
                         $loadpin=$data->loadpin; $checkBal=$data->checkbalance;
                         ?>
                    <?php for($i=0; $i<$data->quantity; $i++): ?>
                          
                                <div class="row border" style="margin:3px;">
                                        <div class="col-4" style="margin:0; padding:0; background-color:<?php echo $cardColor; ?>;">
                                            <div class="text-dark" style="padding:10px;">
                                               
                                                <p style="margin-bottom:5px;"><img src="../../assets/images/icons/<?php echo $cardLogo; ?>" style="width:50px; height:50px;" /></p>
                                                <h6 style="color:<?php echo $textColor; ?>" class="font-15">RECHARGE CARD</h6>
                                                <h6 style="color:<?php echo $textColor; ?>" class="mt-2">N<?php echo $datasize; ?></h6>
                                            </div>
                                        </div>
                                        
                                        <div class="col-8 bg-white" style="margin:0; padding:0; ">   
                                            <div class="text-center" style="padding:10px;">
                                                
                                                <h6 class="font-10"><?php echo strtoupper($data->business); ?></h6>
                                                <button style="background-color:#f2f2f2; border-radius:3rem; padding:7px; width:100%;" onclick="copyToClipboard('<?php echo trim($pins[$i]); ?>')"><h4 class="font-15"><?php echo trim($pins[$i]); ?></h4></button>
                                                <p style="margin-bottom:0; margin-top:2px; color:<?php echo $textColor; ?>" class="font-10">Serial No: <?php echo $sn[$i]; ?></p>
                                                <p style="margin-bottom:0;"><b>Load <?php echo $loadpin; ?></b> <b>Bal:   <?php echo $checkBal; ?></b></p>
                                                <p class="font-10">Powered By: <?php echo $sitename; ?></p>
                                            </div>
                                        </div>
                                </div>
                        
                    <?php endfor; endif; ?>
                   
                </div>


            </div>

        </div>

</div>

