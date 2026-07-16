<?php
session_start();
include_once "api/a/a/conn.php";
require "zen/controller.php";
validate::checkSession();

$user_id = $_SESSION["user_id"];
$user = Control::fetchDetail($conn, $user_id);
$fetchDiscount = Control::fetchDiscount($conn, "Electricity");
 
 $stats = "On";
 $ser = "Betting";
 $sql = "SELECT * FROM service_id WHERE service = ? AND status = ? ORDER BY ab ASC";
 $stmt = $conn->prepare($sql);
 $stmt->bind_param("ss", $ser, $stats);
 $stmt->execute();
 $query = $stmt->get_result();
 
 $allF = [];
 if($query->num_rows > 0){
   while($row = $query->fetch_assoc()){
     $allF[] = $row;
   }
 }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/service.css">
  <link rel="stylesheet" href="css/fontawsome/css/all.min.css">
  <link rel="stylesheet" href="css/bootstrap-icon/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css">
  <link rel="manifest" href="zn/manifest.json">
  <title>Betting</title>
  <style>
    @font-face {
      font-family: 'Mitr';
      src: url('css/mitr/Mitr-Regular.ttf') format('truetype'); /* Adjust the path as needed */
      font-weight: normal;
      font-style: normal;
    }
    <?php  
      $site = Control::site($conn);
    ?>
    :root{
      --site-clr: <?php echo $site["site_color"] ?>;
      --bg: <?php echo $site["site_background"]; ?>;
    }
  </style>
</head>
<body class="<?php echo ($user["mode"] == "On" ? "active" : ""); ?>">
  <div class="head">
    <i class="fas fa-chevron-left" onclick="goBack()"></i>
    <span>Fund Betting</span>
    <?php include "inc/user.php"; ?>
  </div>

  <form class="serviceBox" id="betting-form">
    <div class="inputs" hidden>
      <span>Select Provider</span>
      <select name="provider" id="provider" onchange="checkValue('provider', 'provider-err', '', 'empty', 'Select Provider', '')">
        <option value="">-- Select Provider --</option>
        <?php 
         if(!empty($allF)){
           foreach ($allF as $a){
             echo '
             <option value="'.$a["provider_id"].'">'.$a["provider"].'</option>
             ';
           }
         }
        ?>
      </select>
    </div>

    <div class="inputs" id="provider:inputs" style="margin-top: 0px;">
      <span>Select Provider</span>
      <div class="drop-btn" id="provider:1" onclick="addActive(this, 'provider-detail')">
        <span>-- Select Provider --</span>
        <i class="fas fa-chevron-down"></i>
      </div>
      
     <!---------------------------------------------------- 
     <!---- Html of details
     ---------------------------------------------------->
      <div class="drop-detail" id="provider-detail">
          <?php 
           if(!empty($allF)){
             foreach ($allF as $a){
               echo '
               <div class="drop-detail-divs provider" onclick="removeDrop(`provider`, `'.$a["provider_id"].'`, this, `drop-detail-divs.provider`, `provider:1`)">
                 <i class="bi bi-infinity"></i>
                 <span>'.$a["provider"].'</span>
               </div>
               ';
             }
           }
          ?>
      </div>
     <!---------------------------------------------------> 
     <!---------------------------------------------------> 
      
      <b id="provider-err"></b>
    </div>


    <input type="text" name="betting-name" id="betting-name" hidden>
    <input type="text" name="provider-name" id="provider-name" hidden>
    

    <div class="inputs" id="meter-no:inputs">
      <span>Betting No</span>
      <input type="number" name="betting-no" id="betting-no" placeholder="83756890278" maxlength="11" oninput="checkValue('betting-no', 'betting-no-err', 8, 'length', 'Invalid betting minimum(8 digits)', '')">
      <b id="betting-no-err"></b>
    </div>

    <div class="inputs" id="amount:inputs">
      <span>Amount</span>
      <input type="number" name="amount" id="amount" placeholder="1000" oninput="checkValue('amount', 'amount-err', 100, 'value', 'Invalid amount minimum(NGN 1000)', '')">
      <b id="amount-err"></b>
    </div>
    
    <div class="inputs" id="amount:inputs">
      <span>Amount to pay</span>
      <input type="number" class="disabled" name="discount" id="discount" placeholder="1000" disabled>
      <b id="amount-err"></b>
    </div>

    <?php include "inc/select-method.php"; ?>
    
     <?php include "inc/betting.php";?>
    
    <button type="button" class="showPin" onclick="showPin()">PROCEED <i class="fas fa-arrow-right"></i></button>
  </form>
  <script>
    <?php include "zn/msg.js"; ?>

    function getId(idName){
        return document.getElementById(idName);
    }

    const form = getId("betting-form");
    const provider = getId("provider");
    const bettingNo = getId("betting-no");
    const bettingName = getId("betting-name");
    const amount = getId("amount");
    const discount = getId("discount");


    function showPin(){
      if(provider.value === "" || bettingNo.value.length < 7 || amount.value < 100){
        provider.dispatchEvent(new Event("change"));
        bettingNo.dispatchEvent(new Event("input"));
        amount.dispatchEvent(new Event("input"));
      }else if(bettingName.value === ''){
        showMessage("bi bi-exclamation-circle", "Please verify betting name before proceeding", "err");
      }else{
        document.getElementById("provider-name").value = provider.options[provider.selectedIndex].text;
        document.getElementById("input-p").innerHTML = provider.options[provider.selectedIndex].text;
        document.getElementById("input-nm").innerHTML = bettingName.value;
        document.getElementById("input-n").innerHTML = bettingNo.value;
        document.getElementById("input-a").innerHTML = "NGN " + parseFloat(amount.value).toLocaleString();
        document.querySelector(".pin-body").style.display = "flex";
      }
    }

    bettingNo.addEventListener("input", ()=> {
        if(bettingNo.value.length > 6){
            if(provider.value !== ""){
                makeFetch();
            }else{
                showMessage("bi bi-exclamation-circle", "Please select provider", "err");
            }
        }
    });

    function makeFetch(){
        showChecking(getId("betting-no-err"), "Verifying betting name");
        fetch("zen/auto.php?i=VerifyBetting", {
            method: "POST",
            body: new FormData(form),
        })
        .then(response => response.json())
        .then(data => {
            if(data.status == true){
             getId("betting-name").value = data.name;
             showSuccess(getId("betting-no-err"), data.name);
           }else{
             getId("betting-name").value = "";
             showError(getId("betting-no-err"), data.msg);
           } 
           //alert(data);
        });
    }

    function payBet(payB){
      let formerTxt = payB.innerText;
      showP(payB);
      fetch("zen/auto.php?i=FundBetting", {
        method: "POST",
        body: new FormData(form),
      })
      .then(response => response.text())
      .then(data => {
        if(data.includes("<table>")){
          const newD = document.createElement("div");
          newD.innerHTML = data;
          closePin("pin-body");
          document.body.appendChild(newD);
        }else{
          showMessage("bi bi-x-circle", data, "err");
        }
        removeP(payB, formerTxt);
      })
      .catch(error => alert(error));
    }
  </script>
  <?php
      $int = (INT)$user["bal"];
      $low = 100;
      if($int < $low){
          echo '
          <script>
             function redir(){
                 window.location.href = "dashboard.php";
             }
            showMessage("bi bi-info-circle", "Wallet below minimum vending amount NGN '.number_format($int).'", "ver", redir, redir);
          </script>
          ';
      }
    ?>
</body>
</html>