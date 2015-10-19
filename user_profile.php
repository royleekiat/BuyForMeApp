<?php
include 'header.php';

$todayDate = date("Y-m-d");
?>
<link rel="stylesheet" href="/BuyForMeApp/assets/css/theme.blue.css">
<script type="text/javascript" src="/BuyForMeApp/assets/js/jquery-latest.js"></script>
<script type="text/javascript" src="/BuyForMeApp/assets/js/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="/BuyForMeApp/assets/js/jquery.raty.js"></script>

<script type="text/javascript">
    $(document).ready(function ()
    {
        $("#productList").tablesorter();
        $("#ratingTable").tablesorter();
        $("#bidsTable").tablesorter();
    }
    );
</script>

<div class="body-content">
    <!--<div class="row">-->
    <div class="container">
        <br/><br/>
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-3">My Requests</div>
            <div class="col-md-3">My Bids</div>
            <div class="col-md-3">Ratings</div>
            <div class="col-md-1"></div>
            
            <br/>
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <h2><center>User Profile</center></h2>
                <div id='userBox'>
                    <?php
                    $retrieveUser = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username'");
                    if (mysqli_num_rows($retrieveUser) > 0) {
                        $userRow = mysqli_fetch_array($retrieveUser);
                        $userPhoto = $userRow['profile_img'];
                        ?>
                        <img src='assets/user_img/<?php echo $userPhoto ?>' id='userProfile'>
                        <div id='userDetails'>
                            <span style='font-size: 20px'><?php echo $username; ?></span>
                            <p><?php echo $userRow['introduction']; ?></p>
                            <script>
                                $(document).ready(function () {
                                    $('#requestStar').raty({
                                        readOnly: true,
                                        score: <?php echo $userRow['rating'] ?>
                                    });
                                });</script>
                            <span id='requestStar'></span>
                        <?php } ?>
                    </div>
                </div>
                <br/><br/>
                <!--List of Products Table-->
                <center>
                    <h4>My Requests</h4>
                    <table class="tablesorter" id="productList">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Country</th>
                                <th>Request Date</th>
                                <th>Num. of Bids</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $retrieveUserProduct = mysqli_query($conn, "SELECT * FROM product WHERE user_id = '$username' AND product_status <> 'Deleted'");
                            if (mysqli_num_rows($retrieveUserProduct) > 0) {
                                while ($userPRow = mysqli_fetch_array($retrieveUserProduct)) {
                                    $p_id = $userPRow['product_id'];
                                    ?>
                                    <tr>
                                        <td><?php echo $userPRow['name']; ?></td>
                                        <td><?php echo $userPRow['country']; ?></td>
                                        <td><?php echo $userPRow['requestDate']; ?></td>
                                        <?php
                                        $retrieveProduct = mysqli_query($conn, "SELECT * FROM product_bid WHERE pid = '$p_id' AND bid_status IN ('Accepted', 'Open')");
                                        $numberOfBids = mysqli_num_rows($retrieveProduct);
                                        ?>
                                        <td><?php echo $numberOfBids; ?></td>
                                        <td><?php echo $userPRow['product_status']; ?></td>
                                        <td>
                                            <a href='viewProduct.php?pid=<?php echo $p_id; ?>'><button class="btn btn-sm btn-default">View</button></a>
                                            <?php
                                            $checkTravel = mysqli_query($conn, "SELECT travel_from, product_status FROM product p LEFT OUTER JOIN "
                                                    . "( SELECT travel_from, pid FROM product_bid GROUP BY pid ) AS a ON a.pid = p.product_id "
                                                    . " WHERE p.product_status IN ('Accepted', 'Open') AND p.product_id = '$p_id'"
                                                    . " AND p.user_id = '$username'");
                                            if (mysqli_num_rows($checkTravel) == 1) {
                                                $checkTravelRow = mysqli_fetch_array($checkTravel);
                                                $checkDate = date('Y-m-d', (strtotime('+1 day', strtotime($todayDate))));
                                                if (( $checkDate < $checkTravelRow['travel_from']) || (empty($checkTravelRow['travel_from']))) {
                                                    ?>
                                                    <button href="#cancelRequest" onclick="cancelRequest(this);" p-id='<?php echo $p_id ?>' data-id="<?php echo $username ?>" print='5' data-toggle="modal" class="btn btn-sm btn-danger">Cancel Request</button>
                                                    <?php
                                                } else if ($checkTravelRow['product_status'] == 'Open') {
                                                    ?>
                                                    <button href="#cancelRequest" onclick="cancelRequest(this);" p-id='<?php echo $p_id ?>' data-id="<?php echo $username ?>" print='5' data-toggle="modal" class="btn btn-sm btn-danger">Cancel Request</button>
                                                    <?php
                                                }
                                            }

                                            $productStatus = mysqli_query($conn, "SELECT travel_to, pb.user_id FROM product p, product_bid pb "
                                                    . "WHERE p.product_id = pb.pid AND p.product_id = '$p_id' AND p.product_status = 'Accepted' AND pb.bid_status = 'Accepted'");
                                            if (mysqli_num_rows($productStatus) == 1) {
                                                $productRow = mysqli_fetch_array($productStatus);
                                                if ($todayDate >= $productRow['travel_to']) {
                                                    ?>
                                                    <button href="#submitRating" onclick="submitRating(this);" data-id="<?php echo $productRow['user_id'] ?>" p-id='<?php echo $p_id ?>' print='1' data-toggle="modal" class="btn btn-sm btn-primary">Submit Rating</button>
                                                <?php }
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="6"><center><div style='background-color:lightblue;'>You have not submit any product request</div></center></td>
                            </tr>
<?php } ?>
                        </tbody>
                    </table>
                </center>
                <!--List of Submitted Bids Table-->
                <br/>
                <center>
                    <h4>List of Submitted Bids</h4>
                    <table class="tablesorter" id="bidsTable">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Date Bidded</th>
                                <th>Travel Period</th>
                                <th>Shipping Price</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $retrieveUserBids = mysqli_query($conn, "SELECT pb.pid, name, travel_to, travel_from, price, date_bidded, bid_status"
                                    . " FROM product_bid pb, product p WHERE pb.pid = p.product_id AND pb.user_id = '$username' "
                                    . "AND pb.bid_status IN ('Accepted', 'Open')");
                            if (mysqli_num_rows($retrieveUserBids) > 0) {
                                while ($userBRow = mysqli_fetch_array($retrieveUserBids)) {
                                    $p_id = $userBRow['pid'];
                                    $travelerBStatus = $userBRow['bid_status'];
                                    ?>
                                    <tr>
                                        <td><?php echo $userBRow['name']; ?></td>
                                        <td><?php echo $userBRow['date_bidded']; ?></td>
                                        <td><?php echo $userBRow['travel_from'] . " to " . $userBRow['travel_to']; ?></td>
                                        <td><?php echo $userBRow['price']; ?></td>
                                        <td><?php echo $travelerBStatus; ?></td>
                                        <td>
                                            <a href='viewProduct.php?pid=<?php echo $p_id; ?>'><button class="btn btn-sm btn-default">View</button></a>
                                            <?php
                                            $travelerBidStatus = mysqli_query($conn, "SELECT p.user_id FROM product_bid pb, product p"
                                                    . " WHERE pb.pid = p.product_id AND p.product_id = '$p_id' AND pb.user_id = '$username' AND pb.bid_status = 'Accepted'");
                                            if ((mysqli_num_rows($travelerBidStatus) == 1) && $travelerBStatus == 'Accepted') {
                                                $travelerBRow = mysqli_fetch_array($travelerBidStatus);
                                                ?>
                                                <button href="#submitRating" onclick="submitRating(this);" data-id="<?php echo $travelerBRow['user_id'] ?>" p-id='<?php echo $p_id ?>' print='2' data-toggle="modal" class="btn btn-sm btn-primary">Submit Rating</button>
                                                <?php
                                            }

                                            $checkTravel = mysqli_query($conn, "SELECT travel_from, travel_to, product_status, bid_id FROM product_bid pb, product p "
                                                    . "WHERE pb.pid = p.product_id AND pb.user_id = '$username' "
                                                    . "AND pb.bid_status IN ('Accepted', 'Open') AND pb.pid = '$p_id'");
                                            if (mysqli_num_rows($checkTravel) == 1) {
                                                $checkTravelRow = mysqli_fetch_array($checkTravel);
                                                $checkDate = date('Y-m-d', (strtotime('-1 day', strtotime($todayDate))));
                                                $bid_id = $checkTravelRow['bid_id'];
                                                if (($checkDate < $checkTravelRow['travel_from']) || empty($checkTravelRow['travel_from'])) {
                                                    ?>
                                                    <button href="#cancelBid" onclick="cancelBid(this);" data-id="<?php echo $username ?>" p-id='<?php echo $bid_id ?>' print='4' data-toggle="modal" class="btn btn-sm btn-danger">Cancel Bid</button>
                                                    <?php
                                                } else if ($todayDate >= $checkTravelRow['travel_to']) {
                                                    $checkTravelBid = mysqli_query($conn, "SELECT bid_status FROM product_bid WHERE bid_id = '$bid_id'");
                                                    $checkTravelBidRow = mysqli_fetch_array($checkTravelBid);
                                                    if ($checkTravelBidRow['bid_status'] == 'Accepted') {
                                                        ?>
                                                        <button href="#cancellation" onclick="cancellation(this);" data-id="<?php echo $username ?>" p-id='<?php echo $bid_id ?>' print='6' data-toggle="modal" class="btn btn-sm btn-danger">Cancellation</button>
                                                        <?php
                                                    }
                                                }

                                                if ($checkTravelRow['product_status'] == 'Open') {
                                                    ?>
                                                    <form method="post" action="submitBids.php">
                                                        <button class="btn btn-sm btn-warning">Edit Bid</button>
                                                    </form>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="6"><center><div style='background-color:lightblue;'>You have not submit any bids</div></center></td>
                            </tr>
<?php } ?>
                        </tbody>
                    </table>
                </center>
                <!--View Ratings Table-->
                <br/>
                <center>
                    <h4>View Ratings</h4>
                    <table class="tablesorter" id="ratingTable">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Rating</th>
                                <th>Review</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $retrieveRating = mysqli_query($conn, "SELECT p.name, p.product_id, r.score, r.review, r.user_id AS ruser, p.user_id AS puser FROM user_rating r, product p"
                                    . " WHERE r.pid = p.product_id AND r.user_id = '$username' AND p.product_status = 'Completed'");
                            if (mysqli_num_rows($retrieveRating) > 0) {
                                while ($ratingRow = mysqli_fetch_array($retrieveRating)) {
                                    $p_id = $ratingRow['product_id'];
                                    ?>
                                    <tr>
                                        <td><?php echo $ratingRow['name']; ?></td>
                                        <td><?php echo $ratingRow['score']; ?></td>
                                        <td><?php echo $ratingRow['review']; ?></td>
                                        <?php
                                        $ratingUser = $ratingRow['ruser'];
                                        $productUser = $ratingRow['puser'];
                                        if ($productUser == $username) {
                                            ?>
                                            <td><?php echo "Buyer"; ?></td>
                                        <?php } else { ?>
                                            <td><?php echo "Traveler"; ?></td>    
        <?php } ?>
                                        <td>
                                            <a href='viewProduct.php?pid=<?php echo $p_id; ?>'><button class="btn btn-sm btn-default">View</button></a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="6"><center><div style='background-color:lightblue;'>You have not submit any bids</div></center></td>
                            </tr>
<?php } ?>
                        </tbody>
                    </table>
                </center>
            </div>
            <div class="col-md-1"></div>
        </div>
    </div>
    <!--</div>-->

    <!-- Submit Rating Modal -->
    <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="submitRating" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="form-horizontal style-form" method="post" action="processRating.php">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Submit Rating</h4>
                    </div>
                    <div class="modal-body">
                        <center>
                            <div id="ratingView"></div>
                        </center>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                        <input type="submit" value="Submit Rating" id="submit" name="submit" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Submit Rating Modal -->

    <!-- Cancel Request Modal -->
    <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="cancelRequest" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="form-horizontal style-form" method="post" action="processViewBids.php">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Cancel Request</h4>
                    </div>
                    <div class="modal-body">
                        <center>
                            <div id="cancelRequestView"></div>
                        </center>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                        <input type="submit" value="Cancel Request" id="submit" name="submit" class="btn btn-danger">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Cancel Request Modal -->

    <!-- Cancel Bid Modal -->
    <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="cancelBid" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="form-horizontal style-form" method="post" action="processViewBids.php">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Cancel Bid</h4>
                    </div>
                    <div class="modal-body">
                        <center>
                            <h3>Cancel Bid</h3>
                            <div id="cancelBidView"></div>
                            <br/><br/>
                            <p>
                                Are you sure you want to cancel this bid?
                            </p>
                        </center>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                        <input type="submit" value="Cancel" id="submit" name="submit" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Cancel Bid Modal -->

</div>

<script>
    function submitRating(d) {
        var userid = d.getAttribute("data-id");
        var pid = d.getAttribute("p-id");
        var print = d.getAttribute("print");
        $.ajax({
            type: "GET",
            url: "processViewRating.php",
            data: 'userid=' + userid + '&pid=' + pid + '&print=' + print,
            cache: false,
            success: function (data) {
                $('.modal-body #ratingView').html(data);
            }
        });
    }

    function cancelRequest(d) {
        var userid = d.getAttribute("data-id");
        var pid = d.getAttribute("p-id");
        var print = d.getAttribute("print");
        $.ajax({
            type: "GET",
            url: "processView.php",
            data: 'userid=' + userid + '&pid=' + pid + '&print=' + print,
            cache: false,
            success: function (data) {
                $('.modal-body #cancelRequestView').html(data);
            }
        });
    }

    function cancelBid(d) {
        var userid = d.getAttribute("data-id");
        var pid = d.getAttribute("p-id");
        var print = d.getAttribute("print");
        $.ajax({
            type: "GET",
            url: "processView.php",
            data: 'id=' + userid + '&pid=' + pid + '&print=' + print,
            cache: false,
            success: function (data) {
                $('.modal-body #cancelBidView').html(data);
            }
        });
    }

    function cancellation(d) {
        var userid = d.getAttribute("data-id");
        var pid = d.getAttribute("p-id");
        var print = d.getAttribute("print");
        $.ajax({
            type: "GET",
            url: "processView.php",
            data: 'id=' + userid + '&pid=' + pid + '&print=' + print,
            cache: false,
            success: function (data) {
                $('.modal-body #cancelBidView').html(data);
            }
        });
    }
</script>