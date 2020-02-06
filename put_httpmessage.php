<?php
include "db.php"; 

    $input = file_get_contents('php://input'); 
    $data = json_decode($input, true); 
    $message = array(); 
    $type = $data['type']; 
    $uniqueid = $data['uniqueid']; 
    $approveid = $data['approveid']; 
    $httpmessage = $data['httpmessage']; 
    $url = $data['url']; 

    $q = mysqli_query($con,  "INSERT INTO `httplog` ( `type`,`uniqueid`,`approveid`, `message`, `url` ) 
                                VALUES ('$type','$uniqueid','$approveid', '$httpmessage', '$url')");
    
    if($q){
        $message['status'] = "success"; 
    }
    else{
        $message['status'] = "error";
    }
 ?>