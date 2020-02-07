<?php
    include "db.php"; 
    $start = $_GET['startdate'];
    $end = $_GET['enddate'];


    $data=array(); 
    $q = $con->query("SELECT * FROM vwgameevents WHERE `datetime` BETWEEN '$start' AND '$end'");

    while ($row=mysqli_fetch_object($q)){
        $data[]=$row; 
    }
   echo json_encode($data);
?>