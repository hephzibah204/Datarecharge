<?php $rate = $controller->getConfigValue($data2,"dollarRate");?>
<div class="page-content header-clear-medium">
<div class="card card-style" data-card-height="190">
        <div class="card-top ps-3 pt-2">
            <div class="card-body pt-2 mt-1 mb-n2 text-left">
           <b>Debit</b>
                <div class="d-flex justify-content-between align-content-center">
                   
                    <div class="mt-3">
                        <i class="fa fa-caret-left font-16"></i>
                        <img src="/sim.png" style="max-width: 50px;" />
                    </div>

                    <div>
                       <i class="fa fa-wifi text-primary h3 mb-10"></i>
                    </div> 
                </div>

 <div class="mt-3">
        <div class="d-flex justify-content-between">
        <div class="container">
           <b class="h4"><?php echo $data->first_six;?> </b>
        </div>
        <div class="container">
          <b class="h4"> **** </b>
        </div>
        <div class="container">
            <b class="h4"> <?php echo $data->last_four;?> </b>
        </div>
    </div>
</div> 
     <div class="mt-3">
        <div class="d-flex justify-content-between">
        <div class="container">
           <b class="text-danger"> Expiry: <?php  echo $data->expiry; ?> </b>
        </div>
        <div class="container">
          <b class="text-danger"> <?php  echo $data->expiry; ?> </b>
        </div>
        <div class="container">
            <b class="text-primary"> VTUPRESS </b>
        </div>
    </div>
</div> </div> </div>
<div class="card-overlay" style="background-image: url('/bgg.png'); background-size: cover;"></div>
</div> 


<div class="justify-content-center m-4">
    <form method="post" id="fundCard">
        <div class="input-style input-style-always-active has-borders validate-field">
            <b class="">Dollar Rate: </b><b class="text-primary"> $1 / <?php echo $rate; ?> </b> - <b class="text-success" id="amountToPay"></b>
            <input type="number" id="amount" name="amount" placeholder="Amount" class="round-small" required onkeyup="calculateDollarToPay()">
            <input value="" id="amounttopay" name="amounttopay" type="hidden" />
            
        </div>

        <input type="hidden" name="cardId" id="cardId" value="<?php echo $data->card_id; ?>">
        <input name="transkey" id="transkey" type="hidden" />
        <div class="input-style input-style-always-active has-borders validate-field">
            <button type="submit" name="fund-virtual-card" class="btn btn-success fundBtn">Fund Card</button>
            <button type="submit" name="withdraw-virtual-card" class="btn btn-danger float-end withdrawBtn">Withdraw</button>
        </div>
    </form>
</div>

<div class="card card-style bg-theme pb-0">
            <div class="content" id="tab-group-1">
                
                <div class="clearfix mb-3"></div>
                <div data-bs-parent="#tab-group-1" class="collapse show" id="tab-1">
                    <p class="mb-n1 color-highlight font-600 font-12">Your card Details</p>
                        <h4>Card Information</h4>
                        
                        <div class="list-group list-custom-small">
                            <a href="#">
                                <i class="fa font-14 fa-credit-card rounded-xl shadow-xl color-blue-dark"></i>
                                <span><b>Card Number: </b> <?php echo $data->card_number; ?></span>
                                <i class="fa fa-angle-right"></i>
                            </a>
                            <a href="#">
                                <i class="fa font-14 fa-credit-card rounded-xl shadow-xl color-blue-dark"></i>
                                <span><b>Card Balance: </b> $<?php echo $data->balance; ?></span>
                                <i class="fa fa-angle-right"></i>
                            </a>
                            <a href="#"> 
                                <i class="fa font-14 fa-credit-card rounded-xl shadow-xl color-blue-dark"></i>
                                <span><b>Card Type: </b> <?php echo $data->brand; ?></span>
                                <i class="fa fa-angle-right"></i>
                            </a> 
                            <a href="#"> 
                                <i class="fa font-14 fa-credit-card rounded-xl shadow-xl color-blue-dark"></i>
                                <span><b>Card Currency: </b> <?php echo $data->currency; ?></span>
                                <i class="fa fa-angle-right"></i>
                            </a> 
                            <a href="#"> 
                                <i class="fa font-14 fa-minus-square rounded-xl shadow-xl color-blue-dark"></i>
                                <span><b>Card Expiry: </b> <?php echo $data->expiry; ?></span>
                                <i class="fa fa-angle-right"></i>
                            </a> 
                            <a href="#"> 
                                <i class="fa font-14 fa-credit-card rounded-xl shadow-xl color-blue-dark"></i>
                                <span><b>Card Status: </b> <?php echo $data->status; ?></span>
                                <i class="fa fa-angle-right"></i>
                            </a> 
                            <a href="#"> 
                                <i class="fa font-14 fa-credit-card rounded-xl shadow-xl color-blue-dark"></i>
                                <span><b>Card Create On: </b> <?php echo $data->created_at; ?></span>
                                <i class="fa fa-angle-right"></i>
                            </a> 
                                        
                      </div>
                </div>
            </div>
        </div> 
</div>