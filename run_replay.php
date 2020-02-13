<?php

include "db.php"; 
include "incidents.php";
include "logging.php";
include "get_config.php";

$sport = $_GET['sport'];
$leagues = $_GET['leagues'];
$start = $_GET['start'];
$end = $_GET['end'];
$error = 0;
$output;
$info;


// ******************************************
// *********** validate first ***************
// ******************************************

// get witnesses
$witnesses = $config->subscriptions->witnesses;

// parse list of leagues and add quotes. Uses pipe seperator as comma screws with the URL
$str_arr = explode ("|", $leagues);  

// do one league at a time
foreach ($str_arr as $league)  {
    // get the data
    $data = array(); 
    $game = '';
    $error = 0;
    $count=0;
    $result = $con->query("SELECT * FROM vwgameevents WHERE  `sportname` = '$sport' AND `league` = '$league' 
                            AND `status` = 0 AND `date` BETWEEN '$start' AND '$end'");

    while ($obj = mysqli_fetch_array($result)) {
            $incident = json_encode(make_incident($obj, $config),JSON_UNESCAPED_SLASHES);
            $count++;
            if(send_message($incident) == false){$error++;};
        } 
        output_results($league,$count);  
    }
    if($error > 0){
        $info = 'Incidents failed to send to all witness nodes - see error log';  
    }
    else($info = 'Success');
    echo json_encode($sport .': {' . rtrim($output,', ') . '} info: ' . $info);


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
            // log error
            log_replay_error($info);
        }
        else{
            curl_close($curl);
            $decoded = json_decode($curl_response);
            if($decoded->id == null){
               $error++;
            }
            else{ log_success($decoded, $witness);}
        }
    }
    if($error > 0){
        // at least one witness wasn't reached. 
        return false;
    }
    else
        return true;
    }

    function output_results($league, $count){
        global $output;
        $output .= $league . ': ' . $count . ', ';
    }

?>