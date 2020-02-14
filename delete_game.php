<?php

    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    include "db.php"; 

    $message = array(); 
    $id = $_GET['id']; 

    $q = mysqli_query($con, "DELETE FROM `games` WHERE `id` = $id"); 
    if($q){
        $message->title = "Game deleted";
        $message->message = $game->$id;   
    }
    else{
        $message->status = "400";
        $message->title = "Failed to delete game id [" . $id . "]";
        $message->subcode = "431";
        $message->message = "Game might not exist or parameters are missing.";
    }
?>