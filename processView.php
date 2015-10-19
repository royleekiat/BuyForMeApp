<?php
session_start();
include 'db.php';

$id = filter_input(INPUT_GET, 'id');
$print = filter_input(INPUT_GET, 'print');
$pid = filter_input(INPUT_GET, 'pid');
$bid = filter_input(INPUT_GET, 'bid');

if ($print == 1) {
    $result = mysqli_query($conn, "SELECT * FROM user WHERE username = '$id'");

    $data = array();
    while ($row = mysqli_fetch_array($result)) {
        $username = $row['username'];
        $last_login = $row['last_login'];
        $introduction = $row['introduction'];
        $rating = $row['rating'];
        $image = $row['profile_img'];
        $data['username'] = $username;
        $data['last_login'] = $last_login;
        $data['introduction'] = $introduction;
        $data['rating'] = $rating;
        $data['profile_img'] = $image;
    }

    echo "<div id='travelerBox'>";
    echo "<img src='assets/user_img/" . $image . "' id='travelerPic'/>";
    echo "<span id='travelerProfile' style='margin-left: 5%'>";
    echo "<h5>$username</h5>";
    echo "<h5>Last Login: $last_login</h5>";
    ?>

    <script>
        $(document).ready(function () {
            $('#travelerStar').raty({
                readOnly: true,
                score: <?php echo $rating ?>
            });
        });</script>

    <?php
    echo '<h5>Rating(s): <span id="travelerStar"></span></h5>';
    echo "</span>";
    echo '</div>';
    echo "<h4>About Me</h4><br/><p>" . $introduction . "</p>";
} else if ($print == 2 || $print == 3) {
    $presult = mysqli_query($conn, "SELECT * FROM product_bid WHERE pid = '$pid' AND user_id = '$id'");

    $pdata = array();
    while ($prow = mysqli_fetch_array($presult)) {
        $bidID = $prow['bid_id'];
        $travel_to = $prow['travel_to'];
        $travel_from = $prow['travel_from'];
        $user_id = $prow['user_id'];
        $price = $prow['price'];
        $productID = $prow['pid'];
        $pdata['bid_id'] = $bidID;
        $pdata['travel_to'] = $travel_to;
        $pdata['travel_from'] = $travel_from;
        $pdata['user_id'] = $user_id;
        $pdata['price'] = $price;
    }

    if ($print == 2) {
        echo '<tr>';
        echo '<td align="right">Traveler:</td>';
        echo '<td align ="left"><span style="margin-left:10px;">' . $user_id . '</span></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td align="right">Shipping Price:</td>';
        echo '<td align ="left"><span style="margin-left:10px;">' . $price . '</span></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td align="right">Travel Period:</td>';
        echo '<td align ="left"><span style="margin-left:10px;">' . $travel_from . ' to ' . $travel_to . '</span></td>';
        echo '</tr>';
        echo "<input type='hidden' name='bid_id' value='$bidID' />";
        echo "<input type='hidden' name='status' value='accept' />";
        echo "<input type='hidden' name='product_id' value='$productID' />";
    } else if ($print == 3) {
        echo '<tr>';
        echo '<td align="right">Traveler:</td>';
        echo '<td align ="left"><span style="margin-left:10px;">' . $user_id . '</span></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td align="right">Shipping Price:</td>';
        echo '<td align ="left"><span style="margin-left:10px;">' . $price . '</span></td>';
        echo '</tr>';
        echo "<input type='hidden' name='bid_id' value='$bidID' />";
        echo "<input type='hidden' name='status' value='reject' />";
        echo "<input type='hidden' name='product_id' value='$productID' />";
    }
} else if ($print == 4) {
    $bresult = mysqli_query($conn, "SELECT name, description, p.user_id FROM product_bid pb, product p WHERE pb.pid = p.product_id "
            . "AND pb.user_id = '$id' AND pb.bid_id = '$pid'");
    $bdata = array();
    $brow = mysqli_fetch_array($bresult);
    $productName = $brow['name'];
    $bdata['name'] = $productName;

    echo "Are you sure that you want to delete the bid for $productName?";
    echo "<input type='hidden' name='bid_id' value='$pid' />";
    echo "<input type='hidden' name='status' value='delete' />";
} else if ($print == 5) {
    $cpresult = mysqli_query($conn, "SELECT name FROM product WHERE product_id = '$pid'");
    
    $cpdata = array();
    $cprow = mysqli_fetch_array($cpresult);
    $productName = $cprow['name'];
    $cprow['name'] = $productName;

    echo "Are you sure that you want to delete the request for $productName?";
    echo "<input type='hidden' name='product_id' value='$pid' />";
    echo "<input type='hidden' name='status' value='pdelete' />";
} else if ($print == 6) {
    $ebresult = mysqli_query($conn, "SELECT pb.user_id, product_id, name FROM product_bid pb, product p WHERE p.product_id = pb.pid AND pb.bid_id = '$pid'");
    
    $ebdata = array();
    $ebrow = mysqli_fetch_array($ebresult);
    $productName = $ebrow['name'];
    $productID = $ebrow['product_id'];
    $bidUser = $ebrow['user_id'];
    $ebrow['name'] = $productName;
    $ebrow['product_id'] = $productID;
    $ebrow['user_id'] = $bidUser;

    echo "Are you sure that you want to edit the bid for $productName?";
    echo "<input type='hidden' name='productID' value='$productID' />";
    echo "<input type='hidden' name='userID' value='$bidUser' />";
    echo "<input type='hidden' name='link' value='true' />";
} else if ($print == 7) {
    $bidresult = mysqli_query($conn, "SELECT product_id, name FROM product p, product_bid pb WHERE p.product_id = pb.pid AND pb.bid_id = '$bid'");
    
    $biddata = array();
    $bidrow = mysqli_fetch_array($bidresult);
    $productName = $bidrow['name'];
    $product_id = $bidrow['product_id'];
    $biddata['name'] = $productName;
    $biddata['product_id'] = $product_id;
    
    echo "Are you sure that you want to cancel the bid for $productName?<br/><br/>";
    echo "Reason for cancellation: <br/>";
    echo "<textarea rows='4' cols='50' name='cancellation' required></textarea>";
    echo "<input type='hidden' name='bid_id' value='$bid' />";
    echo "<input type='hidden' name='product_id' value='$product_id' />";
    echo "<input type='hidden' name='status' value='cancel' />";
} else if ($print == 8) {
    $bidresult = mysqli_query($conn, "SELECT product_id, name FROM product WHERE product_id = '$pid'");
    
    $biddata = array();
    $bidrow = mysqli_fetch_array($bidresult);
    $productName = $bidrow['name'];
    $biddata['name'] = $productName;
    
    echo "Are you sure that you want to cancel the request for $productName?<br/>"
            . "There will be a <b>penalty</b> of 10% from your initial deposit (capped at $10) "
            . "as the cancellation is done within the 72 hours based on the traveler start travel date.<br/><br/>";
    echo "Reason for cancellation: <br/>";
    echo "<textarea rows='4' cols='50' name='cancellation' required></textarea>";
    echo "<input type='hidden' name='product_id' value='$pid' />";
    echo "<input type='hidden' name='bid_id' value='$bid' />";
    echo "<input type='hidden' name='status' value='cancelPR' />";
}