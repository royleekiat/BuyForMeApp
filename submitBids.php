<?php
include 'header.php';

$productID = filter_input(INPUT_POST, 'productID');
$link = filter_input(INPUT_POST, 'link');
$userID = filter_input(INPUT_POST, 'userID');

$editTravelTo = "";
$editTravelFrom = "";
$editPrice = "";
$editBidId = "";

echo "Link: " . $link;

if ($link) {
    $editForm = mysqli_query($conn, "SELECT * FROM product_bid WHERE pid = '$productID' AND user_id = '$userID'");
    if (mysqli_num_rows($editForm) == 1) {
        $editRow = mysqli_fetch_array($editForm);
        $editTravelTo = $editRow['travel_to'];
        $editTravelFrom = $editRow['travel_from'];
        $editPrice = $editRow['price'];
        $editBidId = $editRow['bid_id'];
        $editInfo = $editRow['other_info'];
    }
}
?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<!--<script src="//code.jquery.com/jquery-1.10.2.js"></script>-->
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript">
    $(function () {
        $("#periodFrom").datepicker({
            changeMonth: true,
            minDate: 0,
            dateFormat: 'yy-mm-dd',
            onClose: function (selectedDate) {
                $("#periodTo").datepicker("option", "minDate", selectedDate);
            }
        });
        $("#periodTo").datepicker({
            changeMonth: true,
            dateFormat: 'yy-mm-dd',
            onClose: function (selectedDate) {
                $("#periodFrom").datepicker("option", "maxDate", selectedDate);
            }
        });
    });
</script>

<div class="body-content">
    <div class="row">
        <div class="container">
            <br>
            <h3 class="text-center">Submit Product Bids</h3>
            <form class="form-horizontal" id="submitBid">
                <div class="col-md-offset-3 col-md-6">
                    <div class="form-group">
                        <label for="InputName">Travel Period</label>
                        <div style='width: 100%'>
                            <div style='width: 20%;float:left;margin-top: 6px;'>
                                Start Travel Date:
                            </div>
                            <div class='input-group date datepicker' id='datePickerFrom' style="float:left;width:77%;margin-left:3%">
                                <?php if ($link) { ?>
                                    <input type='text' id='periodFrom' name="periodFrom" class="form-control" value='<?php echo $editTravelFrom; ?>' required />
                                <?php } else { ?>
                                    <input type='text' id='periodFrom' name="periodFrom" class="form-control" placeholder="Enter Start Travel Date" required />
                                <?php } ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <br/><br/>
                        <div style='width: 100%'>
                            <div style='width: 20%;float:left;margin-top: 6px;'>
                                End Travel Date:
                            </div>
                            <div class='input-group date datepicker' id='datePickerTo' style="float:left;width:77%;margin-left:3%">
                                <?php if ($link) { ?>
                                    <input type='text' id='periodTo' name="periodTo" class="form-control" value='<?php echo $editTravelTo; ?>' required />
                                <?php } else { ?>
                                    <input type='text' id='periodTo' name="periodTo" class="form-control" placeholder="Enter End Travel Date" required />
                                <?php } ?>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="InputName">Shipping Price (SGD)</label>
                        <div class="input-group">
                            <?php if ($link) { ?>
                                <input type='text' name="shippingPrice" class="form-control" value='<?php echo $editPrice; ?>' required />
                            <?php } else { ?>
                                <input type="text" class="form-control" name="shippingPrice" placeholder="Enter Shipping Price" required>
                            <?php } ?>
                            <span class="input-group-addon"><span class="glyphicon glyphicon-asterisk"></span></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="InputName">Other Information(s)</label>
                        <div class="input-group">
                            <?php if ($link) { ?>
                                <textarea type="textbox" class="form-control" name="information" value='<?php echo $editPrice; ?>'></textarea>
                            <?php } else { ?>
                                <textarea type="textbox" class="form-control" name="information" placeholder="Enter Other Information"></textarea>
                            <?php } ?>
                            <span class="input-group-addon"></span>
                        </div>
                    </div>
                    <input type="hidden" name="productID" value='<?php echo $productID ?>' />
                    <input type="hidden" name="bidID" value='<?php echo $editBidId ?>' />
                    <?php if ($link) { ?>
                        <input type="submit" name="submit" value="Edit" class="btn btn-primary pull-right">
                    <?php } else { ?>
                        <input type="submit" name="submit" value="Submit" class="btn btn-primary pull-right">
                    <?php } ?>
                    <br/><br/>
                    <div id="response"></div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $("#submitBid").submit(function (e) {
        $.ajax({
            type: "POST",
            url: "processBids.php",
            data: $(this).serialize(),
            cache: false,
            dataType: 'json',
            success: function (data) {
                if (data['status'] === "success") {
                    $('#response').html(data['msg']);
                    setTimeout(function () {
                        window.location = "index.php";
                    }, 2000);
                } else {
                    $('#response').html(data['msg']);
                }
            }
        });
        return false;
        e.preventDefault();
    });
</script>