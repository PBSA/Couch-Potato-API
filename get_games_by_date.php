<?php
    include "db.php"; 
    $start = $_GET['start'];
    $end = $_GET['end'];
    $league = $_GET['league'];
      
    $data=array(); 
    $q = $con->query("SELECT * FROM vwgameevents 
                    WHERE (league = '$league')");

    while ($row=mysqli_fetch_object($q)){
        $data[]=$row; 
    }
   echo json_encode($data);
?>