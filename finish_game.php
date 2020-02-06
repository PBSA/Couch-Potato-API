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
        $game->call = 'finish';
        $game->sport = $data['sport']; 
        $game->league = $data['league']; 
        $game->home = $data['home'];
        $game->away = $data['away'];
        $game->start_time = $data['start_time'];
        $game->whistle_end_time = $data['whistle_end_time'];
        $game->match_id  = $data['match_id'];       

        // ******************************************
        // *********** validate first ***************
        // ******************************************

        // make sure sport is valid

        // make sure league is valid

        // make sure both teams are valid

        // make sure teams are not the same

        // game can only be finished if score has been added

         // send BOS incident
         $retval = bos_Send($game);

         if($retval == 'success'){
            // update progress status. Set to 'Finished'
            $q = mysqli_query($con, "UPDATE `progress` SET  `status` = '4' WHERE `game` = '$game->match_id'");  
            if($q){
                $message['status'] = "Success";      
            }
            else{
                $message['status'] = "Error";
                $message['message'] = "Failed to update score";
                echo json_encode($message); 
            }
        }
        else{echo json_encode($retval);}

?>