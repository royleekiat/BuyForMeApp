<?php
include 'header.php';
?>

<div class="body-content">
    <br/><br/>
    <div class="container">
        <div class="row">
            <div class="col-sm-4">
                <h4>List of Product Requests</h4>
            </div>
            <div class="col-sm-5 pull-right">
                <div class="input-group">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-search"></span></span>
                    <form action="searchResult.php" method="get" class="form-search"  id="searchForm">
                        <select name="searchCategory" id="searchCategory" class="form-control">
                            <option value="Product">Product Request</option>
                            <option value="User">User</option>
                        </select>
                        <input type="text" class="form-control" name="search" id="search" placeholder="Looking for something?"/>
                    </form>
                </div>
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-3">
                            <p class="text-center">Country: </p>
                            <?php
                            $countries = mysqli_query($conn, "SELECT country FROM country");
                            if (mysqli_num_rows($countries) > 0) {
                                while ($row = mysqli_fetch_array($countries)) {
                                    ?>
                                    <input type="checkbox" class="country" onclick="browseRequest(this);" name="countryList[]" value="<?php echo $row['country']; ?>" /> <?php echo $row['country'] . "<br/>"; ?>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <div class="col-md-3">
                            <p class="text-center">Category: </p>
                            <?php
                            $categories = mysqli_query($conn, "SELECT * FROM product_cat");
                            if (mysqli_num_rows($categories) > 0) {
                                while ($row = mysqli_fetch_array($categories)) {
                                    ?>
                                    <input type="checkbox" class="category" onclick="browseRequest(this);" name="categoryList[]" value="<?php echo $row['category']; ?>" /> <?php echo $row['category'] . "<br/>"; ?>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <div class="col-md-3">
                            <p class="text-center">Product Price: </p>
                            <select name="country" onchange="browseRequest(this);" id="price" class="form-control">
                                <option value=""> -- SELECT -- </option>
                                <option value="20"> $20 and Below </option>
                                <option value="50"> $21 - $50 </option>
                                <option value="100"> $51 - $100 </option>
                                <option value="101"> Above $100 </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <p class="text-center">Shipping Price: </p>
                            <select name="country" onchange="browseRequest(this);" id="shipping" class="form-control">
                                <option value=""> -- SELECT -- </option>
                                <option value="20"> $20 and Below </option>
                                <option value="50"> $21 - $50 </option>
                                <option value="100"> $51 - $100 </option>
                                <option value="101"> Above $100 </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div id="results"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        browseRequest();
    });
    function browseRequest() {
//        var country = $(".country").val();
//        var cat = $("#category").val();
        var price = $("#price").val();
        var shipping = $("#shipping").val();

        var myCheckboxes = new Array();
        $("input.country:checked").each(function () {
            myCheckboxes.push($(this).val());
        });

        var myCheckCat = new Array();
        $("input.category:checked").each(function () {
            myCheckCat.push($(this).val());
        });

        if (myCheckboxes.length === 0 && myCheckCat.length === 0 && price.length === 0 && shipping.length === 0) {
            $.ajax({
                type: "GET",
                url: "getRequest.php",
                data: "country=" + myCheckboxes + "&cat=" + myCheckCat + "&price=" + price + "&shipping=" + shipping + "&sort=no",
                cache: false,
                success: function (data) {
                    $('#results').html(data);
                }
            });
        } else {
            $.ajax({
                type: "GET",
                url: "getRequest.php",
                data: "country=" + myCheckboxes + "&cat=" + myCheckCat + "&price=" + price + "&shipping=" + shipping + "&sort=yes",
                cache: false,
                success: function (data) {
                    $('#results').html(data);
                }
            });
        }
    }

    document.getElementById('search').onkeydown = function (e) {
        if (e.keyCode == 13) {
            // submit
            document.getElementById('searchForm').submit();
        }
    };
</script>