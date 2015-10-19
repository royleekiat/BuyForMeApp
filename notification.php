<?php
include 'header.php';
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    $username = $_SESSION['username'];
} else {
    header("Location: login.php");
}

$updateNotiQuery = mysqli_query($conn, "UPDATE notification SET status = 'read' WHERE receiver_id = '$username' AND status = 'unread'");
?>

<div class="body-content">
    <div class="row">
        <div class="container">
            <br/><br/>
            <div class="col-md-6 col-md-offset-3">
                <div class="btn-group" role="group" aria-label="...">
                    <button type="button" class="btn btn-default" style="width: 170px">All</button>
                    <button type="button" class="btn btn-default" style="width: 170px">I am the Requester</button>
                    <button type="button" class="btn btn-default" style="width: 170px">I am the Traveler</button>
                </div>
            </div>
            <br/><br/><br/><br/>
            <div class="col-md-6 col-md-offset-3">
                <br/>
                <?php
                $notificationQuery = mysqli_query($conn, "SELECT * FROM notification WHERE receiver_id = '$username' ORDER BY dateTime desc");
                if (mysqli_num_rows($notificationQuery) > 0) {
                    while ($notificationRow = mysqli_fetch_array($notificationQuery)) {
                        $product_id = $notificationRow['product_id'];
                        $category = $notificationRow['category'];
                        ?>
                        <div class="alert alert-warning row">
                            <div class="col-sm-12">
                                <a href="viewProduct.php?pid=<?php echo $product_id; ?>&category=<?php echo $category; ?>" style="color: black;">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <p><?php echo $notificationRow['sender_id']; ?></p>
                                            <p><?php echo $notificationRow['message']; ?></p>
                                        </div>
                                        <div class="col-sm-4">
                                            <p><?php echo $notificationRow['dateTime']; ?></p>
                                        </div>
                                    </div>

                                </a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="alert alert-info" role="alert">You have no notification</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>