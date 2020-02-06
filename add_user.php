<?php
    include "db.php"; 

    $input = file_get_contents('php://input'); 
    $data = json_decode($input, true); 
    $message = array(); 
    $username = $data['username']; 
    $salt = $data['salt']; 
    $password = $data['password']; 
    $email = $data['email']; 

    $q = mysqli_query($con,  "INSERT INTO `users` ( `username`,`salt`,`password`, `email` ) 
                                VALUES ('$username','$salt', '$password', '$email')");
    if($q){
        $message['status'] = "success"; 
    }
    else{
        $message['status'] = "error";
    }
    echo json_encode($message);
 ?>