<?php
    include "db.php"; 

    $message = array(); 
    $id = $_GET['id']; 

    $q = mysqli_query($con, "DELETE FROM `games` WHERE `id` = $id"); 
    if($q){
        $message['status'] = "success"; 
    }
    else{
        $message['status'] = "error";
    }
    echo ($q);
    echo mysqli_error($con); 
?>