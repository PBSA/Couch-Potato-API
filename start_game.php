<?php
    include "db.php";
    include "bos.php";

    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    $postdata = file_get_contents("php://input");
    $data = json_decode($postdata,true);

    $game = new stdClass;
    $game->sport = $data['sport']; 
    $game->league = $data['league']; 
    $game->home = $data['home'];
    $game->away = $data['away'];
    $game->start_time = $data['start_time'];
    $game->whistle_start_time = $data['whistle_start_time'];
    $game->match_id  = $data['match_id'];
    $game->call = 'in_progress';
 
    // game can only be started if status is 'Not Started' (0)

    // Make sure the match_id is valid

    // make sure teams match the teams with the match_id.

     // send BOS incident
     $retval = bos_Send($game);
     if($retval == 'success'){
        // update progress status. Set to 'In Progress'
        $q = mysqli_query($con, "UPDATE `progress` SET `status` = '1' WHERE `game` = '$game->match_id'");  
        if($q){
            $message['status'] = "Success"; 
        }
        else{
            $message->status = "400";
            $message->title = "Failed to update game progress";
            $message->subcode = "481";
            $message->message = "";
            echo json_encode($message); 
            return false;
        }
    }
    else{echo json_encode($retval);}  

       
?>
