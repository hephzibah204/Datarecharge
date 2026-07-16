<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    
    <!-- Bootstrap 5 CDN-Import: -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Light-Theming: -->
    <link id="mainStyle" rel="stylesheet" href="assets/css/style.css">

    <!-- Dark-Theming: -->
    <!-- Uncomment the line below to use dark theming. Don't forget to comment the line above.-->
    <!-- <link rel="stylesheet" href="assets/css/style_dark.css"> -->
    <!-- this also works automatically by clicking the theme_button. -->
</head>
<body>

    <div class="page-content d-flex align-items-center">

        <div class="container d-flex justify-content-center">

            <div class="col-12 col-sm-10 col-md-8 col-lg-7 col-xl-6 col-xxl-5">

                <div class="auth-card">

                    <div class="logo-area">

                        <!-- Light-Theming Logo: -->
                        <!--img id="header_logo" class="logo" src="assets/img/logo.png" />

                        <!-- Dark-Theming: -->
                        <!-- <img class="logo" src="assets/img/logo_dark.png" /> -->

                    </div>

                    <!--h5 class="auth-title"><?php echo $sitename; ?></h5-->
                
                <div class="user-top text-center mt-n5">
                    <img src="../../assets/images/icons/user-smile.png" style="border-radius:5rem; width:100px; height:100px; margin-right:10px;">
                    <h1 class="font-25 mt-2 mb-3 color-highlight">LOGIN</h1>
                    <h6 class="mb-1 mt-2 color-highlight" id="accountname">Welcome To <?php echo $sitename; ?></h6>
                </div>

                    <div id="logininputes">

                    <!-- Login-Form-->
                    <form id="login-form" method="post">

                        <div class="mb-2 mt-5" id="phonediv">
                            <input type="number" class="form-control auth-input" id="phonelogin" name="phone" placeholder="Phone Number" required />
                            <label for="phone" class="color-highlight">Phone</label>
                                        <em>(required)</em>
                        </div>

                        <div class="mb-3">
                            <input type="password" class="form-control auth-input" id="passwordlogin" name="password" placeholder="Password" required />
                            <label for="password" class="color-highlight">Password</label>
                                        <em>(required)</em>
                        </div>

                        <button type="submit" id="submit-btn" class="btn auth-btn mt-2 mb-4">Login</button>

                    </form>

                    <p class="text mb-1"><a href="../recovery/" class="text-link">Forgot</a> Password?</p>
                    <p class="text mb-4">Create new  <a href="../register/" class="text-link">Account?</a></p>

                </div>
            </div>
        </div>
    </div>

    <button id="theme_button" class="btn btn-theme" onclick="onThemeChange()">
        <i id="theme_icon" class="fas fa-moon"></i>
    </button>


    <script type="text/javascript">
        function onThemeChange() {
            let cssStyleSheet = document.getElementById("mainStyle");
            let path = (cssStyleSheet.href).substring((cssStyleSheet.href).length-9, (cssStyleSheet.href).length);
            if(path === "style.css") {
                cssStyleSheet.href = "assets/css/style_dark.css";
                document.getElementById("header_logo").src = "assets/img/logo_dark.png";
                document.getElementById("theme_icon").className = "fas fa-sun";
            } else {
                cssStyleSheet.href = "assets/css/style.css";
                document.getElementById("header_logo").src = "assets/img/logo.png";
                document.getElementById("theme_icon").className = "fas fa-moon";
            }
        }
    </script>


    <!-- Bootstrap 5 JS-Bundle CDN import: -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            //Save Phone Number
            checkIfPhoneNumberSaved();

            //Enable Form Input
            $("#phonelogin").click(function() {
                $(this).removeAttr("readonly");
            });
            $("#passwordlogin").click(function() {
                $(this).removeAttr("readonly");
            });

            //Login Form
            $('#login-form').submit(function(e) {
                e.preventDefault();
                $('#submit-btn').removeClass("btn-primary");
                $('#submit-btn').addClass("btn-secondary");
                $('#submit-btn').html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Processing ...');

                $.ajax({
                    url: '../home/includes/route.php?login',
                    data: new FormData($(this)[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    method: 'POST',
                    type: 'POST',
                    success: function(resp) {
                        resp = JSON.parse(resp);
                        if (resp.status == "success") {
                            swal('Alert!!', "Login Successful", "success");
                            setTimeout(function() {
                                location.replace('../home/')
                            }, 1000);
                        } else {
                            swal('Alert!!', resp.msg, "error");
                        }
                        $('#submit-btn').removeClass("btn-secondary");
                        $('#submit-btn').addClass("btn-primary");
                        $('#submit-btn').html("<b>Login</b>");
                    }
                });
            });
        });

        function checkIfPhoneNumberSaved() {
            $phone = atob(unescape(getCookie("loginPhone")));
            $name = atob(unescape(getCookie("loginName")));
            if ($phone != null && $phone != "") {
                let msg = '<p class="mb-3"><a href="javascript:showNumber();"><b class="text-primary">Login With Another Account?</b></a></p>';
                $("#accountname").after(msg);
                $("#accountname").append(" " + $name + "!");
                $("#phonediv").hide();
                $("#phonelogin").val($phone);
            }
        }

        function showNumber() {
            $("#phonediv").show();
        }

        function getCookie(cname) {
            var name = cname + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1);
                if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
            }
            return "";
        }
    </script>

</body>
</html>
