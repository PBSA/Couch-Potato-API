<?php
    include "db.php"; 
   
   $data=array(); 
   $q = $con->query("SELECT * FROM vwgameevents ORDER BY gameid DESC LIMIT 1");

   $row=mysqli_fetch_object($q);
  
   echo json_encode($row);

?>


