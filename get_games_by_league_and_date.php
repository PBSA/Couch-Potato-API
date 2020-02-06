<?php
    include "db.php"; 
    $league = $_GET['league'];
    $date = $_GET['date'];
   
    $data=array(); 
    $q = $con->query("SELECT * FROM vwgameevents WHERE league = '$league' AND date = '$date'");

    while ($row=mysqli_fetch_object($q)){
        $data[]=$row; 
    }
   echo json_encode($data);
?>