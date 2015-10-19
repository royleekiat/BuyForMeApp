<?php

session_start();
include 'db.php';

$username = $_SESSION['username'];
$userId = filter_input(INPUT_POST, 'userid');
$productId = filter_input(INPUT_POST, 'productid');
$rating = filter_input(INPUT_POST, 'score');
$review = filter_input(INPUT_POST, 'review');
$print = filter_input(INPUT_POST, 'print');

$submitQuery = "INSERT INTO user_rating (user_id, pid, score, review) VALUES ('$userId', '$productId', '$rating', '$review')";
$submitRating = mysqli_query($conn, $submitQuery);
if ($submitRating) {
    if ($print == 1) {
        $updateQuery = "UPDATE product SET product_status = 'Completed' WHERE product_id = '$productId'";
        $updateBid = mysqli_query($conn, $updateQuery);
        header("Location: userProfile.php");
        die();
    } else if ($print == 2) {
        $updateQuery = "UPDATE product_bid SET bid_status = 'Completed' WHERE pid = '$productId'";
        $updateBid = mysqli_query($conn, $updateQuery);
        header("Location: userProfile.php");
        die();
    }
}