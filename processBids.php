<?php

session_start();
include 'db.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    $username = $_SESSION['username'];
} else if ($productID == '') {
    header("Location: index.php");
    die;
}

$bidID = filter_input(INPUT_POST, 'bidID');
$travelTo = filter_input(INPUT_POST, 'periodTo');
$travelFrom = filter_input(INPUT_POST, 'periodFrom');
$shippingPrice = filter_input(INPUT_POST, 'shippingPrice');
$information = filter_input(INPUT_POST, 'information');
$productID = filter_input(INPUT_POST, 'productID');
$dateBidded = date("Y-m-d");

$msg = array();
if ($_SESSION['username'] != null) {
    if (!empty($bidID)) {
        $editQuery = "UPDATE product_bid SET travel_to ='$travelTo', travel_from = '$travelFrom', price = '$shippingPrice', other_info = '$information', date_bidded = '$dateBidded' WHERE bid_id = '$bidID'";
        $editBid = mysqli_query($conn, $editQuery);
        if ($editBid) {
            $msg = array("status" => "success", "msg" => "<div class='alert alert-success role='alert'><center>Bid is successfully updated</center></div>");
        } else {
            $msg = array("status" => "error", "msg" => "<div class='alert alert-danger role='alert'><center>Updating of bid failed</center></div>");
        }
    } else {
        $addBids = mysqli_query($conn, "INSERT INTO product_bid (travel_to,travel_from,price,other_info,pid, date_bidded, bid_status, user_id) VALUES ('$travelTo','$travelFrom','$shippingPrice','$information','$productID', '$dateBidded', 'Open', '$username')");
        if ($addBids) {
            $dateTime = date("Y-m-d H:i:s");
            $checkProduct = mysqli_query($conn, "SELECT * FROM product WHERE product_id = '$productID'");
            $checkProductRow = mysqli_fetch_array($checkProduct);
            $user_id = $checkProductRow['user_id'];
            $message = $username . ' has offered to help you buy <b>' . $checkProductRow["name"] . ' </b> for SGD' . $shippingPrice;
            $updateNotiQuery = mysqli_query($conn, "INSERT INTO notification (receiver_id, sender_id, product_id, message, dateTime, status, category) VALUES ('$user_id', '$username', '$productID', '$message', '$dateTime', 'unread', 'newbids')");
            $msg = array("status" => "success", "msg" => "<div class='alert alert-success role='alert'><center>Bid is successfully submitted</center></div>");
        } else {
            $msg = array("status" => "error", "msg" => "<div class='alert alert-danger role='alert'><center>Submission of bid failed</center></div>");
        }
    }
}

echo json_encode($msg);
