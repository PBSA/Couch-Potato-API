<?php
    include "db.php"; 
    $sport = $_GET['sport'];
   
    $data=array(); 
    $q = $con->query("SELECT * FROM leagues WHERE sport = $sport");

    //echo $sport;

    while ($row=mysqli_fetch_object($q)){
        $data[]=$row; 
    }
   echo json_encode($data);
?>