<?php
session_start();
include_once "zen/conn.php";
require "zen/controller.php";
validate::checkSession();

$user_id = $_SESSION["user_id"];
$user = Control::fetchDetail($conn, $user_id);
$fetchDiscount = Control::fetchDiscount($conn, "Airtime");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="css/service.css">
  <link rel="stylesheet" href="css/fontawsome/css/all.min.css">
  <link rel="stylesheet" href="css/bootstrap-icon/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css">
  <link rel="manifest" href="zn/manifest.json">
  <title>Bulk SMS</title>
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
    body{
      font-family: "Teko", sans-Serif;
    }
    <?php include "css/animation.css"; ?>
  </style>
</head>
<body class="<?php echo ($user["mode"] == "On" ? "active" : ""); ?>">
  <div class="head">
    <i class="fas fa-chevron-left" onclick="goBack()"></i>
    <span>Send Bulk SMS</span>
    <?php include "inc/user.php"; ?>
  </div>
  
  <form class="serviceBox" id="bulk-sms-form">
    <div class="inputs">
      <span>FROM: [SENDER NAME]</span>
      <input type="text" name="sender" id="sender" placeholder="Fluterpay" oninput="checkValue('sender', 'sender-err', '', 'empty', 'Please enter sender name', '')">
      <b id="sender-err"></b>
    </div>

    <select name="type" id="type"  onchange="checkValue('type', 'type-err', '', 'empty', 'Please select type', '')" hidden>
     <option value="">-- Select Type --</option>
     <option value="Normal SMS">Normal SMS</option>
    </select>

    <div class="inputs" id="type:inputs">
      <span>Select Type</span>
      <div class="drop-btn" id="type:1" onclick="addActive(this, 'type-detail')">
        <span>-- Select Type --</span>
        <i class="fas fa-chevron-down"></i>
      </div>
      
      <div class="drop-detail one" id="type-detail">
          <div class="drop-detail-divs type" onclick="removeDrop('type', 'Normal SMS', this, 'drop-detail-divs.type', 'type:1')" onmousemove="createRipple(event, true, 'black')">
            <i class="fas fa-check"></i>
            <span>Normal SMS</span>
          </div>
      </div>
      
      <b id="type-err"></b>
    </div>

    <div class="inputs">
        <span>TO [RECEIPENT]</span>
        <textarea type="text" name="phone-numbers" id="phone-numbers" oninput="checkValue('phone-numbers', 'phone-numbers-err', '', 'empty', 'Please enter phone numbers', '')"></textarea>
        <b id="phone-numbers-err"></b>
        <span class="validator" style="color: #666; font-size: 12px; font-weight: 200;">Type or Paste up to 10,000 phone numbers here (080... or 234)[Separate with SPACE( )][NO COMMA(,)]!</span>
    </div>

    <div class="inputs">
        <span>MESSAGE</span>
        <textarea type="text" name="message" id="message" oninput="checkValue('message', 'message-err', '', 'empty', 'Please enter message', '')"></textarea>
        <b id="message-err"></b>
    </div>

    <input type="tel" name="amount" id="amount" placeholder="1000" hidden>
    <input type="text" name="type-name" id="type-name" placeholder="" hidden>
    <input type="number" name="count" id="count" placeholder="" hidden>

    <div class="inputs">
      <span>Amount To Pay</span>
      <input type="tel" class="disabled" name="discount" id="discount" placeholder="0" disabled>
      <b id="discount-err"></b>
    </div>
    

    <?php include "inc/select-method.php"; ?>
    <?php include "inc/bulk-sms.php"; ?>

    <button class="showPin" type="button" onclick="showPin()">PROCEED <i class="fas fa-arrow-right"></i></button>
  </form>
  
  <?php include "inc/reset-pin.php"; ?>
  
  <script>
   <?php include "zn/msg.js"; ?>
   <?php include "zn/other.js"; ?>

   function getId(idName){
     return document.getElementById(idName);
   }

   const from = getId("sender");
   const type = getId("type");
   const phoneNumbers = getId("phone-numbers");
   const msg = getId("message");
   const amount= getId("amount");
   const discount = getId("discount");

   phoneNumbers.addEventListener("input", ()=> {
    const separatedNumbers = phoneNumbers.value.split(" ");
     if(phoneNumbers.value !== ""){
       const cal = 5 * separatedNumbers.length;
       discount.value = cal;
       amount.value = cal;
       getId("count").value = separatedNumbers.length;
     }else{
      discount.value = "";
     }
   });

   type.addEventListener("input", ()=> {
     getId("type-name").value = type.options[type.selectedIndex].text;
   });

   function showPin(){
     if(from.value.length < 3 || type.value === "" || phoneNumbers.value === "" || msg.value == ""){
       from.dispatchEvent(new Event("input"));
       type.dispatchEvent(new Event("change"));
       phoneNumbers.dispatchEvent(new Event("input"));
       msg.dispatchEvent(new Event("input"));
     }else{
       getId("input-sn").innerHTML = from.value;
       getId("input-t").innerHTML = type.value;
       getId("input-m").innerHTML = msg.value;
       getId("input-a").innerHTML = "NGN " + (amount.value).toLocaleString();
       document.querySelector(".pin-body").style.display = "flex";
     }
   }

   const bulk_sms_from = getId("bulk-sms-form");
    function payBulkSms(payB){
 
      let formerTxt = payB.innerText;
      showP(payB);
      fetch("zen/auto.php?i=BuyBulkSms", {
        method: "POST",
        body: new FormData(bulk_sms_from),
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
      $low = 1;
      if($int < $low){
          echo '
          <script>
             function redir(){
                 window.location.href = "dashboard.php";
             }
            showMessage("bi bi-info-circle", "You have insufficent balance plesae fund your wallet and try again", "ver", redir, redir);
          </script>
          ';
      }
    ?>
  </body>
</html>