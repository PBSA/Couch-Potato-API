<?php

    include "db.php";

    function log_success($msg, $url){
        global $con;
        $message = json_encode($msg->message);
        $q = mysqli_query($con,  "INSERT INTO `httplog` ( `type`,`uniqueid`,`approveid`, `message`, `url` ) 
                                    VALUES ('success', '$msg->id', '$msg->id_approve', '$message', '$url')");             
    }

    function log_replay_error($msg, $url, $incident){
        global $con;
        $errormessage = json_encode($msg->message);
        $q = mysqli_query($con,  "INSERT INTO `replay_error_log` ( `url`,`message`,`incident` ) 
                                    VALUES ('$url','$message', '$incident')");
    }

    function log_error($msg, $url){
        global $con;
        $message = json_encode($msg->message);
        $q = mysqli_query($con,  "INSERT INTO `errorlog` ( `status`,`subcode`,`title`,`message`, `url` ) 
                                    VALUES ('$msg->status','$msg->subcode', '$msg->title', '$message', '$url')");
    }

    function log_incident($data, $url){
        global $con;
        $msg = json_encode($data);
        $q = mysqli_query($con,  "INSERT INTO `incidents` (`timestamp`, `uniquename`, `call`, `message`, `url` ) 
                                    VALUES ('$data->timestamp', '$data->unique_string', '$data->call', '$msg', '$url')");  
        if($q){
            $message['status'] = "success"; 
        }
        else{
            $message['status'] = "error";
        }
    
        //save incident to dump file as well.
        //$myfile = fopen("dump/" . $data->unique_string . ".json", "w") or die("Unable to create file!");
        //fwrite($myfile, $msg);
        //fclose($myfile);    
        echo mysqli_error($con); 
    }

?>