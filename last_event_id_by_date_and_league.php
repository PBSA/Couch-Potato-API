<?php
include "db.php"; 

    $date = $_GET['date'];
    $league = $_GET['league'];
   
   $data=array(); 
   $q = $con->query("SELECT MAX(id) as `id` FROM events WHERE `date` = '$date' AND `league` = '$league'");

   $row=mysqli_fetch_object($q);
  
   echo json_encode($row);

?>