<?php
    include "db.php";
    include "bos.php";
    include "validations.php";

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
    
    // ******************************************
    // *********** Validate first ***************
    // ******************************************
   
    // are all parameters sent
    $retval = validateStartGame($game); 
    if($retval->status !=  $codes->success200){ 
        echo json_encode($retval);
        return false;
    }
    
    // is sport valid
    $retval = validateSport($game->sport);
    if($retval->status !=  $codes->success200){
        echo json_encode($retval);
        return false;
    }
    
    // is league valid
    $retval = validateLeague($game->sport, $game->league);
    if($retval->status !=  $codes->success200){
        echo json_encode($retval);
        return false;
    }
    
    // is home team valid
    $retval = validateTeam($game->league, $game->home, 'home team');
    if($retval->status !=  $codes->success200){
        echo json_encode($retval);
        return false;
    }
    
    // is away team valid
    $retval = validateTeam($game->league, $game->away, 'away team');
    if($retval->status !=  $codes->success200){
        echo json_encode($retval);
        return false;
    }
    
    /* is start time valid
    $retval = validateDateTime($game->start_time);
    if($retval->status !=  $codes->success200){
        echo json_encode($retval);
        return false;
    }*/
    
    $retval = validateStartAndWhistleStart($game->start_time, $game->whistle_start_time);
    if($retval->status !=  $codes->success200){
        echo json_encode($retval);
        return false;
    }

    // ********************************************
    // ** Done validating, now do some real work **
    // ********************************************

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
            $message->subcode = "482";
            $message->message = "";
            echo json_encode($message); 
            return false;
        }
    }
    else{echo json_encode($retval);}  

       
?>
