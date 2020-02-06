<?php
    include "db.php"; 

    $input = file_get_contents('php://input'); 
    $data = json_decode($input, true); 
    $message = array(); 
    $game = $data['game']; 
    $status = $data['status'];

    if($data['action'] == "insert"){
        $q = mysqli_query($con, "INSERT INTO `progress` ( `game`, `status` ) 
                                    VALUES ('$game', '$status')"); 
        if($q){
            $message['status'] = "success"; 
        }
        else{
            $message['status'] = "error"; 
        }
    }

    else{
        $q = mysqli_query($con, "UPDATE `progress` 
                                    SET  `status` = $status
                                    WHERE `game` = $game"); 
        if($q){
            $message['status'] = "success"; 
        }
        else{
            $message['status'] = "error";
        }
      
    }
    echo ($q);
    echo mysqli_error($con); 
?>