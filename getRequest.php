<?php
include 'db.php';

//$country = filter_input(INPUT_GET, "country");
$country = filter_input(INPUT_GET, 'country');
$checkCountry = "";

$splitter = ",";
$pieces = explode($splitter, $country);

foreach ($pieces as $p) {
    if (empty($checkCountry)) {
        $checkCountry = "'" . $p . "'";
    } else {
        $checkCountry = $checkCountry . ", '" . $p . "'";
    }
}

$cat = filter_input(INPUT_GET, "cat");
$checkCategory = "";

$category = explode($splitter, $cat);

foreach ($category as $c) {
    if (empty($checkCategory)) {
        $checkCategory = "'" . $c . "'";
    } else {
        $checkCategory = $checkCategory . ", '" . $c . "'";
    }
}

$prod_price = filter_input(INPUT_GET, "price");
$shipping = filter_input(INPUT_GET, "shipping");
$sort = filter_input(INPUT_GET, "sort");

$priceFrom = 0;
$priceTo = 0;
if ($prod_price == 20 || $shipping == 20) {
    $priceFrom = 0;
    $priceTo = 20;
} else if ($prod_price == 50 || $shipping == 50) {
    $priceFrom = 21;
    $priceTo = 50;
} else if ($prod_price == 100 || $shipping == 100) {
    $priceFrom = 51;
    $priceTo = 100;
} else if ($prod_price == 101 || $shipping == 101) {
    $priceFrom = 101;
    $priceTo = 1000;
}

if ($sort == "yes") {
    if ((!empty($country)) && (!empty($cat)) && (!empty($prod_price)) && (!empty($shipping))) {
//        $count = 0;
        $selectQuery = mysqli_query($conn, "SELECT * FROM product p LEFT OUTER JOIN "
                . " (SELECT COUNT(*) AS numBids, MIN(price) AS minPrice, pid, price FROM product_bid GROUP BY pid ) AS a ON a.pid = p.product_id "
                . " WHERE country IN ($checkCountry) AND cat IN ($checkCategory) AND p.product_status = 'Open' AND approx_price BETWEEN $priceFrom AND $priceTo ORDER BY requestDate desc");
        if (mysqli_num_rows($selectQuery) > 0) {
            while ($row = mysqli_fetch_array($selectQuery)) {
                $shipprice = $row['price'];
                if ($shipprice <= $priceTo && $shipprice >= $priceFrom) {
//                    echo "Success";
                    $count += 1;
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
                                <a href="viewProduct.php?pid=<?php echo $pid; ?>">
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
            }
        } else {
            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
        }
//        if ($count == 0) {
//            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
//        }
    } else if ((!empty($country)) && (empty($cat)) && (empty($prod_price)) && (empty($shipping))) {
        $selectQuery = mysqli_query($conn, "SELECT * FROM product p LEFT OUTER JOIN "
                . " (SELECT COUNT(*) AS numBids, MIN(price) AS minPrice, pid FROM product_bid GROUP BY pid ) AS a ON a.pid = p.product_id "
                . " WHERE country IN ($checkCountry) AND p.product_status = 'Open' ORDER BY requestDate desc");
        if (mysqli_num_rows($selectQuery) > 0) {
            while ($row = mysqli_fetch_array($selectQuery)) {
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
                            <a href="viewProduct.php?pid=<?php echo $pid; ?>">
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
            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
        }
    } else if ((empty($country)) && (!empty($cat)) && (empty($prod_price)) && (empty($shipping))) {
        $selectQuery = mysqli_query($conn, "SELECT * FROM product p LEFT OUTER JOIN "
                . " (SELECT COUNT(*) AS numBids, MIN(price) AS minPrice, pid FROM product_bid GROUP BY pid ) AS a ON a.pid = p.product_id "
                . " WHERE cat IN ($checkCategory) AND p.product_status = 'Open' ORDER BY requestDate desc");
        if (mysqli_num_rows($selectQuery) > 0) {
            while ($row = mysqli_fetch_array($selectQuery)) {
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
                            <a href="viewProduct.php?pid=<?php echo $pid; ?>">
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
            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
        }
    } else if ((empty($country)) && (empty($cat)) && (!empty($prod_price)) && (empty($shipping))) {
        $selectQuery = mysqli_query($conn, "SELECT * FROM product p LEFT OUTER JOIN "
                . " (SELECT COUNT(*) AS numBids, MIN(price) AS minPrice, pid FROM product_bid GROUP BY pid ) AS a ON a.pid = p.product_id "
                . " WHERE p.product_status = 'Open' AND approx_price BETWEEN $priceFrom AND $priceTo ORDER BY requestDate desc");
        if (mysqli_num_rows($selectQuery) > 0) {
            while ($row = mysqli_fetch_array($selectQuery)) {
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
                            <a href="viewProduct.php?pid=<?php echo $pid; ?>">
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
            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
        }
    } else if ((empty($country)) && (empty($cat)) && (empty($prod_price)) && (!empty($shipping))) {
//        $count = 0;
        $selectQuery = mysqli_query($conn, "SELECT * FROM product p LEFT OUTER JOIN "
                . " (SELECT COUNT(*) AS numBids, MIN(price) AS minPrice, pid, price FROM product_bid GROUP BY pid ) AS a ON a.pid = p.product_id "
                . " WHERE p.product_status = 'Open' ORDER BY requestDate desc");
        if (mysqli_num_rows($selectQuery) > 0) {
            while ($row = mysqli_fetch_array($selectQuery)) {
                $shipprice = $row['price'];
                if ($shipprice <= $priceTo && $shipprice >= $priceFrom) {
                    $count += 1;
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
                                <a href="viewProduct.php?pid=<?php echo $pid; ?>">
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
            }
        } else {
            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
        }
//        if ($count == 0) {
//            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
//        }
    } else if ((!empty($country)) && (!empty($cat)) && (empty($prod_price)) && (empty($shipping))) {
        $selectQuery = mysqli_query($conn, "SELECT * FROM product p LEFT OUTER JOIN "
                . " (SELECT COUNT(*) AS numBids, MIN(price) AS minPrice, pid FROM product_bid GROUP BY pid ) AS a ON a.pid = p.product_id "
                . " WHERE country IN ($checkCountry) AND cat IN ($checkCategory) AND p.product_status = 'Open' ORDER BY requestDate desc");
        if (mysqli_num_rows($selectQuery) > 0) {
            while ($row = mysqli_fetch_array($selectQuery)) {
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
                            <a href="viewProduct.php?pid=<?php echo $pid; ?>">
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
            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
        }
    } else if ((!empty($country)) && (empty($cat)) && (!empty($prod_price)) && (empty($shipping))) {
        $selectQuery = mysqli_query($conn, "SELECT * FROM product p LEFT OUTER JOIN "
                . " (SELECT COUNT(*) AS numBids, MIN(price) AS minPrice, pid FROM product_bid GROUP BY pid ) AS a ON a.pid = p.product_id "
                . " WHERE country IN ($checkCountry) AND p.product_status = 'Open' AND approx_price BETWEEN $priceFrom AND $priceTo ORDER BY requestDate desc");
        if (mysqli_num_rows($selectQuery) > 0) {
            while ($row = mysqli_fetch_array($selectQuery)) {
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
                            <a href="viewProduct.php?pid=<?php echo $pid; ?>">
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
            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
        }
    } else if ((!empty($country)) && (empty($cat)) && (empty($prod_price)) && (!empty($shipping))) {
//        $count = 0;
        $selectQuery = mysqli_query($conn, "SELECT * FROM product p LEFT OUTER JOIN "
                . " (SELECT COUNT(*) AS numBids, MIN(price) AS minPrice, pid, price FROM product_bid GROUP BY pid ) AS a ON a.pid = p.product_id "
                . " WHERE country IN ($checkCountry) AND p.product_status = 'Open' ORDER BY requestDate desc");
        if (mysqli_num_rows($selectQuery) > 0) {
            while ($row = mysqli_fetch_array($selectQuery)) {
                $shipprice = $row['price'];
                if ($shipprice <= $priceTo && $shipprice >= $priceFrom) {
                    $count += 1;
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
                                <a href="viewProduct.php?pid=<?php echo $pid; ?>">
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
            }
        } else {
            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
        }
//        if ($count == 0) {
//            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
//        }
    } else if ((empty($country)) && (!empty($cat)) && (!empty($prod_price)) && (empty($shipping))) {
        $selectQuery = mysqli_query($conn, "SELECT * FROM product p LEFT OUTER JOIN "
                . " (SELECT COUNT(*) AS numBids, MIN(price) AS minPrice, pid FROM product_bid GROUP BY pid ) AS a ON a.pid = p.product_id "
                . " WHERE cat IN ($checkCategory) AND p.product_status = 'Open' AND approx_price BETWEEN $priceFrom AND $priceTo ORDER BY requestDate desc");
        if (mysqli_num_rows($selectQuery) > 0) {
            while ($row = mysqli_fetch_array($selectQuery)) {
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
                            <a href="viewProduct.php?pid=<?php echo $pid; ?>">
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
            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
        }
    } else if ((empty($country)) && (!empty($cat)) && (empty($prod_price)) && (!empty($shipping))) {
//        $count = 0;
        $selectQuery = mysqli_query($conn, "SELECT * FROM product p LEFT OUTER JOIN "
                . " (SELECT COUNT(*) AS numBids, MIN(price) AS minPrice, pid, price FROM product_bid GROUP BY pid ) AS a ON a.pid = p.product_id "
                . " WHERE cat IN ($checkCategory) AND p.product_status = 'Open' ORDER BY requestDate desc");
        if (mysqli_num_rows($selectQuery) > 0) {
            while ($row = mysqli_fetch_array($selectQuery)) {
                $shipprice = $row['price'];
                if ($shipprice <= $priceTo && $shipprice >= $priceFrom) {
                    $count += 1;
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
                                <a href="viewProduct.php?pid=<?php echo $pid; ?>">
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
            }
        } else {
            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
        }
//        if ($count == 0) {
//            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
//        }
    } else if ((empty($country)) && (empty($cat)) && (!empty($prod_price)) && (!empty($shipping))) {
//        $count = 0;
        $selectQuery = mysqli_query($conn, "SELECT * FROM product p LEFT OUTER JOIN "
                . " (SELECT COUNT(*) AS numBids, MIN(price) AS minPrice, pid, price FROM product_bid GROUP BY pid ) AS a ON a.pid = p.product_id "
                . " WHERE p.product_status = 'Open' AND approx_price BETWEEN $priceFrom AND $priceTo ORDER BY requestDate desc");
        if (mysqli_num_rows($selectQuery) > 0) {
            while ($row = mysqli_fetch_array($selectQuery)) {
                $shipprice = $row['price'];
                if ($shipprice <= $priceTo && $shipprice >= $priceFrom) {
                    $count += 1;
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
                                <a href="viewProduct.php?pid=<?php echo $pid; ?>">
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
            }
        } else {
            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
        }
//        if ($count == 0) {
//            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
//        }
    } else if ((!empty($country)) && (!empty($cat)) && (!empty($prod_price)) && (empty($shipping))) {
        $selectQuery = mysqli_query($conn, "SELECT * FROM product p LEFT OUTER JOIN "
                . " (SELECT COUNT(*) AS numBids, MIN(price) AS minPrice, pid FROM product_bid GROUP BY pid ) AS a ON a.pid = p.product_id "
                . " WHERE country IN ($checkCountry) AND cat IN ($checkCategory) AND p.product_status = 'Open' AND approx_price BETWEEN $priceFrom AND $priceTo ORDER BY requestDate desc");
        if (mysqli_num_rows($selectQuery) > 0) {
            while ($row = mysqli_fetch_array($selectQuery)) {
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
                            <a href="viewProduct.php?pid=<?php echo $pid; ?>">
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
            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
        }
    } else if ((!empty($country)) && (!empty($cat)) && (empty($prod_price)) && (!empty($shipping))) {
//        $count = 0;
        $selectQuery = mysqli_query($conn, "SELECT * FROM product p LEFT OUTER JOIN "
                . " (SELECT COUNT(*) AS numBids, MIN(price) AS minPrice, pid, price FROM product_bid GROUP BY pid ) AS a ON a.pid = p.product_id "
                . " WHERE country IN ($checkCountry) AND cat IN ($checkCategory) AND p.product_status = 'Open' ORDER BY requestDate desc");
        if (mysqli_num_rows($selectQuery) > 0) {
            while ($row = mysqli_fetch_array($selectQuery)) {
                $shipprice = $row['price'];
                if ($shipprice <= $priceTo && $shipprice >= $priceFrom) {
                    $count += 1;
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
                                <a href="viewProduct.php?pid=<?php echo $pid; ?>">
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
            }
        } else {
            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
        }
//        if ($count == 0) {
//            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
//        }
    } else if ((empty($country)) && (!empty($cat)) && (!empty($prod_price)) && (!empty($shipping))) {
//        $count = 0;
        $selectQuery = mysqli_query($conn, "SELECT * FROM product p LEFT OUTER JOIN "
                . " (SELECT COUNT(*) AS numBids, MIN(price) AS minPrice, pid, price FROM product_bid GROUP BY pid ) AS a ON a.pid = p.product_id "
                . " WHERE cat IN ($checkCategory) AND p.product_status = 'Open' AND approx_price BETWEEN $priceFrom AND $priceTo ORDER BY requestDate desc");
        if (mysqli_num_rows($selectQuery) > 0) {
            while ($row = mysqli_fetch_array($selectQuery)) {
                $shipprice = $row['price'];
                if ($shipprice <= $priceTo && $shipprice >= $priceFrom) {
                    $count += 1;
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
                                <a href="viewProduct.php?pid=<?php echo $pid; ?>">
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
            }
        } else {
            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
        }
//        if ($count == 0) {
//            echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
//        }
    }
} else {
    $selectQuery = mysqli_query($conn, "SELECT * FROM product p LEFT OUTER JOIN "
            . "(SELECT COUNT(*) AS numBids, MIN(price) AS minPrice, pid FROM product_bid WHERE bid_status = 'Open' GROUP BY pid ) AS a ON a.pid = p.product_id "
            . "WHERE p.product_status = 'Open' ORDER BY requestDate desc");
    if (mysqli_num_rows($selectQuery) > 0) {
        while ($row = mysqli_fetch_array($selectQuery)) {
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
                        <a href="viewProduct.php?pid=<?php echo $pid; ?>">
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
        echo '<div class="alert alert-info" role="alert"><center>No record found</center></div>';
    }
}