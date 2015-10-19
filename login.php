<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>BUYFORME</title>
        <!-- Bootstrap Styles-->
        <link href="assets/css/bootstrap.css" rel="stylesheet" />
        <!-- Custom Styles-->
        <link href="assets/css/custom-styles.css" rel="stylesheet" />
        <!-- FontAwesome Styles-->
        <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <!-- Google Fonts-->
        <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
        <!-- JS Scripts-->
        <!-- jQuery Js -->
        <script src="assets/js/jquery-1.10.2.js"></script>
        <!-- Bootstrap Js -->
        <script src="assets/js/bootstrap.min.js"></script>
    </head>
    <body id="login-page">
        <!-- Fixed navbar -->
        <nav class="navbar navbar-inverse login-navbar">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="./">BUYFORME</a>
                </div>
            </div>
        </nav>

        <div class="login-content" style="min-height: 508px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3" style="text-align: center;">
                        <h2> <i class="fa fa-user"></i> Login</h2>

                        <form id="loginForm">
                            <div class="form group">
                                <label>Username</label>
                                <input type="text" name="username" value="" class="form-control" placeholder="User ID" autofocus required>
                            </div>
                            <div class="form group">
                                <label>Password</label>
                                <input type="password" name="password" value=""  class="form-control" placeholder="Password" required>
                            </div>
                            <br/>
                            <button class="btn btn-theme btn-block" name="submit" type="submit"><i class="fa fa-lock"></i> LOGIN</button>
                            <a class="btn btn-theme btn-block" href="signup.php" role="button"><i class="fa fa-check"></i> SIGN UP</a>
                            <div id="response"></div>
                            <br/>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <!-- Begin page content -->
        <!--        <div id="login-page">
                    <div class="container">
                        <form class="form-login"  id="loginForm">
                            <h2 class="form-login-heading">login now</h2>
                            <div class="login-wrap">
                                <input type="text" name="username" value="" class="form-control" placeholder="User ID" autofocus required>
                                <br>
                                <input type="password" name="password" value=""  class="form-control" placeholder="Password" required>
                                <br>
                                <button class="btn btn-theme btn-block" name="submit" type="submit"><i class="fa fa-lock"></i> LOGIN</button>
                                <div id="response"></div>
                            </div>
                        </form>
                    </div>
                </div>-->
        <div class="login-footer">
            <div class="container text-center">
                Don't have an account yet? <a href="signup.php" class="btn btn-sm btn-default"> Register</a>
            </div>
        </div>
        <script>
            $("#loginForm").submit(function (e) {
                $.ajax({
                    type: "POST",
                    url: "processLogin.php",
                    data: $(this).serialize(),
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        if (data['status'] === "success") {
                            $('#response').html(data['msg']);
                            setTimeout(function () {
                                window.location = "index.php";
                            }, 1600);
                        } else {
                            $('#response').html(data['msg']);
                        }
                    }
                });
                return false;
                e.preventDefault();
            });
        </script>
    </body>
</html>
