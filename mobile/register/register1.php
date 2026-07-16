<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link id="mainStyle" rel="stylesheet" href="../login/assets/css/style.css">
</head>
<body>
    <div class="page-content d-flex align-items-center">
        <div class="container d-flex justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-7 col-xl-6 col-xxl-5">
                <div class="auth-card">
                    <div class="user-top text-center mt-n5">
                        <img src="../../assets/images/icons/user-smile.png" style="border-radius:5rem; width:100px; height:100px; margin-right:10px;">
                        <h1 class="font-25 mt-2 mb-3 color-highlight">REGISTER</h1>
                        <h6 class="mb-1 mt-2 color-highlight">Create Account On <?php echo $name; ?></h6>
                    </div>
                    <div id="registerinputs">
                        <form id="register-form" method="post">
                            <div class="mb-2 mt-5">
                                <input type="text" class="form-control auth-input" id="fname" name="fname" placeholder="First Name" required />
                                <label for="fname" class="color-highlight">First Name</label>
                            </div>
                            <div class="mb-2">
                                <input type="text" class="form-control auth-input" id="lname" name="lname" placeholder="Last Name" required />
                                <label for="lname" class="color-highlight">Last Name</label>
                            </div>
                            <div class="mb-2">
                                <input type="number" class="form-control auth-input" id="phone" name="phone" placeholder="Phone Number" required />
                                <label for="phone" class="color-highlight">Phone</label>
                            </div>
                            <div class="mb-2">
                                <input type="email" class="form-control auth-input" id="email" name="email" placeholder="Email Address" required />
                                <label for="email" class="color-highlight">Email</label>
                            </div>
                            <div class="mb-2">
                                <select class="form-control auth-input" id="state" name="state" required>
                                    <option value="">Select State</option>
                                    <option value="Lagos">Lagos</option>
                                    <option value="Abuja FCT">Abuja FCT</option>
                                    <option value="Ogun">Ogun</option>
                                    <option value="Rivers">Rivers</option>
                                    <option value="Kano">Kano</option>
                                    <option value="Oyo">Oyo</option>
                                    <option value="Kaduna">Kaduna</option>
                                    <option value="Delta">Delta</option>
                                    <option value="Edo">Edo</option>
                                    <option value="Anambra">Anambra</option>
                                    <option value="Enugu">Enugu</option>
                                    <option value="Abia">Abia</option>
                                    <option value="Imo">Imo</option>
                                    <option value="Akwa Ibom">Akwa Ibom</option>
                                </select>
                                <label for="state" class="color-highlight">State</label>
                            </div>
                            <input type="hidden" name="account" value="user" />
                            <div class="mb-2">
                                <input type="password" class="form-control auth-input" id="password" name="password" placeholder="Password" required />
                                <label for="password" class="color-highlight">Password</label>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control auth-input" id="transpin" name="transpin" placeholder="Transaction PIN" required />
                                <label for="transpin" class="color-highlight">Transaction PIN</label>
                            </div>
                            <button type="submit" id="submit-btn" class="btn auth-btn mt-2 mb-4">Register</button>
                        </form>
                        <p class="text mb-4">Already have an account? <a href="../login/" class="text-link">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#register-form').submit(function(e) {
                e.preventDefault();
                $('#submit-btn').removeClass("btn-primary");
                $('#submit-btn').addClass("btn-secondary");
                $('#submit-btn').html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Processing ...');
                $.ajax({
                    url: '../home/includes/route.php?register',
                    data: new FormData($(this)[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    method: 'POST',
                    type: 'POST',
                    success: function(resp) {
                        resp = JSON.parse(resp);
                        if (resp.status == "success") {
                            swal('Alert!!', "Registration Successful", "success");
                            setTimeout(function() {
                                location.replace('../login/')
                            }, 1000);
                        } else {
                            swal('Alert!!', resp.msg, "error");
                        }
                        $('#submit-btn').removeClass("btn-secondary");
                        $('#submit-btn').addClass("btn-primary");
                        $('#submit-btn').html("<b>Register</b>");
                    }
                });
            });
        });
    </script>
</body>
</html>
