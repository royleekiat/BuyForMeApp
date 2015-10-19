<?php
ob_start();
session_start();
include 'db.php';
?>
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
        <!--<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />-->
    </head>
    <body>
        <div>
            <div class="topBarDark">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-offset-4 col-sm-8 text-right">
                            <ul class="list-inline topDarkRight">
                                <?php
                                $message = 0;
                                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
                                    $username = $_SESSION['username'];
                                    $message = $_SESSION['chats'];
                                    $getName = mysqli_query($conn, "SELECT name FROM user WHERE username ='$username'");
                                    $array = mysqli_fetch_array($getName);
                                    $name = $array['name'];
                                    echo '<li><a href="notification.php">';
                                    $checkNotiQuery = mysqli_query($conn, "SELECT COUNT(*) AS count FROM notification WHERE receiver_id = '$username' AND status = 'unread'");
                                    if (mysqli_num_rows($checkNotiQuery) > 0) {
                                        $checkNotiRow = mysqli_fetch_array($checkNotiQuery);
                                        echo '<span class="badge" style="background-color: #b94a48;">' . $checkNotiRow["count"] . '</span>&nbsp;';
                                    }
                                    echo '<i class="fa fa-inbox fa-2" style="font-size: 2em; margin-top: 20%;"></i></a></li>';
                                    echo '<li><a href="userProfile.php"><i class="fa fa-user"></i> ' . $name . '</a></li>';
                                    echo '<li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>';
                                } else {
                                    ?>
                                    <li><a href="login.php"><i class="fa fa-lock"></i> Login</a></li>
                                    <li><a href="signup.php"><i class="fa fa-user"></i> Sign Up</a></li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="navbar navbar-default navbar-fixed-top second-navbar">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse" aria-expanded="false" aria-controls="navbar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="index.php">BUYFORME</a>
                    </div>
                    <div class="collapse navbar-collapse">
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="./">HOME</a></li>
                            <li class="divider-vertical"></li>
                            <li><a href="aboutus.php">ABOUT US</a></li>
                            <li class="divider-vertical"></li>
                            <li><a href="browse.php">BROWSE REQUEST</a></li>
                            <li class="divider-vertical"></li>
                            <li><a href="post_request.php">POST REQUEST</a></li>
                            <li class="divider-vertical"></li>
                            <li><a href="howitworks.php">HOW IT WORKS</a></li>
                            <li class="divider-vertical"></li>
                            <li><a href="faq.php">FAQ</a></li>
                            <li class="divider-vertical"></li>
                            <li><a href="viewAllMessages.php">Messages <span class="badge" style="background-color:#b94a48"><?php echo $message ?></span></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!--/. NAV TOP  -->
        <!-- JS Scripts-->
        <!-- jQuery Js -->
        <script src="assets/js/jquery-1.10.2.js"></script>
        <!-- Bootstrap Js -->
        <script src="assets/js/bootstrap.min.js"></script>


