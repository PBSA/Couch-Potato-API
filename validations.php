<?php

    include "db.php"; 

    $data=array(); 
    $message = new stdClass;
    $codes = new stdClass;
    $paramList = array();
    $codes->error400 = "400";
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
            $message->title = "Missing parameter(s)" . json_encode($paramList);
            $message->message = "Parameters are: sport, league, home, away, start_time, user, [optional] match_id" ;
        }
        else{ $message->status = $codes->success200;  }
        return $message;
    }

    function validateStartGame($game){
        global $message;
        global $codes;
        global $paramList;
       
        if($game->sport == null){$paramList[]="sport";}
        if($game->league == null){$paramList[]="league";}
        if($game->home == null){$paramList[]="home";}
        if($game->away == null){$paramList[]="away";}
        if($game->start_time == null){$paramList[]="start_time";}
        if($game->whistle_start_time == null){$paramList[]="whistle_start_time";}

        if($paramList != null){
            $message->status = $codes->error400;
            $message->subcode = "480";
            $message->title = "Missing parameter(s)" . json_encode($paramList);
            $message->message = "Parameters are: sport, league, home, away, start_time, whistle_start_time, [optional] match_id" ;
        }
        else{ $message->status = $codes->success200;  }
        return $message;
    }

    function validateAddScore($game){
        global $message;
        global $codes;
        global $paramList;
       
        if($game->sport == null){$paramList[]="sport";}
        if($game->league == null){$paramList[]="league";}
        if($game->home == null){$paramList[]="home";}
        if($game->away == null){$paramList[]="away";}
        if($game->start_time == null){$paramList[]="start_time";}
        if($game->home_score == null){$paramList[]="home_score";}
        if($game->away_score == null){$paramList[]="away_score";}
        
        if($paramList != null){
            $message->status = $codes->error400;
            $message->subcode = "485";
            $message->title = "Missing parameter(s)" . json_encode($paramList);
            $message->message = "Parameters are: sport, league, home, away, start_time, home_score, away_score, [optional] match_id" ;
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

    function validateStartAndWhistleStart($start_time, $whistle_start_time){
        global $message;
        global $codes;

        // whistle start time must be after start time.
        if($whistle_start_time < $start_time){
            $message->status = $codes->error400;
            $message->subcode = "481";
            $message->title = "Whistle start time is before start time";
            $message->message = "whistle_start_time must be equal to, or after, the start_time";
        }
        else{ $message->status = $codes->success200; }
        return $message;
    }

    function validateWhistleStartAndWhistleEnd($start_time, $whistle_start_time){
        global $message;
        global $codes;

        // whistle start time must be after start time.
        if($whistle_start_time < $start_time){
            $message->status = $codes->error400;
            $message->subcode = "481";
            $message->title = "Whistle end time is before whistle start time";
            $message->message = "whistle_end_time must be equal to, or after, the whistle_start_time";
        }
        else{ $message->status = $codes->success200; }
        return $message;
    }
?>