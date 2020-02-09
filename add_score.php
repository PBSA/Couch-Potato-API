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

        // ******************************************
        // *********** validate first ***************
        // ******************************************


        // score can only be added if status is 'In Progress' (1)


         // send BOS incident
         $retval = bos_Send($game);
         if($retval == 'success'){
            // update the score
            $q = mysqli_query($con, "UPDATE `games` SET  `homescore` = '$game->home_score',`awayscore` = '$game->away_score' 
                                        WHERE `id` = '$game->match_id'"); 
            if($q){
                $message['status'] = "Success"; 
            }
            else{
                $message['status'] = "Error";
                $message['message'] = "Failed to add score";
                $message->status = "400";
            $message->title = "Failed to update game progress";
            $message->subcode = "481";
            $message->message = "";
                echo json_encode($message);        
            }
        }
        else{echo json_encode($retval);}
?>