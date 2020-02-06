<?php
    include "db.php"; 

    $input = file_get_contents('php://input'); 
    $data = json_decode($input, true); 
    $message = array(); 
    $json = $data['json']; 
    $timestamp = $data['timestamp']; 
    $unique_string = $data['unique_string']; 
    $user = $data['user']; 
    $call = $data['call']; 

    $q = mysqli_query($con,  "INSERT INTO `incidents` ( `user`, `timestamp`, `uniquename`, `call`, `message` ) 
                                VALUES ('$user', '$timestamp', '$unique_string', '$call', '$json')");
    
    if($q){
        $message['status'] = "success"; 
    }
    else{
        $message['status'] = "error";
    }

    // save incident to dump file as well.
    $myfile = fopen("dump/" . $unique_string . ".json", "w") or die("Unable to create file!");
    fwrite($myfile, $json);
    fclose($myfile);    
    
    echo ($q);
    echo mysqli_error($con); 
?>