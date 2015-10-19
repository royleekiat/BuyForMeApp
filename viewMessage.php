<?php

session_start();
include 'db.php';
$sender = filter_input(INPUT_GET, 'sender');
$receiver = filter_input(INPUT_GET, 'receiver');
if ($sender == $_SESSION['username']) {
    $sent_by = $sender;
    $sent_to = $receiver;
} else {
    $sent_to = $sender;
    $sent_by = $receiver;
}
$update = mysqli_query($conn, "UPDATE chat SET seen = true WHERE sender_id ='$receiver' AND receiver_id='$sender'");
if($update){
    $numbers = mysqli_query($conn, "SELECT count(*) as number FROM chat WHERE receiver_id ='$_SESSION[username]' AND seen = 'false'");
    while($fetchNum = mysqli_fetch_array($numbers)){
        $_SESSION['chats'] = $fetchNum['number'];
    }
}
$chats = mysqli_query($conn, "SELECT * FROM chat WHERE (receiver_id ='$receiver' AND sender_id='$sender') OR (receiver_id ='$sender' AND sender_id='$receiver')order by datetime desc");
$msg1 = "";
while ($row = mysqli_fetch_array($chats)) {
    $sent_by = $row['sender_id'];
    $userQuery = mysqli_query($conn, "SELECT profile_img FROM user WHERE username = '$sent_by'");
    $userResult = mysqli_fetch_array($userQuery);
    $msg1.="<p align='right' style='font-size:12px; color:#989898'> Sent on = " . $row['datetime'] . +"</p>";
    $msg1.="<img src='assets/user_img/$userResult[profile_img]' id='chatPic'>";
    $msg1.="<br><br><p align='left' style='font-weight:bold;color:#800000' id='chatName'>" . $sent_by."</p>";
    $msg1.="<br><br><p align='left' id='chatMessage'>" . $row['message'] . "</p><hr>";
}
$msg1.="<form method='post' class='form-horizontal' id='messageForm' action='processMessage.php'>
                <div class='col-md-offset-3 col-md-6'>
                    <div class='form-group'>
                            <textarea class='form-control' name='message' rows='5' id='desc' required></textarea>
                    </div>
                    <input type='hidden' name='sender' value=$sent_by>
                    <input type='hidden' name='receiver' value=$sent_to>
                    <input type='submit' name='submit' id='submit' value='Send Message' class='btn btn-info pull-right'>
                </div>
            </form>
            <script>$('#messageForm').submit(function (e) {
                $.ajax({
                    type: 'POST',
                    url: 'processMessage.php',
                    data: $(this).serialize(),
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        if (data['status'] === 'success') {
                                setTimeout(function () {
                                window.location ='viewAllMessages.php?selectedUser='$sent_to;
                            }, 10);
                        }
                    }
                });
                return false;
                e.preventDefault();
            });
            </script>";
$msg = array("msg" => $msg1);
echo json_encode($msg);
?>
