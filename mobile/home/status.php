<!-- Page content start here-->
        <div class="page-content header-clear-medium">
<div class="card card-style">
     
    <div class="content"><h1 class="card-tittle">NETWORK STRENGTH</h1>
 <?php
    $mtn = $controller->mtnStatus();
    if ($mtn == 20)     {
        $mtnStatus = 100; 
        $progressColor = 'bg-success';
    } elseif ($mtn >= 16 && $mtn <= 19){
        $mtnStatus = 85; 
        $progressColor = 'bg-success';
        
    } elseif ($mtn >= 11 && $mtn <= 15){
        $mtnStatus = 50; 
        $progressColor = 'bg-warning text-dark';
        
    } elseif ($mtn >= 6 && $mtn <= 10) {
        $mtnStatus = 40; 
        $progressColor = 'bg-warning text-dark';
        
    } elseif ($mtn >= 4 && $mtn <= 5) {
        $mtnStatus = 35; 
        $progressColor = 'bg-warning text-dark';
        
    } elseif ($mtn >= 2 && $mtn <= 3) {
        $mtnStatus = 20; 
        $progressColor = 'bg-danger';
        
    } elseif ($mtn == 1) {
        $mtnStatus = 5; 
        $progressColor = 'bg-danger';
        
    }
?>
<div class="progress mt-4">
    <div class="progress-bar <?php echo $progressColor; ?> progress-bar-sm" role="progressbar" style="width: <?php echo $mtnStatus;?>%; min-width: 50px;" aria-valuenow="<?php echo $mtnStatus;?>" aria-valuemin="0" aria-valuemax="100">MTN <?php echo $mtnStatus;?>%</div>
</div>

  
  
  <?php
    $airtel = $controller->airtelStatus();
    if ($airtel == 20) {
        $airteStatus = 100;
        $progressColor = 'bg-success';
    } elseif ($airtel >= 16 && $airtel <= 19) {
        $airteStatus = 85;
        $progressColor = 'bg-success';
    } elseif ($airtel >= 11 && $airtel <= 15) {
        $airteStatus = 50;
        $progressColor = 'bg-warning text-dark';
    } elseif ($airtel >= 6 && $airtel <= 10) {
        $airteStatus = 40;
        $progressColor = 'bg-warning text-dark';
    } elseif ($airtel >= 4 && $airtel <= 5) {
        $airteStatus = 35;
        $progressColor = 'bg-warning text-dark';
    } elseif ($airtel >= 2 && $airtel <= 3) {
        $airteStatus = 20;
        $progressColor = 'bg-danger';
    } elseif ($airtel == 1) {
        $airteStatus = 5;
        $progressColor = 'bg-danger';
    }
?>

<div class="progress mt-4">
    <div class="progress-bar <?php echo $progressColor; ?> progress-bar-sm" role="progressbar" style="width: <?php echo $airteStatus; ?>%; min-width: 50px;" aria-valuenow="<?php echo $airteStatus; ?>" aria-valuemin="0" aria-valuemax="100">Airtel <?php echo $airteStatus; ?>%</div>
</div>

      
      
 <?php
    $glo = $controller->gloStatus();
    if ($glo == 20) {
        $gloStatus = 100;
        $progressColor = 'bg-success';
    } elseif ($glo >= 16 && $glo <= 19) {
        $gloStatus = 85;
        $progressColor = 'bg-success';
    } elseif ($glo >= 11 && $glo <= 15) {
        $gloStatus = 50;
        $progressColor = 'bg-warning text-dark';
    } elseif ($glo >= 6 && $glo <= 10) {
        $gloStatus = 40;
        $progressColor = 'bg-warning text-dark';
    } elseif ($glo >= 4 && $glo <= 5) {
        $gloStatus = 35;
        $progressColor = 'bg-warning text-dark';
    } elseif ($glo >= 2 && $glo <= 3) {
        $gloStatus = 20;
        $progressColor = 'bg-danger';
    } elseif ($glo == 1) {
        $gloStatus = 5;
        $progressColor = 'bg-danger';
    }
?>

<div class="progress mt-4">
    <div class="progress-bar <?php echo $progressColor; ?> progress-bar-sm" role="progressbar" style="width: <?php echo $gloStatus; ?>%; min-width: 50px;" aria-valuenow="<?php echo $gloStatus; ?>" aria-valuemin="0" aria-valuemax="100">Glo <?php echo $gloStatus; ?>%</div>
</div>

      
 <?php
    $mobile = $controller->mobileStatus();
    if ($mobile == 20) {
        $mobileStatus = 100;
        $progressColor = 'bg-success';
    } elseif ($mobile >= 16 && $mobile < 20) {
        $mobileStatus = 85;
        $progressColor = 'bg-success';
    } elseif ($mobile >= 11 && $mobile < 15) {
        $mobileStatus = 50;
        $progressColor = 'bg-warning text-dark';
    } elseif ($mobile == 9) {
        $mobileStatus = 40;
        $progressColor = 'bg-warning text-dark';
    } elseif ($mobile >= 6 && $mobile < 9) {
        $mobileStatus = 35;
        $progressColor = 'bg-warning text-dark';
    } elseif ($mobile >= 4 && $mobile < 6) {
        $mobileStatus = 20;
        $progressColor = 'bg-danger';
    } elseif ($mobile == 1) {
        $mobileStatus = 5;
        $progressColor = 'bg-danger';
    }
?>

<div class="progress mt-4">
    <div class="progress-bar <?php echo $progressColor; ?> progress-bar-sm" role="progressbar" style="width: <?php echo $mobileStatus; ?>%; min-width: 50px;" aria-valuenow="<?php echo $mobileStatus; ?>" aria-valuemin="0" aria-valuemax="100">9mobile <?php echo $mobileStatus; ?>%</div>
</div>

   <div class="content">
       <div class="content">
        <h1 class="font-20">NETWORK STATUS</h1>
                <table class="table table-bordered table-striped">
                    <tr style="background-color: #002db3; ">
                        <td class="text-white"><b>Id</b></td>
                        <td class="text-white"><b>Network</b></td>
                        <td class="text-white"><b>Data Type</b></td>
                    </tr>
                                            <tr>
                            <td>1</td>
                            <td>MTN</td> 
                            <td> SME <b>"On"</b> | SME2 <b>"On"</b> | Corporate <b>"On"</b> | Gifting <b>"Off"</b> | Coupon  <b>"Off"</b> </td>
                        </tr>
                                            <tr>
                            <td>2</td>
                            <td>GLO</td> 
                            <td> SME <b>"Off"</b> | SME2 <b>"Off"</b> | Corporate <b>"On"</b> | Gifting <b>"Off"</b> | Coupon  <b>"Off"</b> </td>
                        </tr>
                                            <tr>
                            <td>3</td>
                            <td>9MOBILE</td> 
                            <td> SME <b>"Off"</b> | SME2 <b>"Off"</b> | Corporate <b>"On"</b> | Gifting <b>"Off"</b> | Coupon  <b>"Off"</b> </td>
                        </tr>
                                            <tr>
                            <td>4</td>
                            <td>AIRTEL</td> 
                            <td> SME <b>"Off"</b> | SME2 <b>"Off"</b> | Corporate <b>"On"</b> | Gifting <b>"On"</b> | Coupon  <b>"Off"</b> </td>
                        </tr>
                
                <table class="table text-white">
              <tr class="bg-success text-white">
                <td>Green Status Means</td>
                <td>Data is fully available</td>
            </tr>  <br/>
            <tr class="bg-warning text-white">
                <td>Yellow Status Means</td>
                <td>Available but not stable</td>
            </tr> <br/>
            <tr class="bg-danger text-white">
                <td>Red Status Means</td>
                <td>Down (Data is not available)</td>
            </tr>
                </table>
  
        </div>
</div></div>