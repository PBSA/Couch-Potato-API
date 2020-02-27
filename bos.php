<?php
function bos_Send($game){
    // send a create incident to BOS
   
   include "incidents.php";
   include "logging.php";
   include "get_config.php";

   $witnesses = $config->subscriptions->witnesses;
   $message = new stdClass();
   $good = 0;

  
  
  // send incident message to all BOS witnesses
  foreach($witnesses as $witness) {
   
    $curl = curl_init(trim($witness->url));
    $incident = json_encode(make_incident($game, $config),JSON_UNESCAPED_SLASHES);
    $headers = ['Content-Type: application/json'];
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $incident);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
    $curl_response = curl_exec($curl);
    if ($curl_response === false) {
        $info = curl_getinfo($curl);
        curl_close($curl);
        $message->status = "520";
        $message->subcode = "520";
        $message->title = "BOS server may not be available";
        $message->message = $info;
        log_error($message, $witness->url);
    }
    else{
        curl_close($curl);
        $decoded = json_decode($curl_response);
        if($decoded == null){
                $message->status = "400";
                $message->title = $curl_response;
                if($message->title == "Not normalized incident"){$message->subcode = "450";}
                if($message->title == "Invalid data format"){$message->subcode = "451";}
                $message->message = $incident;
                //log_error($message, $witness->url);
               
            }
            else{   
                log_incident(json_decode($incident), $witness->url);
                log_success($decoded, $witness->url);
                $good++;
            }
        }
    }  
    $message->status = "200";
    $message->title = "Incident sent";
    $message->message = "[" . $good . " of " . count($witnesses) . "] subscribers reached";
    return $message;
}

?>