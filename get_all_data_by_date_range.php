<?php

    //header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    include "db.php"; 

    $start = $_GET['startdate'];
    $end = $_GET['enddate'];

    $message = new stdClass();

    $data=array(); 
    $q = $con->query("SELECT * FROM couch_potato.vwgameevents WHERE `datetime` BETWEEN '$start' AND '$end'");

    while ($row=mysqli_fetch_object($q)){
        $data[]=$row; 
    }
    
    if(!$q){
        $message->status = "400";
        $message->title = "Failed to get all games in the range [" . $start . "] to [". $end . "]";
        $message->subcode = "432";
        $message->message = "Invalid date range or parameters are missing";
        echo json_encode($message);
    }
    else{
        echo json_encode($data);
    }

?>