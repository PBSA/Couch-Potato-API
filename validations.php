<?php

    include "db.php"; 

    $data=array(); 
    $message = new stdClass;
    $codes = new stdClass;
    $paramList = array();
    $codes->error400 = "400: Bad Request";
    $codes->success200 = "Success";

    function validateAddGame($game){
        global $message;
        global $codes;
        global $paramList;
       
        if($game->sport == null){$paramList[]="sport";}
        if($game->league == null){$paramList[]="league";}
        if($game->home == null){$paramList[]="home";}
        if($game->away == null){$paramList[]="away";}
        if($game->start_time == null){$paramList[]="start_time";}
        if($game->user == null){$paramList[]="user";}

        if($paramList != null){
            $message->status = $codes->error400;
            $message->subcode = "470";
            $message->title = "Missing parameters" . json_encode($paramList);
            $message->message = "Parameters are: sport, league, home, away, start_time, user, [optional] match_id" ;
        }
        else{ $message->status = $codes->success200;  }
        return $message;
    }


    function validateSport($sport){
        global $con;
        global $message;
        global $codes;

        // get all sports and check for match
        $q = $con->query("SELECT `name` FROM sports");
        while ($row=mysqli_fetch_object($q)){
            $data[]=$row->name; 
        }
        if(array_search($sport,$data) === false){
            $message->status = $codes->error400;
            $message->subcode = "460";
            $message->title = "Invalid sport [" . $sport . "]";
            $message->message = "Try one of: " . json_encode($data);
        }
        else{ $message->status = $codes->success200;  }
        return $message;
    }


    function validateLeague($sport, $league){
        global $con;
        global $message;
        global $codes;

        // get all leagues for selected sport and check for match
        $q = $con->query("SELECT `leaguename` FROM vwsports WHERE `sportsname`= '$sport'");
        while ($row=mysqli_fetch_object($q)){
            $data[]=$row->leaguename; 
        }
        if(array_search($league, $data) === false){
            $message->status = $codes->error400;
            $message->subcode = "461";
            $message->title = "Invalid league [" . $league . "]";
            $message->message = "Try one of: " . json_encode($data);
        }
        else{ $message->status = $codes->success200; }
        return $message;
    }

    function validateTeam($league, $team, $type){
        global $con;
        global $message;
        global $codes;

        // get all teams for selected league and check for match
        $q = $con->query("SELECT `teamname` FROM vwteams WHERE `leaguename`= '$league'");
        while ($row=mysqli_fetch_object($q)){
            $data[]=$row->teamname; 
        }
        if(array_search($team, $data) === false){      
            $message->status = $codes->error400;
            if($type == "home team") {
                $message->subcode = "462";}
            else{
                $message->subcode = "463";}
            $message->title = "Invalid " . $type . " [" . $team . "]";
            $message->message = "Try one of: " . json_encode($data);
        }
        else{ $message->status = $codes->success200; }
        return $message;
    }

    function validateDateTime($datetime){
        global $message;
        global $codes;
/*
            $message->status = $codes->error400;
            $message->subcode = "464";
            $message->title = "Invalid DateTime" . " [" . $datetime . "]";
            $message->message = "Format should be [YYYY-MM-DDTHH:MM:SS.000Z";
        
        return $message; */
    }

    function validateBothTeams($home, $away){
        global $message;
        global $codes;

        // are the teams the same
        if($home == $away){
            $message->status = $codes->error400;
            $message->subcode = "465";
            $message->title = "Invalid teams selection [" . $home . " v " . $away . "]";
            $message->message = "Teams must be different";
        }
        else{ $message->status = $codes->success200; }
        return $message;
    }

    function validateUser($user){
        global $con;
        global $message;
        global $codes;

        // get all leagues for selected sport and check for match
        $q = $con->query("SELECT `id` FROM users WHERE `id`= '$user'");
        $row=mysqli_fetch_object($q);

        if($row == null){
            $message->status = $codes->error400;
            $message->subcode = "466";
            $message->title = "Invalid user id [" . $user . "]";
            $message->message = "";
        }
        else{ $message->status = $codes->success200; }
        return $message;
    }
?>