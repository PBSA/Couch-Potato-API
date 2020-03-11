<?php

    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    include "db.php"; 

    $sport = $_GET['sport'];
    $message = new stdClass();
   
    $data=array(); 
    $q = $con->query("SELECT * FROM leagues WHERE sport = $sport");

    while ($row=mysqli_fetch_object($q)){
        $data[]=$row; 
    }
    if(count($data) != 0){
        echo json_encode($data);
    }
    else{
        $message->status = "400";
        $message->title = "Failed to get all leagues for sport [" . $sport . "]";
        $message->subcode = "437";
        $message->message = "";
        echo json_encode($message);
    }
?>