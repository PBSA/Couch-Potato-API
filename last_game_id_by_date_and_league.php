<?php
include "db.php"; 

    $date = $_GET['date'];
    $league = $_GET['league'];
   
   $data=array(); 
   $q = $con->query("SELECT MAX(gameid) as `id` FROM vwgameevents WHERE `date` = '$date' AND `league` = '$league'");

   $row=mysqli_fetch_object($q);
  
   echo json_encode($row);

?>