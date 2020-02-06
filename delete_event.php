<?php
    include "db.php"; 

    $message = array(); 
    $date = $_GET['date']; 
    $league = $_GET['league']; 

    $q = mysqli_query($con, "DELETE FROM `events` WHERE `date` = '$date' AND `league` = '$league'"); 
    if($q){
        $message['status'] = "success"; 
    }
    else{
        $message['status'] = "error";
    }
    echo ($q);
    echo mysqli_error($con); 
?>