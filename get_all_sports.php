<?php
    include "db.php"; 
   
    $data=array(); 
    $q = $con->query("SELECT * FROM sports");

    while ($row=mysqli_fetch_object($q)){
        $data[]=$row; 
    }
   echo json_encode($data);
?>