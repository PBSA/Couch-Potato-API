<?php

    //header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    include "db.php"; 

    $league = $_GET['league'];
    $start = $_GET['start'];
    $end = $_GET['end'];

    $message = new stdClass();

   
    $games=array(); 
    $q = $con->query("SELECT * FROM games");
    while ($row=mysqli_fetch_object($q)){
        $games[]=$row; 
    }
   
    $data=array(); 
    $q = $con->query("SELECT * FROM couch_potato.vwgameevents WHERE league = '$league' AND datetime BETWEEN '$start' AND '$end'");

    while ($row=mysqli_fetch_object($q)){
        $data[]=$row; 
    }
     // first game ever we can allow query to return zero records, after that it's an error.
    if(count($data) != 0 || count($games == 0)){
        echo json_encode($data);
    }
    else{
        $message->status = "400";
        $message->title = "Failed to get all games in league [" . $league . "] for the date range [" . $start . "] to [". $end . "]";
        $message->subcode = "435";
        $message->message = "Invalid date range or parameters are missing";
        echo json_encode($message);
    }
?>