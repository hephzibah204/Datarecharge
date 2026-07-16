<?php session_start(); if(isset($_SESSION['sysId'])){header("Location:dashboard/");} require_once("dashboard/includes/auto_loader.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>

    
    
    <meta name="ROBOTS" content="NOINDEX,NOFOLLOW" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png" />
    <link href="../assets/vendor_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../assets/vendor_components/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="../assets/vendor_components/sweetalert/sweetalert.css" rel="stylesheet" />
    <style>
        body{background:linear-gradient(#000099,#3333ff); background-image:url("../assets/img/bg/cover7.jpg");   background-position:center center;-webkit-background-size:cover;-moz-background-size:cover;
            background-size:cover;-o-background-size:cover;background-repeat:no-repeat;background-attachment:fixed}
            #loginContainerContent{margin-top:20vh;margin-bottom:10px;text-align:left;color:#000;font-weight:600;}
            .brand_logo_container{margin-top:-120px; margin-bottom: 20px; padding:10px;}
            .brand_logo{height:150px;width:155px;text-align:center;}
            .position{max-width:400px;height:auto;margin:auto;position:relative}
            .user_card{
                background:rgba(255,255,255,0.9); padding:30px;  
                box-shadow:0 4px 8px 0 rgba(0,0,0,.2),0 6px 20px 0 rgba(0,0,0,.19);
                -webkit-box-shadow:0 4px 8px 0 rgba(0,0,0,.2),0 6px 20px 0 rgba(0,0,0,.19);
                -moz-box-shadow:0 4px 8px 0 rgba(0,0,0,.2),0 6px 20px 0 rgba(0,0,0,.19);
                border-radius:2rem; color:#000;
            }
                h3{color:#000;text-align:left;font-weight:600}
                .loginbtn{border-radius:1rem;}
                .form-control{border-radius:1rem;}
            #pindiv{display:none;}
    </style>
</head>

<body >

<div class="container">
<div id="loginContainerContent">
            <div  class="position user_card">
            <div>
                <div align="center" class="brand_logo_container"><img src="../assets/img/adminicon2.png" alt="Logo" class="brand_logo" /></div>
                <h5 class="text-center"><b>Welcome Back!</b></h5>
                <h3 class="text-center">ADMIN LOGIN</h3>
                
                <hr/>
                <form id="login-form" method="post">
                    <div class="text-center" id="logindiv">
                    
                        <div class="form-group mb-3">
                        <input type="text" name="username"  maxlength="50" class="form-control" placeholder="Username" required />
                        </div>
                        
                        <div class="form-group mb-3">
                        <input type="password" name="password" maxlength="50" class="form-control" placeholder="Password" required />
                        </div>

                    </div>

                    <div class="text-center" id="pindiv">
                    
                        <div class="form-group mb-3">
                        <label class="mb-2"><b>Enter You Login Pin To Continue</b></label>
                        <input type="password" name="loginpin" id="loginpin" maxlength="50" class="form-control" placeholder="Login Pin" />
                        </div>

                    </div>
                                            
                    <div class="form-group">            
                        <button class="btn loginbtn btn-primary btn-lg btn-block" type="submit" name="login" id="loginbtn"><i class="fa fa-sign-in" aria-hidden="true"></i> Login</button>               
                    </div>  
                    
                </form>
    </div>
    </div>
</div>
</div>

<script src="../assets/vendor_components/jquery-3.3.1/jquery-3.3.1.min.js"></script>
<script src="../assets/vendor_components/sweetalert/sweetalert.min.js"></script>
<script>
    $("document").ready(function(){

        //Login Check For Admininstrator
        $('#login-form').submit(function(e){
		e.preventDefault()
		$('#loginbtn').addClass("disabled");
		$('#loginbtn').html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Processing ...');
		
		$.ajax({
			url:'dashboard/includes/route.php?login',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
                
                resp = JSON.parse(resp);

				if(resp.status == "success"){
					swal('Alert!!',"Login Succesfull","success");
					setTimeout(function(){
						location.replace('dashboard/')
					},1000)
				}else if(resp.status == "invalid"){
                    if($("#loginpin").val() == ""){swal('Alert!!',"Incorrect Username or Password","error");}
                    else{swal('Alert!!',"Incorrect Pin Provided","error");}
					
                    $('#loginbtn').removeClass("disabled");
		            $('#loginbtn').html('<i class="fa fa-sign-in" aria-hidden="true"></i> Login');
				}
                else if(resp.status == "blocked"){
					swal('Alert!!',"Sorry, Your Account Has Been Blocked, Please Contact Admin","error");
                    $('#loginbtn').removeClass("disabled");
		            $('#loginbtn').html('<i class="fa fa-sign-in" aria-hidden="true"></i> Login');
				}
                else if(resp.status == "pinrequired"){
					$('#loginbtn').removeClass("disabled");
		            $('#loginbtn').html('<i class="fa fa-sign-in" aria-hidden="true"></i> Login');
                    $('#logindiv').hide();
                    $('#pindiv').show();
				}
                else{
					swal('Alert!!',"Unknow Error, Please Contact Admin","error");
                    $('#loginbtn').removeClass("disabled");
		            $('#loginbtn').html('<i class="fa fa-sign-in" aria-hidden="true"></i> Login');
				}
			},
			error:function(resp){
			        
			        swal('Alert!!',"Server Error, Please Contact Admin","error");
                    $('#loginbtn').removeClass("disabled");
		            $('#loginbtn').html('<i class="fa fa-sign-in" aria-hidden="true"></i> Login');
			}
		})
	})

    });
</script>
</body>

</html>