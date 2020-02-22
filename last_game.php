<?php

    //header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    include "db.php"; 

    $message = new stdClass();
   
   $data=array(); 
   $q = $con->query("SELECT * FROM couch_potato.gameevents ORDER BY gameid DESC LIMIT 1");

   $row=mysqli_fetch_object($q);
    if($row != null){
        echo json_encode($row);
    }
    else{
        $message->status = "400";
        $message->title = "Failed to get the last game for all sports";
        $message->subcode = "444";
        $message->message = "";
        echo json_encode($message);
    }
?>


