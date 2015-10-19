<?php
session_start();
    include 'db.php';
    $message = filter_input(INPUT_POST, 'message');
    $receiver = filter_input(INPUT_POST, 'receiver');
    $sender = filter_input(INPUT_POST, 'sender');
    if($sender == $_SESSION['username']){
        $user= $receiver;
    }else{
        $user= $sender;
    }
    $seen = false;
    date_default_timezone_set('Asia/Singapore');
    $date = date("Y-m-d H:i:s");
    $insertRequest = mysqli_query($conn, "INSERT INTO chat (sender_id, receiver_id, message, seen, datetime) VALUES ('$sender','$receiver','$message','false','$date')");
    if($insertRequest){
        header("Location:viewAllMessages.php?selectedUser=".$user);
    }