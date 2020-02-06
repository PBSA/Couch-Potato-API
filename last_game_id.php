<?php
include "db.php"; 
   
   $data=array(); 
   $q = $con->query("SELECT MAX(id) as `id` FROM games");

   $row=mysqli_fetch_object($q);
  
   echo json_encode($row);

?>