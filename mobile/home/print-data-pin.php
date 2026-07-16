<head>
    <style>
        *{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: sans-serif;
}
body{
    height: 100%;
    background: rgb(205, 117, 10);
}

.login-page{
    width: 350px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    padding: 40px;
    box-shadow: 0 0 20px rgb(0, 0, 0, 0.2), 
    0 5px 5px 0 rgb(0, 0, 0, 0.24) ;
    background: #ffff;
}

.login-page h2{
    text-align: center;
    margin-bottom: 30px;
}

.input-box{
    margin-bottom: 10px;
}

.input-box input{
    width: 100%;
    font-size: 16px;
    padding: 15px;
    border: 0;
    background: #f2f2f2;
    outline: none;
}

.login-page button{
    width: 100%;
    font-size: 16px;
    padding: 15px;
    font-weight: bold;
    letter-spacing: 2px;
    background: rgb(205, 89, 7);
    border: 0;
    color: #ffff;
    text-transform: uppercase;
    transition: all ease .5s;
    cursor: pointer;
    margin-bottom: 10px;
}

.login-page button:hover{
    background: rgb(205, 89, 7,.8);
}

.login-page p{
    color: #b3b3b3;
    font-size: 12px;
}

.login-page p a{
    text-decoration: none;
    color: #ef7036;
}

.registration-form{
    display: none;
}

    </style>
</head>
<body>
    
    <div class="login-page">
        <form action="#" class="login-form">
            <h2>Login</h2>
            <div class="input-box">
                <input type="text" id="username" placeholder="Username" required>
            </div>
            <div class="input-box">
                <input type="password" id="password" placeholder="password" required>
            </div>
            <button type="submit">Login</button>
    
            <p class="message">Don't have an account? <a href="#">Register now</a></p>
        </form>
    
        <form action="#" class="registration-form">
            <h2>New Registration</h2>
            <div class="input-box">
                <input type="text" id="username" placeholder="Username" required>
            </div>
            <div class="input-box">
                <input type="email" id="email" placeholder="email" required>
            </div>
            <div class="input-box">
                <input type="password" id="password" placeholder="password" required>
            </div>
            <button type="submit">Register</button>
    
            <p class="message">Already have account? <a href="#">Sign in</a></p>
        </form>
    </div>
    
    <script src="js/main.js"></script>

<!--div class="page-content">
        
        <div class="">
            <div class="content">
                <div class="row">
                    <?php if(!empty($data)) : $pins=explode(",",$data->tokens); $sn=explode(",",$data->serial); ?>
                    <?php $network=$data->network; $datasize=$data->datasize; $loadpin="*347*383*3*3*PIN#"; if($datasize=="1.5GB"){$loadpin="*460*6*1# Then PIN or Text PIN to 460"; $checkBal="*131*4#";} ?>
                    <?php if($network == "AIRTEL"){$cardColor="#ff1a1a"; $cardLogo="airtel.png"; $textColor="#ffffff"; $checkBal="*140#";} 
                    else {$cardColor="#ffcc00"; $cardLogo="mtn.png"; $textColor="#000000"; $checkBal="*461*4#";} ?>
                    <?php for($i=0; $i<$data->quantity; $i++): ?>
                    <div class="col-6">
                    <div class="row" style="margin:3px;">
                            
                            <div class="col-4" style="margin:0; padding:0; background-color:<?php echo $cardColor; ?>; ">
                                <div class="text-dark" style="padding:10px;">
                                   
                                    <p style="margin-bottom:5px;"><img src="../../assets/images/icons/<?php echo $cardLogo; ?>" style="width:50px; height:50px;" /></p>
                                    <h6 style="color:<?php echo $textColor; ?>">DATA PIN</h6>
                                    <h6 style="color:<?php echo $textColor; ?>"><?php echo $datasize; ?></h6>
                                    <p style="margin-bottom:0; color:<?php echo $textColor; ?>;"><?php echo $sn[$i]; ?></p>
                                </div>
                            </div>
                            
                            <div class="col-8 bg-white" style="margin:0; padding:0; ">   
                                <div class="text-center" style="padding:10px;">
                                    
                                    <h6><?php echo strtoupper($data->business); ?></h6>
                                    <h4 style="background-color:#f2f2f2; border-radius:3rem; padding:7px;"><?php echo $pins[$i]; ?></h4>
                                    <p style="margin-bottom:0;"><b>Load <?php echo $loadpin; ?></b> <b>Bal:   <?php echo $checkBal; ?></b></p>
                                    <p>Powered By: <?php echo $sitename; ?></p>
                                </div>
                            </div>
                    </div>
                    </div>
                         
                    <?php endfor; endif; ?>
                   
                </div>
                
            </div>

        </div>

</div>
<script>window.print();</script>

