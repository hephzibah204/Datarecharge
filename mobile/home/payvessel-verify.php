<div class="page-content header-clear-medium">


    <div class="card card-style">

        <div class="content text-center">
            <img src="../../assets/images/icons/Pay.png" style="width:100px; height:100px;" />

            <p class="mb-0 font-600 color-highlight">KYC Verification</p>

            <div id="kycNoteBox">
                <?php if(empty($data->sPayvesselBank)){ ?>
        <h1 class="mb-5">Payvessel Account</h1>
        <hr />
    <div id="kycNoteBox" class="mb-5">
    <p class="mb-0 font-600 color-highlight">KYC Verification Required To Continue Using Payvessel Virtual Account</p>
    <p class="mb-1 font-600 text-danger">As Required By The Central Bank Of Nigeria (CBN), Payvessel Need To Verify Your Identity Using Your BVN. before you can be using the account to fund your wallet This Process Is Automatic And You Would Be Able To get an account Once Verified.</p>
    </div>
                            
     <form method="post" enctype="multipart/form-data">
    <div class="input-style input-style-always-active has-borders mb-4">
    <label for="bvn">Enter BVN:</label>
    <input type="number" id="bvn" name="bvn" required>
    </div>
    <div class="form-button">
    <button type="submit" id="airtime-btn" name="generate-payvessel-account" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">Generate Payvessel Account</button>
    </div>
    </form>
    
    <?php } else {?>
    
    <h4 class="mb-5">Update Payvessel Account</h4>
        
    <div id="kycNoteBox" class="mb-5">
    <p class="mb-0 font-600 color-highlight">KYC Verification Required To Continue Using Payvessel Virtual Account: <?php echo $data->sPayvesselBank;?></p>
    <p class="mb-1 font-600 text-danger">As Required By The Central Bank Of Nigeria (CBN), Payvessel Need To Verify Your Identity Using Your NIN or BVN. before you can be using the account to fund your wallet This Process Is Automatic And You Would Be Able To get an account Once Verified.</p>
    </div>
                            
     <form method="post" enctype="multipart/form-data">
         
     
    <div class="input-style input-style-always-active has-borders mb-4">
    <label for="bvn">Enter BVN:</label>
    <input type="number" id="bvn" name="bvn" required>
    </div>
    
        <div class="form-button">
        <button type="submit" id="update-btn" name="update-payvessel-account" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">Generate Payvessel Account</button>
        </div>
    </form>
<?php }?>
</div>
</div>
</div>