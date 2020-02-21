<?php

    //header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    
    include "db.php"; 

    $message = new stdClass;

    $date = $_GET['date']; 
    $league = $_GET['league']; 

    $q = mysqli_query($con, "DELETE FROM `events` WHERE `date` = '$date' AND `league` = '$league'"); 
    if($q){
        $message->title = "League deleted";
        $message->message = $league;   
    }
    else{
        $message->status = "400";
        $message->title = "Failed to delete league [" . $league . "]";
        $message->subcode = "430";
        $message->message = "League might not exist or parameters are missing.";
    }
    echo json_encode($message);
?>