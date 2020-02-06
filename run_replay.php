<?php
include "db.php"; 

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
$witnesses =  witness_list();

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
            $incident = json_encode(make_incident($obj),JSON_UNESCAPED_SLASHES);
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

function witness_list(){
   // get config info for witness url list, read one line at a time
    $winesses=array();
    $file = fopen("work/config-dataproxy.yaml", "r") or die("Unable to open file!");
    while(! feof($file))
    {
      // only interested in lines with 'url:'
    $line =  fgets($file);
    if(strpos($line,'url:')){
            $witnesses[]= str_replace(' - url: ','',$line);
        }
    }
    fclose($file);
    return $witnesses;
}

function send_message($incident){
    global $witnesses;
    $error = 0;
    // send incident message to BOS witnesses
    foreach($witnesses as $witness) {
        
        $curl = curl_init(trim($witness)); // one witness for now
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

function log_success($msg, $url){
  global $con;
  $message = json_encode($msg->message);
  $q = mysqli_query($con,  "INSERT INTO `httplog` ( `type`,`uniqueid`,`approveid`, `message`, `url` ) 
                                VALUES ('success', '$msg->id', '$msg->id_approve', '$message', '$url')");             
}

function log_error(){
    global $con;
    $errormessage = json_encode($msg->message);
    $q = mysqli_query($con,  "INSERT INTO `replay_error_log` ( `url`,`message`,`incident` ) 
                                VALUES ('$url','$message', '$incident')");
}

function make_incident($game){
    // make a create incident message

    $timestamp = gmdate("Y-m-d") . 'T' . gmdate("H:i:s") . '.000Z';
    $starttime = rtrim(gmdate(DATE_ISO8601, strtotime($game['date'] . "T" . $game['starttime'])),':+00000') . ':00.000Z';
    $unique_string = unique_string($starttime, $game);
    
    $incident = new stdClass();
    $incident->call = 'create';
    $incident->unique_string = $unique_string;
    $incident->timestamp = $timestamp;

    $arguments = new stdClass();
    $arguments->season = '2019/2020';

    $id = new stdClass();
    $id->home = $game['hometeam'];
    $id->away = $game['awayteam'];
    $id->sport = $game['sportname'];
    $id->start_time = $starttime;
    $id->event_group_name = $game['league'];

    $provider_info = new stdClass();
    $provider_info->match_id = $game['gameid'];
    $provider_info->name = 'carrot';
    $provider_info->source = 'direct string input';
    $provider_info->source_file = '';
    $provider_info->pushed = $timestamp;

    $incident->arguments = $arguments;
    $incident->id = $id;
    $incident->provider_info = $provider_info;

    return $incident;
}

function unique_string($starttime, $game){
    // create a unique incident identifier
    $uniquestring = $starttime . '__';
    $uniquestring .= format_text($game['sportname']) . "__" . format_text($game['league']) . "__";
    $uniquestring .= format_text($game['hometeam']) . "__" . format_text($game['awayteam']) . "__";
    $uniquestring .= 'create__20192020';
    return strtolower($uniquestring);
}

function format_text($string){
    // replace space with underscores
    return str_replace(' ', '_', $string);
}

function output_results($league, $count){
    global $output;
    $output .= $league . ': ' . $count . ', ';
}

?>