<?php

    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    include "db.php"; 
    $message = new stdClass();
   
    $data=array(); 
    $q = $con->query("SELECT * FROM couch_potato.vwsports");

    while ($row=mysqli_fetch_object($q)){
        $data[]=$row; 
    }
   if(count($data) != 0){
        echo json_encode($data);
    }
    else{
        $message->status = "400";
        $message->title = "Failed to get all sports and leagues";
        $message->subcode = "438";
        $message->message = "";
        echo json_encode($message);
    }
?>