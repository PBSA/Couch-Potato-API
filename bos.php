<?php
function bos_Send($game){
    // send a create incident to BOS
   
   include "get_witnesses.php";
   include "incidents.php";
   include "logging.php";

   $witnesses = getWitnesses();

   $message = new stdClass;

  // send incident message to all BOS witnesses
  foreach($witnesses as $witness) {
    $curl = curl_init(trim($witness));
    $incident = json_encode(make_incident($game),JSON_UNESCAPED_SLASHES);
    //echo json_encode($incident);
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
        $message->title = "Unknown error";
        $message->message = $info;
        echo json_encode($message);
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
                echo json_encode($message);
            }
            else{    
                log_incident(json_decode($incident));
                log_success($decoded, $witness);
                $message->status = "200";
                $message->title = "Incident added";
                $message->message = $decoded;
                echo json_encode($message);
                return 'success';
            }
        }
    }  
}
?>