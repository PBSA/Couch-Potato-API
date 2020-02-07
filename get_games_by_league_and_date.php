<?php
    include "db.php"; 
    $league = $_GET['league'];
    $start = $_GET['start'];
    $end = $_GET['end'];
   
    $data=array(); 
    $q = $con->query("SELECT * FROM vwgameevents WHERE league = '$league' AND datetime BETWEEN '$start' AND '$end'");

    while ($row=mysqli_fetch_object($q)){
        $data[]=$row; 
    }
   echo json_encode($data);
?>