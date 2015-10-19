<?php

session_start();
include 'db.php';

$userid = filter_input(INPUT_GET, 'userid');
$pid = filter_input(INPUT_GET, 'pid');
$print = filter_input(INPUT_GET, 'print');
$bid = filter_input(INPUT_GET, 'bid');

$presult = "";
if ($print == 1) {
    $presult = mysqli_query($conn, "SELECT bid_id, pb.user_id, name FROM product_bid pb, product p "
            . " WHERE pb.pid = p.product_id AND pid = '$pid' AND pb.user_id = '$userid' AND pb.bid_id = '$bid'");
} else {
    $presult = mysqli_query($conn, "SELECT bid_id, p.user_id, name FROM product_bid pb, product p "
            . " WHERE pb.pid = p.product_id AND p.product_id = '$pid' AND pb.bid_id = '$bid'");
}

$pdata = array();
$prow = mysqli_fetch_array($presult);
$productName = $prow['name'];
$userId = $prow['user_id'];
$bidId = $prow['bid_id'];
$pdata['name'] = $userId;
$pdata['user_id'] = $productName;
$pdate['bid_id'] = $bidId;

if ($print == '1' || $print == '2') {
    echo '<table>';
    echo '<tr>';
    if ($print == '1') {
        echo '<td align="right">Traveler:</td>';
    } else {
        echo '<td align="right">Buyer:</td>';
    }
    echo '<td align ="left"><span style="margin-left:10px;">' . $userId . '</span></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td align="right">Product Name:</td>';
    echo '<td align ="left"><span style="margin-left:10px;">' . $productName . '</span></td>';
    echo '</tr>';
    ?>

    <script>
        $(document).ready(function () {
            $('#viewStar').raty();
        });</script>

    <?php

    echo '<tr>';
    echo '<td align="right">Ratings: </td>';
    echo '<td align ="left"><span style="margin-left:10px;" id="viewStar" name="rating"></span></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td align="right">Review: </td>';
    echo '<td align ="left"><textarea name="review" style="margin-left:10px;"></textarea></td>';
    echo '</tr>';
    echo '<input type="hidden" name="print" value="' . $print . '" />';
    echo '<input type="hidden" name="userid" value="' . $userId . '" />';
    echo '<input type="hidden" name="productid" value="' . $pid . '" />';
    echo '</table>';
} else if ($print == '3') {
    echo '<table><tr>';
    echo '';
    echo '';
    echo '</tr></table>';
}