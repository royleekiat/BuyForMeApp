<?php

session_start();
include 'db.php';
$username = filter_input(INPUT_POST, 'username');
$password = filter_input(INPUT_POST, 'password');
$dateLogin = date("Y-m-d");

$authenticate = mysqli_query($conn, "SELECT * FROM user WHERE username ='$username' AND password = '$password'");
$msg = array();
if (mysqli_num_rows($authenticate) == 1) {
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = $username;
    $chats = mysqli_query($conn, "SELECT count(*) as number FROM chat WHERE receiver_id ='$username' AND seen = 'false'");
    while($row = mysqli_fetch_array($chats)){
        $_SESSION['chats'] = $row['number'];
    }
    $userQuery = "UPDATE user SET last_login = '$dateLogin' WHERE username = '$username'";
    $updateUser = mysqli_query($conn, $userQuery);
    $msg = array("status" => "success", "msg" => "<br/><div class='alert alert-success role='alert'><center>Successfully logged in. We will redirect you back to the homepage</center></div>");
} else {
    $msg = array("status" => "error", "msg" => "<br/><div class='alert alert-danger role='alert'><center>Wrong username or password</center></div>");
}
echo json_encode($msg);

