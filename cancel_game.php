        
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
    $game->call = 'canceled';
    $game->sport = $data['sport']; 
    $game->league = $data['league']; 
    $game->home = $data['home'];
    $game->away = $data['away'];
    $game->start_time = $data['start_time'];
    $game->match_id  = $data['match_id'];


    // ******************************************
    // *********** Validate first ***************
    // ******************************************
   
    // are all parameters sent
    $retval = validateCancelGame($game); 
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

     // can only cancel games that are 'Not Started' or 'In Progress'
     $retval = validateProgress($game);
     if($retval->status !=  $codes->success200){
         echo json_encode($retval);
         return false;
     } 

    // send BOS incident
    $retval = bos_Send($game);
    if($retval->status == '200'){
        // update progress status. Set to 'Canceled'
        $q = mysqli_query($con, "UPDATE `progress` SET  `status` = '2' WHERE `game` = $game->match_id");  
        if($q){
           $message->status = "200";
            $message->title = "Game canceled";
            $message->message = $game->home . " v " . $game->away;
        }
        else{
            $message->status = "400";
            $message->title = "Failed to update game progress";
            $message->subcode = "496";
            $message->message = "";
        }
        echo json_encode($message); 
        return $message;
    }
    else{
        return $retval;
    }
?>