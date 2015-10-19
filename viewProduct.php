<?php
include 'header.php';
$username = $_SESSION['username'];
//$productID = "";
$productID = filter_input(INPUT_GET, 'pid');
$search = filter_input(INPUT_GET, 'search');
$category = filter_input(INPUT_GET, 'category');

if (empty($productID)) {
    header('Location: index.php');
    die;
}

$checkProduct = mysqli_query($conn, "SELECT * FROM product WHERE product_id = '$productID'");
if (mysqli_num_rows($checkProduct) == 1) {
    $row = mysqli_fetch_array($checkProduct);
    $buyerProfile = $row['user_id'];
    $product_status = $row['product_status'];
}

$ownProduct = false;
if ($username == $buyerProfile) {
    $ownProduct = true;
}

$todayDate = date("Y-m-d");
?>

<script type="text/javascript" src="/BuyForMeApp/assets/js/jquery.raty.js"></script>
<style>
    .profileBox {
        height: 100px;
        width: 100%;
        margin-top: 5%;
    }

    .profilePic {
        height: 80%;
        width: 35%;
        border-radius: 50%;
        margin-left: 5%;
        border: 1px solid #cccfd0;
        float: left;
    }
</style>
<div class="body-content">
    <div class="row">
        <div class="container">
            <br/><br/>
            <?php
            if ($product_status == 'Deleted') {
                echo '<div class="alert alert-warning" role="alert">Product have been deleted. Please view other product</div>';
            } else {
                ?>
                <div class="row">
                    <ol class="breadcrumb">
                        <?php if (!$search) { ?>
                            <li><a href="browse.php">List of Requests</a></li>
                            <?php
                        } else if ($search) {
                            $searchCategory = filter_input(INPUT_GET, 'searchCategory');
                            $search = filter_input(INPUT_GET, 'search');
                            ?>
                            <li><a href="searchResult.php?searchCategory=<?php echo $searchCategory; ?>&search=<?php echo $search; ?>">Search Requests</a></li>
                        <?php } ?>
                        <li class="active"><?php echo $row['name']; ?></li>
                    </ol>
                    <div class="col-md-1"></div>
                    <div class="col-md-3">
                        <center><span style='font-size: 18px'><?php echo $row['name']; ?></span></center>
                        <div class='thumbnail'>
                            <img src='assets/product_img/<?php echo $row["image"]; ?>' width='150px' height='150px'>
                        </div>
                        <?php if (!$ownProduct) { ?>
                            <div class='profileBox'>
                                <?php
                                $userQuery = mysqli_query($conn, "SELECT profile_img FROM user WHERE username = '$buyerProfile'");
                                $userResult = mysqli_fetch_array($userQuery);
                                ?>

                                <a href="#" class="requesterPop">
                                    <img id="requester" class="profilePic" src='assets/user_img/<?php echo $userResult['profile_img']; ?>' />
                                </a>
                                <span id='profileDetails'>
                                    <span class="requesterPop" style='font-size: 20px'><?php echo $row['user_id']; ?></span> <a href="createMessage.php?id=<?php echo $row['user_id']; ?>&pid=<?php echo $productID ?>"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span></a>
                                    <br/>
                                    <?php
                                    $buyer = $row['user_id'];
                                    $retrieveBuyer = mysqli_query($conn, "SELECT * FROM user WHERE username = '$buyer'");
                                    if (mysqli_num_rows($retrieveBuyer) > 0) {
                                        $buyerRow = mysqli_fetch_array($retrieveBuyer);
                                        ?>
                                        <script>
                                            $(document).ready(function () {
                                                $('#requestStar').raty({
                                                    readOnly: true,
                                                    score: <?php echo $buyerRow['rating'] ?>
                                                });
                                            });</script>
                                        <span id='requestStar'></span>
                                    <?php } ?>
                                    <br/>
                                    <span style='font-size: 16px'>03</span> <span style='font-size: 12px'>request(s)</span>
                                </span>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="col-md-7">
                        <?php
                        echo "<p><span class='glyphicon glyphicon-th-list' aria-hidden='true' style='margin-right:3%'></span>" . $row['cat'] . "</p>";
                        if (!empty($row['store'])) {
                            echo "<p><span class='glyphicon glyphicon-map-marker' aria-hidden='true' style='margin-right:3%'></span>" . $row['country'] . ", " . $row['store'] . "</p>";
                        } else {
                            echo "<p><span class='glyphicon glyphicon-map-marker' aria-hidden='true' style='margin-right:3%'></span>" . $row['country'] . "</p>";
                        }
                        echo "<p><span class='glyphicon glyphicon-tag' aria-hidden='true' style='margin-right:3%'></span>" . $row['approx_price'] . "</p>";
                        echo "<p><span class='glyphicon glyphicon-pencil' aria-hidden='true' style='margin-right:3%'></span>" . $row['description'] . "</p>";
                        if (!empty($row['url'])) {
                            echo "<p><span class='glyphicon glyphicon-link' aria-hidden='true' style='margin-right:3%'></span><a href='" . $row['url'] . "' target='_blank'>Product Link</a></p>";
                        }

                        $checkBidStatus = false;
                        $travelerBidId = "";
                        $checkBids = mysqli_query($conn, "SELECT user_id FROM product_bid WHERE pid = '$productID' AND bid_status IN ('Accepted', 'Pending Delivery')");
                        if (mysqli_num_rows($checkBids) > 0) {
                            $checkBidStatus = true;
                            $checkBidRow = mysqli_fetch_array($checkBids);
                            $travelerBidId = $checkBidRow['user_id'];
                        }

                        $checkUserStatus = false;
                        $checkUser = mysqli_query($conn, "SELECT * FROM product_bid WHERE pid = '$productID' AND bid_status = 'Open' AND user_id = '$username'");
                        if (mysqli_num_rows($checkUser) > 0) {
                            $checkUserStatus = true;
                        }

                        $checkProductStatus = mysqli_query($conn, "SELECT product_status, user_id FROM product WHERE product_id = '$productID'");
                        $checkPSrow = mysqli_fetch_array($checkProductStatus);
                        $productStatus = $checkPSrow['product_status'];
                        $productUser = $checkPSrow['user_id'];

                        $checkProductTravelerStatus = mysqli_query($conn, "SELECT bid_status FROM product_bid"
                                . " WHERE pid = '$productID' AND bid_status = 'Completed' AND user_id = '$username'");

                        if ($productStatus == 'Completed' || $productStatus == 'Cancelled') {
                            echo "<form method='post' action='post_request.php'>";
                            echo "<button type='submit' class='btn btn-primary'>Repost Product</button>";
                            echo "<input type='hidden' name='productID' value='" . $productID . "' />";
                            echo "<input type='hidden' name='link' value='repost' />";
                            echo "</form>";
                            echo "<br/>";

                            if ($productStatus == 'Completed') {
                                ?>
                                <div class="alert alert-info" role="alert">Product trade has been completed</div>
                                <?php
                            } else if ($productStatus == 'Cancelled') {
                                ?>
                                <div class="alert alert-info" role="alert">Product trade has been cancelled</div>
                                <?php
                                $checkCancel = mysqli_query($conn, "SELECT cancellation FROM product_bid WHERE pid = '$productID' AND bid_status = 'Cancelled'");
                                $checkCancelRow = mysqli_fetch_array($checkCancel);

                                echo "<p> Reason: <br/>" . $checkCancelRow['cancellation'] . "</p>";
                                ?>
                                <?php
                            }
                        } else if ((mysqli_num_rows($checkProductTravelerStatus) == 1) || ($productStatus == 'Pending Delivery')) {
                            ?>
                            <div class="alert alert-info" role="alert">Product trade has been completed</div>
                            <?php
                        } else {
                            if (!$ownProduct && !$checkBidStatus && !$checkUserStatus) {
                                ?>
                                <br/>
                                <form action="submitBids.php" method="post">
                                    <input type="hidden" name="productID" value='<?php echo $productID ?>' />
                                    <input type="submit" value="Submit Bid" class="btn btn-primary" />
                                </form>
                                <?php
                            } else if (!$ownProduct && !$checkBidStatus && $checkUserStatus) {
                                echo '<div class="alert alert-info" role="alert">You already bidded for this product</div>';
                            }
                            ?>
                            <br/>
                            <?php
                            if (!$checkBidStatus) {
                                if ($ownProduct) {
                                    $checkNumQuery = mysqli_query($conn, "SELECT bid_id FROM product_bid WHERE pid = '$productID' AND bid_status = 'Open'");
                                    if (mysqli_num_rows($checkNumQuery) == 0) {
                                        $checkNumRow = mysqli_fetch_array($checkNumQuery);
                                        echo "<form method='post' action='post_request.php'>";
                                        echo "<button type='submit' class='btn btn-primary'>Edit Product</button>";
                                        echo "<input type='hidden' name='productID' value='" . $productID . "' />";
                                        echo "<input type='hidden' name='link' value='edit' />";
                                        echo "</form>";
                                        echo "<br/>";
                                    }

                                    if ($category == 'nobids') {
//                                        “Delete Product Request”, “Promote Product Request” and “Do Nothing”.
                                        ?>
                                        <a class="btn btn-default" href="processViewBids.php?pid=<?php echo $productID; ?>&status=deleteProduct" role="button">Delete Product Request</a>
                                        <a class="btn btn-default" href="processViewBids.php?pid=<?php echo $productID; ?>&status=promoteProduct" role="button">Promote Product Request</a>
                                        <a class="btn btn-default" href="viewProduct.php?pid=<?php echo $productID; ?>" role="button">Do Nothing</a>
                                        <br/><br/>
                                        <?php
//                                        $updateRequestQuery = mysqli_query($conn, "UPDATE product SET requestDate = $todayDate WHERE pid = '$productID'");
//                                        mysqli_fetch_array($updateRequestQuery);
                                    }
                                }

                                $retrieveBids = mysqli_query($conn, "SELECT * FROM product_bid WHERE pid = '$productID' AND bid_status = 'Open'");
                                if (mysqli_num_rows($retrieveBids) > 0) {
                                    ?>
                                    <h4>List of Bids</h4>
                                    <table class="table table-bordered">
                                        <tr>
                                            <?php if ($ownProduct) { ?>
                                            <tr style="background-color: lightsteelblue; font-weight: bold;">
                                                <td>Travel Period</td>
                                                <td>Price</td>
                                                <td>Traveler</td>
                                                <td>Actions</td>
                                            </tr>
                                        <?php } else { ?>
                                            <tr style="background-color: lightsteelblue; font-weight: bold;">
                                                <td>Date Bidded</td>
                                                <td>Price</td>
                                                <td>Actions</td>
                                            </tr>
                                        <?php } ?>
                                        </tr>
                                        <?php
                                        while ($bidRow = mysqli_fetch_array($retrieveBids)) {
                                            echo "<tr>";
                                            if ($ownProduct) { //if own product
                                                ?>
                                                <td width='200px'><?php echo $bidRow['travel_from'] . " <b>to</b> " . $bidRow['travel_to']; ?></td>
                                                <td width='70px'><?php echo $bidRow['price']; ?></td>
                                                <td width='100px'> 
                                                    <button href="#travelerProfile" onclick="travelerProfile(this);"  data-id="<?php echo $bidRow['user_id'] ?>" p-id='<?php echo $productID ?>' data-toggle="modal" class="btn btn-sm btn-default" id='travelerBtn'><?php echo $bidRow['user_id'] ?></button>
                                                </td>
                                                <td width='100px'>
                                                    <button href="#acceptBid" onclick="acceptBid(this);" data-id="<?php echo $bidRow['user_id'] ?>" p-id='<?php echo $productID ?>' data-toggle="modal" class="btn btn-sm btn-success">Accept</button>
                                                    <button href="#rejectBid" onclick="rejectBid(this);" data-id="<?php echo $bidRow['user_id'] ?>" p-id='<?php echo $productID ?>' data-toggle="modal" class="btn btn-sm btn-danger">Reject</button>
                                                </td>
                                                <?php
                                            } else {
                                                $bidUser = $bidRow['user_id'];
                                                $bidId = $bidRow['bid_id'];
                                                echo "<td>" . $bidRow['date_bidded'] . "</td>";
                                                echo "<td>" . $bidRow['price'] . "</td>";
                                                if ($bidUser == $username) {
                                                    ?>
                                                    <td width='150px'>
                                                        <button style="width: 60px;" href="#editBid" onclick="editBid(this);" data-id="<?php echo $username ?>" p-id='<?php echo $bidId ?>' data-toggle="modal" class="btn btn-sm btn-warning">Edit</button>
                                                        <button style="width: 60px;" href="#deleteBid" onclick="deleteBid(this);" data-id="<?php echo $username ?>" p-id='<?php echo $bidId ?>' data-toggle="modal" class="btn btn-sm btn-danger">Delete</button>   
                                                    </td>
                                                    <?php
                                                } else {
                                                    echo "<td></td>";
                                                }
                                            }
                                            echo "</tr>";
                                        }
                                        ?>
                                    </table>
                                    <?php if (!$search) { ?>
                                        <a class="btn btn-default" href="browse.php" role="button">Back</a>
                                        <?php
                                    } else if ($search) {
                                        $searchCategory = filter_input(INPUT_GET, 'searchCategory');
                                        $search = filter_input(INPUT_GET, 'search');
                                        ?>
                                        <a class="btn btn-default" href="searchResult.php?searchCategory=<?php echo $searchCategory; ?>&search=<?php echo $search; ?>" role="button">Back</a>
                                    <?php } ?>

                                    <?php
                                } else {
                                    echo '<div class="alert alert-info" role="alert">Currently, no bids for this product</div>';
                                }
                            } else {
                                if ($ownProduct || $travelerBidId == $username) {
                                    $bidHistoryQuery = "SELECT * FROM product_bid WHERE pid = '$productID' AND bid_status = 'Accepted'";
                                    $checkBidHistory = mysqli_query($conn, $bidHistoryQuery);
                                    if (mysqli_num_rows($checkBidHistory) == 1) {
                                        $bidHistoryRow = mysqli_fetch_array($checkBidHistory);
                                        $travelFrom = $bidHistoryRow['travel_from'];
                                        ?>
                                        <div class="alert alert-info" role="alert">Bidding for product is closed</div>
                                        <p>Bidding Record</p>
                                        <table>
                                            <tr>
                                                <td>Traveler: </td>
                                                <td><span style="margin-left: 10px;"><?php echo $bidHistoryRow['user_id']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td>Travel Period: </td>
                                                <td><span style="margin-left: 10px;"><?php echo $bidHistoryRow['travel_from'] . " to " . $bidHistoryRow['travel_to']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td>Shipping Price: </td>
                                                <td><span style="margin-left: 10px;"><?php echo $bidHistoryRow['price']; ?></span></td>
                                            </tr>
                                        </table>
                                        <br/>

                                        <?php
                                        $checkTravelDate = date('Y-m-d', (strtotime('-1 day', strtotime($travelFrom))));
                                        if ($todayDate < $checkTravelDate) {
                                            ?>
                                            <button href="#deleteRequest" onclick="deleteRequest(this);" data-id="<?php echo $username ?>" p-id='<?php echo $productID ?>' print='4' data-toggle="modal" class="btn btn-sm btn-danger">Cancel Request</button>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <div class="alert alert-info" role="alert">Bidding for product is closed</div>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <div class="alert alert-info" role="alert">Bidding for product is closed</div>
                                    <?php
                                }
                            }
                        }
                        ?>

                        <!-- Traveler Profile Modal -->
                        <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="travelerProfile" class="modal fade">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form class="form-horizontal style-form" method="post">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title">About the Traveller</h4>
                                        </div>
                                        <div class="modal-body">
                                            <center>
                                                <h3>Traveler Profile</h3>
                                                <div id="profile"></div>
                                            </center>
                                        </div>
                                        <div class="modal-footer">
                                            <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>

                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Traveler Profile Modal -->

                        <!-- Accept Modal -->
                        <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="acceptBid" class="modal fade">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form class="form-horizontal style-form" method="post" action='processViewBids.php'>
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title">Confirmation</h4>
                                        </div>
                                        <div class="modal-body">
                                            <center>
                                                <h3>Accept Bid</h3>
                                                <div id="accept"></div>
                                                <br/><br/>
                                                <p>
                                                    Would you like to accept this bid?
                                                </p>
                                            </center>
                                        </div>
                                        <div class="modal-footer">
                                            <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                                            <input type="submit" value="Accept Bid" id="submit" name="submit" class="btn btn-success">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Accept Modal -->

                        <!-- Reject Modal -->
                        <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="rejectBid" class="modal fade">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form class="form-horizontal style-form" method="post" action='processViewBids.php'>
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title">Confirmation</h4>
                                        </div>
                                        <div class="modal-body">
                                            <center>
                                                <h3>Reject Bid</h3>
                                                <div id="reject"></div>
                                                <br/><br/>
                                                <p>
                                                    Are you sure you want to reject this bid?
                                                </p>
                                            </center>
                                        </div>
                                        <div class="modal-footer">
                                            <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                                            <input type="submit" value="Reject Bid" id="submit" name="submit" class="btn btn-danger">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Reject Modal -->

                        <!-- Delete Modal -->
                        <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="deleteBid" class="modal fade">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form class="form-horizontal style-form" method="post" action='processViewBids.php'>
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title">Confirmation</h4>
                                        </div>
                                        <div class="modal-body">
                                            <center>
                                                <h3>Delete Bid</h3>
                                                <div id="delete"></div>
                                            </center>
                                        </div>
                                        <div class="modal-footer">
                                            <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                                            <input type="submit" value="Delete Bid" id="submit" name="submit" class="btn btn-danger">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Delete Modal -->

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
                                                <h3>Edit Bid</h3>
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

                        <!-- Delete Request Modal -->
                        <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="deleteRequest" class="modal fade">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form class="form-horizontal style-form" method="post" action='processViewBids.php'>
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title">Confirmation</h4>
                                        </div>
                                        <div class="modal-body">
                                            <center>
                                                <h3>Cancel Request</h3>
                                                <div id="deleteRequest"></div>
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
                        <!-- Delete Request Modal -->

                        <!-- Requester Modal -->
                        <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="requesterModal" class="modal fade">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title">About the Traveller</h4>
                                    </div>
                                    <div class="modal-body">
                                        <center>
                                            <h3>Requester Profile</h3>
                                            <?php
                                            $result = mysqli_query($conn, "SELECT * FROM user WHERE username = '$buyerProfile'");

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
                                            ?>
                                        </center>
                                    </div>
                                    <div class="modal-footer">
                                        <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Requester Modal -->

                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
    function travelerProfile(d) {
        var id = d.getAttribute("data-id");
        var pid = d.getAttribute("p-id");
        $.ajax({
            type: "GET",
            url: "processView.php",
            data: 'id=' + id + "&print=1" + '&pid=' + pid,
            cache: false,
            success: function (data) {
                $('.modal-body #profile').html(data);
            }
        });
    }

    function acceptBid(d) {
        var id = d.getAttribute("data-id");
        var pid = d.getAttribute("p-id");
        $.ajax({
            type: "GET",
            url: "processView.php",
            data: 'id=' + id + "&print=2" + '&pid=' + pid,
            cache: false,
            success: function (data) {
                $('.modal-body #accept').html(data);
            }
        });
    }

    function rejectBid(d) {
        var id = d.getAttribute("data-id");
        var pid = d.getAttribute("p-id");
        $.ajax({
            type: "GET",
            url: "processView.php",
            data: 'id=' + id + "&print=3" + '&pid=' + pid,
            cache: false,
            success: function (data) {
                $('.modal-body #reject').html(data);
            }
        });
    }

    function deleteBid(d) {
        var id = d.getAttribute("data-id");
        var pid = d.getAttribute("p-id");
        $.ajax({
            type: "GET",
            url: "processView.php",
            data: 'id=' + id + "&print=4" + '&pid=' + pid,
            cache: false,
            success: function (data) {
                $('.modal-body #delete').html(data);
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

    function deleteRequest(d) {
        var id = d.getAttribute("data-id");
        var pid = d.getAttribute("p-id");
        $.ajax({
            type: "GET",
            url: "processView.php",
            data: 'id=' + id + "&print=5" + '&pid=' + pid,
            cache: false,
            success: function (data) {
                $('.modal-body #deleteRequest').html(data);
            }
        });
    }

    $(".requesterPop").on("click", function () {
//        $('#imagepreview').attr('src', $('#requester').attr('src'));
        $('#requesterModal').modal('show');

    });
</script>