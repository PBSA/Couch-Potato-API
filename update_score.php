<?php
    include "db.php"; 

    $input = file_get_contents('php://input'); 
    $data = json_decode($input, true); 
    $message = array(); 
    $homescore = $data['homescore']; 
    $awayscore = $data['awayscore']; 
    $id = $data['id'];

    $q = mysqli_query($con, "UPDATE `games` 
                                SET  `homescore` = $homescore,
                                    `awayscore` = $awayscore
                                WHERE `id` = $id"); 
    if($q){
        $message['status'] = "success"; 
    }
    else{
        $message['status'] = "error";
    }
      
    
    echo ($q);
    echo mysqli_error($con); 
?>