<?php
    include "db.php"; 
    $month = $_GET['month'];
    $year = $_GET['year'];
    $league = $_GET['league'];
      
    $data=array(); 
    $q = $con->query("SELECT * FROM vwgameevents 
                    WHERE (month(date) = $month) AND (year(date) = $year) AND (league = '$league')");

    while ($row=mysqli_fetch_object($q)){
        $data[]=$row; 
    }
   echo json_encode($data);
?>