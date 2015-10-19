<?php
include 'header.php';
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    $username = $_SESSION['username'];
} else {
    header("Location: login.php");
}

$productID = filter_input(INPUT_POST, 'productID');
$link = filter_input(INPUT_POST, 'link');

$editName = "";
$editCategory = "";
$editCountry = "";
$editAddress = "";
$editPrice = "";
$editImage = "";
$editURL = "";
$editDesc = "";

if ($link) {
    echo "SELECT * FROM product WHERE product_id = '$productID' AND user_id = '$username'";
    $editRequest = mysqli_query($conn, "SELECT * FROM product WHERE product_id = '$productID' AND user_id = '$username'");
    if (mysqli_num_rows($editRequest) == 1) {
        $editRow = mysqli_fetch_array($editRequest);
        $editName = $editRow['name'];
        $editCategory = $editRow['cat'];
        $editCountry = $editRow['country'];
        $editAddress = $editRow['store'];
        $editPrice = $editRow['approx_price'];
        $editImage = $editRow['image'];
        $editURL = $editRow['url'];
        $editDesc = $editRow['description'];
    }
}
?>

<div class="body-content">
    <div class="row">
        <div class="container">
            <h3 class="text-center">Submit Product Request</h3>
            <form enctype="multipart/form-data" class="form-horizontal" id="requestForm">
                <div class="col-md-offset-3 col-md-6">
                    <div class="form-group">
                        <label for="InputName">Name of Product</label>
                        <div class="input-group">
                            <?php if (!empty($link)) { ?>
                                <input type="text" id="name" class="form-control" name="name" value='<?php echo $editName; ?>' placeholder="Enter Product Name" autocomplete="off" required>
                            <?php } else { ?>
                                <input type="text" id="name" class="form-control" name="name" placeholder="Enter Product Name" autocomplete="off" required>
                            <?php } ?>
                            <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="InputName">Category</label>
                        <div class="input-group">
                            <?php if (!empty($link)) { ?>
                                <select class="form-control" id="cat" name="category" required>
                                    <option value="">-- SELECT CATEGORY --</option>
                                    <?php
                                    $categories = mysqli_query($conn, "SELECT * FROM product_cat ORDER BY category asc");
                                    if (mysqli_num_rows($categories) > 0) {
                                        while ($row = mysqli_fetch_array($categories)) {
                                            if ($row['category'] == $editCategory) {
                                                echo "<option value='" . $row['category'] . "' selected>" . $row['category'] . "</option>";
                                            } else {
                                                echo "<option value='" . $row['category'] . "'>" . $row['category'] . "</option>";
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            <?php } else { ?>
                                <select class="form-control" id="cat" name="category" required>
                                    <option value="">-- SELECT CATEGORY --</option>
                                    <?php
                                    $categories = mysqli_query($conn, "SELECT * FROM product_cat ORDER BY category asc");
                                    if (mysqli_num_rows($categories) > 0) {
                                        while ($row = mysqli_fetch_array($categories)) {
                                            echo "<option value='" . $row['category'] . "'>" . $row['category'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            <?php } ?>
                            <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="InputName">Product Country</label>
                        <div class="input-group">
                            <?php if (!empty($link)) { ?>
                                <select class="form-control" id="country" name="country" required>
                                    <option value="">-- SELECT COUNTRY --</option>
                                    <?php
                                    $countries = mysqli_query($conn, "SELECT * FROM country ORDER BY country asc");
                                    if (mysqli_num_rows($countries) > 0) {
                                        while ($row = mysqli_fetch_array($countries)) {
                                            if ($row['country'] == $editCountry) {
                                                echo "<option value='" . $row['country'] . "' selected>" . $row['country'] . "</option>";
                                            } else {
                                                echo "<option value='" . $row['country'] . "'>" . $row['country'] . "</option>";
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            <?php } else { ?>
                                <select class="form-control" id="country" name="country" required>
                                    <option value="">-- SELECT COUNTRY --</option>
                                    <?php
                                    $countries = mysqli_query($conn, "SELECT * FROM country ORDER BY country asc");
                                    if (mysqli_num_rows($countries) > 0) {
                                        while ($row = mysqli_fetch_array($countries)) {
                                            echo "<option value='" . $row['country'] . "'>" . $row['country'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            <?php } ?>
                            <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="InputName">Store Address</label>
                        <?php if (!empty($link)) { ?>
                            <input type="text" id="store" class="form-control" name="store" value="<?php echo $editAddress ?>" placeholder="Enter Store Address" autocomplete="off" >
                        <?php } else { ?>
                            <input type="text" id="store" class="form-control" name="store" placeholder="Enter Store Address" autocomplete="off" >
                        <?php } ?>
                    </div>
                    <div class="form-group">
                        <label for="InputName">Approximate Product Price (SGD)</label>
                        <div class="input-group">
                            <?php if (!empty($link)) { ?>
                                <input type="number" class="form-control" min="0" step="1" id="price" name="price"  value="<?php echo $editPrice; ?>" placeholder="Enter Product Price" required>
                            <?php } else { ?>
                                <input type="number" class="form-control" min="0" step="1" id="price" name="price" placeholder="Enter Product Price" required>
                            <?php } ?>
                            <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="InputName">Product Image</label>
                        <?php if (!empty($link)) { ?>
                            <div class="input-group">
                                <input id="fileInput" type="file" class="form-control" name="file"/>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                            </div>
                            <span><b>Current Image:</b> <?php //echo $editImage ?>
                                <img class="" src="assets/product_img/<?php echo $editImage ?>" width="150px" height="150px"/></span>
                        <?php } else { ?>
                            <div class="input-group">
                                <input id="fileInput" type="file" class="form-control" name="file" required/>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="form-group">
                        <label for="InputName">Product URL</label>
                        <?php if (!empty($link)) { ?>
                            <input type="text" class="form-control" name="url" id="url" value="<?php echo $editURL; ?>" placeholder="Enter Product URL">
                        <?php } else { ?>
                            <input type="text" class="form-control" name="url" id="url" placeholder="Enter Product URL">
                        <?php } ?>
                    </div>
                    <div class="form-group">
                        <label for="InputName">Product Description</label>
                        <?php if (!empty($link)) { ?>
                            <textarea class="form-control" name="description" rows="5" id="desc" required><?php echo $editDesc; ?></textarea>
                        <?php } else { ?>
                            <textarea class="form-control" name="description" rows="5" id="desc" required></textarea>
                        <?php } ?>
                    </div>
                    <input type="hidden" id="user" value="<?php echo $_SESSION['username'] ?>"/>
                    <?php if ($link == 'edit') { ?>
                        <input type="hidden" id="status" value="edit" />
                        <input type="hidden" id="product_id" value="<?php echo $productID; ?>" />
                        <input type="submit" name="submit" id="submit" value="Edit Request" class="btn btn-info pull-right">
                    <?php } else if ($link == 'repost') { ?>
                        <!--<input type="hidden" id="status" value="repost" />-->
                        <input type="submit" name="submit" id="submit" value="Repost Request" class="btn btn-info pull-right">
                    <?php } else { ?>
                        <input type="submit" name="submit" id="submit" value="Submit Request" class="btn btn-info pull-right">
                    <?php } ?>
                    <div id="result"></div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>

    $("#requestForm").submit(function (e) {
        e.preventDefault();
        var file = $("#fileInput").prop('files')[0];
        var name = $("#name").val();
        var cat = $("#cat").val();
        var country = $("#country").val();
        var store = $("#store").val();
        var price = $("#price").val();
        var url = $("#url").val();
        var user = $("#user").val();
        var desc = $("#desc").val();
        var status = $("#status").val();
        var product_id = $("#product_id").val();
        var formData = new FormData();
        formData.append('file', file);
        formData.append('name', name);
        formData.append('cat', cat);
        formData.append('country', country);
        formData.append('store', store);
        formData.append('price', price);
        formData.append('url', url);
        formData.append('user', user);
        formData.append('desc', desc);
        formData.append('status', status);
        formData.append('product_id', product_id);
        $.ajax({
            type: "POST",
            url: "processRequestSubmission.php",
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (data) {
                if (data.status === "success") {
                    $('#result').html(data.msg);
                    setTimeout(function () {
                        window.location = "userProfile.php";
                    }, 2600);
                } else {
                    $('#result').html(data.msg);
                }
            }
        });
        return false;
    });
</script>