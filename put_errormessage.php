<?php
include "db.php"; 

    $input = file_get_contents('php://input'); 
    $data = json_decode($input, true); 
    $message = array(); 
    $status = $data['status']; 
    $statustext = $data['statustext']; 
    $errormessage = $data['errormessage']; 

    $q = mysqli_query($con,  "INSERT INTO `errorlog` ( `status`,`statustext`,`message` ) 
                                VALUES ('$status','$statustext', '$errormessage')");
    if($q){
        $message['status'] = "success"; 
    }
    else{
        $message['status'] = "error";
    }
 ?>