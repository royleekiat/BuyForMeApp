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
    <body>
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
        <div class="container">
            <div class="row">
                <form id="registerForm">
                    <div class="col-md-offset-3 col-lg-6">
                        <div class="well well-sm"><strong><span class="glyphicon glyphicon-asterisk"></span>Required Field</strong></div>
                        <div class="form-group">
                            <label for="InputName">Name</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="name" placeholder="Enter Name" required>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="InputEmail">Contact No.</label>
                            <div class="input-group">
                                <input type="number" maxlength="8" min="0" class="form-control"  name="contact" placeholder="Enter Contact No." required>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="InputEmail">Email</label>
                            <div class="input-group">
                                <input type="email" class="form-control" name="email" placeholder="Enter Email" required>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="InputEmail">Username</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="username" onkeyup="checkUsername();"  name="username" placeholder="Enter Username" autocomplete="off" required>
                                <span class="input-group-addon"><span  class="glyphicon glyphicon-asterisk"></span></span>
                            </div>
                            <p id="username_check"></p>
                        </div>
                        <div class="form-group">
                            <label for="InputEmail">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="InputEmail">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmPass" onkeyup="checkPass();" name="confirmpw" placeholder="Confirm Password" required>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                            </div>
                            <p id="passCheckResult"></p>
                        </div>
                        <div class="form-group">
                            <label for="InputName">User Profile</label>
                            <div class="input-group">
                                <input id="fileInput" type="file" class="form-control" name="file"/>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                            </div>
                        </div>
                        <input type="hidden" name="check" value="2"/>
                        <input type="submit" name="submit" id="submit" value="Submit" class="btn btn-info pull-right">
                        <div id="response"></div>
                    </div>
                </form>
                <br/><br/>
            </div>
        </div>
        <div class="login-footer">
            <div class="container text-center">
                Already have an account? <a href="login.php" class="btn btn-sm btn-default"> Login</a>
            </div>
        </div>
        <!-- JS Scripts-->
        <!-- jQuery Js -->
        <script src="assets/js/jquery-1.10.2.js"></script>
        <!-- Bootstrap Js -->
        <script src="assets/js/bootstrap.min.js"></script>
        <script>
                                    function checkUsername() {
                                        var minlength = 3;
                                        var username = $("#username").val();
                                        if (username.length >= minlength) {
                                            $.ajax({
                                                type: "POST",
                                                url: "processRegister.php",
                                                data: {'username': username, 'check': 1},
                                                dataType: "text",
                                                success: function (msg) {
                                                    $("#username_check").html(msg);
                                                }
                                            });
                                        }
                                    }
                                    function checkPass() {
                                        var pass = $("#password").val();
                                        var confirmPass = $("#confirmPass").val();
                                        if (pass === confirmPass) {
                                            $("#passCheckResult").html("Password Match!");
                                        } else {
                                            $("#passCheckResult").html("Password Do Not Match!");
                                        }
                                    }

                                    $("#registerForm").submit(function (e) {
                                        var checkPass = $("#passCheckResult").html();
                                        var checkUsername = $("#username_check").html();
                                        if (checkPass === "Password Match!" && checkUsername === "Username is available.") {
                                            $.ajax({
                                                type: "POST",
                                                url: "processRegister.php",
                                                data: $(this).serialize(),
                                                cache: false,
                                                success: function (html) {
                                                    $('#response').html(html);
                                                }
                                            });
                                        } else {
                                            alert(checkUsername);
                                            $('#response').html("Please ensure that username entered is available for use and password match.");
                                        }
                                        return false;
                                        e.preventDefault();
                                    });
        </script>
    </body>
</html>