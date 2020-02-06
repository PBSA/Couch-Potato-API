<?php
    // update the replay after it's done.

    include "db.php"; 

    $input = file_get_contents('php://input'); 
    $data = json_decode($input, true); 
    $message = array(); 
    $id = $data['id']; 
   
    $q = mysqli_query($con, "UPDATE `replays` SET  `done` = 'true' WHERE `id` = $id"); 
    
    if($q){
        $message['status'] = "success"; 
    }
    else{
        $message['status'] = "error";
    }
      
    
    echo ($q);
    echo mysqli_error($con); 
?>