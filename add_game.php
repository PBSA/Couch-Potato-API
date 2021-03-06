<?php

include "db.php";
include "bos.php";
include "validations.php";

header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

$postdata = file_get_contents("php://input");
$data = json_decode($postdata,true);

$game = new stdClass();
$game->sport = $data['sport']; 
$game->league = $data['league']; 
$game->user = $data['user'];
$game->home = $data['home'];
$game->away = $data['away'];
$game->start_time = $data['start_time'];
$game->date = substr($game->start_time,0,10);
$game->time = substr($game->start_time,11,5);
$game->call = 'create';

$message = new stdClass();

// ******************************************
// *********** Validate first ***************
// ******************************************

// are all parameters sent
$retval = validateAddGame($game);
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

// are teams different
$retval = validateBothTeams($game->home, $game->away);
if($retval->status !=  $codes->success200){
    echo json_encode($retval);
    return false;
}

// is user valid
$retval = validateUser($game->user);
if($retval->status !=  $codes->success200){
    echo json_encode($retval);
    return false;
}


// game must not already exist
$retval = validateProgress($game);
if($retval->status !=  $codes->success200){
    echo json_encode($retval);
    return false;
} 
// ********************************************
// ** Done validating, now do some real work **
// ********************************************

        // send new game to BOS
        $bosval =  bos_Send($game);  
        if($bosval->status == "200"){
            $message->status = "200";
            $message->title = "Game added ";
            $message->message = $game->home . " v " . $game->away . " - " . $game->start_time . " ";
            $message->message .= $bosval->message; 
        }
        else{
            echo json_encode($bosval);
            return $bosval; 
        }  
        // BOS send went ok so update CP tables now.
   
        // get the last event id
        $q = $con->query("SELECT MAX(id) as `id` FROM events WHERE `date` = '$game->date' AND `league` = '$game->league'");
        $row=mysqli_fetch_object($q);
        if(!$q){
            $message->status = "400";
            $message->title = "Failed to get last event id";
            $message->subcode = "471";
            $message->message = "";
            echo json_encode($message); 
            return $message;
        }
        // if this is the first game then a new event has to be created as well
        if($row->id == null){
            // first game so add new event
            $q = mysqli_query($con, "INSERT INTO `events` ( `user`, `league`, `date` ) VALUES ('$game->user', '$game->league', '$game->date')"); 
            if(!$q){
                $message->status = "400";
                $message->title = "Failed to add new event";
                $message->subcode = "472";
                $message->message = "";
                echo json_encode($message); 
                return $message;
            }
            // get the new event id
            $q = $con->query("SELECT MAX(id) as `id` FROM events");
            $row=mysqli_fetch_object($q);
            if($q){ $game->eventid = $row->id; }
            else{
                $message->status = "400";
                $message->title = "Failed to get new event id";
                $message->subcode = "473";
                $message->message = "";
                echo json_encode($message); 
                return $message;
            }
        }
        else{ $game->eventid = $row->id; }
        $q = mysqli_query($con, "INSERT INTO `games` ( `user`, `event`, `hometeam`,`awayteam`,`starttime` ) 
                                    VALUES ('$game->user', '$game->eventid', '$game->home', '$game->away', '$game->time')"); 
        if(!$q){
            $message->status = "400";
            $message->title = "Failed to add new game";
            $message->subcode = "474";
            $message->message = "";
            return $message;
        } 
        // get the next id
        $q = $con->query("SELECT MAX(id) as `id` FROM `games`");
        $row=mysqli_fetch_object($q);
        if($q){ $game->match_id = $row->id;}
        else{
            $message->status = "400";
            $message->title = "Failed to get new game id";
            $message->subcode = "475";
            $message->message = "";
            return $message;
        }
        // update the game progress table
        $q = $con->query("SELECT `status` FROM `progress` WHERE `game` = '$game->match_id'");  
        $row=mysqli_fetch_object($q);
        if($row != null)
        {   //update
            $q = mysqli_query($con, "UPDATE `progress` SET `status` = '0' WHERE `game` = '$game->match_id'"); 
            if(!$q){
                $message->status = "400";
                $message->title = "Failed to update game progress";
                $message->subcode = "476";
                $message->message = "";
                return $message;
            } 
        }
        else{ //insert
            $q = mysqli_query($con, "INSERT INTO `progress` (`game`, `status`) VALUES ('$game->match_id', '0')"); 
            if(!$q){
                $message->status = "400";
                $message->title = "Failed to add new game progress";
                $message->subcode = "477";
                $message->message = "";
                return $message;
            } 
        } 
        echo json_encode($message);
        return $message; 

?>