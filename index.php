<?php
include 'header.php';

$currentDate = date("Y-m-d");
$checkStatus = mysqli_query($conn, "SELECT product_id, travel_from FROM product p, product_bid pb "
        . "WHERE p.product_id = pb.pid AND p.product_status = 'Accepted' AND pb.bid_status = 'Accepted'");
if (mysqli_num_rows($checkStatus) > 0) {
    while ($checkRow = mysqli_fetch_array($checkStatus)) {
        $travelFrom = $checkRow['travel_from'];
        if ($travelFrom <= $currentDate) {
            $p_id = $checkRow['product_id'];
            $updateProduct = mysqli_query($conn, "UPDATE product SET product_status = 'Pending Delivery' WHERE product_id = '$p_id'");
            $updateBid = mysqli_query($conn, "UPDATE product_bid SET bid_status = 'Pending Delivery' WHERE pid = '$p_id'");
        }
    }
}
?>

<script type="text/javascript" src="/BuyForMeApp/assets/js/jquery.raty.js"></script>

<style>
    .indexUserProfile {
        border-radius: 50%;
        margin-left: 5%;
        border: 1px solid #cccfd0;
        float: left;
    }

    .indexUserDetails {
        margin-left: 2%;
        float: left;
    }

    .indexUserProfileBox {
        height: 100%;
    }
</style>

<div class="body-content">
    <div class="jumbotron">
        <div class="container">
            <br>
            <h1>Welcome to BuyForMe!</h1>
            <p>Need help with buying products overseas?<br>Post request and product will be delivered</p>
            <p><a class="btn btn-default btn-md" href="howitworks.php" role="button">HOW IT WORKS</a></p>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12" style="text-align:center">
        <h2 class="introtitle">SEE WHAT OTHERS ARE REQUESTING</h2><br/><br/>
        <div class="col-md-10 col-md-offset-1">
            <?php
            $retrieveProduct = mysqli_query($conn, "SELECT * FROM product p LEFT OUTER JOIN "
                    . "(SELECT COUNT(*) AS numBids, MIN(price) AS minPrice, pid FROM product_bid "
                    . " WHERE bid_status = 'Open' GROUP BY pid ) AS a ON a.pid = p.product_id "
                    . " WHERE p.product_status = 'Open' ORDER BY requestDate desc LIMIT 0,6");
            if (mysqli_num_rows($retrieveProduct) > 0) {
                while ($row = mysqli_fetch_array($retrieveProduct)) {
                    $pid = $row['product_id'];
                    $image = "assets/product_img/" . $row['image'];
                    $name = $row['name'];
                    $num = $row['numBids'];
                    if ($num == NULL) {
                        $num = 0;
                    }
                    $price = $row['approx_price'];
                    $minShipping = $row['minPrice'];
                    if ($minShipping == NULL) {
                        $minShipping = 0;
                    }
                    ?>

                    <div class="col-sm-4">
                        <div class="thumbnail">
                            <div class="thumbnail-top">
                                <a href="viewProduct.php?pid=<?php echo $pid; ?>">
                                    <img src="<?php echo $image; ?>" class="thumbnail-pic" style="width: 150px; height: 150px;">
                                </a>
                            </div>
                            <div class="caption">
                                <h4><?php echo $name; ?></h4>
                                <h6>Buy From: <?php echo $row['country']; ?></h6>
                                <table style="margin-left: 8%;">
                                    <tr>
                                        <td><?php echo "$" . $price; ?></td>
                                        <td><?php echo $num; ?></td>
                                        <td><?php echo $minShipping; ?></td>
                                    </tr>
                                    <tr>
                                        <td width="80px">Product Price</td>
                                        <td width="80px">No. of Bids</td>
                                        <td width="80px">Min Bid Price</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
    <p style="text-align:center"><a class="btn btn-default btn-md" href="browse.php" role="button">View All Requests</a></p>

    <br/><br/>

    <div class="col-xs-12 col-sm-12" style="text-align:center">
        <h2 class="introtitle">TOP RATED USERS</h2><br/><br/>
        <div class="col-md-8 col-md-offset-2">
            <?php
            $userRatingQuery = mysqli_query($conn, "SELECT rating, username, profile_img FROM user ORDER BY rating desc LIMIT 0,2");
            if (mysqli_num_rows($userRatingQuery) > 0) {
                while ($userRatingRow = mysqli_fetch_array($userRatingQuery)) {
                    $userPhoto = $userRatingRow['profile_img'];
                    $user = $userRatingRow['username'];
                    ?>
                    <div class="indexUserProfileBox col-md-6">
                        <img src='assets/user_img/<?php echo $userPhoto ?>' class='indexUserProfile' style="width: 150px; height: 150px;" />
                        <div class='indexUserDetails'>
                            <span style='font-size: 20px'><?php echo $userRatingRow['username']; ?></span>
                            <script>
                                $(function () {
                                    $('.rate').raty({
                                        readOnly: true,
                                        score: function () {
                                            return $(this).attr('data-score');
                                        }
                                    });
                                });
                            </script>
                            <div class='rate' data-score='<?php echo $userRatingRow['rating']; ?>'></div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</div>

<?php
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    $username = $_SESSION['username'];
    $checkProductQuery = mysqli_query($conn, "SELECT requestDate, name, product_id FROM product"
            . " WHERE user_id = '$username' AND product_status = 'Open'"
            . " AND requestDate < (NOW() - INTERVAL 7 DAY)");
    if (mysqli_num_rows($checkProductQuery) > 0) {
        while ($checkProductRow = mysqli_fetch_array($checkProductQuery)) {
            $productID = $checkProductRow['product_id'];
            $checkStatusQuery = mysqli_query($conn, "SELECT bid_id FROM product_bid WHERE pid = '$productID'");
            if (mysqli_num_rows($checkStatusQuery) == 0) {
                $dateTime = date("Y-m-d H:i:s");
                $message = 'Product Name: <b>' . $checkProductRow["name"] . ' </b>do not have any bids for the past few days. Click to view product.';
                $checkSendQuery = mysqli_query($conn, "SELECT dateTime FROM notification WHERE product_id = '$productID' "
                        . " AND receiver_id = '$username' ORDER BY dateTime desc");
                if (mysqli_num_rows($checkSendQuery) > 0) {
                    $checkSendRow = mysqli_fetch_array($checkSendQuery);
                    $checkDateTime = $checkSendRow['dateTime'];
                    $checkDateSend = date('Y-m-d', (strtotime('+5 day', strtotime($checkDateTime))));
                    if ($checkDateSend < $currentDate) {
                        $updateNotiQuery = mysqli_query($conn, "INSERT INTO notification (receiver_id, sender_id, product_id, message, dateTime, status, category) VALUES ('$username', 'admin', '$productID', '$message', '$dateTime', 'unread', 'nobids')");
                    }
                } else {
                    $updateNotiQuery = mysqli_query($conn, "INSERT INTO notification (receiver_id, sender_id, product_id, message, dateTime, status, category) VALUES ('$username', 'admin', '$productID', '$message', '$dateTime', 'unread', 'nobids')");
                }
            }
        }
    }

    $checkBidQuery = mysqli_query($conn, "SELECT product_id, name FROM product WHERE user_id = '$username' AND product_status = 'Open'");
    if (mysqli_num_rows($checkBidQuery) > 0) {
        while ($checkBidRow = mysqli_fetch_array($checkBidQuery)) {
            $product_id = $checkBidRow['product_id'];
            $productBidTableQuery = mysqli_query($conn, "SELECT date_bidded, pid FROM product_bid WHERE pid = '$product_id' "
                    . " AND bid_status NOT IN ('Rejected', 'Deleted', 'Cancelled') "
                    . " AND date_bidded < (NOW() - INTERVAL 5 DAY) ORDER BY date_bidded asc");
            if (mysqli_num_rows($productBidTableQuery) > 0) {
                $productBidTableRow = mysqli_fetch_array($productBidTableQuery);
                $productBidId = $productBidTableRow['pid'];
                $dateTime = date("Y-m-d H:i:s");
                $message = 'You have not accepted any bids for product name: <b>' . $checkBidRow["name"] . ' </b>';
                $checkSendQuery = mysqli_query($conn, "SELECT dateTime FROM notification WHERE product_id = '$productBidId' "
                        . " AND receiver_id = '$username' ORDER BY dateTime desc");
                if (mysqli_num_rows($checkSendQuery) > 0) {
                    $checkSendRow = mysqli_fetch_array($checkSendQuery);
                    $checkDateTime = $checkSendRow['dateTime'];
                    $checkDateSend = date('Y-m-d', (strtotime('+5 day', strtotime($checkDateTime))));
                    if ($checkDateSend < $currentDate) {
                        $updateNotiQuery = mysqli_query($conn, "INSERT INTO notification (receiver_id, sender_id, product_id, message, dateTime, status, category) VALUES ('$username', 'admin', '$productBidId', '$message', '$dateTime', 'unread', 'noaccept')");
                    }
                } else {
                    $updateNotiQuery = mysqli_query($conn, "INSERT INTO notification (receiver_id, sender_id, product_id, message, dateTime, status, category) VALUES ('$username', 'admin', '$productBidId', '$message', '$dateTime', 'unread', 'noaccept')");
                }
            }
        }
    }
}

$userQuery = mysqli_query($conn, "SELECT username FROM user");
if (mysqli_num_rows($userQuery) > 0) {
    while ($userRow = mysqli_fetch_array($userQuery)) {
        $user_id = $userRow['username'];
        $checkRating = mysqli_query($conn, "SELECT user_id, score FROM user_rating WHERE user_id = '$user_id'");
        $checkRows = mysqli_num_rows($checkRating);
        if ($checkRows > 0) {
            $totalRating = 0;
            $rows = 0;
            while ($checkRatingRow = mysqli_fetch_array($checkRating)) {
                $totalRating = $totalRating + $checkRatingRow['score'];
                $rows = $rows + 1;
                if ($rows == $checkRows) {
                    break;
                }
            }
            $avg = $totalRating / $rows;
            $updateReview = mysqli_query($conn, "UPDATE user SET rating = '$avg' WHERE username = '$user_id'");
        }
    }
}

