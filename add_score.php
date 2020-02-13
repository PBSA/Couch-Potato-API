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
        $game->match_id = $data['match_id'];
        $game->home_score = $data['home_score'];
        $game->away_score = $data['away_score'];
        $game->call = 'result';

        $message = new stdClass;

        // ******************************************
        // *********** validate first ***************
        // ******************************************

        // score can only be added if status is 'In Progress' (1)

        // are all parameters sent
        $retval = validateAddScore($game); 
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

        // game must be in progress
        /*$retval = validateProgress($game->match_id, 'result');
        if($retval->status !=  $codes->success200){
            echo json_encode($retval);
            return false;
        } */
  
         // send BOS incident
         $retval = bos_Send($game);

         if($retval->status == '200'){
                // update the score
                $q = mysqli_query($con, "UPDATE `games` SET  `homescore` = '$game->home_score',`awayscore` = '$game->away_score' 
                                            WHERE `id` = '$game->match_id'"); 
                if($q){
                    $message->status = "200";
                    $message->title = "Scores added";
                    $message->message = $game->home . " " . $game->home_score . " v " . $game->away . " " . $game->away_score;   
                }
                else{
                    $message->status = "400";
                    $message->subcode = "487";
                    $message->title = "Failed to add score";
                    $message->message = "";
                } 
                echo json_encode($message);
                return $message;          
        }
        else{
            //echo json_encode($retval);
            return $retval;
        }
?>