<?php
include 'header.php';
include 'db.php';

$searchCategory = filter_input(INPUT_GET, "searchCategory");
$search = filter_input(INPUT_GET, "search");
?>
<script type="text/javascript" src="/BuyForMeApp/assets/js/jquery.raty.js"></script>

<style>
    .searchProfile {
        border-radius: 50%;
        margin-left: 5%;
        border: 1px solid #cccfd0;
        float: left;
    }

    .searchDetails {
        margin-left: 2%;
        float: left;
    }

    .searchProfileBox {
        height: 100%;
    }
</style>
<div class="body-content">
    <br/><br/>
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="browse.php">Browse Request</a></li>
            <li class="active"><?php echo $search; ?></li>
        </ol>
        <div class="row">
            <div class="col-md-12">
                <div class="col-sm-4">
                    <h4>Result(s) of <?php echo $search; ?></h4>
                </div>
                <br/><br/><br/>
                <?php
                if (!empty($searchCategory) && !empty($search)) {
                    if ($searchCategory == 'Product') {
                        $retrieveProduct = mysqli_query($conn, "SELECT * FROM product p LEFT OUTER JOIN "
                                . "(SELECT COUNT(*) AS numBids, MIN(price) AS minPrice, pid FROM product_bid WHERE bid_status = 'Open' GROUP BY pid ) AS a ON a.pid = p.product_id "
                                . "WHERE p.product_status = 'Open' AND p.name LIKE '%$search%'");
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

                                <div class="col-sm-3">
                                    <div class="thumbnail">
                                        <div class="thumbnail-top">
                                            <a href="viewProduct.php?pid=<?php echo $pid; ?>&search=true&searchCategory=<?php echo $searchCategory; ?>&search=<?php echo $search ?>">
                                                <img src="<?php echo $image; ?>" class="thumbnail-pic" style="width: 150px; height: 150px;">
                                            </a>
                                        </div>
                                        <div class="caption">
                                            <h4><?php echo $name; ?></h4>
                                            <h6>Buy From: <?php echo $row['country']; ?></h6>
                                            <table>
                                                <tr>
                                                    <td><?php echo "$" . $price; ?></td>
                                                    <td><?php echo $num; ?></td>
                                                    <td><?php echo $minShipping; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Product Price</td>
                                                    <td>No. of Bids</td>
                                                    <td>Min Bid Price</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="alert alert-info" role="alert"><center>No record found under ' . $search . '</center></div>';
                        }
                    } else {
                        $retrieveUser = mysqli_query($conn, "SELECT * FROM user WHERE username LIKE '%$search%' ORDER BY rating desc");

                        if (mysqli_num_rows($retrieveUser) > 0) {
                            while ($row = mysqli_fetch_array($retrieveUser)) {
                                $username = $row['username'];
                                $image = "assets/user_img/" . $row['profile_img'];
                                $rating = $row['rating'];

                                $retrieveRequest = mysqli_query($conn, "SELECT * FROM product WHERE user_id = '$username' AND product_status = 'Open'");
                                $noOfReq = mysqli_num_rows($retrieveRequest);
                                ?>
                                <div class="searchProfileBox">
                                    <img src='<?php echo $image; ?>' class='searchProfile' style="width: 150px; height: 150px;"/>
                                    <div class="searchDetails">
                                        <span style='font-size: 20px'>
                                            <br/><?php echo $username; ?>
                                        </span>
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
                                        <div class='rate' data-score='<?php echo $rating; ?>'></div>
                                        <p><?php echo $noOfReq; ?> Request(s)</p>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="alert alert-info" role="alert"><center>No record found for ' . $search . '</center></div>';
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
