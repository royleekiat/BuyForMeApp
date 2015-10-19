<?php
include 'header.php';

$todayDate = date("Y-m-d");
?>
<!--<link rel="stylesheet" href="/BuyForMeApp/assets/css/theme.blue.css">-->
<script type="text/javascript" src="/BuyForMeApp/assets/js/jquery-latest.js"></script>
<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
<script type="text/javascript" src="/BuyForMeApp/assets/js/jquery.raty.js"></script>
<script>
    $(document).ready(function () {
        $("#requestFilter").on('change', function () {
            var status = $("#requestFilter").val();
            $('#myrequeststable tbody > tr').show();

            $("#myrequeststable tbody > tr").each(function (index, tdAR) {
                if (!($(tdAR).hasClass(status))) {
                    $('#myrequeststable tbody > tr').hide();
                }
            });

            if (status === 'zero') {
                $('#myrequeststable tbody > tr').show();
            }
        });

        $("#bidsFilter").on('change', function () {
            var status = $("#bidsFilter").val();
            $('#mybidsTable tbody > tr').show();

            $("#mybidsTable tbody > tr").each(function (index, tdAR) {
                if (!($(tdAR).hasClass(status))) {
                    $('#mybidsTable tbody > tr').hide();
                }
            });

            if (status === 'zero') {
                $('#mybidsTable tbody > tr').show();
            }
        });

        $("#reviewsFilter").on('change', function () {
            var status = $("#reviewsFilter").val();
            $('#myreviewsTable tbody > tr').show();

            $("#myreviewsTable tbody > tr").each(function (index, tdAR) {
                if (!($(tdAR).hasClass(status))) {
                    $('#myreviewsTable tbody > tr').hide();
                }
            });

            if (status === 'zero') {
                $('#myreviewsTable tbody > tr').show();
            }
        });
    });
</script>

<div class="body-content">
    <div class="container">
        <br/><br/>
        <!-- User Profile -->
        <div class="row">
            <div class="col-md-5 col-md-offset-4" id="userProfileBox">
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
        </div>
        <!-- User Profile -->

        <br/>
        <div class="row">
            <!-- My Request Table  -->
            <div id="myrequests" class="col-xs-12 col-sm-4">
                <h3><span class='glyphicon glyphicon-shopping-cart'></span>&nbsp; My Requests</h3>
                <select id="requestFilter">
                    <option name="status" value="zero">-----Select------</option>
                    <option name="status" value="Open">Open</option>
                    <option name="status" value="Accepted">Accepted</option>
                    <option name="status" value="Pending Delivery">Pending Delivery</option>
                    <option name="status" value="Completed">Completed</option>
                    <option name="status" value="Cancelled">Cancelled</option>
                </select>
                <br/><br/>
                <table class="table table-hover" id="myrequeststable">
                    <?php
                    $productQuery = mysqli_query($conn, "SELECT name, image, product_id, product_status "
                            . " FROM product WHERE user_id = '$username' AND product_status <> 'Deleted'"
                            . " ORDER BY requestDate desc");
                    if (mysqli_num_rows($productQuery) > 0) {
                        while ($productRow = mysqli_fetch_array($productQuery)) {
                            $p_id = $productRow['product_id'];
                            $retrieveProduct = mysqli_query($conn, "SELECT bid_id FROM product_bid WHERE pid = '$p_id' AND bid_status IN ('Accepted', 'Open')");
                            $numberOfBids = mysqli_num_rows($retrieveProduct);
                            $numMsg = "";
                            if ($productRow['product_status'] == 'Accepted' || $productRow['product_status'] == 'Open') {
                                if ($numberOfBids == 0) {
                                    $numMsg = "No bids by traveler yet";
                                } else {
                                    $numMsg = $numberOfBids . " bid(s)";
                                }
                            } else {
                                $dateQuery = mysqli_query($conn, "SELECT travel_to FROM product_bid "
                                        . " WHERE pid = '$p_id' AND bid_status IN ('Pending Delivery', 'Completed')");
                                if (mysqli_num_rows($dateQuery) > 0) {
                                    $dateRow = mysqli_fetch_array($dateQuery);
                                    $numMsg = "Travel until: " . $dateRow['travel_to'];
                                }
                            }
                            ?>
                            <tr class="<?php echo $productRow['product_status']; ?>">
                                <td width="70px"><a href="viewProduct.php?pid=<?php echo $p_id; ?>"><img src='assets/product_img/<?php echo $productRow["image"]; ?>' width="70px" height="70px"></a></td>
                                <td class="productRequest"><a href="viewProduct.php?pid=<?php echo $p_id; ?>" class="href-link" style="text-decoration:none; color:black;"><div><?php echo $productRow['name'] . "<br/>" . $productRow['product_status'] . "<br/>" . $numMsg; ?></div></a></td>
                                <td>
                                    <?php
                                    $checkTravel = mysqli_query($conn, "SELECT travel_from, product_status FROM product p LEFT OUTER JOIN "
                                            . "( SELECT travel_from, pid FROM product_bid GROUP BY pid ) AS a ON a.pid = p.product_id "
                                            . " WHERE p.product_status IN ('Accepted', 'Open') AND p.product_id = '$p_id'"
                                            . " AND p.user_id = '$username'");
                                    if (mysqli_num_rows($checkTravel) == 1) {
                                        $checkTravelRow = mysqli_fetch_array($checkTravel);
                                        $travelFrom = $checkTravelRow['travel_from'];
                                        $checkTravelDate = date('Y-m-d', (strtotime('-1 day', strtotime($travelFrom))));

                                        if (( $todayDate < $checkTravelDate) || (empty($travelFrom))) {
                                            ?>
                                            <button style="width: 60px;" href="#cancelRequest" onclick="cancelRequest(this);" p-id='<?php echo $p_id ?>' data-id="<?php echo $username ?>" print='5' data-toggle="modal" class="btn btn-sm btn-danger">Cancel</button>
                                            <?php
                                        } else if ($checkTravelRow['product_status'] == 'Open') {
                                            ?>
                                            <button style="width: 60px;" href="#cancelRequest" onclick="cancelRequest(this);" p-id='<?php echo $p_id ?>' data-id="<?php echo $username ?>" print='5' data-toggle="modal" class="btn btn-sm btn-danger">Cancel</button>
                                            <?php
                                        }
                                    }

                                    $productStatus = mysqli_query($conn, "SELECT bid_id, travel_to, travel_from, pb.user_id FROM product p, product_bid pb "
                                            . "WHERE p.product_id = pb.pid AND p.product_id = '$p_id' AND p.product_status = 'Pending Delivery'");
                                    if (mysqli_num_rows($productStatus) == 1) {
                                        $productRow = mysqli_fetch_array($productStatus);
                                        
                                        if ( ($todayDate >= $productRow['travel_from']) && ($todayDate < $productRow['travel_to']) ) {
                                            ?>
                                            <button style="width: 60px;" href="#cancelProductRequest" onclick="cancelProductRequest(this);" bid-id='<?php echo $productRow["bid_id"]?>' p-id='<?php echo $p_id ?>' print='8' data-toggle="modal" class="btn btn-sm btn-danger">Cancel</button>
                                            <?php
                                        } else if ($todayDate >= $productRow['travel_to']) {
                                            ?>
                                            <button style="width: 60px;" href="#submitRating" onclick="submitRating(this);" data-id="<?php echo $productRow['user_id'] ?>" p-id='<?php echo $p_id ?>' bid-id='<?php echo $productRow['bid_id'] ?>' print='1' data-toggle="modal" class="btn btn-sm btn-primary">Rate</button>
                                            <?php
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo '<div class="alert alert-info">You have not submit any product request</div>';
                    }
                    ?>
                </table>
            </div>

            <!-- My Bids Table  -->
            <div id="mybids" class="col-xs-12 col-sm-4">
                <h3><span class='glyphicon glyphicon-usd'></span>&nbsp; My Bids</h3>
                <select id="bidsFilter">
                    <option name="status" value="zero">-----Select------</option>
                    <option name="status" value="Open">Open</option>
                    <option name="status" value="Accepted">Accepted</option>
                    <option name="status" value="Pending Delivery">Pending Delivery</option>
                    <option name="status" value="Completed">Completed</option>
                </select>
                <br/><br/>
                <table class="table table-hover" id="mybidsTable">
                    <?php
                    $bidQuery = mysqli_query($conn, "SELECT name, travel_to, travel_from, bid_status, pid, image "
                            . " FROM product_bid pb, product p "
                            . " WHERE pb.pid = p.product_id AND pb.user_id = '$username' "
                            . " AND bid_status IN ('Accepted', 'Pending Delivery', 'Open', 'Completed') ORDER BY date_bidded desc");
                    if (mysqli_num_rows($bidQuery) > 0) {
                        while ($bidRow = mysqli_fetch_array($bidQuery)) {
                            $p_id = $bidRow['pid'];
                            ?>
                            <tr class="<?php echo $bidRow['bid_status']; ?>">
                                <td width="70px"><a href="viewProduct.php?pid=<?php echo $p_id; ?>"><img src='assets/product_img/<?php echo $bidRow["image"]; ?>' width="70px" height="70px"></a></td>
                                <td class="bidSubmitted"><a href="viewProduct.php?pid=<?php echo $p_id; ?>" class="href-link" style="text-decoration:none; color:black;"><div><?php echo $bidRow['name'] . "<br/>" . $bidRow['bid_status'] . "<br/>" . $bidRow['travel_from'] . " to " . $bidRow['travel_to']; ?></div></a></td>
                                <td>
                                    <?php
                                    $checkTravel = mysqli_query($conn, "SELECT travel_from, travel_to, product_status, bid_id FROM product_bid pb, product p "
                                            . "WHERE pb.pid = p.product_id AND pb.user_id = '$username' "
                                            . "AND pb.bid_status IN ('Accepted', 'Open') AND pb.pid = '$p_id'");
                                    if (mysqli_num_rows($checkTravel) == 1) {
                                        $checkTravelRow = mysqli_fetch_array($checkTravel);
                                        $travelFrom = $checkTravelRow['travel_from'];
                                        $checkTravelDate = date('Y-m-d', (strtotime('-1 day', strtotime($travelFrom))));
                                        if (( $todayDate < $checkTravelDate) || (empty($travelFrom))) {
                                            $bid_id = $checkTravelRow['bid_id'];
                                            ?>
                                            <button style="width: 60px;" href="#cancelBid" onclick="cancelBid(this);" data-id="<?php echo $username ?>" p-id='<?php echo $bid_id ?>' print='4' data-toggle="modal" class="btn btn-sm btn-danger">Cancel</button>
                                            <?php
                                        } else if ($todayDate >= $checkTravelRow['travel_to']) {
                                            $checkTravelBid = mysqli_query($conn, "SELECT bid_status FROM product_bid WHERE bid_id = '$bid_id'");
                                            $checkTravelBidRow = mysqli_fetch_array($checkTravelBid);
                                            if ($checkTravelBidRow['bid_status'] == 'Accepted') {
                                                ?>
                                                <button style="width: 60px;" href="#cancellation" onclick="cancellation(this);" data-id="<?php echo $username ?>" p-id='<?php echo $bid_id ?>' print='6' data-toggle="modal" class="btn btn-sm btn-danger">Cancel</button>
                                                <?php
                                            }
                                        }
                                        echo "<div style='margin-top: 5px;'></div>";
                                        if ($checkTravelRow['product_status'] == 'Open') {
                                            ?>
                                            <button style="width: 60px;" href="#editBid" onclick="editBid(this);" data-id="<?php echo $username ?>" p-id='<?php echo $bid_id ?>' data-toggle="modal" class="btn btn-sm btn-warning">Edit</button>
                                            <?php
                                        }
                                    }

                                    $productStatus = mysqli_query($conn, "SELECT bid_id, travel_from, travel_to, p.user_id FROM product p, product_bid pb "
                                            . "WHERE p.product_id = pb.pid AND p.product_id = '$p_id' AND pb.bid_status = 'Pending Delivery'");
                                    if (mysqli_num_rows($productStatus) == 1) {
                                        $productRow = mysqli_fetch_array($productStatus);
                                        
                                        if ( ($todayDate >= $productRow['travel_from']) && ($todayDate < $productRow['travel_to']) ) {
                                            ?>
                                            <button style="width: 60px;" href="#cancelRequestBid" onclick="cancelRequestBid(this);" bid-id='<?php echo $productRow['bid_id'] ?>' print='7' data-toggle="modal" class="btn btn-sm btn-danger">Cancel</button>
                                            <?php
                                        } else if ($todayDate >= $productRow['travel_to']) {
                                            ?>
                                            <button style="width: 60px;" href="#submitRating" onclick="submitRating(this);" data-id="<?php echo $productRow['user_id'] ?>" p-id='<?php echo $p_id ?>' bid-id='<?php echo $productRow['bid_id'] ?>' print='2' data-toggle="modal" class="btn btn-sm btn-primary">Rate</button>
                                            <br/>
                                            <button style="width: 60px;" href="#cancelRequestBid" onclick="cancelRequestBid(this);" bid-id='<?php echo $productRow['bid_id'] ?>' print='7' data-toggle="modal" class="btn btn-sm btn-danger">Cancel</button>
                                            <?php
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo '<div class="alert alert-info">You have not submit any bid</div>';
                    }
                    ?>
                </table>
            </div>

            <!-- My Ratings Table  -->
            <div id="ratings" class="col-xs-12 col-sm-4">
                <h3><span class='glyphicon glyphicon-star'></span>&nbsp; My Reviews</h3>
                <select id="reviewsFilter">
                    <option name="status" value="zero">-----Select------</option>
                    <option name="status" value="Requester">Requester</option>
                    <option name="status" value="Traveler">Traveler</option>
                </select>
                <br/><br/>
                <table class="table table-hover" id="myreviewsTable">
                    <?php
                    $ratingStatus = "";
                    $ratingQuery = mysqli_query($conn, "SELECT r.score, r.review, r.user_id AS ruser, p.user_id AS puser "
                            . " FROM user_rating r, product p"
                            . " WHERE r.pid = p.product_id AND r.user_id = '$username' ORDER BY score desc");
                    $checkRating = mysqli_fetch_array($ratingQuery);
                    if (($checkRating['score'] == 0) && (empty($checkRating['review']))) {
                        echo '<div class="alert alert-info">You do not have any reviews</div>';
                    } else {
                        if (mysqli_num_rows($ratingQuery) > 0) {
                            $rating2Query = mysqli_query($conn, "SELECT r.score, r.review, r.user_id AS ruser, p.user_id AS puser "
                                    . " FROM user_rating r, product p"
                                    . " WHERE r.pid = p.product_id AND r.user_id = '$username' ORDER BY score desc");
                            while ($ratingRow = mysqli_fetch_array($rating2Query)) {
                                $ratingUser = $ratingRow['ruser'];
                                $productUser = $ratingRow['puser'];
                                if ($productUser == $username) {
                                    $role = "Requester";
                                } else {
                                    $role = "Traveler";
                                }
                                ?>
                                <tr class="<?php echo $role; ?>" height="87px">
                                <script>
                                    $(document).ready(function () {
                                        $('#score').raty({
                                            readOnly: true,
                                            score: <?php echo $ratingRow['score'] ?>
                                        });
                                    });</script>
                                <td class="myreviews">
                                    <?php if (!empty($ratingRow['review'])) { ?>
                                        <div>
                                            Role: <?php echo $role . "<br/>"; ?>
                                            <span id="score"></span><br/>
                                            <?php echo $ratingRow['review'] ?>
                                        </div>
                                    <?php } else if (($ratingRow['score'] > 0) && (empty($ratingRow['review']))) { ?>
                                        <div>
                                            Role: <?php echo $role . "<br/>"; ?>
                                            <span id="score"></span><br/>
                                            <?php echo "No reviews given"; ?>
                                        </div>
                                    <?php } ?>
                                </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<div class="alert alert-info">You do not have any reviews</div>';
                        }
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>

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
                            <div id="cancelBidView"></div>
                        </center>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                        <input type="submit" value="Cancel Bid" id="submit" name="submit" class="btn btn-danger">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Cancel Bid Modal -->

    <!-- Edit Modal -->
    <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="editBid" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="form-horizontal style-form" method="post" action='submitBids.php'>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Confirmation</h4>
                    </div>
                    <div class="modal-body">
                        <center>
                            <div id="edit"></div>
                        </center>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                        <input type="submit" value="Edit Bid" id="submit" name="submit" class="btn btn-warning">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Modal -->
    
    <!-- Cancel Request Bid Modal -->
    <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="cancelRequestBid" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="form-horizontal style-form" method="post" action='processViewBids.php'>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Confirmation</h4>
                    </div>
                    <div class="modal-body">
                        <center>
                            <div id="cancelRequestBidView"></div>
                        </center>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                        <input type="submit" value="Cancellation" id="submit" name="submit" class="btn btn-danger">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Cancel Request Bid Modal -->
    
    <!-- Cancel Product Request Modal -->
    <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="cancelProductRequest" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="form-horizontal style-form" method="post" action='processViewBids.php'>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Confirmation</h4>
                    </div>
                    <div class="modal-body">
                        <center>
                            <div id="cancelProductRequestView"></div>
                        </center>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                        <input type="submit" value="Cancellation" id="submit" name="submit" class="btn btn-danger">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Cancel Product Request Modal -->

</div>

<script>
    function submitRating(d) {
        var userid = d.getAttribute("data-id");
        var pid = d.getAttribute("p-id");
        var bid = d.getAttribute("bid-id");
        var print = d.getAttribute("print");
        $.ajax({
            type: "GET",
            url: "processViewRating.php",
            data: 'userid=' + userid + '&pid=' + pid + '&bid=' + bid + '&print=' + print,
            cache: false,
            success: function (data) {
                $('.modal-body #ratingView').html(data);
            }
        });
    }
    
    function cancelRequestBid(d) {
        var bid = d.getAttribute("bid-id");
        var print = d.getAttribute("print");
        $.ajax({
            type: "GET",
            url: "processView.php",
            data: 'bid=' + bid + '&print=' + print,
            cache: false,
            success: function (data) {
                $('.modal-body #cancelRequestBidView').html(data);
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

    function editBid(d) {
        var id = d.getAttribute("data-id");
        var pid = d.getAttribute("p-id");
        $.ajax({
            type: "GET",
            url: "processView.php",
            data: 'id=' + id + "&print=6" + '&pid=' + pid,
            cache: false,
            success: function (data) {
                $('.modal-body #edit').html(data);
            }
        });
    }
    
    function cancelProductRequest(d) {
        var pid = d.getAttribute("p-id");
        var print = d.getAttribute("print");
        var bid = d.getAttribute("bid-id");
        $.ajax({
            type: "GET",
            url: "processView.php",
            data: "print=" + print + '&pid=' + pid + '&bid=' + bid,
            cache: false,
            success: function (data) {
                $('.modal-body #cancelProductRequestView').html(data);
            }
        });
    }
</script>