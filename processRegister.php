<?php

include 'db.php';

$check = filter_input(INPUT_POST,'check');

if($check == 1){
    $username = filter_input(INPUT_POST,'username');
    $checkUsername = mysqli_query($conn, "SELECT username FROM user WHERE username LIKE '%$username%'");
    if(mysqli_num_rows($checkUsername) == 0){
        echo "Username is available.";
    }else{
        echo "Username has already taken up.";
    }
}else{
   $username = filter_input(INPUT_POST,'username'); 
   $password = filter_input(INPUT_POST,'password'); 
   $name = filter_input(INPUT_POST,'name'); 
   $contact = filter_input(INPUT_POST,'contact'); 
   $email = filter_input(INPUT_POST,'email'); 
   
   $addUser = mysqli_query($conn, "INSERT INTO user (name,contact,email,username,password) VALUES ('$name','$contact','$email','$username','$password')");
   if($addUser){
       echo $name." has been added to the database";
   }else{
       echo "Error adding user to the database";
   }
}
