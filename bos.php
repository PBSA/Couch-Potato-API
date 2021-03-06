<?php
    function bos_Send($game){

    // send incidents/triggers to BOS
    
    include "incidents.php";
    include "logging.php";
    include "get_config.php";

    $witnesses = $config->subscriptions->witnesses;
    $message = new stdClass();
    $errors = new stdClass();
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
            $message->message = $incident;
            log_error($message, $witness->url);
        }
        else{
            curl_close($curl);
            $decoded = json_decode($curl_response);
            if($decoded == null){
                    $errors->status = "400";
                    $errors->title = $curl_response;
                    if($errors->title == "Not normalized incident"){
                        // if this fails for one BOS node it will fail for all
                        $errors->subcode = "450";
                    }
                    elseif($errors->title == "Invalid data format"){
                        // if this fails for one BOS node it will fail for all
                        $errors->subcode = "451"; 
                    }
                    else{
                        $errors->title = "Internal server error";
                        $errors->subcode = "452";
                    }
                    $errors->message = $incident;
                    log_error($errors, $witness->url);
            }
            else{   
                log_incident(json_decode($incident), $witness->url);
                log_success($decoded, $witness->url);
                $good++;
            }
        }
    }  
    if($good > 0)
    {
        // at least one end point was reached
        $message->status = "200";
        $message->message = "[" . $good . " of " . count($witnesses) . " subscribers reached]";
        return $message;
    }
    else
    {
        // could have failed for various reasons, most likely not normalized or bad format
        return $errors;
    }
}

?>