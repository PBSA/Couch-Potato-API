<?php

    //header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    include "db.php"; 
   
    $data=array(); 
    $q = $con->query("SELECT * FROM games");

    while ($row=mysqli_fetch_object($q)){
        $data[]=$row; 
    }
    if(count($data) != 0){
        echo json_encode($data);
    }
    else{
        $message->status = "400";
        $message->title = "Failed to get all games";
        $message->subcode = "433";
        $message->message = "";
        echo json_encode($message);
    }

?>