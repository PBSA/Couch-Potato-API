<?php

    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);    
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    include "db.php"; 

    $league = $_GET['league'];
    $message = new stdClass();
   
    $data=array(); 
    $q = $con->query("SELECT * FROM couch_potato.leagues WHERE name = '$league'");

    $row=mysqli_fetch_object($q);
    if($row != null){
        echo json_encode($row);
    }
    else{
        $message->status = "400";
        $message->title = "Failed to get league details for [" . $league . "]";
        $message->subcode = "446";
        $message->message = "";
        echo json_encode($message);
    }
?>