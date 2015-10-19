<?php
include 'header.php';
$username = $_SESSION['username'];
$senders = mysqli_query($conn, "SELECT distinct sender_id as id FROM chat WHERE receiver_id ='$username' order by datetime desc");
$receivers = mysqli_query($conn, "select distinct receiver_id as id from chat where sender_id ='$username' and receiver_id not in (SELECT distinct sender_id FROM chat WHERE receiver_id ='$username')order by datetime desc");
$user = filter_input(INPUT_GET, 'selectedUser');
?>
<div class="body-content">
    <br/><br/>
    <div class="container">

        <div class="row">
            <center>
                <h4>View All Messages</h4>
            </center>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p>
                            <div class="col-md-3">
                            <?php
                            if (mysqli_num_rows($senders) > 0) {
                                
                                while ($receiverList = mysqli_fetch_array($receivers)) {
                                    ?>
                                    
                                    <a href='#message'id="user_<?php echo $receiverList['id'] ?>"  onclick='readMessage(this);'receiver="<?php echo $receiverList['id'] ?>"sender="<?php echo $username ?>"
                                            data-toggle='modal'>
                                        <!--<div class="col-md-3">-->
                                            <div id='profileBox'>
                                        
                                    <?php
                                    $userQuery = mysqli_query($conn, "SELECT profile_img FROM user WHERE username = '$receiverList[id]'");
                                    $userResult = mysqli_fetch_array($userQuery);
                                    $chats = mysqli_query($conn, "SELECT count(*) as number FROM chat WHERE receiver_id ='$username' AND "
                                            . "sender_id = '$receiverList[id]' AND seen = 'false'");
                                    while($row = mysqli_fetch_array($chats)){
                                            $num = $row['number'];
                                    }
                                    ?>
                                    <img src='assets/user_img/<?php echo $userResult['profile_img']; ?>' id='profilePic'>
                                    <span id='profileDetails'>
                                        <br>
                                        <span style='font-size: 20px'><?php echo $receiverList['id'] ?></span>
                                        <span class="badge"><?php echo $num?></span>
                                    </span>
                                    
                                    </div><!--</div>-->
                                    </a>
                                    
                                    <!--<button id="user_<?//php echo $receiverList['id'] ?>" href='#message' onclick='readMessage(this);' receiver="<?php echo $receiverList['id'] ?>"sender=<?php echo $username ?>
                                            data-toggle='modal' class='btn btn-sm btn-default'>
                                        <?php// echo $receiverList['id'] ?></button>-->
                                        <?php
                                    //echo "<br>";
                                }
                                while ($senderList = mysqli_fetch_array($senders)) {
                                    ?>
                                    <a href='#message'id="user_<?php echo $senderList['id'] ?>"  onclick='readMessage(this);'receiver="<?php echo $senderList['id'] ?>"sender="<?php echo $username ?>"
                                            data-toggle='modal'>
                                        <!--<div class="col-md-3">-->
                                            <div id='profileBox'>
                                        
                                    <?php
                                    $userQuery = mysqli_query($conn, "SELECT profile_img FROM user WHERE username = '$senderList[id]'");
                                    $userResult = mysqli_fetch_array($userQuery);
                                    $chats = mysqli_query($conn, "SELECT count(*) as number FROM chat WHERE receiver_id ='$username' AND "
                                            . "sender_id = '$senderList[id]' AND seen = 'false'");
                                    while($row = mysqli_fetch_array($chats)){
                                            $num = $row['number'];
                                    }
                                    ?>

                                    <img src='assets/user_img/<?php echo $userResult['profile_img']; ?>' id='profilePic'>
                                    <span id='profileDetails'>
                                        <br>
                                        <span style='font-size: 20px'><?php echo $senderList['id'] ?></span>
                                        <span class="badge"><?php echo $num?></span>
                                    </span>
                                    
                                    </div><!--</div>-->
                                    </a>
                                    <!--<button id="user_<?php //echo $senderList['id'] ?>" href='#message' onclick='readMessage(this);' receiver="<?php echo $username ?>"sender=<?php echo $senderList['id'] ?>
                                            data-toggle='modal' class='btn btn-sm btn-default'>
                                        <?php //echo $senderList['id'] ?></button>-->
                                        <?php
                                    //echo "<br>";
                                }
                                ?>
                            </div>
                            <div class="col-md-9" style="border-left:2px #E8E8E8 solid">
                            <div id='displayMessage'></div>
                            </div>
                            <!--</div>-->
                            <?php
                        }
                        ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if (empty($user) == false) {
    ?>
    <script>
        window.onload = function () {
            document.getElementById("user_<?php echo $user ?>").click();
        }
    </script>
    <?php
}
?>
<script>
    function readMessage(d) {
        var receiver = d.getAttribute("receiver");
        var sender = d.getAttribute("sender");
        $.ajax({
            type: "GET",
            url: "viewMessage.php",
            data: 'receiver=' + receiver + '&sender=' + sender,
            cache: false,
            dataType: 'json',
            success: function (data) {
                $('#displayMessage').html(data['msg']);
            }
        });
    }
</script>