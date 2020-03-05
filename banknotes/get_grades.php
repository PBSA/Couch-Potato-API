<?php

    //header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    include "db.php"; 

   
    $data=array(); 
    $q = $con->query("SELECT * FROM `grade`");

    while ($row=mysqli_fetch_object($q)){
        $data[]=$row; 
    }
    if(count($data) != 0){
        echo json_encode($data);
    }
    else{
       
        echo json_encode("error");
    }

?>