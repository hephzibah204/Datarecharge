<!-- Page content start here-->
        <div class="page-content header-clear-medium">
<div class="card card-style">
     
    <div class="content"><h1 class="card-tittle">Add Beneficiary</h1>
        
             <form method="post" enctype="multipart/form-data">
            <div class="input-style input-style-always-active has-borders validate-field mb-4">
           <label for="name" class="color-theme opacity-80 font-700 font-12">Name:</label>
          <input type="text" name="name" id="name" required>
          </div>
   
         <div class="input-style input-style-always-active has-borders validate-field mb-4" id="phoneInput">
         <label for="phone" class="color-theme opacity-80 font-700 font-12">Phone Number:</label>
         <input type="text" name="phone" id="" required>
        </div>
        <div class="form-button">
        <button type="submit" id="airtime-btn" name="save-beneficiary" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">Add Beneficiary</button>
        </div>
    </form>
 
<div class="input-style input-style-always-active has-borders validate-field mb-4" id="beneficiarySelect" style="display: none;">
  <input type="number" onkeyup="verifyNetwork()" name="phone" placeholder="Phone Number" value="" class="round-small" id="phone" />
  <select name="phone" onchange="populatePhoneInput()">
    <option value="">Select Beneficiary</option>

  </select>
</div>

            <table class="table">
                <h1 class="text-primary mt-5"> Beneficiary </h1>
                 <?php $data = $controller->getBeneficiary(); ?>
                <?php foreach ($data as $bv) { $name = $bv['name']; $phone = $bv['phone']; $id = $bv['id'];?>
                <tr>
                 <td><b><i class="fa fa-address-book"></i>  <?php echo $name;?></b></td>
                 <td><b> <?php echo $phone; ?></b></td>
                 <td><form method="POST">
                 <input type="hidden" name="id" value="<?php echo $id;?>">
                 <button type="submit" name="delete-beneficiary" class="text-danger"><b>Delete</b></button></form>
                 </td>
                 </tr>
            <?php } ?>
            </table> 
    </div>
</div>
</div>
