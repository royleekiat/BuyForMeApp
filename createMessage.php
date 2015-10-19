<?php
include 'header.php';
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    $username = $_SESSION['username'];
} else {
    header("Location: login.php");
}
$receiver = filter_input(INPUT_GET, "id");
$productID = filter_input(INPUT_GET, "pid");
?>

<style>
    .RbtnMargin { margin-left: 10px; }
</style>
<div class="body-content">
    <div class="row">
        <div class="container">
            <h3 class="text-center">Enter Message for <?php echo $receiver ?></h3>
            <form method="post" class="form-horizontal" id="messageForm" action="processMessage.php">
                <div class="col-md-offset-3 col-md-6">
                    <div class="form-group">
                        <textarea class="form-control" name="message" rows="8" id="desc" required></textarea>
                    </div>
                    <input type="hidden" name="receiver" value="<?php echo $receiver ?>"/>
                    <input type="hidden" name="sender" value="<?php echo $username ?>"/>
                    <input type="submit" name="submit" id="submit" value="Send Message" class="btn btn-info pull-right RbtnMargin"/>
                    <a class="btn btn-info pull-right" href="viewProduct.php?pid=<?php echo $productID ?>" role="button">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>