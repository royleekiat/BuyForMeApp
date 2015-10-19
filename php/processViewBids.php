<?php

include 'db.php';

$productID = filter_input(INPUT_POST, 'product_id');
$status = filter_input(INPUT_POST, 'status');
$bidID = filter_input(INPUT_POST, 'bid_id');

$product_id = filter_input(INPUT_GET, 'pid');
$categoryStatus = filter_input(INPUT_GET, 'status');

$dateTime = date("Y-m-d H:i:s");

if ($status == 'accept') {
    $updateBid = "UPDATE product_bid SET bid_status = 'Accepted' WHERE bid_id = '$bidID'";
    if (mysqli_query($conn, $updateBid)) {
        $updateProduct = "UPDATE product SET product_status = 'Accepted' WHERE product_id = '$productID'";
        if (mysqli_query($conn, $updateProduct)) {
            $checkNoti = mysqli_query($conn, "SELECT p.user_id AS sender, pb.user_id AS receiver, name "
                    . " FROM product p, product_bid pb WHERE p.product_id = pb.pid"
                    . " AND bid_id = '$bidID' AND product_id = '$productID'");
            $checkNotiRow = mysqli_fetch_array($checkNoti);
            $sender_id = $checkNotiRow['sender'];
            $receiver_id = $checkNotiRow['receiver'];
            $message = $sender_id . " has accepted your bid for product <b>" . $checkNotiRow['name'] . " </b>";
            $updateNotiQuery = mysqli_query($conn, "INSERT INTO notification (receiver_id, sender_id, product_id, message, dateTime, status, category) VALUES ('$receiver_id', '$sender_id', '$productID', '$message', '$dateTime', 'unread', 'bidaccepted')");
            $updateProductBids = "UPDATE product_bid SET bid_status = 'Rejected' WHERE pid = '$productID' AND bid_status = 'Open'";
            mysqli_query($conn, $updateProductBids);
        }
    }
} else if ($status == 'pdelete') {
    $updateBid = "UPDATE product SET product_status = 'Deleted' WHERE product_id = '$productID'";
    if (mysqli_query($conn, $updateBid)) {
        $findBidQuery = mysqli_query($conn, "SELECT bid_id FROM product_bid WHERE pid = '$productID' AND bid_status IN ('Accepted', 'Open')");
        if (mysqli_num_rows($findBidQuery) > 0) {
            while ($findBidRow = mysqli_fetch_array($findBidQuery)) {
                $bidID = $findBidRow['bid_id'];
                $updateProductBid = mysqli_query($conn, "UPDATE product_bid SET bid_status = 'Deleted' WHERE pid = '$productID'");
                $checkNoti = mysqli_query($conn, "SELECT p.user_id AS sender, pb.user_id AS receiver, name "
                        . " FROM product p, product_bid pb WHERE p.product_id = pb.pid"
                        . " AND bid_id = '$bidID' AND product_id = '$productID'");
                $checkNotiRow = mysqli_fetch_array($checkNoti);
                $sender_id = $checkNotiRow['sender'];
                $receiver_id = $checkNotiRow['receiver'];
                $message = $sender_id . " has cancelled your request for product <b>" . $checkNotiRow['name'] . "</b>.<br/><br/>";
                $updateNotiQuery = mysqli_query($conn, "INSERT INTO notification (receiver_id, sender_id, product_id, message, dateTime, status, category) VALUES ('$receiver_id', '$sender_id', '$productID', '$message', '$dateTime', 'unread', 'cancellation')");
            }
        }
    }
} else if ($status == 'reject') {
    $rejectBid = "UPDATE product_bid SET bid_status = 'Rejected' WHERE bid_id = '$bidID'";
    mysqli_query($conn, $rejectBid);
} else if ($status == 'cancel') {
    $cancellation = filter_input(INPUT_POST, 'cancellation');
    $cancellationQuery = "UPDATE product_bid SET bid_status = 'Cancelled', cancellation = '$cancellation' WHERE bid_id = '$bidID'";
    if (mysqli_query($conn, $cancellationQuery)) {
        mysqli_query($conn, "UPDATE product SET product_Status = 'Cancelled' WHERE product_id = '$productID'");
        $checkNoti = mysqli_query($conn, "SELECT p.user_id AS receiver, pb.user_id AS sender, name "
                . " FROM product p, product_bid pb WHERE p.product_id = pb.pid"
                . " AND bid_id = '$bidID' AND product_id = '$productID'");
        $checkNotiRow = mysqli_fetch_array($checkNoti);
        $sender_id = $checkNotiRow['sender'];
        $receiver_id = $checkNotiRow['receiver'];
        $message = $sender_id . " has cancelled your request for product <b>" . $checkNotiRow['name'] . "</b>.<br/> Reason: " . $cancellation;
        $updateNotiQuery = mysqli_query($conn, "INSERT INTO notification (receiver_id, sender_id, product_id, message, dateTime, status, category) VALUES ('$receiver_id', '$sender_id', '$productID', '$message', '$dateTime', 'unread', 'cancellation')");
    }
} else if ($status == 'cancelPR') {
    $cancellation = filter_input(INPUT_POST, 'cancellation');
    $cancellationQuery = "UPDATE product SET product_status = 'Cancelled', cancellation = '$cancellation' WHERE product_id = '$productID'";
    if (mysqli_query($conn, $cancellationQuery)) {
        $updatePRQuery = "UPDATE product_bid SET bid_status = 'Cancelled' WHERE pid = '$productID'";
        if (mysqli_query($conn, $updatePRQuery)) {
            $checkNoti = mysqli_query($conn, "SELECT p.user_id AS sender, pb.user_id AS receiver, name "
                    . " FROM product p, product_bid pb WHERE p.product_id = pb.pid"
                    . " AND bid_id = '$bidID' AND product_id = '$productID'");
            $checkNotiRow = mysqli_fetch_array($checkNoti);
            $sender_id = $checkNotiRow['sender'];
            $receiver_id = $checkNotiRow['receiver'];
            $message = $sender_id . " has cancelled his/her request for product <b>" . $checkNotiRow['name'] . "</b>.<br/> Reason: " . $cancellation;
            $updateNotiQuery = mysqli_query($conn, "INSERT INTO notification (receiver_id, sender_id, product_id, message, dateTime, status, category) VALUES ('$receiver_id', '$sender_id', '$productID', '$message', '$dateTime', 'unread', 'cancellation')");
        }
    }
} else if ($categoryStatus == 'deleteProduct') {
    $deleteProduct = "UPDATE product SET product_status = 'Deleted' WHERE product_id = '$product_id'";
    mysqli_query($conn, $deleteProduct);
} else if ($categoryStatus == 'promoteProduct') {
    $todayDate = date("Y-m-d");
    $deleteProduct = "UPDATE product SET requestDate = '$todayDate' WHERE product_id = '$product_id'";
    mysqli_query($conn, $deleteProduct);
} else {
    $updateBid = "UPDATE product_bid SET bid_status = 'Deleted' WHERE bid_id = '$bidID'";
    mysqli_query($conn, $updateBid);
}

header("Location: index.php");
die();
