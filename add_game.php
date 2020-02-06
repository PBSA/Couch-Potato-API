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
$game->user = $data['user'];
$game->home = $data['home'];
$game->away = $data['away'];
$game->start_time = $data['start_time'];
$game->date = substr($game->start_time,0,10);
$game->time = substr($game->start_time,11,5);
$game->call = 'create';

$message = new stdClass;

// ******************************************
// *********** validate first ***************
// ******************************************

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

/* is start time valid
$retval = validateDateTime($game->start_time);
if($retval->status !=  $codes->success200){
    echo json_encode($retval);
    return false;
}*/

// is user valid
$retval = validateUser($game->user);
if($retval->status !=  $codes->success200){
    echo json_encode($retval);
    return false;
}


// ********************************************
// ** Done validating, now do some real work **
// ********************************************


// send new game to BOS
$retval =  bos_Send($game);   

if($retval == 'success'){

        // if this API call is made from the client app then the local start time will need to be used for creating
        // new events and games.
        if($data['local_start_time'] != null){
            $game->date = $data['local_start_date'];
            $game->time = $data['local_start_time'];
        }

        // get the last event id
        $q = $con->query("SELECT MAX(id) as `id` FROM events WHERE `date` = '$game->date' AND `league` = '$game->league'");
        $row=mysqli_fetch_object($q);
        if($q){
            $message->status = "Success"; 
        }
        else{
            $message->message = "Failed to get last event id";
            $message->status = "Error"; 
            echo json_encode($message); 
            return false;
        }

        // if this is the first game then a new event has to be created as well
        if($row->id == null){
            // first game so add new event
            $q = mysqli_query($con, "INSERT INTO `events` ( `user`, `league`, `date` ) 
                                        VALUES ('$game->user', '$game->league', '$game->date')"); 
            if($q){
                $message->status = "Success"; 
            }
            else{
                $message->status = "Error"; 
                $message->message = "Failed to add new event";
                echo json_encode($message); 
            }

            // get the new event id
            $q = $con->query("SELECT MAX(id) as `id` FROM events");
            $row=mysqli_fetch_object($q);
            if($q){
                $game->eventid = $row->id ;
            }
            else{
                $message->status = "Error";
                $message->message = "Failed to get new event id";
                echo json_encode($message); 
            }
        }
        else{$game->eventid = $row->id;}
        // add game
        addGame($game);
    }
    else{
        //echo json_encode($retval);
    }


 function addGame($game){
     // adds a new game 
   
    global $con;
    global $message;

    $q = mysqli_query($con, "INSERT INTO `games` ( `user`, `event`, `hometeam`,`awayteam`,`starttime` ) 
                                VALUES ('$game->user', '$game->eventid', '$game->home', '$game->away', '$game->time')"); 
    if($q){
        $message->status = "Success"; 
    }
    else{
        $message->status = "Error"; 
        $message->message = "Failed to add new game";
        echo json_encode($message); 
        return false;
    } 
    
    // get the last id
    $q = $con->query("SELECT MAX(id) as `id` FROM `games`");
    $row=mysqli_fetch_object($q);
    if($q){
        $game->id = $row->id;
        $message->status = "Success"; 
    }
    else{
        $message->status = "Error"; 
        $message->message = "Failed to get new game id";
        echo json_encode($message); 
        return false;
    } 
    
    // insert the game progress. Set to 'Not Started'
    $q = mysqli_query($con, "INSERT INTO `progress` (`status`,`game`) VALUES ('0','$game->id')"); 
    if($q){
        $message->status = "Success"; 
    }
    else{
        $message->status = "Error";
        $message->message = "Failed to update game progress";
        echo json_encode($message); 
        return false;
    }
    $game->matchid = $game->id;
}

?>