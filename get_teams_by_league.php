<?php
    include "db.php"; 
    $league = $_GET['league'];
   
    $data=array(); 
    $q = $con->query("SELECT * FROM teams WHERE league = '$league'");

    while ($row=mysqli_fetch_object($q)){
        $data[]=$row; 
    }
   echo json_encode($data);
?>