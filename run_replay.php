<?php

include "db.php"; 
include "logging.php";
include "incidents.php";

include "get_config.php";

$sport = $_GET['sport'];
$leagues = $_GET['leagues'];
$start = $_GET['start'];
$end = $_GET['end'];
$replay = new stdClass();
$error = 0;
$total = 0;
$output;
$message = new stdClass();


// get witnesses
$witnesses = $config->subscriptions->witnesses;

// parse list of leagues and add quotes. Uses pipe seperator as comma screws with the URL
$str_arr = explode ("|", $leagues);  

// do one league at a time
foreach ($str_arr as $league)  {
        // get the data
        $data = array(); 
        $game = '';
        $count=0;
        $q = $con->query("SELECT * FROM couch_potato.vwgameevents WHERE  `sportname` = '$sport' AND `league` = '$league' 
                                AND `status` = 0 AND `date` BETWEEN '$start' AND '$end'");
        
        while ($row=mysqli_fetch_object($q)){
            $replay->call = "create";
            $replay->sport = $row->sportname; 
            $replay->league = $row->league; 
            $replay->home = $row->hometeam;
            $replay->away = $row->awayteam;
            $replay->start_time = str_replace(' ', 'T',$row->datetime) . ":00.000Z";
            $replay->match_id  = $row->gameid;
            $incident = json_encode(make_incident($replay, $config),JSON_UNESCAPED_SLASHES); 
          
            if(send_message($incident) > 0 ){
                $error++;
            }
            else{
                $count++;
                $total++;
            }
        } 
        output_results($league,$count);     
    }
    if($error > 0 || $total == 0){
        $message->status = "400";
        $message->title = "Failed to run replay for [" . $sport . "]";
        $message->subcode = "445";
        $message->message = "Replay failed to send to one or more endpoints";
    }
    else{
        $message->status = "200";
        $message->title = "Replay completed";
        $message->message = $sport .': {' . rtrim($output,', ') . '}';
    }
    echo json_encode($message);


function send_message($incident){
    global $witnesses;
    $error = 0;
    // send incident message to BOS witnesses
    foreach($witnesses as $witness) {
        
        $curl = curl_init(trim($witness->url)); // one witness for now
        $headers = [
            'Content-Type: application/json'
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $incident);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        $curl_response = curl_exec($curl);
        
        if ($curl_response === false) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            $error++;
        }
        else{
            curl_close($curl);
            $decoded = json_decode($curl_response);
            if($decoded->id == null){
               $error++;
            }
            else{ 
                log_incident(json_decode($incident), $witness->url);
                log_success($decoded, $witness->url);
                //echo json_encode($decoded);
            }
        }
    }
    return $error;
}

function output_results($league, $count){
    global $output;
    $output .= $league . ': ' . $count . ', ';
}

?>