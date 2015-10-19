<?php

include 'db.php';
$user = filter_input(INPUT_POST, 'user');
$uploadDir = "assets/product_img/";
$dateTime = date('His');
$file = $uploadDir . $user . "_" . $dateTime . "_" . basename($_FILES['file']['name']);
$fileType = strtolower(pathinfo($file, PATHINFO_EXTENSION));
$data = array();
$errMSg = "";
$uploadOk = 1;

// Check if image file is a actual image or fake image
$check = getimagesize($_FILES["file"]["tmp_name"]);
if ($check !== false) {
    $uploadOk = 1;
} else {
    $errMSg .= "File is not an image.<br>";
    $data = array("status" => "error", "msg" => $errMSg);
    $uploadOk = 0;
}


// Check file size
if ($_FILES["file"]["size"] > 500000) {
    $errMSg .= "Sorry, your file is larger than the upload size limit (<500kb)<br>";
    $data = array("status" => "error", "msg" => $errMSg);
    $uploadOk = 0;
}
// Allow certain file formats
if ($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg" && $fileType != "gif") {
    $errMSg .= "Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br>";
    $data = array("status" => "error", "msg" => $errMSg);
    $uploadOk = 0;
}

if ($uploadOk == 0) {
    $errMSg .= "Sorry, your file was not uploaded.<br>";
} else {
    $user = filter_input(INPUT_POST, 'user');
    $name = filter_input(INPUT_POST, 'name');
    $cat = filter_input(INPUT_POST, 'cat');
    $country = filter_input(INPUT_POST, 'country');
    $store = filter_input(INPUT_POST, 'store');
    $price = filter_input(INPUT_POST, 'price');
    $url = filter_input(INPUT_POST, 'url');
    $desc = filter_input(INPUT_POST, 'desc');
    $status = filter_input(INPUT_POST, 'status');
    $dateTime = date('His');
    $fileName = $user . "_" . $dateTime . "_" . basename($_FILES['file']['name']);
    $date = date("Y-m-d");

    if ($status != 'edit') {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $file)) {
            $insertRequest = mysqli_query($conn, "INSERT INTO product(name,cat,country,store,approx_price,url,image,description,requestDate, product_status, user_id) "
                    . "VALUES ('$name','$cat','$country','$store','$price','$url','$fileName','$desc', '$date', 'Open', '$user')");
            if ($insertRequest) {
                $errMSg .= "Your product is successfully submitted.<br>";
                $data = array("status" => "success", "msg" => $errMSg);
            } else {
                $errMSg .= "Error in submitting your product request<br>";
                $data = array("status" => "error", "msg" => $errMSg);
            }
        }
    } else {
        if (empty($_FILES['file']['name'])) {
            $product_id = filter_input(INPUT_POST, 'product_id');
            $insertRequest = mysqli_query($conn, "UPDATE product SET name = '$name', cat = '$cat', country = '$country', store = '$store', approx_price = '$price',"
                    . " url = '$url', description = '$desc' WHERE product_id = '$product_id'");
            if ($insertRequest) {
                $errMSg .= "Your product is successfully updated.<br>";
                $data = array("status" => "success", "msg" => $errMSg);
            } else {
                $errMSg .= "Error in updating your product request<br>";
                $data = array("status" => "error", "msg" => $errMSg);
            }
        } else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $file)) {
                $product_id = filter_input(INPUT_POST, 'product_id');
                $insertRequest = mysqli_query($conn, "UPDATE product SET name = '$name', cat = '$cat', country = '$country', store = '$store', approx_price = '$price',"
                        . " url = '$url', image = '$fileName', description = '$desc' WHERE product_id = '$product_id'");
                if ($insertRequest) {
                    $errMSg .= "Your product is successfully updated.<br>";
                    $data = array("status" => "success", "msg" => $errMSg);
                } else {
                    $errMSg .= "Error in updating your product request<br>";
                    $data = array("status" => "error", "msg" => $errMSg);
                }
            }
        }
    }
}
echo json_encode($data);
