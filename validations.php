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
            $message->message = "Parameters are: sport, league, home, away, start_time, user" ;
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
        if($game->match_id == null){$paramList[]="match_id";}

        if($paramList != null){
            $message->status = $codes->error400;
            $message->subcode = "480";
            $message->title = "Missing parameter(s)" . json_encode($paramList);
            $message->message = "Parameters are: sport, league, home, away, start_time, whistle_start_time,match_id" ;
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
        if($game->match_id == null){$paramList[]="match_id";}
        
        if($paramList != null){
            $message->status = $codes->error400;
            $message->subcode = "485";
            $message->title = "Missing parameter(s)" . json_encode($paramList);
            $message->message = "Parameters are: sport, league, home, away, start_time, home_score, away_score, match_id" ;
        }
        else{ $message->status = $codes->success200;  }
        return $message;
    }

    function validateFinishGame($game){
        global $message;
        global $codes;
        global $paramList;
       
        if($game->sport == null){$paramList[]="sport";}
        if($game->league == null){$paramList[]="league";}
        if($game->home == null){$paramList[]="home";}
        if($game->away == null){$paramList[]="away";}
        if($game->start_time == null){$paramList[]="start_time";}
        if($game->whistle_end_time == null){$paramList[]="whistle_end_time";}
        if($game->match_id == null){$paramList[]="match_id";}

        if($paramList != null){
            $message->status = $codes->error400;
            $message->subcode = "490";
            $message->title = "Missing parameter(s)" . json_encode($paramList);
            $message->message = "Parameters are: sport, league, home, away, start_time, whistle_end_time,match_id" ;
        }
        else{ $message->status = $codes->success200;  }
        return $message;
    }

    function validateCancelGame($game){
        global $message;
        global $codes;
        global $paramList;
       
        if($game->sport == null){$paramList[]="sport";}
        if($game->league == null){$paramList[]="league";}
        if($game->home == null){$paramList[]="home";}
        if($game->away == null){$paramList[]="away";}
        if($game->start_time == null){$paramList[]="start_time";}
        if($game->match_id == null){$paramList[]="match_id";}

        if($paramList != null){
            $message->status = $codes->error400;
            $message->subcode = "495";
            $message->title = "Missing parameter(s)" . json_encode($paramList);
            $message->message = "Parameters are: sport, league, home, away, start_time, whistle_end_time, match_id" ;
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
            $message->title = "Whistle start time [" . $whistle_start_time . "] is before start time [" . $start_time . "]";
            $message->message = "Whistle start time must be equal to, or after, the start time";
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
            $message->subcode = "491";
            $message->title = "Whistle end time is before whistle start time";
            $message->message = "Whistle end time must be equal to, or after, the whistle start time";
        }
        else{ $message->status = $codes->success200; }
        return $message;
    }

    function validateProgress($match_id, $call){
        global $message;
        global $codes;

        if($call == "create"){
            // game can't already have been created
            $q = $con->query("SELECT `id` FROM progress WHERE `game`= '$match_id'");
            $row=mysqli_fetch_object($q);
            if($row != null){
                $message->status = $codes->error400;
                $message->subcode = "478";
                $message->title = "Game already exists";
                $message->message = "A game can't be created more than once";
            }
        }
        elseif($call == "in_progress"){
            // game can't already have started
            $message->status = $codes->error400;
            $message->subcode = "484";
            $message->title = "Game has already started";
            $message->message = "A game can't be started more than once";
        }
        elseif($call == "result"){
            // game must be in progress
            $message->status = $codes->error400;
            $message->subcode = "486";
            $message->title = "Game hasn't started";
            $message->message = "Scores can only be added to a game in progress";
        }
        elseif($call == "finish"){
            // game must have a result
            $message->status = $codes->error400;
            $message->subcode = "";
            $message->title = "Game must have a score";
            $message->message = "A game can't be finished until the scores are added";
        }
        elseif($call == "canceled"){
            // game must have not started or be in progress
            $message->status = $codes->error400;
            $message->subcode = "";
            $message->title = "Game can't be canceled";
            $message->message = "A game can only be canceled if it hasn't started, or if it's still in progress";
        }
        else{ $message->status = $codes->success200; }
        return $message;
    }
?>