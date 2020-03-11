<?php

    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    include "db.php"; 

    $date = $_GET['date'];
    $league = $_GET['league'];
    $message = new stdClass();
   
   $data=array(); 
   $q = $con->query("SELECT MAX(gameid) as `id` FROM couch_potato.vwgameevents WHERE `date` = '$date' AND `league` = '$league'");

   $row=mysqli_fetch_object($q);
   if($row != null){
    echo json_encode($row);
    }
    else{
        $message->status = "400";
        $message->title = "Failed to get the last game id for [" . $league . "] on [" . $date . "]";
        $message->subcode = "442";
        $message->message = "";
        echo json_encode($message);
    }

?>